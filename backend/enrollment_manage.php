<?php
// backend/enrollment_manage.php — GET enrollments for a course (with pagination)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';

$courseId = $_GET['course_id'] ?? '';
if ($courseId === '') {
  $_SESSION['form_errors'] = ['Missing course ID.'];
  header('Location: ../admin_enrollments.php');
  exit;
}

// ---------- Fetch Course ----------
$stmt = $conn->prepare("SELECT id, course_code, title, description 
                        FROM courses 
                        WHERE id = ? LIMIT 1");
$stmt->bind_param("s", $courseId);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$course) {
  $_SESSION['form_errors'] = ['Course not found.'];
  header('Location: ../admin_enrollments.php');
  exit;
}

// ---------- Pagination Setup ----------
$perPage = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// Count total enrollments
$sqlCount = "SELECT COUNT(*) FROM enrollments WHERE course_id = ?";
$stmt = $conn->prepare($sqlCount);
$stmt->bind_param("s", $courseId);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$pages = max(1, ceil($total / $perPage));

// ---------- Fetch Enrolled Users (paged) ----------
$sql = "SELECT e.id AS enrollment_id, e.status, e.enrolled_at,
               a.id AS user_id, a.name, a.email
        FROM enrollments e
        INNER JOIN accounts a ON e.account_id = a.id
        WHERE e.course_id = ?
        ORDER BY e.enrolled_at DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $courseId, $perPage, $offset);
$stmt->execute();
$enrollments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- Fetch Available Users (not enrolled yet) ----------
$sql = "SELECT a.id, a.name, a.email
        FROM accounts a
        WHERE a.account_type = 'user'
          AND a.id NOT IN (
            SELECT account_id FROM enrollments WHERE course_id = ?
          )
        ORDER BY a.name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $courseId);
$stmt->execute();
$availableUsers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Expose variables for the view
// $course, $enrollments, $availableUsers, $page, $pages, $total
