<?php
// backend/course_delete.php — Handle Delete Course
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

function redirect_with_msg(string $msg, bool $error = false): void {
    if ($error) {
        $_SESSION['form_errors'] = [$msg];
    } else {
        $_SESSION['form_success'] = $msg;
    }
    header('Location: ../admin_courses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_msg('Invalid request.', true);
}

if (!csrf_validate($_POST['csrf'] ?? '')) {
    redirect_with_msg('Invalid CSRF token.', true);
}

$id = trim($_POST['id'] ?? '');
if ($id === '') {
    redirect_with_msg('Missing course ID.', true);
}

// Check if course exists
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? LIMIT 1");
if (!$stmt) {
    redirect_with_msg('Database error: ' . $conn->error, true);
}
$stmt->bind_param("s", $id);
$stmt->execute();
$res = $stmt->get_result();
$course = $res->fetch_assoc();
$stmt->close();

if (!$course) {
    redirect_with_msg('Course not found.', true);
}

// Delete course
$stmt = $conn->prepare("DELETE FROM courses WHERE id = ? LIMIT 1");
if (!$stmt) {
    redirect_with_msg('Database error: ' . $conn->error, true);
}
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    $stmt->close();
    redirect_with_msg('Course deleted successfully.');
} else {
    $err = $stmt->error ?: 'Delete failed.';
    $stmt->close();
    redirect_with_msg($err, true);
}
