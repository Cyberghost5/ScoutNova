<?php
include 'include/session.php';
$id = $_GET['id'];
$status = $_GET['status'];

$stmt = $conn->prepare("UPDATE videos SET public_status=?, updated_at=NOW() WHERE uuid=?");
$stmt->execute([$status, $id]);

$_SESSION['success'] = "Video status updated successfully.";
header('Location: video/' . $id);
exit();
?>
