<?php
include 'include/session.php';

$conn = $pdo->open();

// Get logged-in user ID
$logged_in_user_id = $user['id']; // or set manually for testing
$other_user_id = $_GET['user_id'] ?? null;

if (!$other_user_id) {
    // die("No user specified.");
    $_SESSION['error'] = "No user specified.";
    echo "<script>window.location.assign('messages')</script>";
    exit;
}

if ($other_user_id == $logged_in_user_id) {
    // die("You cannot start a chat with yourself.");
    $_SESSION['error'] = "You cannot start a chat with yourself.";
    echo "<script>window.location.assign('messages')</script>";
    exit;
}

// Step 1: Check if chat already exists
$sql = "
    SELECT id FROM chats 
    WHERE (user1_id = ? AND user2_id = ?) 
       OR (user1_id = ? AND user2_id = ?)
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->execute([$logged_in_user_id, $other_user_id, $other_user_id, $logged_in_user_id]);
$chat = $stmt->fetch(PDO::FETCH_ASSOC);

if ($chat) {
    // Chat exists → go to messages
    header("Location: message?chat_id=" . $chat['id']);
    exit;
}

// Send email notification to the other user and admin (optional)

// Step 2: Create a new chat
$sql = "INSERT INTO chats (user1_id, user2_id, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->execute([$logged_in_user_id, $other_user_id]);

$new_chat_id = $conn->lastInsertId();

// Redirect to messages page
header("Location: message?chat_id=$new_chat_id");
exit;