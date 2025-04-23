<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentproj_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add/Update/Delete Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add Supervisor
    if (isset($_POST['add_supervisor'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $qualification = $conn->real_escape_string($_POST['qualification']);
        $department = $conn->real_escape_string($_POST['department']);
        $level = $conn->real_escape_string($_POST['level']);
        $query = "INSERT INTO supervisors (name, email, phone, qualification, department, level) 
              VALUES ('$name', '$email', '$phone', '$qualification', '$department', '$level')";
        if ($conn->query($query)) {
            $message = "Supervisor added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif (isset($_POST['update_supervisor'])) {
        // Update Supervisor
        $id = $_POST['id'];
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $qualification = $conn->real_escape_string($_POST['qualification']);
        $department = $conn->real_escape_string($_POST['department']);
        $level = $conn->real_escape_string($_POST['level']);
        $query = "UPDATE supervisors 
          SET name='$name', email='$email', phone='$phone', qualification='$qualification', department='$department', level='$level'
          WHERE id='$id'";

        if ($conn->query($query)) {
            $message = "Supervisor updated successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif (isset($_POST['delete_supervisor'])) {
        // Delete Supervisor
        $id = $_POST['id'];
        $query = "DELETE FROM supervisors WHERE id='$id'";
        if ($conn->query($query)) {
            $message = "Supervisor deleted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Fetch All Supervisors
$supervisors = $conn->query("SELECT * FROM supervisors");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Manage Supervisors</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Manage Supervisors</h2>

        <!-- Display Message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add Supervisor Form -->
        <h4>Add Supervisor</h4>
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
                <label for="qualification">Qualification:</label>
                <select class="form-control" id="qualification" name="qualification" required>
                    <option value="HND">HND</option>
                    <option value="BSc">BSc</option>
                    <option value="MSc">MSc</option>
                    <option value="PhD">PhD</option>

                </select>
            </div>
            <div class="form-group">
                <label for="qualification">Supervisor Level:</label>
                <select class="form-control" id="level" name="level" required>
                    <option value="Hod">HOD</option>
                    <option value="Senior">Senior</option>
                    <option value="Junior">Junior</option>
                    <option value="Chief Lecturer">Chief Lecturer</option>
                    <!-- <option value="PhD">PhD</option> -->
                </select>
            </div>
            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" class="form-control" id="department" name="department" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_supervisor">Add Supervisor</button>
            <!-- Back to Dashboard Button -->
            <a href="admin_dashboard.php" class="btn btn-secondary btn-back">Back to Dashboard</a>

        </form>

        <!-- Supervisors Table -->
        <h4 class="mt-5">Supervisor List</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Qualification</th>
                    <th>Department</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($supervisor = $supervisors->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $supervisor['id']; ?></td>
                        <td><?php echo $supervisor['name']; ?></td>
                        <td><?php echo $supervisor['email']; ?></td>
                        <td><?php echo $supervisor['phone']; ?></td>
                        <td><?php echo $supervisor['qualification']; ?></td>
                        <td><?php echo $supervisor['department']; ?></td>
                        <td><?php echo $supervisor['level']; ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $supervisor['id']; ?>">Edit</button>
                            <!-- Delete Button -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $supervisor['id']; ?>">
                                <button type="submit" name="delete_supervisor" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo $supervisor['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Supervisor</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $supervisor['id']; ?>">
                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $supervisor['name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $supervisor['email']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone:</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $supervisor['phone']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="qualification">Qualification:</label>
                                            <select class="form-control" id="qualification" name="qualification" required>
                                                <option value="HND" <?php echo $supervisor['qualification'] == 'HND' ? 'selected' : ''; ?>>HND</option>
                                                <option value="BSc" <?php echo $supervisor['qualification'] == 'BSc' ? 'selected' : ''; ?>>BSc</option>
                                                <option value="MSc" <?php echo $supervisor['qualification'] == 'MSc' ? 'selected' : ''; ?>>MSc</option>
                                                <option value="PhD" <?php echo $supervisor['qualification'] == 'PhD' ? 'selected' : ''; ?>>PhD</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="department">Department:</label>
                                            <input type="text" class="form-control" id="department" name="department" value="<?php echo $supervisor['department']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="qualification">Supervisor Level:</label>
                                            <select class="form-control" id="level" name="level" required>
                                                <option value="Senior">Senior</option>
                                                <option value="Junior">Junior</option>
                                                <option value="Chief Lecturer">Chief Lecturer</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="update_supervisor">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>