<?php
// We use getenv() so we can change settings in Render without touching the code again!
$host = getenv('DB_HOST');
$dbname = getenv('DB_DATABASE'); // This will be 'frosty_bites'
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');
$port = "4000"; // TiDB always uses port 4000

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA       => true, // TiDB Cloud REQUIRES this "Safety Lock"
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // This tells you exactly what went wrong if it fails
    die("Database Connection failed: " . $e->getMessage());
}
?>
