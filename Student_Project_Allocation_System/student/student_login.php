<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'studentproj_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $matric_number = $conn->real_escape_string($_POST['matric_number']);
    $query = "SELECT * FROM students WHERE matric_number='$matric_number'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['name'] = $student['name'];
        header("Location: ../student/student_dashboard.php");
    } else {
        $error_message = "Invalid Matric Number!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Full page background image */
        body {
            background-image: url('https://source.unsplash.com/1600x900/?technology,education');
            background-size: cover;
            background-position: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
        }

        /* Center the login card */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
        }

        .alert {
            margin-top: 20px;
        }

        .form-group label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Student Login</h2>

            <!-- Display error message if any -->
            <?php if (isset($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            } ?>

            <!-- Login Form -->
            <form method="POST">
                <div class="form-group">
                    <label for="matric_number">Matric Number:</label>
                    <input type="text" class="form-control" id="matric_number" name="matric_number" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>

</html>