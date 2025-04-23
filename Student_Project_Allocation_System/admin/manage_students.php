<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentproj_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add/Update/Delete Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_student'])) {
        // Add Student
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $matric_number = $conn->real_escape_string($_POST['matric_number']);
        $cgpa = $conn->real_escape_string($_POST['cgpa']);
        $query = "INSERT INTO students (name, email, phone, matric_number, cgpa) VALUES ('$name', '$email', '$phone', '$matric_number', '$cgpa')";
        if ($conn->query($query)) {
            $message = "Student added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif (isset($_POST['update_student'])) {
        // Update Student
        $id = $_POST['id'];
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $matric_number = $conn->real_escape_string($_POST['matric_number']);
        $cgpa = $conn->real_escape_string($_POST['cgpa']);
        $query = "UPDATE students SET name='$name', email='$email', phone='$phone', matric_number='$matric_number', cgpa='$cgpa' WHERE id='$id'";
        if ($conn->query($query)) {
            $message = "Student updated successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif (isset($_POST['delete_student'])) {
        // Delete Student
        $id = $_POST['id'];
        $query = "DELETE FROM students WHERE id='$id'";
        if ($conn->query($query)) {
            $message = "Student deleted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Fetch All Students
$students = $conn->query("SELECT * FROM students");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Manage Students</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
        }

        h2,
        h4 {
            font-family: 'Arial', sans-serif;
            color: #343a40;
        }

        .form-control,
        .btn {
            border-radius: 20px;
        }

        .table {
            margin-top: 30px;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .modal-footer button {
            border-radius: 20px;
        }

        .alert {
            border-radius: 10px;
        }

        .back-btn {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Manage Students</h2>

        <!-- Display Message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="admin_dashboard.php" class="btn back-btn mb-3">Back to Dashboard</a>

        <!-- Add Student Form -->
        <h4>Add Student</h4>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="matric_number">Matric Number:</label>
                <input type="text" class="form-control" id="matric_number" name="matric_number" required>
            </div>
            <div class="form-group">
                <label for="cgpa">CGPA:</label>
                <input type="number" step="0.01" class="form-control" id="cgpa" name="cgpa" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_student">Add Student</button>
        </form>

        <!-- Students Table -->
        <h4 class="mt-5">Student List</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Matric Number</th>
                    <th>CGPA</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                        <td><?php echo htmlspecialchars($student['matric_number']); ?></td>
                        <td><?php echo htmlspecialchars($student['cgpa']); ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $student['id']; ?>">Edit</button>
                            <!-- Delete Button -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                <button type="submit" name="delete_student" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo $student['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Student</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $student['name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone:</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $student['phone']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="matric_number">Matric Number:</label>
                                            <input type="text" class="form-control" id="matric_number" name="matric_number" value="<?php echo $student['matric_number']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="cgpa">CGPA:</label>
                                            <input type="number" step="0.01" class="form-control" id="cgpa" name="cgpa" value="<?php echo $student['cgpa']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" name="update_student" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>