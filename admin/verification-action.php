<?php
include 'include/session.php';
if (!isset($admin['id'])) {
  header("Location: admin-login.php");
  exit;
}

$admin_id = $admin['id'];
$id       = $_POST['id'];
$type     = $_POST['type']; // player | scout
$action   = $_POST['action'];
$notes    = trim($_POST['notes']);

$status = ($action === 'approve') ? 'approved' : 'rejected';
$now    = date('Y-m-d H:i:s');

try {
  if ($type === 'player') {
    // Fetch the record
    $stmt = $conn->prepare("SELECT player_id FROM player_verifications WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update status
    $update = $conn->prepare("
      UPDATE player_verifications 
      SET status=?, reviewed_by=?, review_notes=?, updated_at=? 
      WHERE id=?
    ");
    $update->execute([$status, $admin_id, $notes, $now, $id]);

    // Update player table if approved
    if ($status === 'approved') {
      $conn->prepare("UPDATE users SET verified=1 WHERE id=?")
            ->execute([$record['player_id']]);
    }
    if ($status === 'rejected') {
      $conn->prepare("UPDATE users SET verified=2 WHERE id=?")
            ->execute([$record['player_id']]);
    }

  } elseif ($type === 'scout') {
    $stmt = $conn->prepare("SELECT scout_id FROM scout_verifications WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    $update = $conn->prepare("
      UPDATE scout_verifications 
      SET status=?, reviewed_by=?, review_notes=?, updated_at=? 
      WHERE id=?
    ");
    $update->execute([$status, $admin_id, $notes, $now, $id]);

    if ($status === 'approved') {
      $conn->prepare("UPDATE users SET verified=1 WHERE id=?")
           ->execute([$record['scout_id']]);
    }
    if ($status === 'rejected') {
      $conn->prepare("UPDATE users SET verified=2 WHERE id=?")
            ->execute([$record['scout_id']]);
    }
  }

  $_SESSION['success'] = ucfirst($type) . " verification " . $status . " successfully.";
  header("Location: verifications");
  exit;

} catch (Exception $e) {
  error_log($e->getMessage());
  $_SESSION['error'] = "Something went wrong: " . $e->getMessage();
  header("Location: verifications");
  exit;
}
