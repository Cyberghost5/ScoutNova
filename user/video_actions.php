<?php
include 'include/session.php';
$action = $_POST['action'] ?? '';
$video_id = (int)($_POST['video_id'] ?? 0);

if ($action === 'like') {
    $conn->query("UPDATE videos SET likes = likes + 1 WHERE id = $video_id");
    $likes = $conn->query("SELECT likes FROM videos WHERE id = $video_id")->fetchColumn();
    echo json_encode(['likes' => $likes]);
    exit;
}

if ($action === 'get_comments') {
    $stmt = $conn->prepare("SELECT c.comment_text, p.username FROM video_comments c JOIN users p ON c.player_id=p.id WHERE c.video_id=? ORDER BY c.created_at DESC");
    $stmt->execute([$video_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p><b>{$row['username']}:</b> {$row['comment_text']}</p>";
    }
    exit;
}

if ($action === 'add_comment') {
    $text = trim($_POST['text'] ?? '');
    $player_id = $user['id']; // assuming session has player_id
    if ($text !== '') {
        $stmt = $conn->prepare("INSERT INTO video_comments (video_id, player_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$video_id, $player_id, $text]);
    }
    // Return updated list
    $stmt = $conn->prepare("SELECT c.comment_text, p.username FROM video_comments c JOIN users p ON c.player_id=p.id WHERE c.video_id=? ORDER BY c.created_at DESC");
    $stmt->execute([$video_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p><b>{$row['username']}:</b> {$row['comment_text']}</p>";
    }
    exit;
}
