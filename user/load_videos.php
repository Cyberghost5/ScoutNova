<?php
// include 'include/session.php';

// $limit = 5;
// $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// $offset = ($page - 1) * $limit;

// // Fetch public videos only
// $stmt = $conn->prepare("
//   SELECT v.id, v.file_url, v.thumbnail_url, v.description, v.likes, p.firstname, p.photo
//   FROM videos v
//   JOIN users p ON v.player_id = p.id
//   WHERE v.public_status = 1
//   ORDER BY v.created_at DESC
//   LIMIT ? OFFSET ?
// ");
// $stmt->bindValue(1, $limit, PDO::PARAM_INT);
// $stmt->bindValue(2, $offset, PDO::PARAM_INT);
// $stmt->execute();

// while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
// //   $profilePic = $row['photo'] ? htmlspecialchars($row['photo']) : 'images/profile.jpg';
//   if($row['photo'] == null || $row['photo'] == ''){
//     $profilePic = $settings['site_url'] ."assets/images/favicon.png";
//   } else {
//     $profilePic = "images/".$row['photo'];
//   }
//   if($row['thumbnail_url'] == null || $row['thumbnail_url'] == ''){
//     $thumbnail = $settings['site_url'] ."assets/images/favicon.png";
//   } else {
//     $thumbnail = $row['thumbnail_url'];
//   }
//   echo '
//   <div class="video-card">
//     <video preload="none" poster="' . htmlspecialchars($thumbnail) . '" controls muted loop>
//       <source src="' . htmlspecialchars($row['file_url']) . '" type="video/mp4">
//     </video>

//     <div class="video-overlay">
//       <h3>' . htmlspecialchars($row['firstname']) . '</h3>
//       <p class="text-sm text-gray-300">' . htmlspecialchars($row['description']) . '</p>
//     </div>

//     <div class="video-actions text-white">
//       <img src="' . $profilePic . '" alt="User" class="w-12 h-12 rounded-full border-2 border-white mb-2">
//       <i class="mdi mdi-heart-outline"></i>
//       <i class="mdi mdi-comment-outline"></i>
//       <i class="mdi mdi-share-outline"></i>
//     </div>
//   </div>';
// }
?>


<?php
include 'include/session.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("
  SELECT v.id, v.file_url, v.thumbnail_url, v.description, v.likes, p.firstname, p.photo
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
    <video muted loop playsinline preload="auto" poster="' . htmlspecialchars($thumbnail) . '" onclick="toggleMute(this)">
      <source src="' . htmlspecialchars($row['file_url']) . '" type="video/mp4">
    </video>

    <div class="video-overlay">
      <h3>' . htmlspecialchars($row['firstname']) . '</h3>
      <p class="text-sm text-gray-300">' . htmlspecialchars($row['description']) . '</p>
    </div>

    <div class="video-actions text-white">
      <div onclick="toggleLike(' . $row['id'] . ', this)">
        <i class="mdi mdi-heart-outline"></i><span>' . (int)$row['likes'] . '</span>
      </div>
      <div onclick="openComments(' . $row['id'] . ')">
        <i class="mdi mdi-comment-outline"></i>
      </div>
      <i class="mdi mdi-share-outline"></i>
    </div>
  </div>';
}
