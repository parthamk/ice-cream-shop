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
        // We force SSL to be REQUIRED
        PDO::MYSQL_ATTR_SSL_CA       => true,
        // We tell PHP to use the system's built-in certificates to verify
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>

