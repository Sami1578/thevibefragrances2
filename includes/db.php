<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_errno) {
    http_response_code(500);
    die('<div style="font-family:sans-serif;padding:40px;max-width:560px;margin:auto">
        <h2>Database not ready</h2>
        <p>Could not connect to the database. Check <code>config.php</code>, then run
        <a href="' . BASE_URL . '/install.php"><strong>install.php</strong></a> to create the database.</p></div>');
}
$db->set_charset('utf8mb4');

function db_query($sql, $types = '', $params = [])
{
    global $db;
    $st = $db->prepare($sql);
    if (!$st) {
        die('SQL error: ' . htmlspecialchars($db->error));
    }
    if ($types !== '') {
        $st->bind_param($types, ...$params);
    }
    $st->execute();
    return $st;
}

function db_all($sql, $types = '', $params = [])
{
    $st = db_query($sql, $types, $params);
    $r = $st->get_result();
    return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}

function db_row($sql, $types = '', $params = [])
{
    $a = db_all($sql, $types, $params);
    return $a[0] ?? null;
}

function db_exec($sql, $types = '', $params = [])
{
    global $db;
    $st = db_query($sql, $types, $params);
    return $st->affected_rows >= 0 ? ($db->insert_id ?: $st->affected_rows) : 0;
}
