<?php
// backend/dashboard.php — fetch dashboard metrics
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php'; // provides $conn

/* ==== Accounts ==== */
$sql = "SELECT COUNT(*) AS total,
               SUM(is_verified=1) AS verified
        FROM accounts";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$total_accounts  = (int)($row['total'] ?? 0);
$total_verified  = (int)($row['verified'] ?? 0);
$verified_pct    = $total_accounts > 0 ? round(($total_verified / $total_accounts) * 100) : 0;

/* ==== Inquiries ==== */
$sql = "SELECT COUNT(*) AS cnt FROM contact_inquiries";
$total_inquiries = (int)($conn->query($sql)->fetch_assoc()['cnt'] ?? 0);

/* ==== Enrollments by Course (top 8) ==== */
$labels_courses = [];
$data_courses   = [];

$sql = "
  SELECT c.course_code, COUNT(e.id) AS cnt
  FROM courses c
  LEFT JOIN enrollments e ON e.course_id = c.id
  GROUP BY c.course_code
  ORDER BY cnt DESC
  LIMIT 8
";
$res = $conn->query($sql);
while ($r = $res->fetch_assoc()) {
  $labels_courses[] = $r['course_code'];
  $data_courses[]   = (int)$r['cnt'];
}

// Summary
$enroll_total = array_sum($data_courses);
$enroll_peak  = $data_courses ? max($data_courses) : 0;
$enroll_avg   = $data_courses ? round($enroll_total / count($data_courses)) : 0;
