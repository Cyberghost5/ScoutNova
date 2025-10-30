<?php
include 'include/session.php';

$conn = $pdo->open();

// Get logged-in user ID
$user_id = $user['id']; // or set manually for testing
$player_id = $_GET['player_id'] ?? null;

if (!$player_id) {
    $_SESSION['error'] = "No user specified.";
    header("Location: watchlist");
    exit;
}

if ($player_id == $user_id) {
    $_SESSION['error'] = "You cannot watchlist a chat with yourself.";
    header("Location: watchlist");
    exit;
}

$stmt = $conn->prepare("DELETE FROM watchlist WHERE agent_id = ? AND player_id = ?");
$stmt->execute([$user_id, $player_id]);

// Redirect to messages page
$_SESSION['success'] = "Player removed from your watchlist.";
header("Location: watchlist");
exit;