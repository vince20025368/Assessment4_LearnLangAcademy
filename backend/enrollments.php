<?php
// backend/enrollments.php — GET listing of courses with enrollment counts

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';

$q       = trim($_GET['q'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

$params = [];
$types  = "";

// ---------- COUNT total courses ----------
$whereSql = "";
if ($q !== "") {
    $whereSql = "WHERE (c.course_code LIKE ? OR c.title LIKE ?)";
    $like = "%{$q}%";
    $params[] = $like; $types .= "s";
    $params[] = $like; $types .= "s";
}

$sqlCount = "SELECT COUNT(*) AS cnt FROM courses c $whereSql";
$stmt = $conn->prepare($sqlCount);
if (!$stmt) {
    die("SQL error (count): " . $conn->error);
}
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$total = (int)($res->fetch_assoc()['cnt'] ?? 0);
$stmt->close();

$pages = max(1, (int)ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

// ---------- PAGE DATA with enrolled count ----------
$sql = "
    SELECT c.id, c.course_code, c.title, c.description,
           COUNT(e.id) AS enrolled_count
    FROM courses c
    LEFT JOIN enrollments e ON e.course_id = c.id
    $whereSql
    GROUP BY c.id, c.course_code, c.title, c.description, c.created_at
    ORDER BY enrolled_count DESC, c.title ASC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error (data): " . $conn->error);
}

if ($params) {
    $types2 = $types . "ii";
    $params2 = [...$params, $perPage, $offset];
    $stmt->bind_param($types2, ...$params2);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

// Expose: $rows, $total, $page, $pages, $q
