<?php
// We use getenv() so we can change settings in Render without touching the code again!
$host = getenv('DB_HOST');
$dbname = getenv('DB_DATABASE'); // This will be 'frosty_bites'
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');
$port = "4000"; // TiDB always uses port 4000

// Path to CA certificate for SSL connection
$ca_cert = __DIR__ . '/certs/isrgrootx1.pem';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Using the verified realpath
        PDO::MYSQL_ATTR_SSL_CA       => $ca_cert,
        // Some cloud providers require this for the handshake to finish
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}