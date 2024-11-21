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

// Handle form submission for updating the subject
if (isset($_POST['update_subject'])) {
    $subject_name = trim($_POST['subject_name']);

    // Validate inputs
    if (!empty($subject_name)) {
        // Check if the new subject name already exists (excluding the current subject)
        $checkQuery = "SELECT * FROM subjects WHERE subject_name = ? AND id != ?";
        $existingSubject = executeQuery($checkQuery, [$subject_name, $subject_id]);

        if ($existingSubject) {
            $error = "The subject name already exists.";
        } else {
            // Update the subject's details
            $updateQuery = "UPDATE subjects SET subject_name = ? WHERE id = ?";
            $pdo = getDBConnection();
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([$subject_name, $subject_id]);

            // Set a success message in the session
            $_SESSION['success_message'] = "Subject successfully updated.";

            // Redirect back to the subject list
            header("Location: add.php");
            exit;
        }
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
    <title>Edit Subject</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Edit Subject</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../admin/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
            </ol>
        </nav>

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
                        <label for="subject_code" class="form-label">Subject ID</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subject['subject_code']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
                    </div>
                    <button type="submit" name="update_subject" class="btn btn-primary w-100">Update Subject</button>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
