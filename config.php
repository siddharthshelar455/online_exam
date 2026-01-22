<?php
session_start();

/* ✅ SET TIMEZONE (MUST BE FIRST) */
date_default_timezone_set('Asia/Kolkata');

/* Load .env file */
if (!file_exists(__DIR__ . '/.env')) {
    die('.env file not found');
}

$env = parse_ini_file(__DIR__ . '/.env');

/* Database connection */
$conn = pg_connect(
    "host={$env['DB_HOST']} 
     port={$env['DB_PORT']}
     dbname={$env['DB_NAME']} 
     user={$env['DB_USER']} 
     password={$env['DB_PASS']}"
);

if (!$conn) {
    die("Database connection failed");
}
?>
