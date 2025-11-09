<?php
include 'include/session.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$videoId = (int)($input['video_id'] ?? 0);
$currentUserId = $user['id'] ?? 0;

if (!$currentUserId) {
    echo json_encode([
        'success' => false,
        'error' => 'User not authenticated'
    ]);
    exit;
}

if (!$videoId) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid video ID'
    ]);
    exit;
}

try {
    switch ($action) {
        case 'toggle_like':
            // Check if user already liked this video
            $stmt = $conn->prepare("SELECT id FROM video_likes WHERE video_id = ? AND player_id = ?");
            $stmt->execute([$videoId, $currentUserId]);
            $existingLike = $stmt->fetch();
            
            if ($existingLike) {
                // Unlike - remove like
                $stmt = $conn->prepare("DELETE FROM video_likes WHERE video_id = ? AND player_id = ?");
                $stmt->execute([$videoId, $currentUserId]);
                
                // Update likes count
                $stmt = $conn->prepare("UPDATE videos SET likes = GREATEST(0, likes - 1) WHERE id = ?");
                $stmt->execute([$videoId]);
                
                $liked = false;
            } else {
                // Like - add like
                $stmt = $conn->prepare("INSERT INTO video_likes (video_id, player_id, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$videoId, $currentUserId]);
                
                // Update likes count
                $stmt = $conn->prepare("UPDATE videos SET likes = likes + 1 WHERE id = ?");
                $stmt->execute([$videoId]);
                
                $liked = true;
            }
            
            // Get updated likes count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM video_likes WHERE video_id = ?");
            $stmt->execute([$videoId]);
            $likesCount = $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'liked' => $liked,
                'likes_count' => (int)$likesCount
            ]);
            break;
            
        case 'get_comments':
            $stmt = $conn->prepare("
                SELECT 
                    vc.comment_text as text,
                    vc.created_at,
                    u.firstname,
                    u.lastname,
                    u.username,
                    u.photo
                FROM video_comments vc
                INNER JOIN users u ON vc.player_id = u.id
                WHERE vc.video_id = ?
                ORDER BY vc.created_at DESC
                LIMIT 50
            ");
            $stmt->execute([$videoId]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $processedComments = [];
            foreach ($comments as $comment) {
                // Handle avatar
                $avatar = null;
                if (!empty($comment['photo'])) {
                    $avatar = '../images/' . $comment['photo'];
                } else {
                    $avatar = $settings['site_url'] . 'assets/images/favicon.png';
                }
                
                // Create username
                $username = $comment['username'] ?: $comment['firstname'];
                if (empty($username)) {
                    $username = 'Anonymous User';
                }
                
                $processedComments[] = [
                    'text' => $comment['text'],
                    'username' => $username,
                    'avatar' => $avatar,
                    'time_ago' => timeAgo($comment['created_at'])
                ];
            }
            
            echo json_encode([
                'success' => true,
                'comments' => $processedComments
            ]);
            break;
            
        case 'add_comment':
            $text = trim($input['text'] ?? '');
            
            if (empty($text)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Comment text is required'
                ]);
                break;
            }
            
            if (strlen($text) > 500) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Comment is too long (max 500 characters)'
                ]);
                break;
            }
            
            // Add comment
            $stmt = $conn->prepare("INSERT INTO video_comments (video_id, player_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$videoId, $currentUserId, $text]);
            
            // Get updated comments
            $stmt = $conn->prepare("
                SELECT 
                    vc.comment_text as text,
                    vc.created_at,
                    u.firstname,
                    u.lastname,
                    u.username,
                    u.photo
                FROM video_comments vc
                INNER JOIN users u ON vc.player_id = u.id
                WHERE vc.video_id = ?
                ORDER BY vc.created_at DESC
                LIMIT 50
            ");
            $stmt->execute([$videoId]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $processedComments = [];
            foreach ($comments as $comment) {
                // Handle avatar
                $avatar = null;
                if (!empty($comment['photo'])) {
                    $avatar = '../images/' . $comment['photo'];
                } else {
                    $avatar = $settings['site_url'] . 'assets/images/favicon.png';
                }
                
                // Create username
                $username = $comment['username'] ?: $comment['firstname'];
                if (empty($username)) {
                    $username = 'Anonymous User';
                }
                
                $processedComments[] = [
                    'text' => $comment['text'],
                    'username' => $username,
                    'avatar' => $avatar,
                    'time_ago' => timeAgo($comment['created_at'])
                ];
            }
            
            echo json_encode([
                'success' => true,
                'comments' => $processedComments
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action'
            ]);
            break;
    }
    
} catch (PDOException $e) {
    error_log("Database error in video-actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("General error in video-actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred'
    ]);
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . 'm ago';
    if ($time < 86400) return floor($time/3600) . 'h ago';
    if ($time < 2592000) return floor($time/86400) . 'd ago';
    if ($time < 31536000) return floor($time/2592000) . 'mo ago';
    
    return floor($time/31536000) . 'y ago';
}
?>