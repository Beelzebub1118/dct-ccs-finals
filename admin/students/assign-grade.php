<?php
require '../../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Initialize variables
$error = '';
$success = '';

// Validate input parameters
if (!isset($_GET['student_id']) || !isset($_GET['subject_id'])) {
    header("Location: ../students/register.php"); // Redirect if parameters are missing
    exit;
}

$student_id = $_GET['student_id'];
$subject_id = $_GET['subject_id'];

// Fetch student details
$studentQuery = "SELECT * FROM students WHERE id = ?";
$student = executeQuery($studentQuery, [$student_id]);

if (!$student) {
    header("Location: ../students/register.php"); // Redirect if student does not exist
    exit;
}

// Fetch subject details
$subjectQuery = "SELECT * FROM subjects WHERE id = ?";
$subject = executeQuery($subjectQuery, [$subject_id]);

if (!$subject) {
    header("Location: ../students/register.php"); // Redirect if subject does not exist
    exit;
}

// Handle form submission for assigning grade
if (isset($_POST['assign_grade'])) {
    $grade = trim($_POST['grade']);

    // Validate grade input
    if (is_numeric($grade) && $grade >= 0 && $grade <= 100) {
        $pdo = getDBConnection();
        $updateGradeQuery = "UPDATE students_subjects SET grade = ? WHERE student_id = ? AND subject_id = ?";
        $stmt = $pdo->prepare($updateGradeQuery);
        $stmt->execute([$grade, $student_id, $subject_id]);

        // Redirect to attach subject page with success message
        $_SESSION['success_message'] = "Grade successfully assigned.";
        header("Location: attach-subject.php?id=$student_id");
        exit;
    } else {
        $error = "Please enter a valid grade between 0 and 100.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Assign Grade to Subject</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Assign Grade to Subject</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../admin/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../students/register.php">Register Student</a></li>
                <li class="breadcrumb-item"><a href="attach-subject.php?id=<?php echo $student_id; ?>">Attach Subject to Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Assign Grade to Subject</li>
            </ol>
        </nav>

        <!-- Display success or error messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Selected Student and Subject Information -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Selected Student and Subject Information</h5>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subject['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                </ul>
            </div>
        </div>

        <!-- Assign Grade Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            class="form-control" 
                            id="grade" 
                            name="grade" 
                            value="99.00"
                            required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="attach-subject.php?id=<?php echo $student_id; ?>" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" name="assign_grade" class="btn btn-primary">Assign Grade to Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
