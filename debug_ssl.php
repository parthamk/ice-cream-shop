<?php
echo "<h3>SSL Debugger</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "OpenSSL enabled: " . (extension_loaded('openssl') ? 'YES' : 'NO') . "<br>";

$cert_path = __DIR__ . '/certs/isrgrootx1.pem';
echo "Expected Cert Path: " . $cert_path . "<br>";
echo "File exists: " . (file_exists($cert_path) ? 'YES' : 'NO') . "<br>";

if (file_exists($cert_path)) {
    echo "File Permissions: " . substr(sprintf('%o', fileperms($cert_path)), -4) . "<br>";
}

echo "<h3>Environment Check</h3>";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NOT SET') . "<br>";
echo "DB_DATABASE: " . (getenv('DB_DATABASE') ?: 'NOT SET') . "<br>";
?>
