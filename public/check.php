<?php
// Output PHP configuration
echo "Max execution time: " . ini_get('max_execution_time') . "<br>";
echo "Memory limit: " . ini_get('memory_limit') . "<br>";
echo "Upload max filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "Post max size: " . ini_get('post_max_size') . "<br>";

// Check if we can modify these settings
ini_set('max_execution_time', 120);
echo "New max execution time: " . ini_get('max_execution_time') . "<br>";

// Get DB settings from .env
$envFile = file_get_contents('../.env');
preg_match('/DB_CONNECTION=(.*)/', $envFile, $dbConnection);
preg_match('/DB_HOST=(.*)/', $envFile, $dbHost);
preg_match('/DB_PORT=(.*)/', $envFile, $dbPort);
preg_match('/DB_DATABASE=(.*)/', $envFile, $dbName);
preg_match('/DB_USERNAME=(.*)/', $envFile, $dbUser);
preg_match('/DB_PASSWORD=(.*)/', $envFile, $dbPass);

echo "DB Connection: " . ($dbConnection[1] ?? 'Not found') . "<br>";
echo "DB Host: " . ($dbHost[1] ?? 'Not found') . "<br>";
echo "DB Port: " . ($dbPort[1] ?? 'Not found') . "<br>";
echo "DB Name: " . ($dbName[1] ?? 'Not found') . "<br>";
echo "DB User: " . ($dbUser[1] ?? 'Not found') . "<br>";
echo "DB Password: " . (isset($dbPass[1]) ? '[HIDDEN]' : 'Not found') . "<br>";

// Test database connection
try {
    $dsn = ($dbConnection[1] ?? 'mysql') . 
           ':host=' . ($dbHost[1] ?? 'localhost') . 
           ';port=' . ($dbPort[1] ?? '3306') . 
           ';dbname=' . ($dbName[1] ?? 'laravel');
    
    echo "Trying to connect with DSN: " . $dsn . "<br>";
    
    $pdo = new PDO(
        $dsn, 
        $dbUser[1] ?? 'root', 
        $dbPass[1] ?? '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Database connection successful<br>";
    
    // Test a simple query
    $start = microtime(true);
    $stmt = $pdo->query('SELECT COUNT(*) FROM articals');
    $count = $stmt->fetchColumn();
    $time = microtime(true) - $start;
    echo "Found $count articals in $time seconds<br>";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
    echo "Error code: " . $e->getCode();
}