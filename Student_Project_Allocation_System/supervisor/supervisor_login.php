<?php
session_start();
// include 'db_connection.php'; // Ensure you have a file for database connection
// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentproj_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (!empty($email) && !empty($phone)) {
        $query = "SELECT * FROM supervisors WHERE email = ? AND phone = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['supervisor_id'] = $row['id'];
            $_SESSION['supervisor_name'] = $row['name'];
            header("Location: supervisor_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or phone number!";
        }
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h3 class="text-center">Supervisor Login</h3>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>

</html>