<?php
session_start();
if (!isset($_SESSION['supervisor_id'])) {
    header("Location: supervisor_login.php");
    exit();
}

$supervisor_id = $_SESSION['supervisor_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentproj_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students assigned to the supervisor (including email)
$query_students = "SELECT id, name, email FROM students WHERE supervisor_id = '$supervisor_id'";
$students = $conn->query($query_students);

// Fetch student's project files along with their names and emails
$query_files = "SELECT students.id AS student_id, students.name AS student_name, students.email AS student_email, 
                       project_files.id AS file_id, project_files.file_name, project_files.file_path 
                FROM students
                LEFT JOIN project_files ON students.id = project_files.student_id
                WHERE students.supervisor_id = '$supervisor_id'";
$files = $conn->query($query_files);

// Handle sending comments
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && isset($_POST['file_id'])) {
    $comment = $conn->real_escape_string(trim($_POST['comment']));
    $file_id = intval($_POST['file_id']); // Ensure it's an integer

    // Validate that the file_id exists in the database
    $check_file = $conn->query("SELECT id FROM project_files WHERE id = '$file_id'");
    if ($check_file->num_rows > 0) {
        // Insert comment into the database
        $query = "INSERT INTO comments (file_id, supervisor_id, comment) VALUES ('$file_id', '$supervisor_id', '$comment')";
        if ($conn->query($query)) {
            $message = "<div class='alert alert-success'>Comment added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error adding comment: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Invalid file selected.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #343a40;
            color: white;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #495057;
        }

        .content {
            margin-left: 260px;
            padding: 20px;
            width: 100%;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Supervisor Panel</h4>
        <a href="#">Dashboard</a>
        <a href="#students">Assigned Students</a>
        <a href="#files">Student Files</a>
        <a href="supervisor_login.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Welcome, Supervisor</h2>

        <?php if (isset($message)) {
            echo "<div class='alert alert-info'>$message</div>";
        } ?>

        <!-- Assigned Students -->
        <section id="students">
            <h4>Assigned Students</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Project Files Uploaded by Students -->
        <section id="files">
            <h4>Project Files Uploaded by Students</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student Email</th>
                        <th>File Name</th>
                        <th>File Path</th>
                        <th>Actions</th>
                        <!-- <th>Download</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($file = $files->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($file['student_email']); ?></td>
                            <td><?php echo !empty($file['file_name']) ? htmlspecialchars($file['file_name']) : 'No File Uploaded'; ?></td>
                            <td>
                                <?php if (!empty($file['file_path'])): ?>
                                    <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank">View</a>

                                <?php else: ?>
                                    No File
                                <?php endif; ?>
                            </td>

                            <td>
                                <form method="POST" class="mt-2">
                                    <input type="hidden" name="file_id" value="<?php echo $file['file_id']; ?>">
                                    <div class="form-group">
                                        <textarea class="form-control" name="comment" rows="2" placeholder="Enter your comment"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Add Comment</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>

</body>

</html>