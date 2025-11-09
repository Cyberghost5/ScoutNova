<?php
include 'include/session.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(20, max(1, (int)($_GET['limit'] ?? 5)));
    $offset = ($page - 1) * $limit;
    
    // Get current user ID for like status
    $currentUserId = $user['id'] ?? 0;
    
    // Fetch videos with user information and interaction counts
    // $stmt = $conn->prepare("
    //     SELECT 
    //         v.id,
    //         v.file_url,
    //         v.thumbnail_url,
    //         v.description,
    //         v.created_at,
    //         u.id as user_id,
    //         u.firstname,
    //         u.lastname,
    //         u.username,
    //         u.photo,
    //         COUNT(DISTINCT vl.id) as likes_count,
    //         COUNT(DISTINCT vc.id) as comments_count,
    //         MAX(CASE WHEN vl.player_id = ? THEN 1 ELSE 0 END) as user_liked
    //     FROM videos v
    //     INNER JOIN users u ON v.player_id = u.id
    //     LEFT JOIN video_likes vl ON v.id = vl.video_id
    //     LEFT JOIN video_comments vc ON v.id = vc.video_id
    //     WHERE v.public_status = 1 
    //     AND v.file_url IS NOT NULL 
    //     AND v.file_url != ''
    //     GROUP BY v.id, u.id
    //     ORDER BY v.created_at DESC
    //     LIMIT ? OFFSET ?
    // ");

    // Simple query to avoid potential missing columns
    $stmt = $conn->prepare("
        SELECT 
            v.id,
            v.file_url,
            COALESCE(v.thumbnail_url, '') as thumbnail_url,
            COALESCE(v.description, '') as description,
            v.created_at,
            u.id AS user_id,
            COALESCE(u.firstname, '') as firstname,
            COALESCE(u.lastname, '') as lastname,
            COALESCE(u.username, '') as username,
            COALESCE(u.photo, '') as photo,
            COALESCE(v.likes, 0) AS likes_count,
            0 AS comments_count,
            0 AS user_liked
        FROM videos v
        INNER JOIN users u ON v.player_id = u.id
        WHERE v.file_url IS NOT NULL 
        AND v.file_url != ''
        ORDER BY v.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process video data
    $processedVideos = [];
    foreach ($videos as $video) {
        // Handle avatar
        $avatar = null;
        if (!empty($video['photo'])) {
            $avatar = '../user/images/' . $video['photo'];
        } else {
            $avatar = $settings['site_url'] . 'assets/images/favicon.png';
        }
        
        // Handle thumbnail
        $thumbnail = null;
        if (!empty($video['thumbnail_url'])) {
            $thumbnail = $video['thumbnail_url'];
        } else {
            $thumbnail = $settings['site_url'] . 'assets/images/favicon.png';
        }
        
        // Create username display
        $username = $video['username'] ?: $video['firstname'];
        if (empty($username)) {
            $username = 'User' . $video['user_id'];
        }
        
        $processedVideos[] = [
            'id' => (int)$video['id'],
            'file_url' => $video['file_url'],
            'thumbnail_url' => $thumbnail,
            'description' => $video['description'] ?: '',
            'username' => $username,
            'avatar' => $avatar,
            'likes_count' => (int)$video['likes_count'],
            'comments_count' => (int)$video['comments_count'],
            'user_liked' => (bool)$video['user_liked'],
            'created_at' => $video['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'videos' => $processedVideos,
        'page' => $page,
        'has_more' => count($videos) === $limit
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in load-videos.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred',
        'reason' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in load-videos.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while loading videos',
        'reason' => $e->getMessage()
    ]);
}
?>