<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><title>Lapaknita Diagnostic</title></head><body style='font-family: sans-serif; padding: 20px; line-height: 1.6;'>";
echo "<h1 style='color: #2563eb;'>Lapaknita Deployment Diagnostic</h1>";

echo "<h2>1. PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO MySQL Loaded: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "<br>";
echo "PDO PostgreSQL Loaded: " . (extension_loaded('pdo_pgsql') ? 'Yes' : 'No') . "<br>";

echo "<h2>2. Environment Variables</h2>";
$vars = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'APP_KEY', 'APP_URL', 'CACHE_DRIVER', 'SESSION_DRIVER', 'LOG_CHANNEL'];
echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%; max-width: 600px;'>";
echo "<tr style='background: #f3f4f6;'><th>Key</th><th>Value</th></tr>";
foreach ($vars as $var) {
    // Check various sources of env vars
    $val = getenv($var) ?: (isset($_ENV[$var]) ? $_ENV[$var] : (isset($_SERVER[$var]) ? $_SERVER[$var] : 'NOT SET'));
    if ($var === 'DB_PASSWORD' || $var === 'APP_KEY') {
        $val = ($val !== 'NOT SET' && $val !== '') ? (substr($val, 0, 4) . '...' . substr($val, -4)) : $val;
    }
    echo "<tr><td><strong>{$var}</strong></td><td>{$val}</td></tr>";
}
echo "</table>";

echo "<h2>3. Database Connection Test</h2>";
$driver = getenv('DB_CONNECTION') ?: 'mysql';
$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '3306';
$db = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

if ($driver === 'sqlite') {
    echo "Testing SQLite connection...<br>";
    try {
        $path = __DIR__ . '/../database/database.sqlite';
        echo "SQLite path: {$path}<br>";
        if (!file_exists($path)) {
            echo "<span style='color: red;'>File database SQLite tidak ditemukan!</span><br>";
        } else {
            $pdo = new PDO("sqlite:{$path}");
            echo "<span style='color: green;'>SQLite Connection Success!</span><br>";
        }
    } catch (\Exception $e) {
        echo "<span style='color: red;'>SQLite Connection Failed: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "Testing Connection to <strong>{$driver}://{$host}:{$port}</strong>...<br>";
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$db}";
        if ($driver === 'pgsql') {
            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
        }
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout
        ];
        
        $pdo = new PDO($dsn, $user, $pass, $options);
        echo "<span style='color: green; font-weight: bold;'>Database Connection Success!</span><br>";
        
        // Cek apakah tabel ada
        $stmt = $pdo->query("SHOW TABLES"); // mysql
        if ($driver === 'pgsql') {
            $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
        }
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tabel yang ditemukan: " . implode(', ', $tables) . "<br>";
        
    } catch (\Exception $e) {
        echo "<span style='color: red; font-weight: bold;'>Database Connection Failed:</span><br>";
        echo "<pre style='background: #fee2e2; padding: 10px; border-radius: 4px; overflow-x: auto;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
    }
}

echo "</body></html>";
