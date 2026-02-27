<?php
echo "<h2>Frosty Bites - Connection Debugger</h2>";

echo "<h3>1. SSL & Environment Setup</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "OpenSSL enabled: " . (extension_loaded('openssl') ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";

$cert_path = realpath(__DIR__ . '/certs/isrgrootx1.pem');
echo "Expected Cert Path: " . ($cert_path ?: __DIR__ . '/certs/isrgrootx1.pem (Not Found)') . "<br>";
echo "File exists: " . (file_exists($cert_path) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";

if (file_exists($cert_path)) {
    echo "File Permissions: " . substr(sprintf('%o', fileperms($cert_path)), -4) . "<br>";
}

echo "<h3>2. Environment Variables Check</h3>";

// Check DB_HOST
$host = getenv('DB_HOST');
echo "DB_HOST: " . ($host ? "<span style='color:green'>SET</span> (" . htmlspecialchars($host) . ")" : "<span style='color:red'>NOT SET</span>") . "<br>";

// Check DB_DATABASE
$dbname = getenv('DB_DATABASE');
echo "DB_DATABASE: " . ($dbname ? "<span style='color:green'>SET</span> (" . htmlspecialchars($dbname) . ")" : "<span style='color:red'>NOT SET</span>") . "<br>";

// Check DB_USERNAME
$user = getenv('DB_USERNAME');
echo "DB_USERNAME: " . ($user ? "<span style='color:green'>SET</span> (" . htmlspecialchars($user) . ")" : "<span style='color:red'>NOT SET</span>") . "<br>";

// Check DB_PASSWORD (SECURELY)
$pass = getenv('DB_PASSWORD');
if ($pass) {
    $len = strlen($pass);
    $first_char = substr($pass, 0, 1);
    $last_char = substr($pass, -1);
    
    echo "DB_PASSWORD: <span style='color:green'>SET</span> (Length: $len chars | Starts with: '$first_char' | Ends with: '$last_char')<br>";
    
    // Check for accidental invisible spaces
    if (trim($pass) !== $pass) {
        echo "<span style='color:orange'>⚠️ WARNING: Your password has accidental blank spaces at the beginning or end!</span><br>";
    }
} else {
    echo "DB_PASSWORD: <span style='color:red'>NOT SET</span><br>";
}

echo "<h3>3. PDO Connection Test</h3>";
if ($host && $dbname && $user && $pass) {
    try {
        $dsn = "mysql:host=$host;port=4000;dbname=$dbname;charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_SSL_CA       => $cert_path,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true, 
        ];
        
        $pdo = new PDO($dsn, $user, $pass, $options);
        echo "<h4 style='color:green'>✅ SUCCESS! Connected to TiDB database '" . htmlspecialchars($dbname) . "' successfully!</h4>";
    } catch (PDOException $e) {
        echo "<h4 style='color:red'>❌ CONNECTION FAILED:</h4>";
        echo "<p><strong>Error Details:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<h4 style='color:orange'>⚠️ Cannot attempt connection: One or more Environment Variables are missing. Please add them in Render.</h4>";
}
?>