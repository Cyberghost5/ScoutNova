<?php
include 'include/session.php';

$conn = $pdo->open();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat_id = $_POST['chat_id'] ?? 0;
    $chat_uuid = $_POST['chat_uuid'] ?? 0;
    $username = $_POST['user_id'] ?? 0;
    $message = $_POST['message'] ?? '';
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    if (!empty($message) && $chat_id > 0 && $username > 0) {
        $stmt = $conn->prepare("INSERT INTO messages (uuid, chat_id, user_id, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$chat_uuid, $chat_id, $username, $message]);
    }
}
