<?php
// Fetch credentials from Render Environment Variables
$host = getenv('DB_HOST');
$dbname = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');
$port = "23238"; // Standard Aiven MySQL port

try {
    // Added port and SSL requirement for Aiven
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA       => true, // Required for Aiven
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Only show detailed errors during debugging; hide them in production!
    die("Database Connection failed: " . $e->getMessage());
}
?>
