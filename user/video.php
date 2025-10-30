<?php include 'include/session.php'; 
$video_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM videos WHERE id=:id");
$stmt->execute(['id'=>$video_id]);
$video = $stmt->fetch();

if (!$video) {
    $_SESSION['error'] = 'Video not found.';
    header('location: videos');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM players WHERE user_id=:id");
$stmt->execute(['id'=>$video['player_id']]);
$player = $stmt->fetch();

$stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
$stmt->execute(['id'=>$player['user_id']]);
$user_player = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css" integrity="" crossorigin="anonymous" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .playerpod .container { max-width: 900px; margin: 40px auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
  .playerpod h2 { text-align: center; margin-bottom: 20px; color: #1e40af; }
  .playerpod .chart-container { width: 100%; height: 350px; margin-top: 30px; }
  .playerpod .stats { display: flex; justify-content: space-around; margin-top: 20px; }
  .playerpod .stat-box { background: #f1f5f9; border-radius: 12px; padding: 20px; width: 45%; text-align: center; }
  .playerpod .stat-box h3 { margin: 0; font-size: 1.2rem; color: #1e293b; }
  .playerpod .stat-box p { font-size: 2rem; margin-top: 8px; font-weight: bold; color: #2563eb; }
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
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold"><?php echo $user_player['firstname']; ?> <?php echo $user_player['lastname']; ?></h3>
                  <h6 class="font-weight-normal mb-0">A player on <?php echo $settings['site_name']; ?>.</h6>
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

                if ($user_player['role'] == 'agent') {
                    $user_playertype = '<span class="badge badge-primary">Agent</span>';
                }
                if ($user_player['role'] == 'user') {
                    $user_playertype = '<span class="badge badge-info">Player</span>';
                }              
                
                // var_dump($player);
              ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Video Details</h4>

                  <p>Video ID: <br> <?php echo $video['video_id']; ?></p><br>
                  
                  <p>Uploaded by: <br> <?php echo $user_player['firstname'] . ' ' . $user_player['lastname']; ?></p><br>
                  
                  <p>Video Description: <br> <?php echo $video['description']; ?></p><br>

                  <p>Full Link: <br> <?php echo $video['full_link']; ?></p><br>

                  <p>Video: <br> 
                  <div id="video-gallery" class="row lightGallery text-center">
                    <a class="image-tile col-xl-3 col-lg-3 col-md-3 col-md-4 col-6g glightbox" data-gallery="videos" data-title="<?php echo $video['description']; ?>, Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?>" href="<?php echo $video['file_url']; ?>">
                      <img src="<?php echo $settings['site_url']; ?>assets/images/favicon.png" alt="image" />
                      <div class="demo-gallery-poster">
                        <img src="../assets/images/lightbox/play-button.png" alt="image">
                      </div>
                    </a>
                  </div>
                  </p>
                  
                  <small class="text-muted">Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?></small>
                
                  <hr>

                  <h4 class="card-title">Results</h4>

                  <div class="playerpod">

                    <?php if($video['status'] == 1): 
                    $stmt = $conn->prepare("
                        SELECT v.*, r.total_score, r.consistency_index, r.rating_breakdown, r.category
                        FROM videos v
                        LEFT JOIN PODRatings r ON v.id = r.video_id
                        WHERE v.id = ?
                    ");
                    $stmt->execute([$video_id]);
                    $video = $stmt->fetch(PDO::FETCH_ASSOC);
  
                    $breakdown = json_decode($video['rating_breakdown'], true);
                    ?> 
                    
                    <div class="container">
                      <h2>🎥 Video Analysis</h2>
                      <p><strong>Category:</strong> <?php echo htmlspecialchars($video['category']); ?></p>
                      <p><strong>Uploaded:</strong> <?php echo date('M d, Y', strtotime($video['created_at'])); ?></p>
  
                      <div class="chart-container">
                          <canvas id="skillsChart"></canvas>
                      </div>
  
                      <div class="stats">
                          <div class="stat-box">
                              <h3>Total Score</h3>
                              <p><?php echo number_format($video['total_score'], 2); ?></p>
                          </div>
                          <div class="stat-box">
                              <h3>Consistency</h3>
                              <p><?php echo number_format($video['consistency_index'], 2); ?></p>
                          </div>
                      </div>
                    </div>
  
                    <script>
                        const ctx = document.getElementById('skillsChart');
                        new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels: ['Stamina', 'Passing', 'Speed', 'Agility'],
                                datasets: [{
                                    label: 'Skill Breakdown',
                                    data: [
                                        <?php echo $breakdown['stamina']; ?>,
                                        <?php echo $breakdown['passing']; ?>,
                                        <?php echo $breakdown['speed']; ?>,
                                        <?php echo $breakdown['agility']; ?>
                                    ],
                                    fill: true,
                                    backgroundColor: 'rgba(37,99,235,0.2)',
                                    borderColor: '#2563eb',
                                    pointBackgroundColor: '#1e3a8a',
                                }]
                            },
                            options: {
                                scales: { r: { beginAtZero: true, max: 100 } },
                                plugins: { legend: { position: 'top' } }
                            }
                        });
                    </script>

                    <?php elseif($video['status'] == 2): ?>
                      <div class="alert alert-warning text-center">This video has been rejected.</div>
                    <?php else: ?>
                    <div class="container py-5">
                      <h2 class="mb-4 text-center">No POD Data Available</h2>
                      <p class="text-center">There is currently no Player Overtime Development (POD) data available for this video. Please check back later.</p>
                    </div>
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
</body>

</html>
