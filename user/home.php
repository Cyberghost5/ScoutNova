<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
    echo "<script>window.location.assign('set-profile')</script>"; 
    // header('location: set-profile');
}?>
<!-- Plugin css for this page -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css" integrity="" crossorigin="anonymous" />
<!-- End plugin css for this page -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <style>
  .card {
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .player-card:hover {
      transform: translateY(-3px);
      transition: 0.3s ease;
    }
 </style>
<body class="sidebar-dark">
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php include 'includes/navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <?php include 'includes/settings.php'; ?>
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <?php include 'includes/sidebar.php'; ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">

        <?php
          $conn = $pdo->open();

          $logged_in_user_id = $user['id'];

          // Suppose the logged-in user ID is 1 (Alexander for now)
          $sql = "
          SELECT 
            c.id AS chat_id,
            IF(c.user1_id = ?, u2.firstname, u1.firstname) AS firstname,
            IF(c.user1_id = ?, u2.lastname, u1.lastname) AS lastname,
            IF(c.user1_id = ?, u2.type, u1.type) AS usertype2,
            IF(c.user1_id = ?, u2.photo, u1.photo) AS photo,
            IF(c.user1_id = ?, p2.id, p1.id) AS userid2,
            m.message,
            m.timestamp
          FROM chats c
          JOIN users u1 ON c.user1_id = u1.id
          JOIN users u2 ON c.user2_id = u2.id
          LEFT JOIN players p1 ON u1.id = p1.user_id
          LEFT JOIN players p2 ON u2.id = p2.user_id
          JOIN (
            SELECT chat_id, message, timestamp
            FROM messages
            WHERE id IN (
              SELECT MAX(id) FROM messages GROUP BY chat_id
            )
          ) AS m ON m.chat_id = c.id
          WHERE (c.user1_id = ? OR c.user2_id = ?)
          ORDER BY m.timestamp DESC
          ";

          $stmt = $conn->prepare($sql);
          $stmt->execute([$logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id]);
          $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if($user['role'] == 'user'): ?>

          <?php
          $stmt = $conn->prepare("SELECT * FROM players WHERE user_id = ?");
          $stmt->execute([$user['id']]);
          $player = $stmt->fetch(PDO::FETCH_ASSOC);

          // Player stats
          $statsStmt = $conn->prepare("SELECT * FROM PlayerStats WHERE player_id = ?");
          $statsStmt->execute([$player['id']]);
          $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
          
          // All video ratings
          $ratingsStmt = $conn->prepare("
            SELECT v.description, r.total_score, r.consistency_index, r.rating_breakdown, r.created_at
            FROM PODRatings r
            JOIN videos v ON v.id = r.video_id
            WHERE r.player_id = ?
            ORDER BY r.created_at ASC
          ");
          $ratingsStmt->execute([$player['id']]);
          $ratings = $ratingsStmt->fetchAll(PDO::FETCH_ASSOC);
          
          $videoLabels = [];
          $totalScores = [];
          $consistencies = [];
          foreach ($ratings as $r) {
            $videoLabels[] = '"' . date('M d', strtotime($r['created_at'])) . '"';
            $totalScores[] = $r['total_score'];
            $consistencies[] = $r['consistency_index'];
          }
          
          // Average skill breakdown
          $skills = ['stamina' => 0, 'passing' => 0, 'speed' => 0, 'agility' => 0];
          foreach ($ratings as $r) {
            $b = json_decode($r['rating_breakdown'], true);
            foreach ($skills as $k => $v) $skills[$k] += $b[$k] ?? 0;
          }
          $count = count($ratings) ?: 1;
          foreach ($skills as $k => $v) $skills[$k] = $v / $count;
          ?>

          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Welcome <?php echo $user['firstname']; ?></h3>
                  <h6 class="font-weight-normal mb-0">All systems are running smoothly!</h6>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- <div class="col-md-12 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-people mt-auto">
                  <img src="../assets/images/dashboard/people.svg" alt="people">
                  <div class="weather-info">
                    <div class="d-flex">
                      <div>
                        <h2 class="mb-0 font-weight-normal"><i class="icon-sun me-2"></i>31<sup>C</sup></h2>
                      </div>
                      <div class="ms-2">
                        <h4 class="location font-weight-normal">Chicago</h4>
                        <h6 class="font-weight-normal">Illinois</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            <div class="col-md-12 grid-margin transparent">
              <div class="row">
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Overall Score</p>
                      <p class="fs-30 mb-2"><?php echo number_format($stats['average_score'] ?? 0, 2); ?></p>
                      <p>+5% vs last week</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Videos Uploaded</p>
                      <p class="fs-30 mb-2"><?php echo $stats['total_videos'] ?? 0; ?></p>
                      <p>2 awaiting analysis</p>
                    </div>
                  </div>
                </div>
              <!-- </div>
              <div class="row"> -->
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Profile Views</p>
                      <p class="fs-30 mb-2"><?php echo $player['profile_views']; ?></p>
                      <p>+18 this week</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Scout Messages</p>
                      <p class="fs-30 mb-2"><?php echo count($chats); ?></p>
                      <p>1 new this week</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title"><i class="mdi mdi-chart-line me-2"></i> Player Overtime Development (POD)</p>
                  <p class="font-weight-500 mb-4">Performance Trend Over Videos</p>
                  <?php
                  if (count($ratings) > 0): ?>
                  <canvas id="trendChart"></canvas>
                  <?php else: ?>
                    <li class="list-group-item text-muted text-center">No POD yet.</li>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="col-md-5 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title"><i class="mdi mdi-chart-line me-2"></i> Average Skill Breakdown</p>
                  <p class="font-weight-500 mb-4">Performance Trend Over Videos</p>
                  <?php if (count($ratings) > 0): ?>
                  <canvas id="skillChart"></canvas>
                  <?php else: ?>
                    <li class="list-group-item text-muted text-center">No Skill Ratings yet.</li>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card px-3">
                <div class="card-body">
                  <h4 class="card-title">Recent Video Uploads</h4>
                  <div id="video-gallery" class="row lightGallery text-center">
                    <?php
                    
                    // FETCH VIDEOS
                    $stmt = $conn->prepare("SELECT * FROM videos WHERE player_id = ? ORDER BY id DESC");
                    $stmt->execute([$user['id']]);
                    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);      
                    ?>
                    <?php if (count($videos) > 0): ?>
                      <?php foreach ($videos as $video): 
                      
                      if($video['status'] == 0){
                        $video_analysis = 'Pending';
                      }else{
                        $video_analysis = 'Completed';
                      }

                      // Display thumbnail from Cloudinary if available
                      if(!empty($video['thumbnail_url'])){
                        $thumbnail = $video['thumbnail_url'];
                      } else {
                        $thumbnail = $settings['site_url'] . 'assets/images/favicon.png'; // default thumbnail
                      }
                      
                      ?>
                      <a class="image-tile col-xl-3 col-lg-3 col-md-3 col-md-4 col-6g glightbox" data-gallery="videos" data-title="<?php echo $video['description']; ?>, Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?>, Analysis Status:</b> <?php echo $video_analysis; ?>" href="<?php echo $video['file_url']; ?>">
                        <img src="<?php echo $thumbnail; ?>" class="img-fluid" height="100px" alt="image" />
                        <div class="demo-gallery-poster">
                          <img src="../assets/images/lightbox/play-button.png" alt="image">
                        </div>
                        <small class="text-muted">Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?></small>
                        <p class="mb-0"><b>Analysis Status:</b> <?php echo $video_analysis; ?></p>
                      </a>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <li class="list-group-item text-muted w-100">No videos uploaded yet.</li>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col-md-12 grid-margin grid-margin-md-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Messages</h4>
                  
                  <div class="table-responsive">
                    <?php if (count($chats) > 0): ?>
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="pt-1 ps-0">
                              Chat
                            </th>
                            <th class="pt-1">
                              Last Message
                            </th>
                            <th class="pt-1">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($chats as $chat): 

                            if($chat['usertype2'] == '1'){
                              $profile_avatar = (!empty($chat['photo'])) ? '../admin/images/'.$chat['photo'] : '../admin/images/profile.jpg';
                            }else{
                              $profile_avatar = (!empty($chat['photo'])) ? 'images/'.$chat['photo'] : 'images/profile.jpg';
                            }
                          ?>
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <img src="<?php echo $profile_avatar; ?>" alt="profile" class="mr-3">
                                <div class="ms-3">
                                  <p class="mb-0"><?php echo $chat['firstname'] . ' ' . $chat['lastname']; ?></p>
                                  <p class="mb-0 text-muted text-small">
                                    <?php if (!empty($chat['timestamp'])): ?>
                                      <?= date("M j, g:i a", strtotime($chat['timestamp'])) ?>
                                    <?php endif; ?>
                                  </p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <?php if (!empty($chat['message'])): ?>
                                <?= mb_strimwidth($chat['message'], 0, 30, '...') ?>
                              <?php else: ?>
                                No messages yet.
                              <?php endif; ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="message?chat_id=<?= $chat['chat_id'] ?>"><i class="mdi mdi-chat-outline"></i> View</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <li class="list-group-item text-muted text-center">No chats yet.</li>
                    <?php endif; ?>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>

          <script>
          const labels = [<?php echo implode(',', $videoLabels); ?>];
          const trendCtx = document.getElementById('trendChart');
          
          new Chart(trendCtx, {
            type: 'line',
            data: {
              labels: labels,
              datasets: [
                {
                  label: 'Total Score',
                  data: [<?php echo implode(',', $totalScores); ?>],
                  borderColor: '#2563eb',
                  fill: false,
                  tension: 0.3
                },
                {
                  label: 'Consistency',
                  data: [<?php echo implode(',', $consistencies); ?>],
                  borderColor: '#f59e0b',
                  fill: false,
                  tension: 0.3
                }
                ]
              },
              options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, max: 100 } }
              }
            });
            
            // Skill Breakdown Chart
            const skillCtx = document.getElementById('skillChart');
            new Chart(skillCtx, {
              type: 'radar',
              data: {
                labels: ['Stamina', 'Passing', 'Speed', 'Agility'],
                datasets: [{
                  label: 'Average Skills',
                  data: [
                    <?php echo $skills['stamina']; ?>,
                    <?php echo $skills['passing']; ?>,
                    <?php echo $skills['speed']; ?>,
                    <?php echo $skills['agility']; ?>
                  ],
                  fill: true,
                  backgroundColor: 'rgba(16,185,129,0.2)',
                  borderColor: '#10b981',
                  pointBackgroundColor: '#047857'
                }]
              },
              options: {
                scales: { r: { beginAtZero: true, max: 100 } }
              }
            });
          </script>
        <?php elseif($user['role'] == 'agent'): ?>
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Welcome <?php echo $user['firstname']; ?></h3>
                  <h6 class="font-weight-normal mb-0">All systems are running smoothly!</h6>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- <div class="col-md-12 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-people mt-auto">
                  <img src="../assets/images/dashboard/people.svg" alt="people">
                  <div class="weather-info">
                    <div class="d-flex">
                      <div>
                        <h2 class="mb-0 font-weight-normal"><i class="icon-sun me-2"></i>31<sup>C</sup></h2>
                      </div>
                      <div class="ms-2">
                        <h4 class="location font-weight-normal">Chicago</h4>
                        <h6 class="font-weight-normal">Illinois</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            <div class="col-md-12 grid-margin transparent">
              <div class="row">
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Players Viewed</p>
                      <p class="fs-30 mb-2">128</p>
                      <p>+22 this week</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Players Shortlisted</p>
                      <p class="fs-30 mb-2">18</p>
                      <p>5 new additions</p>
                    </div>
                  </div>
                </div>
              <!-- </div>
              <div class="row"> -->
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Messages</p>
                      <p class="fs-30 mb-2">9</p>
                      <p>2 unread</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="mb-4">Upcoming Trials</p>
                      <p class="fs-30 mb-2">3</p>
                      <p>Next: Oct 14</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <form action="search" class="w-100" method="post">
                      <div class="input-group">
                        <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                          <span class="input-group-text" id="search">
                            <i class="icon-search"></i>
                          </span>
                        </div>
                        <input type="text" name="search" class="form-control" id="navbar-search-input" placeholder="Search players by name, position, or country..." aria-label="search" aria-describedby="search" required>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Recommended Players</h4>
                  <div class="row">
                    <?php
                    // FETCH Players
                    $sql = "
                      SELECT u.firstname, u.lastname, p.country, p.positions, p.dob, p.user_id, p.id, p.game_type, p.footedness, p.gender, plstats.average_score AS average_score, plstats.average_consistency AS average_consistency,
                      plstats.pod_score AS pod_score, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age
                      FROM players p
                      JOIN users u ON u.id = p.user_id
                      LEFT JOIN playerstats plstats ON plstats.player_id = p.id
                    ";

                    $sql .= " ORDER BY p.featured DESC";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if(count($players) > 0):
                    foreach ($players as $player): 

                    // FETCH Player
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$player['user_id']]);
                    $player_user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $roles = explode(',', $player['positions']);
                    ?>
                    <div class="col-md-4 mb-3">
                      <div class="card mt-3 p-3 player-card">
                        <img src="<?php echo (!empty($player['profile_image'])) ? 'images/'.$player['profile_image'] : 'images/profile.jpg'; ?>" style="width: 100px;" class="img-fluid rounded mb-2" alt="">
                        <h6 class="fw-bold mb-0"><?php echo $player_user['firstname']; ?> <?php echo $player_user['lastname']; ?></h6>
                        <small class="text-muted"><?php echo trim($roles[0]); ?> • <?php echo $player['country']; ?> • Age <?php echo (new DateTime())->diff(new DateTime(($player['dob'])))->y; ?></small>
                        <hr>
                        <p class="mb-1"><b>POD Score:</b> <?= $player['pod_score'] ?: 'N/A' ?></p>
                        <p class="mb-1"><b>Overall Score:</b> <?= $player['average_score'] ?: 'N/A' ?></p>
                        <p class="mb-1"><b>Consistency:</b> <?= !empty($player['average_consistency']) ? $player['average_consistency'] . '%' : 'N/A' ?></p>
                        <a class="btn btn-sm btn-outline-primary mt-2 w-100" href="player?id=<?php echo $player['id']; ?>"><i class="bi bi-eye me-1"></i>View Profile</a>
                      </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                      <li class="list-group-item text-muted w-100 p-3 m-3 text-center">No players found.</li>
                    <?php endif; ?>
                    
                  </div>
                
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 grid-margin grid-margin-md-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">My Shortlisted Players</h4>
                  <div class="table-responsive">
                    <?php if (count($chats) > 0): ?>
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="pt-1 ps-0">
                              Player
                            </th>
                            <th class="pt-1">
                              Overall Score
                            </th>
                            <th class="pt-1">
                              Last Contacted
                            </th>
                            <th class="pt-1">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($chats as $chat): ?>
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <img src="<?php echo (!empty($chat['photo'])) ? 'images/'.$chat['photo'] : 'images/profile.jpg'; ?>" alt="profile" class="mr-3">
                                <div class="ms-3">
                                  <p class="mb-0"><a href="player?id=<?php echo $chat['userid2']; ?>"><?php echo $chat['firstname'] . ' ' . $chat['lastname']; ?></p>
                                  <p class="mb-0 text-muted text-small">
                                    <?= htmlspecialchars($chat['country'] . ' | ' . (new DateTime())->diff(new DateTime(($chat['dob'])))->y); ?> years old
                                  </p>
                                </div>
                              </div>
                            </td>
                            <td>
                              90
                            </td>
                            <td>
                              <?php if (!empty($chat['timestamp'])): ?>
                                <?= date("M j, g:i a", strtotime($chat['timestamp'])) ?>
                              <?php else: ?>
                                Never contacted
                              <?php endif; ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="message?chat_id=<?= $chat['chat_id'] ?>"><i class="mdi mdi-chat-outline"></i> Message</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <li class="list-group-item text-muted p-3 text-center">No chats yet.</li>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <?php include 'includes/footer.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <?php include 'includes/scripts.php'; ?>
  <!-- GLightbox JS (from cdnjs) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/js/glightbox.min.js" integrity="" crossorigin="anonymous"></script>
  <script>
    // Initialize a simple GLightbox instance for links with class .glightbox
    const lightbox = GLightbox({
      selector: '.glightbox',
      touchNavigation: true,
      loop: true,
      // If you want videos to start playing immediately when the slide opens, try autoplayVideos: true
      // Note: autoplay behavior is constrained by browser policies (mobile often requires muted autoplay).
      // autoplayVideos: true,
    });

    // Example: programmatic API usage — open the first item on load (commented out by default)
    // lightbox.open();

    // Events: useful if you want to pause other players when a slide changes
    lightbox.on('open', ({index, slide}) => {
      // console.log('opened slide', index, slide);
    });
  </script>
    
</body>

</html>
