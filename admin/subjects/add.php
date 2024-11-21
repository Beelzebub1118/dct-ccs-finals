<?php
require '../../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Initialize variables
$error = '';
$success = '';

// Handle form submission for adding a new subject
if (isset($_POST['add_subject'])) {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);

    // Validate inputs
    if (!empty($subject_code) && !empty($subject_name)) {
        // Check if the subject code or subject name already exists
        $checkQuery = "SELECT * FROM subjects WHERE subject_code = ? OR subject_name = ?";
        $existingSubject = executeQuery($checkQuery, [$subject_code, $subject_name]);

        if ($existingSubject) {
            if ($existingSubject['subject_code'] === $subject_code) {
                $error = "Subject code already exists.";
            } elseif ($existingSubject['subject_name'] === $subject_name) {
                $error = "Subject name already exists.";
            }
        } else {
            // Insert the new subject into the database
            $insertQuery = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)";
            $pdo = getDBConnection();
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute([$subject_code, $subject_name]);

            $success = "Subject added successfully.";
        }
    } else {
        $error = "All fields are required.";
    }
}

// Fetch all subjects
$subjectsQuery = "SELECT * FROM subjects";
$subjects = getDBConnection()->query($subjectsQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Subject</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Add a New Subject</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../admin/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
            </ol>
        </nav>

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

        <!-- Add Subject Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="subject_code" class="form-label">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" required>
                    </div>
                    <button type="submit" name="add_subject" class="btn btn-primary w-100">Add Subject</button>
                </form>
            </div>
        </div>

        <!-- Subject List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Subject List</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $subject['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                    <a href="delete.php?id=<?php echo $subject['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
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
