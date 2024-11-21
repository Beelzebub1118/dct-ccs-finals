<?php
require '../../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Initialize variables
$error = '';
$success = '';

// Get the student ID from the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../students/register.php"); // Redirect to student list if no ID is provided
    exit;
}

$student_id = $_GET['id'];

// Fetch the student's details
$studentQuery = "SELECT * FROM students WHERE id = ?";
$student = executeQuery($studentQuery, [$student_id]);

if (!$student) {
    header("Location: ../students/register.php"); // Redirect back if the student does not exist
    exit;
}

// Fetch the subjects already attached to the student
$attachedSubjectsQuery = "
    SELECT 
        subjects.id AS subject_id, 
        subjects.subject_code, 
        subjects.subject_name, 
        students_subjects.grade 
    FROM students_subjects
    INNER JOIN subjects ON students_subjects.subject_id = subjects.id
    WHERE students_subjects.student_id = ?
";
$attachedSubjects = getDBConnection()->prepare($attachedSubjectsQuery);
$attachedSubjects->execute([$student_id]);
$attachedSubjects = $attachedSubjects->fetchAll(PDO::FETCH_ASSOC);

// Fetch all subjects that are not yet attached to the student
$availableSubjectsQuery = "
    SELECT * FROM subjects 
    WHERE id NOT IN (
        SELECT subject_id FROM students_subjects WHERE student_id = ?
    )
";
$availableSubjectsStmt = getDBConnection()->prepare($availableSubjectsQuery);
$availableSubjectsStmt->execute([$student_id]);
$availableSubjects = $availableSubjectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for attaching subjects
if (isset($_POST['attach_subjects'])) {
    $selectedSubjects = $_POST['subjects'] ?? []; // Get selected subjects

    if (!empty($selectedSubjects)) {
        $pdo = getDBConnection();
        $insertQuery = "INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)";

        foreach ($selectedSubjects as $subject_id) {
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute([$student_id, $subject_id, 0.00]); // Assign a default grade of 0.00
        }

        // Redirect to refresh the page and show updated lists
        header("Location: attach-subject.php?id=$student_id");
        exit;
    } else {
        $error = "Please select at least one subject to attach.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Attach Subject to Student</title>
</head>
<body>
    <?php require_once '../../admin/partials/header.php'; ?>
    <?php require_once '../../admin/partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Attach Subject to Student</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../admin/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../students/register.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attach Subject to Student</li>
            </ol>
        </nav>

        <!-- Display success or error messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Selected Student Information -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Selected Student Information</h5>
                <ul>
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></li>
                </ul>
            </div>
        </div>

        <!-- Attach Subjects Form -->
        <div class="card mb-4">
            <div class="card-body">
                <?php if (!empty($availableSubjects)): ?>
                    <form method="post" action="">
                        <?php foreach ($availableSubjects as $subject): ?>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="subjects[]" 
                                    value="<?php echo $subject['id']; ?>" 
                                    id="subject-<?php echo $subject['id']; ?>">
                                <label class="form-check-label" for="subject-<?php echo $subject['id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" name="attach_subjects" class="btn btn-primary mt-3">Attach Subjects</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">No subjects available to attach.</p>
                <?php endif; ?>
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
                            <th>Grade</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($attachedSubjects)): ?>
                            <?php foreach ($attachedSubjects as $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td>
                                        <?php echo $subject['grade'] ? number_format($subject['grade'], 2) : '--.--'; ?>
                                    </td>
                                    <td>
                                        <a href="dettach-subject.php?student_id=<?php echo $student_id; ?>&subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-danger btn-sm">Detach Subject</a>
                                        <a href="assign-grade.php?student_id=<?php echo $student_id; ?>&subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-success btn-sm">Assign Grade</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No subject found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
