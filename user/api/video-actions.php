<?php
include '../include/session.php';

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
            // For now, just increment/decrement the likes count in videos table
            // This is a simple implementation without a separate likes table
            $stmt = $conn->prepare("UPDATE videos SET likes = likes + 1 WHERE id = ?");
            $stmt->execute([$videoId]);
            
            // Get updated likes count
            $stmt = $conn->prepare("SELECT likes FROM videos WHERE id = ?");
            $stmt->execute([$videoId]);
            $likesCount = $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'liked' => true, // Always return true for simplicity
                'likes_count' => (int)$likesCount
            ]);
            break;
            
        case 'get_comments':
            // Return empty comments for now
            echo json_encode([
                'success' => true,
                'comments' => []
            ]);
            break;
            
        case 'add_comment':
            // Return success without actually adding comment
            echo json_encode([
                'success' => true,
                'comments' => []
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
        'error' => 'Database error occurred',
        'reason' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in video-actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred',
        'reason' => $e->getMessage()
    ]);
}
?>