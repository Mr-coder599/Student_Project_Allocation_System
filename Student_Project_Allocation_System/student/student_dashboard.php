<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentproj_db');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Prevent SQL Injection
$student_id = $conn->real_escape_string($student_id);

// Get student details along with supervisor details
$query = "SELECT students.*, supervisors.name AS supervisor_name, supervisors.id AS supervisor_id
          FROM students
          LEFT JOIN supervisors ON students.supervisor_id = supervisors.id
          WHERE students.id = '$student_id'";

$result = $conn->query($query);
$student = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

$supervisor_name = $student['supervisor_name'] ?? "Not Assigned";
$supervisor_id = $student['supervisor_id'] ?? null;

// Get project files
$query_files = "SELECT * FROM project_files WHERE student_id = '$student_id'";
$result_files = $conn->query($query_files);
$files = ($result_files && $result_files->num_rows > 0) ? $result_files->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            color: #fff;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #ddd;
            display: block;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #575757;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card-custom {
            margin-top: 20px;
        }

        .comment-box {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .comment-box .comment-header {
            font-weight: bold;
            color: #007bff;
        }

        .comment-box .comment-body {
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Student Dashboard</h4>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="upload_project.php">Upload Project</a>
        <a href="student_profile.php">Profile</a>
        <a href="../student/student_login.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($student['name'] ?? 'Student'); ?></h1>

            <!-- Supervisor Information Card -->
            <div class="card card-custom">
                <div class="card-header">
                    <h5>Assigned Supervisor</h5>
                </div>
                <div class="card-body">
                    <p><strong>Supervisor Name:</strong> <?php echo htmlspecialchars($supervisor_name); ?></p>
                </div>
            </div>

            <!-- Project Files Card -->
            <div class="card card-custom">
                <div class="card-header">
                    <h5>Your Project Files & Comments</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($files)): ?>
                        <ul class="list-group">
                            <?php foreach ($files as $file): ?>
                                <li class="list-group-item">
                                    <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($file['file_name']); ?>
                                    </a>
                                    <p class="small text-muted">Uploaded on: <?php echo htmlspecialchars($file['upload_date']); ?></p>

                                    <!-- Fetch Comments for this File -->
                                    <?php
                                    $file_id = $file['id'];
                                    $query_comments = "SELECT * FROM comments WHERE file_id = '$file_id' AND supervisor_id = '$supervisor_id' ORDER BY created_at DESC";
                                    $result_comments = $conn->query($query_comments);
                                    ?>

                                    <?php if ($result_comments && $result_comments->num_rows > 0): ?>
                                        <div class="comment-box">
                                            <div class="comment-header">Supervisor's Comments:</div>
                                            <?php while ($comment = $result_comments->fetch_assoc()): ?>
                                                <div class="comment-body"><?php echo htmlspecialchars($comment['comment']); ?></div>
                                                <small class="text-muted">Posted on: <?php echo htmlspecialchars($comment['created_at']); ?></small>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="small text-muted">No comments yet.</p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No project files uploaded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>