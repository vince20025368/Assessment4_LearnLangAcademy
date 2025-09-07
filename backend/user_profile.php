<?php
// backend/user_profile.php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';

$userId = $_SESSION['account_id'] ?? '';
$user = [];

if ($userId !== '') {
  $sql = "SELECT id, name, email FROM accounts WHERE id = ? LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $userId);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc() ?: [];
  $stmt->close();
}
