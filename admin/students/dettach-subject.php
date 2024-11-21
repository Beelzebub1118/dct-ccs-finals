<?php
require '../../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Get the student ID and subject ID from the query string
if (!isset($_GET['student_id'], $_GET['subject_id']) || empty($_GET['student_id']) || empty($_GET['subject_id'])) {
    header("Location: ../students/register.php"); // Redirect if no IDs are provided
    exit;
}

$student_id = $_GET['student_id'];
$subject_id = $_GET['subject_id'];

// Fetch the student's details
$studentQuery = "SELECT * FROM students WHERE id = ?";
$student = executeQuery($studentQuery, [$student_id]);

if (!$student) {
    header("Location: ../students/register.php"); // Redirect if the student does not exist
    exit;
}

// Fetch the subject details
$subjectQuery = "SELECT * FROM subjects WHERE id = ?";
$subject = executeQuery($subjectQuery, [$subject_id]);

if (!$subject) {
    header("Location: ../students/register.php"); // Redirect if the subject does not exist
    exit;
}

// Handle form submission to detach the subject
if (isset($_POST['detach_subject'])) {
    $detachQuery = "DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?";
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($detachQuery);
    $stmt->execute([$student_id, $subject_id]);

    // Redirect to the attach-subject.php page with success message
    $_SESSION['success_message'] = "Subject detached successfully.";
    header("Location: attach-subject.php?id=$student_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Detach Subject</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Detach Subject</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../admin/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../students/register.php">Register Student</a></li>
                <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo $student_id; ?>">Attach Subject to Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
            </ol>
        </nav>

        <!-- Confirmation Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Are you sure you want to detach this subject from this student record?</h5>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subject['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                </ul>
                <form method="post" action="">
                    <button type="submit" name="detach_subject" class="btn btn-danger">Detach Subject from Student</button>
                    <a href="attach-subject.php?id=<?php echo $student_id; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
