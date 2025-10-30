<?php
include 'include/session.php';

$conn = $pdo->open();

// Get logged-in user ID
$user_id = $user['id']; // or set manually for testing
$player_id = $_GET['user_id'] ?? null;

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

// Step 1: Check if chat already exists
$stmt = $conn->prepare("SELECT id FROM watchlist WHERE agent_id = ? AND player_id = ?");
$stmt->execute([$user_id, $player_id]);
$exists = $stmt->fetch(PDO::FETCH_ASSOC);

if ($exists) {
    // Chat exists → go to messages
    $_SESSION['error'] = "This player is already in your watchlist.";
    header("Location: watchlist");
    exit;
}

// Send email notification to player (optional)

// Step 2: Create a new chat
$sql = "INSERT INTO watchlist (agent_id, player_id, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id, $player_id]);

// Redirect to messages page
$_SESSION['success'] = "Player added to your watchlist.";
header("Location: watchlist");
exit;