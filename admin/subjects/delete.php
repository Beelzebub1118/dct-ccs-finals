<?php
require '../../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Initialize variables
$error = '';
$success = '';

// Get the subject ID from the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: add.php"); // Redirect back to the subject list if no ID is provided
    exit;
}

$subject_id = $_GET['id'];

// Fetch the subject's existing details
$query = "SELECT * FROM subjects WHERE id = ?";
$subject = executeQuery($query, [$subject_id]);

if (!$subject) {
    header("Location: add.php"); // Redirect back if subject does not exist
    exit;
}

// Handle form submission for deleting the subject
if (isset($_POST['delete_subject'])) {
    $deleteQuery = "DELETE FROM subjects WHERE id = ?";
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute([$subject_id]);

    // Set a success message in the session
    $_SESSION['success_message'] = "Subject successfully deleted.";

    // Redirect back to the subject list
    header("Location: add.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Delete Subject</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Delete Subject</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../admin/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
            </ol>
        </nav>

        <!-- Confirmation Form -->
        <div class="card mb-4">
            <div class="card-body">
                <p>Are you sure you want to delete the following subject record?</p>
                <ul>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subject['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                </ul>
                <form method="post" action="">
                    <button type="submit" name="delete_subject" class="btn btn-primary">Delete Subject Record</button>
                    <a href="add.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
