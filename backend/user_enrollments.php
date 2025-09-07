<?php
// backend/user_enrollments.php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';

$userId = $_SESSION['account_id'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10; 
$offset = ($page - 1) * $limit;

/* ==== Available Courses (not enrolled yet) ==== */
$sql = "SELECT id, course_code, title FROM courses
        WHERE id NOT IN (
          SELECT course_id FROM enrollments WHERE account_id = ?
        )
        ORDER BY title";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$availableCourses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ==== My Enrollments (paged) ==== */
$sql = "SELECT e.id AS enrollment_id, e.status, e.enrolled_at,
               c.course_code, c.title
        FROM enrollments e
        INNER JOIN courses c ON e.course_id = c.id
        WHERE e.account_id = ?
        ORDER BY e.enrolled_at DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $userId, $limit, $offset);
$stmt->execute();
$myEnrollments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ==== Count total ==== */
$sql = "SELECT COUNT(*) AS cnt FROM enrollments WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$total = (int)($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
$stmt->close();

$pages = $total > 0 ? (int)ceil($total / $limit) : 1;
