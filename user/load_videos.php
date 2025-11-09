<?php
include 'include/session.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
  $stmt = $conn->prepare("
    SELECT v.id, v.file_url, v.thumbnail_url, v.description, v.likes, 
           p.firstname, p.photo,
           (SELECT COUNT(*) FROM video_comments WHERE video_id = v.id) as comment_count
    FROM videos v
    JOIN users p ON v.player_id = p.id
    WHERE v.public_status = 1
    ORDER BY v.created_at DESC
    LIMIT ? OFFSET ?
  ");
  $stmt->bindValue(1, $limit, PDO::PARAM_INT);
  $stmt->bindValue(2, $offset, PDO::PARAM_INT);
  $stmt->execute();

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($row['photo'] == null || $row['photo'] == ''){
      $profilePic = $settings['site_url'] ."assets/images/favicon.png";
    } else {
      $profilePic = "images/".$row['photo'];
    }
    if($row['thumbnail_url'] == null || $row['thumbnail_url'] == ''){
      $thumbnail = $settings['site_url'] ."assets/images/favicon.png";
    } else {
      $thumbnail = $row['thumbnail_url'];
    }
    
    echo '
    <div class="video-card">
      <video muted loop playsinline preload="metadata" poster="' . htmlspecialchars($thumbnail) . '" onclick="toggleMute(this)">
        <source src="' . htmlspecialchars($row['file_url']) . '" type="video/mp4">
        Your browser does not support the video tag.
      </video>

      <div class="video-overlay">
        <h3>' . htmlspecialchars($row['firstname']) . '</h3>
        <p class="text-sm text-gray-300">' . htmlspecialchars($row['description']) . '</p>
      </div>

      <div class="video-actions text-white">
        <div class="mb-4">
          <img src="' . htmlspecialchars($profilePic) . '" alt="User" class="w-12 h-12 rounded-full border-2 border-white mb-2 object-cover">
        </div>
        <div onclick="toggleLike(' . $row['id'] . ', this)" class="mb-4 text-center cursor-pointer">
          <i class="mdi mdi-heart-outline text-2xl"></i>
          <span class="text-xs block">' . (int)$row['likes'] . '</span>
        </div>
        <div onclick="openComments(' . $row['id'] . ')" class="mb-4 text-center cursor-pointer">
          <i class="mdi mdi-comment-outline text-2xl"></i>
          <span class="text-xs block">' . (int)$row['comment_count'] . '</span>
        </div>
        <div class="text-center cursor-pointer">
          <i class="mdi mdi-share-outline text-2xl"></i>
        </div>
      </div>
    </div>';
  }
} catch(PDOException $e) {
  error_log("Database error in load_videos.php: " . $e->getMessage());
  echo '<div class="video-card"><p class="text-white text-center">Error loading videos</p></div>';
}
?>
