<?php
/**
 * Database connection (PostgreSQL / Supabase) using PDO.
 *
 * On Render, set ONE environment variable:
 *   DATABASE_URL = the Supabase "Session pooler" connection string, e.g.
 *   postgresql://postgres.abcdefghijkl:YOUR-PASSWORD@aws-0-ap-southeast-1.pooler.supabase.com:5432/postgres
 *
 * For local testing you can instead set DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS.
 *
 * Every page gets $conn (a PDO object) after including this file.
 */

$database_url = getenv('DATABASE_URL');

if ($database_url) {
    $db_url   = parse_url($database_url);
    $host     = $db_url['host'] ?? 'localhost';
    $port     = $db_url['port'] ?? 5432;
    $user     = urldecode($db_url['user'] ?? 'postgres');
    $password = urldecode($db_url['pass'] ?? '');
    $dbname   = ltrim($db_url['path'] ?? '/postgres', '/');
} else {
    $host     = getenv('DB_HOST') ?: '127.0.0.1';
    $port     = getenv('DB_PORT') ?: 5432;
    $user     = getenv('DB_USER') ?: 'postgres';
    $password = getenv('DB_PASS') ?: '';
    $dbname   = getenv('DB_NAME') ?: 'postgres';
}

// Supabase requires SSL. Local databases usually don't support it.
$sslmode = getenv('DB_SSLMODE') ?: ($database_url ? 'require' : 'prefer');

try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=$sslmode",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Needed when connecting through Supabase's connection pooler.
            PDO::ATTR_EMULATE_PREPARES   => true,
        ]
    );
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed. Check the DATABASE_URL setting on Render.');
}

/**
 * Get one row from site_content (used by index.php and about.php).
 * Returns an array like ['section_name' => ..., 'content_text' => ..., 'image_path' => ...] or null.
 */
function getSectionContent($section_name, $conn) {
    $stmt = $conn->prepare("SELECT * FROM site_content WHERE section_name = ?");
    $stmt->execute([$section_name]);
    $row = $stmt->fetch();
    return $row ?: null;
}
