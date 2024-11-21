<?php
require '../../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Initialize variables
$error = '';
$success = '';

// Get the student ID from the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: register.php"); // Redirect back to the student list if no ID is provided
    exit;
}

$student_id = $_GET['id'];

// Fetch the student's existing details
$query = "SELECT * FROM students WHERE id = ?";
$student = executeQuery($query, [$student_id]);

if (!$student) {
    header("Location: register.php"); // Redirect back if student does not exist
    exit;
}

// Handle form submission for updating the student
if (isset($_POST['update_student'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validate inputs
    if (!empty($first_name) && !empty($last_name)) {
        // Update the student's details
        $updateQuery = "UPDATE students SET first_name = ?, last_name = ? WHERE id = ?";
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([$first_name, $last_name, $student_id]);

        // Set a success message in the session
        $_SESSION['success_message'] = "Student successfully updated.";

        // Redirect to the dashboard
        header("Location: ../students/register.php");
        exit;
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Student</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Edit Student</h1>

        <!-- Display success or error messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                    </div>
                    <button type="submit" name="update_student" class="btn btn-primary w-100">Update Student</button>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
