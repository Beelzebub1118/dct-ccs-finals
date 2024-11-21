<?php
require '../functions.php'; // Include functions file
guard(); // Restrict access to logged-in users

// Query the database for the required dashboard stats
$subjectQuery = "SELECT COUNT(*) AS subject_count FROM subjects";
$subjectCount = executeQuery($subjectQuery)['subject_count'] ?? 0;

$studentQuery = "SELECT COUNT(*) AS student_count FROM students";
$studentCount = executeQuery($studentQuery)['student_count'] ?? 0;

$passQuery = "
    SELECT COUNT(DISTINCT student_id) AS pass_count 
    FROM students_subjects 
    WHERE grade >= 75
";
$passCount = executeQuery($passQuery)['pass_count'] ?? 0;

$failQuery = "
    SELECT COUNT(DISTINCT student_id) AS fail_count 
    FROM students_subjects 
    WHERE grade < 75
";
$failCount = executeQuery($failQuery)['fail_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body>
    <?php include 'partials/header.php'; ?>
    <?php include 'partials/side-bar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
        <h1 class="h2">Dashboard</h1>

        <div class="row mt-5">
            <!-- Number of Subjects -->
            <div class="col-12 col-xl-3">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                    <div class="card-body text-primary">
                        <h5 class="card-title"><?php echo $subjectCount; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Number of Students -->
            <div class="col-12 col-xl-3">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                    <div class="card-body text-success">
                        <h5 class="card-title"><?php echo $studentCount; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Number of Failed Students -->
            <div class="col-12 col-xl-3">
                <div class="card border-danger mb-3">
                    <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                    <div class="card-body text-danger">
                        <h5 class="card-title"><?php echo $failCount; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Number of Passed Students -->
            <div class="col-12 col-xl-3">
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                    <div class="card-body text-success">
                        <h5 class="card-title"><?php echo $passCount; ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
