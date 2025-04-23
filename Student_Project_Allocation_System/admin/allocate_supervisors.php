<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentproj_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Allocation Logic
if (isset($_POST['allocate'])) {
    // Step 1: Clear previous allocations
    $conn->query("DELETE FROM allocations");
    $conn->query("UPDATE students SET supervisor_id = NULL"); // Reset student supervisor_id

    // Step 2: Fetch all Supervisors
    $supervisors = [];
    $result = $conn->query("SELECT * FROM supervisors");
    while ($row = $result->fetch_assoc()) {
        $supervisors[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'qualification' => $row['qualification'],
            'count' => 0 // Track students allocated per supervisor
        ];
    }

    // Step 3: Fetch all Students ordered by CGPA
    $students = $conn->query("SELECT * FROM students ORDER BY cgpa DESC");
    $studentList = [];
    while ($student = $students->fetch_assoc()) {
        $studentList[] = $student;
    }

    // Step 4: Allocate Students to Supervisors
    $supervisorCount = count($supervisors);
    $currentSupervisorIndex = 0;

    foreach ($studentList as $student) {
        $allocated = false;

        // Try to allocate student
        for ($i = 0; $i < $supervisorCount; $i++) {
            $supervisor = $supervisors[$currentSupervisorIndex];

            if ($supervisor['count'] < 2) { // Maximum 8 students per supervisor (adjust as needed)
                if (
                    ($student['cgpa'] >= 1.50 && $student['cgpa'] <= 1.99 && $supervisor['qualification'] === 'OND') ||
                    ($student['cgpa'] >= 2.00 && $student['cgpa'] <= 2.49 && in_array($supervisor['qualification'], ['OND', 'HND'])) ||
                    ($student['cgpa'] >= 2.50 && $student['cgpa'] <= 2.99 && in_array($supervisor['qualification'], ['HND', 'BSc'])) ||
                    ($student['cgpa'] >= 3.00 && $student['cgpa'] <= 4.00 && in_array($supervisor['qualification'], ['MSc', 'BSC', 'PhD']))
                ) {
                    // Allocate student to supervisor
                    $query = "INSERT INTO allocations (student_id, supervisor_id) VALUES ('{$student['id']}', '{$supervisor['id']}')";
                    if ($conn->query($query)) {
                        // Update students table
                        $updateStudent = "UPDATE students SET supervisor_id = '{$supervisor['id']}' WHERE id = '{$student['id']}'";
                        $conn->query($updateStudent);

                        $supervisors[$currentSupervisorIndex]['count']++; // Increase supervisor allocation count
                        $allocated = true;
                        break;
                    }
                }
            }

            // Move to the next supervisor (circular rotation)
            $currentSupervisorIndex = ($currentSupervisorIndex + 1) % $supervisorCount;
        }

        if (!$allocated) {
            // Handle unallocated students
            echo "<div class='alert alert-danger'>Could not allocate student: {$student['name']}</div>";
        }
    }

    echo "<div class='alert alert-success'>Allocation complete!</div>";
}

// Fetch Allocations for Display
$allocations = $conn->query("
    SELECT students.name AS student_name, students.cgpa, 
           supervisors.name AS supervisor_name, supervisors.qualification
    FROM allocations
    INNER JOIN students ON allocations.student_id = students.id
    INNER JOIN supervisors ON allocations.supervisor_id = supervisors.id
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Allocate Supervisors</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center text-primary">Supervisor Allocation System</h2>

        <!-- Back to Dashboard -->
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

        <!-- Allocation Button -->
        <form method="POST">
            <button type="submit" name="allocate" class="btn btn-primary btn-block">Run Allocation</button>
        </form>

        <!-- Allocation Results -->
        <h4 class="mt-4 text-success">Allocation Results</h4>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Student Name</th>
                    <th>CGPA</th>
                    <th>Supervisor Name</th>
                    <th>Supervisor Qualification</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $allocations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['cgpa']); ?></td>
                        <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['qualification']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>