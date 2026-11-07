<?php
// Parse the DATABASE_URL environment variable set on Render
$db_url = parse_url(getenv('DATABASE_URL'));

$host     = $db_url['host'];
$port     = isset($db_url['port']) ? $db_url['port'] : 5432;
$user     = $db_url['user'];
$password = $db_url['pass'];
$dbname   = ltrim($db_url['path'], '/');

try {
    // Construct PostgreSQL DSN string
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    
    // Create PDO connection instance
    $conn = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
