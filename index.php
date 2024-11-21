<?php
require 'functions.php'; // Include the functions.php file

// Initialize variables for error handling
$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']); // Sanitize email input
    $password = trim($_POST['password']); // Sanitize password input

    // Check if both fields are filled
    if (!empty($email) && !empty($password)) {
        // Hash the input password with MD5
        $hashedPassword = md5($password);

        // Query to validate user credentials
        $query = "SELECT * FROM users WHERE email = ? AND password = ?";
        $result = executeQuery($query, [$email, $hashedPassword]);

        if ($result) {
            // If login is successful, set session variables
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $result['name'];

            // Redirect to the admin dashboard
            header("Location: admin/dashboard.php");
            exit;
        } else {
            // Display error message for invalid credentials
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Login</title>
</head>

<body class="bg-secondary-subtle">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-3">
            <!-- Server-Side Validation Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4 fw-normal">Login</h1>
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com" required>
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
