<?php
session_start();

/**
 * Establishes and returns a connection to the MySQL database.
 */
function getDBConnection() {
    $host = 'localhost'; // Update if your host is different
    $dbname = 'dct-ccs-finals'; // Your database name
    $username = 'root'; // Database username
    $password = 'root'; // Database password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Executes a database query with optional parameters.
 * 
 * @param string $query The SQL query to execute.
 * @param array $params Optional parameters for prepared statements.
 * @return array|null The result row if found, or null if no rows match.
 */
function executeQuery($query, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Executes a database query with optional parameters.
 * 
 * @param string $query The SQL query to execute.
 * @param array $params Optional parameters for prepared statements.
 * @return array|null The result row if found, or null if no rows match.
 */

/**
 * Guard function to restrict access to authenticated users only.
 * Redirects to the login page if the user is not logged in.
 */
function guard() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit;
    }
}
?>
