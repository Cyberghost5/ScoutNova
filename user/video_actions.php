<?php
include 'include/session.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$video_id = (int)($_POST['video_id'] ?? 0);

if (!$video_id) {
    echo json_encode(['error' => 'Invalid video ID']);
    exit;
}

try {
    if ($action === 'like') {
        // Check if user already liked this video to prevent duplicate likes
        $stmt = $conn->prepare("SELECT id FROM video_likes WHERE video_id = ? AND player_id = ?");
        $stmt->execute([$video_id, $user['id']]);
        
        if ($stmt->fetch()) {
            // Unlike
            $conn->prepare("DELETE FROM video_likes WHERE video_id = ? AND player_id = ?")->execute([$video_id, $user['id']]);
            $conn->prepare("UPDATE videos SET likes = GREATEST(0, likes - 1) WHERE id = ?")->execute([$video_id]);
            $liked = false;
        } else {
            // Like
            $conn->prepare("INSERT INTO video_likes (video_id, player_id, created_at) VALUES (?, ?, NOW())")->execute([$video_id, $user['id']]);
            $conn->prepare("UPDATE videos SET likes = likes + 1 WHERE id = ?")->execute([$video_id]);
            $liked = true;
        }
        
        $stmt = $conn->prepare("SELECT likes FROM videos WHERE id = ?");
        $stmt->execute([$video_id]);
        $likes = $stmt->fetchColumn();
        
        echo json_encode(['likes' => (int)$likes, 'liked' => $liked]);
        exit;
    }

    if ($action === 'get_comments') {
        header('Content-Type: text/html');
        $stmt = $conn->prepare("
            SELECT c.comment_text, p.firstname, p.photo, c.created_at 
            FROM video_comments c 
            JOIN users p ON c.player_id = p.id 
            WHERE c.video_id = ? 
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$video_id]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $profilePic = $row['photo'] ? "images/".$row['photo'] : $settings['site_url']."assets/images/favicon.png";
            $timeAgo = time_elapsed_string($row['created_at']);
            echo '<div class="mb-3 flex items-start space-x-2">
                    <img src="'.htmlspecialchars($profilePic).'" alt="User" class="w-8 h-8 rounded-full object-cover">
                    <div>
                        <p class="text-sm"><b>'.htmlspecialchars($row['firstname']).'</b> <span class="text-gray-400 text-xs">'.$timeAgo.'</span></p>
                        <p class="text-sm">'.htmlspecialchars($row['comment_text']).'</p>
                    </div>
                  </div>';
        }
        exit;
    }

    if ($action === 'add_comment') {
        header('Content-Type: text/html');
        $text = trim($_POST['text'] ?? '');
        
        if ($text !== '' && strlen($text) <= 500) {
            $stmt = $conn->prepare("INSERT INTO video_comments (video_id, player_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$video_id, $user['id'], $text]);
        }
        
        // Return updated list
        $stmt = $conn->prepare("
            SELECT c.comment_text, p.firstname, p.photo, c.created_at 
            FROM video_comments c 
            JOIN users p ON c.player_id = p.id 
            WHERE c.video_id = ? 
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$video_id]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $profilePic = $row['photo'] ? "images/".$row['photo'] : $settings['site_url']."assets/images/favicon.png";
            $timeAgo = time_elapsed_string($row['created_at']);
            echo '<div class="mb-3 flex items-start space-x-2">
                    <img src="'.htmlspecialchars($profilePic).'" alt="User" class="w-8 h-8 rounded-full object-cover">
                    <div>
                        <p class="text-sm"><b>'.htmlspecialchars($row['firstname']).'</b> <span class="text-gray-400 text-xs">'.$timeAgo.'</span></p>
                        <p class="text-sm">'.htmlspecialchars($row['comment_text']).'</p>
                    </div>
                  </div>';
        }
        exit;
    }

} catch (PDOException $e) {
    error_log("Database error in video_actions.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;

    $string = array();
    
    if ($diff->y) $string[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
    if ($diff->m) $string[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    if ($weeks) $string[] = $weeks . ' week' . ($weeks > 1 ? 's' : '');
    if ($days) $string[] = $days . ' day' . ($days > 1 ? 's' : '');
    if ($diff->h) $string[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
    if ($diff->i) $string[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    if ($diff->s) $string[] = $diff->s . ' second' . ($diff->s > 1 ? 's' : '');

    if (!$full && count($string) > 0) {
        $string = array_slice($string, 0, 1);
    }
    
    return count($string) > 0 ? implode(', ', $string) . ' ago' : 'just now';
}
?>