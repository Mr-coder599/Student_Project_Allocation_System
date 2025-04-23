<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login_admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Admin Dashboard</title>
    <style>
        body {
            display: flex;
            height: 100vh;
        }

        .drawer {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .drawer a {
            color: white;
            text-decoration: none;
            margin-bottom: 15px;
        }

        .drawer a:hover {
            text-decoration: underline;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="drawer">
        <h4>Admin Dashboard</h4>
        <p>Welcome, <?php echo $_SESSION['admin_name']; ?>!</p>
        <a href="../admin/manage_students.php">Manage Students</a>
        <a href="../admin/manage_supervisors.php">Manage Supervisors</a>
        <!-- <a href="#projects">Manage Projects</a> -->
        <a href="../admin/allocate_supervisors.php">Allocate Supervisors</a>
        <a href="../admin/login_admin.php">Logout</a>
    </div>

    <div class="content">
        <div id="students" class="mb-4">
            <h3>Manage Students</h3>
            <div class="card">
                <div class="card-body">
                    <p>Add, edit, or delete student records.</p>
                    <a href="manage_students.php" class="btn btn-primary">Go to Student Management</a>
                </div>
            </div>
        </div>

        <div id="supervisors" class="mb-4">
            <h3>Manage Supervisors</h3>
            <div class="card">
                <div class="card-body">
                    <p>Add, edit, or delete supervisor records.</p>
                    <a href="manage_supervisors.php" class="btn btn-primary">Go to Supervisor Management</a>
                </div>
            </div>
        </div>

        <!-- <div id="projects" class="mb-4">
            <h3>Manage Projects</h3>
            <div class="card">
                <div class="card-body">
                    <p>Add, edit, or delete project records.</p>
                    <a href="manage_projects.php" class="btn btn-primary">Go to Project Management</a>
                </div>
            </div>
        </div> -->

        <div id="allocation" class="mb-4">
            <h3>Allocate Supervisors to Students</h3>
            <div class="card">
                <div class="card-body">
                    <p>Allocate supervisors to students based on their CGPA.</p>
                    <a href="allocate_supervisors.php" class="btn btn-primary">Go to Allocation</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>