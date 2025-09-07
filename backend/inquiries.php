<?php
// backend/inquiries.php — GET listing for contact inquiries (search + pagination)
// Populates: $rows, $total, $page, $pages, $q

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';

// Collect query params
$q     = trim($_GET['q'] ?? '');
$page  = (int)($_GET['page'] ?? 1);
$limit = 10;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where  = "1=1";
$params = [];
$types  = '';

if ($q !== '') {
  $where .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)";
  $like = '%' . $q . '%';
  $params[] = $like; $types .= 's';
  $params[] = $like; $types .= 's';
  $params[] = $like; $types .= 's';
}

/* ---------- COUNT ---------- */
$sqlCount = "SELECT COUNT(*) AS c FROM contact_inquiries WHERE $where";
$stmt = $conn->prepare($sqlCount);
if (!$stmt) { throw new RuntimeException('Prepare failed: '.$conn->error); }
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
$total = (int)($res->fetch_assoc()['c'] ?? 0);
$stmt->close();

$pages = max(1, (int)ceil($total / $limit));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $limit; }

/* ---------- PAGE DATA ---------- */
$sql = "
  SELECT id, ref_code, account_id, name, email, subject, message, action_taken, created_at
  FROM contact_inquiries
  WHERE $where
  ORDER BY created_at DESC
  LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) { throw new RuntimeException('Prepare failed: '.$conn->error); }

if ($params) {
  $types2  = $types . 'ii';
  $bind    = [...$params, $limit, $offset];
  $stmt->bind_param($types2, ...$bind);
} else {
  $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($row = $result->fetch_assoc()) { $rows[] = $row; }
$stmt->close();

// Expose: $rows, $total, $page, $pages, $q
