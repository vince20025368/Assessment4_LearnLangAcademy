<?php
// backend/db.php
mysqli_report(MYSQLI_REPORT_OFF);

$DB_HOST = 'localhost';
$DB_NAME = 'learnlang';
$DB_USER = 'root';
$DB_PASS = '';
$DB_PORT = 3306;

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($conn->connect_errno) {
  http_response_code(500);
  exit('Database connection error.');
}
$conn->set_charset('utf8mb4');
