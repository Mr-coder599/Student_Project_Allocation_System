<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php"); // Redirect if not logged in
    exit();
}

$student_id = $_SESSION['student_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentproj_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the supervisor assigned to the student
$query = "SELECT supervisor_id FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $supervisor_id = $student['supervisor_id'];
} else {
    die("Student not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['project_file'])) {
    $file_name = $_FILES['project_file']['name'];
    $file_tmp = $_FILES['project_file']['tmp_name'];
    $file_size = $_FILES['project_file']['size'];
    $file_error = $_FILES['project_file']['error'];
    $file_type = $_FILES['project_file']['type'];

    if ($file_error === 0) {
        // Read file content as binary
        $file_content = file_get_contents($file_tmp);

        // Insert the file directly into the database
        $query = "INSERT INTO project_files (student_id, supervisor_id, file_name, file_path, upload_date) 
                  VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $student_id, $supervisor_id, $file_name, $file_content);

        if ($stmt->execute()) {
            $success_message = "Project file uploaded successfully!";
        } else {
            $error_message = "Error saving file in database: " . $stmt->error;
        }
    } else {
        $error_message = "There was an error uploading the file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Project</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2 class="card-title">Upload Your Project</h2>
            <?php if (isset($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            } ?>
            <?php if (isset($success_message)) {
                echo "<div class='alert alert-success'>$success_message</div>";
            } ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="project_file">Project File:</label>
                    <input type="file" class="form-control" id="project_file" name="project_file" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="" class="btn btn-secondary"><a href="../student/student_dashboard.php">Dashboard</a></button>
            </form>
        </div>
    </div>
</body>

</html>