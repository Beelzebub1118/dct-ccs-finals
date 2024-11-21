<?php
require '../../functions.php'; // Include the functions file
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

// Handle delete confirmation
if (isset($_POST['confirm_delete'])) {
    $deleteQuery = "DELETE FROM students WHERE id = ?";
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute([$student_id]);

    // Set a success message in the session
    $_SESSION['success_message'] = "Student record deleted successfully.";

    // Redirect to the dashboard
    header("Location: /admin/students/register.php");
    exit;
}

// Handle cancel action
if (isset($_POST['cancel_delete'])) {
    header("Location: /admin/students/register.php"); // Redirect back to the dashboard
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Delete Student</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Delete a Student</h1>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../admin/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
            </ol>
        </nav>

        <!-- Confirmation Section -->
        <div class="card mb-4">
            <div class="card-body">
                <p>Are you sure you want to delete the following student record?</p>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                </ul>
                <form method="post" action="">
                    <button type="submit" name="cancel_delete" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="confirm_delete" class="btn btn-primary">Delete Student Record</button>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
