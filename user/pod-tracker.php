<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
    echo "<script>window.location.assign('set-profile')</script>"; 
    exit;
    // header('location: set-profile');
}?> 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css" integrity="" crossorigin="anonymous" />
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
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">POD Tracker</h3>
                  <h6 class="font-weight-normal mb-0">This page is coming soon.</h6>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <?php
                if(isset($_SESSION['error'])){
                  echo "
                    <div class='alert alert-danger alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-close'></i>Error!</h4>
                      ".$_SESSION['error']."
                    </div>
                  ";
                  unset($_SESSION['error']);
                }
                if(isset($_SESSION['success'])){
                  echo "
                    <div class='alert alert-success alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-check'></i> Success!</h4>
                      ".$_SESSION['success']."
                    </div>
                  ";
                  unset($_SESSION['success']);
                }
              ?>
            </div>
          </div>

          <?php
          $stmt = $conn->prepare("SELECT * FROM players WHERE user_id = ?");
          $stmt->execute([$user['id']]);
          $player = $stmt->fetch(PDO::FETCH_ASSOC);

          // Player stats
          $statsStmt = $conn->prepare("SELECT * FROM playerstats WHERE player_id = ?");
          $statsStmt->execute([$player['id']]);
          $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
          
          // All video ratings
          $ratingsStmt = $conn->prepare("
            SELECT v.description, r.total_score, r.consistency_index, r.rating_breakdown, r.created_at
            FROM podratings r
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
            <div class="col-md-12 grid-margin stretch-card">
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
          </div>

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">POD Tracking for each video</h4>
                  
                    <?php
                    // FETCH VIDEOS
                    $stmt = $conn->prepare("SELECT * FROM videos WHERE player_id = ? ORDER BY id DESC");
                    $stmt->execute([$player['id']]);
                    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);    
                    ?>

                      <div class="table-responsive">
                        <?php if (count($videos) > 0): ?>
                          <table class="table" id="example2">
                            <thead>
                              <tr>
                                <th class="pt-1 ps-0">
                                  Video
                                </th>
                                <th class="pt-1">
                                  Total Score
                                </th>
                                <th class="pt-1">
                                  Consistency Index
                                </th>
                                <th class="pt-1">
                                  Action
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              foreach ($videos as $video): 

                              // Display thumbnail from Cloudinary if available
                              if(!empty($video['thumbnail_url'])){
                                  $thumbnail = $video['thumbnail_url'];
                              } else {
                                  $thumbnail = $settings['site_url'] . 'assets/images/favicon.png'; // default thumbnail
                              }

                              // Fetch POD rating for this video
                              $ratingStmt = $conn->prepare("SELECT * FROM podratings WHERE video_id = ? AND player_id = ?");
                              $ratingStmt->execute([$video['id'], $player['id']]);
                              $r = $ratingStmt->fetch(PDO::FETCH_ASSOC);
                              if (!$r) continue; // Skip videos without POD rating

                              ?>
                              <tr>
                                <td class="py-1 ps-0">
                                  <div class="d-flex align-items-center mb-3">
                                    <a class="image-tile col-xl-3 col-lg-3 col-md-3 col-md-4 col-6g glightbox" data-gallery="videos" data-title="<?php echo $video['description']; ?>, Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?>, Analysis Status:</b> <?php echo $video_analysis2; ?>" href="<?php echo $video['file_url']; ?>">
                                      <img src="<?php echo $thumbnail; ?>" alt="image" />
                                    </a>
                                  </div>
                                  <p class="mb-0"><?php echo $video['video_id']; ?></p>
                                  <p class="mb-0 text-muted text-small">Date Analysis: <?php echo date('M d, Y - h:i a', strtotime($r['created_at'])); ?></p>
                                </td>
                                <td>
                                  <?php echo $r['total_score']; ?>
                                </td>
                                <td>
                                  <?php echo $r['consistency_index']; ?>
                                </td>
                                <td>
                                  <a class="btn btn-sm btn-outline-success" href="video?id=<?= $video['id'] ?>"><i class="mdi mdi-eye"></i> View</a>
                                </td>
                              </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        <?php else: ?>
                          <li class="list-group-item text-muted w-100 text-center">No videos yet.</li>
                        <?php endif; ?>
                      </div>

                  
                </div>
              </div>
            </div>
          </div>
          
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
  </script>
</body>

</html>
