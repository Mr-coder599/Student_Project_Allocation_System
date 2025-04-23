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

// Fetch student details along with supervisor info
$query = "SELECT students.*, supervisors.name AS supervisor_name
          FROM students
          LEFT JOIN supervisors ON students.supervisor_id = supervisors.id
          WHERE students.id = '$student_id'";
$result = $conn->query($query);
$student = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

// Handle password update
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            // Verify current password
            $query = "SELECT password FROM students WHERE id = '$student_id'";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($current_password, $row['password'])) {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $update_query = "UPDATE students SET password = '$hashed_password' WHERE id = '$student_id'";
                    if ($conn->query($update_query)) {
                        $success_message = "Password updated successfully.";
                    } else {
                        $error_message = "Error updating password.";
                    }
                } else {
                    $error_message = "Current password is incorrect.";
                }
            }
        } else {
            $error_message = "New password and confirm password do not match.";
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        <a href="student_login.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Student Profile</h1>

            <!-- Profile Details -->
            <div class="card card-custom">
                <div class="card-header">
                    <h5>Student Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></p>
                    <p><strong>Assigned Supervisor:</strong> <?php echo htmlspecialchars($student['supervisor_name'] ?? 'Not Assigned'); ?></p>
                </div>
            </div>

            <!-- Password Update Form -->
            <div class="card card-custom">
                <div class="card-header">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password:</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                    </form>
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
