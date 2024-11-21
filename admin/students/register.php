<?php
require '../../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Initialize variables
$error = '';
$success = '';

// Handle form submission for adding a new student
if (isset($_POST['add_student'])) {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validate the inputs
    if (!empty($student_id) && !empty($first_name) && !empty($last_name)) {
        // Check if the student ID already exists
        $checkQuery = "SELECT * FROM students WHERE student_id = ?";
        $existingStudent = executeQuery($checkQuery, [$student_id]);

        if ($existingStudent) {
            $error = "Student ID already exists.";
        } else {
            // Insert the new student into the database
            $insertQuery = "INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)";
            $pdo = getDBConnection();
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute([$student_id, $first_name, $last_name]);

            $success = "Student added successfully.";
        }
    } else {
        $error = "All fields are required.";
    }
}

// Fetch all students
$studentsQuery = "SELECT * FROM students";
$students = getDBConnection()->query($studentsQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Register Students</title>
</head>
<body>
    <!-- Corrected file paths for header and side-bar -->
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Register a New Student</h1>
        
        <!-- Display success or error messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Registration Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <button type="submit" name="add_student" class="btn btn-primary w-100">Add Student</button>
                </form>
            </div>
        </div>

        <!-- Student List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Student List</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                    <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    <a href="attach-subject.php?id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">Attach Subject</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
