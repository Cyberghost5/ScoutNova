<?php include 'include/session.php'; 
$player_id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM players WHERE id=:userid");
$stmt->execute(['userid'=>$player_id]);
$player = $stmt->fetch();

if (!$player) {
    $_SESSION['error'] = 'Player not found.';
    header('location: discover');
    exit();
}

$increase_view_count = $player['id'];

if (!isset($_SESSION["viewed_player_$increase_view_count"])) {
  $_SESSION["viewed_player_$increase_view_count"] = true;
  $stmt = $conn->prepare("UPDATE players SET profile_views = profile_views + 1 WHERE id = ?");
  $stmt->execute([$increase_view_count]);
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id=:userid");
$stmt->execute(['userid'=>$player['user_id']]);
$user_player = $stmt->fetch();

if (!$user_player) {
  $_SESSION['error'] = 'User not found.';
  header('location: videos');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css" integrity="" crossorigin="anonymous" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .playerpod .container { background: white; padding: 10px; border-radius: 16px; }
    .playerpod h2 { text-align: center; color: #9f04c8; margin-bottom: 10px; }
    .playerpod h4 { text-align: center; color: #64748b; margin-top: 0; }
    .playerpod .stats { display: flex; justify-content: space-around; margin: 30px 0; }
    .playerpod .card { background: #f1f5f9; padding: 10px; border-radius: 12px; width: 30%; text-align: center; }
    .playerpod .card h3 { margin: 0; font-size: 1.1rem; }
    .playerpod .card p { font-size: 1.3rem; margin-top: 8px; font-weight: bold; color: #9f04c8; }
    .playerpod .chart-container { margin: 40px 0; }
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

                if ($user_player['verification'] == 1) {
                    $kyc = '<span class="badge badge-success">Passed</span>';
                }
                if ($user_player['verification'] == 0) {
                    $kyc = '<span class="badge badge-danger">Pending</span>';
                }
                if ($user_player['role'] == 'agent') {
                    $user_playertype = '<span class="badge badge-primary">Agent</span>';
                }
                if ($user_player['role'] == 'user') {
                    $user_playertype = '<span class="badge badge-info">Player</span>';
                }                
              ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-body box-profile">
                  <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle mb-4" src="<?php echo (!empty($player['profile_image'])) ? 'images/'.$player['profile_image'] : 'images/profile.jpg'; ?>" alt="User profile picture">
                  </div>

                  <h3 class="profile-username text-center"><?php echo $user_player['username'];?><sup><i class="mdi mdi-checkbox-marked-circle-outline text-success" style="font-size:10px;"></i></sup> </h3>

                  <p class="text-muted text-center"><?php echo $user_player['firstname'].' '.$user_player['lastname']; ?></p>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>Sport</b> <span class="float-right"> <?php echo ucfirst($player['game_type']); ?></span>
                    </li>
                    <li class="list-group-item">
                      <b>Club</b> <span class="float-right"> <?php echo ucfirst($player['club']); ?></span>
                    </li>
                    <li class="list-group-item">
                      <b>Age</b> <span class="float-right"> <?php echo (new DateTime())->diff(new DateTime(($player['dob'])))->y; ?> years old</span>
                    </li>
                    <li class="list-group-item">
                      <b>Gender</b> <span class="float-right"> <?php echo ucfirst($player['gender']); ?></span>
                    </li>
                    <li class="list-group-item">
                      <b>Verification</b> <span class="float-right"> <?php echo $kyc; ?></span>
                    </li>
                    <li class="list-group-item">
                      <b>User Type</b> <span class="float-right"> <?php echo $user_playertype; ?></span>
                    </li>
                  </ul>
                  <a href="new_message?user_id=<?= $player['user_id'] ?>" class="btn btn-success btn-rounded btn-block"><b><i class="mdi mdi-chat-outline"></i> Message</b></a>
                  <?php if($user['role'] == 'agent'): ?>
                  <a href="watchlist_action?user_id=<?= $player['user_id'] ?>" class="btn btn-info btn-rounded btn-block"><b><i class="mdi mdi-account-multiple-outline"></i> Add to Watchlist</b></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="col-md-9 grid-margin transparent">
              <div class="demo-tabs">

                <div data-pws-tab="pod" data-pws-tab-name="POD" class="playerpod">
                  <?php
                  // Player stats
                  $statsStmt = $conn->prepare("SELECT * FROM PlayerStats WHERE player_id = ?");
                  $statsStmt->execute([$player_id]);
                  $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

                  // All video ratings
                  $ratingsStmt = $conn->prepare("
                      SELECT v.description, r.total_score, r.consistency_index, r.rating_breakdown, r.created_at
                      FROM PODRatings r
                      JOIN videos v ON v.id = r.video_id
                      WHERE r.player_id = ?
                      ORDER BY r.created_at ASC
                  ");
                  $ratingsStmt->execute([$player_id]);
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
                  
                  <?php if (count($ratings) > 0): ?>
                  <div class="container">
                    <h2>Player Performance Dashboard</h2>
                    <h4>Player ID: <?php echo $player_id; ?></h4>

                    <div class="stats">
                        <div class="card">
                            <h3>POD Score</h3>
                            <p><?php echo number_format($stats['pod_score'], 2); ?></p>
                        </div>
                        <div class="card">
                            <h3>Average Score</h3>
                            <p><?php echo number_format($stats['average_score'], 2); ?></p>
                        </div>
                        <div class="card">
                            <h3>Consistency</h3>
                            <p><?php echo number_format($stats['average_consistency'], 2); ?></p>
                        </div>
                    </div>

                    <div class="chart-container">
                        <h3>📈 Performance Trend Over Videos</h3>
                        <canvas id="trendChart"></canvas>
                    </div>

                    <div class="chart-container">
                        <h3>⚡ Average Skill Breakdown</h3>
                        <canvas id="skillChart"></canvas>
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
                  <?php else: ?>
                    <li class="list-group-item text-muted">No POD yet.</li>
                  <?php endif; ?>

                </div>

                <div data-pws-tab="player_profile" data-pws-tab-name="Player Profile">
                  <form class="form-horizontal">
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Position</label>
                      <div class="col-sm-10">
                        <input type="text" value="<?php echo $player['positions']; ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Date of Birth</label>
                      <div class="col-sm-5">
                        <input type="email" value="<?php echo ucfirst($player['dob']); ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                      <div class="col-sm-5">
                        <input type="email" value="<?php echo (new DateTime())->diff(new DateTime(($player['dob'])))->y; ?> years old" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">Country/Gender</label>
                      <div class="col-sm-5">
                        <input type="text" value="<?php echo $player['country']; ?>" class="form-control" id="inputName" placeholder="Name">
                      </div>
                      <div class="col-sm-5">
                        <input type="text" value="<?php echo ucfirst($player['gender']); ?>" class="form-control" id="inputName" placeholder="Name">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-4 col-form-label">Weight/Height/Footedness</label>
                      <div class="col-sm-2">
                        <input type="text" value="<?php echo $player['weight']; ?> KG" class="form-control" id="inputName" placeholder="Name">
                      </div>
                      <div class="col-sm-2">
                        <input type="text" value="<?php echo ucfirst($player['height']); ?> m" class="form-control" id="inputName" placeholder="Name">
                      </div>
                      <div class="col-sm-3">
                        <input type="text" value="<?php echo ucfirst($player['footedness']); ?> foot" class="form-control" id="inputName" placeholder="Name">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Academy Player</label>
                      <div class="col-sm-10">
                        <input type="email" value="<?php echo ucfirst($player['academy_status']); ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <?php if($player['academy_status'] == 'yes'): ?>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Academy Name</label>
                      <div class="col-sm-10">
                        <input type="email" value="<?php echo ucfirst($player['academy_name']); ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Description</label>
                      <div class="col-sm-10">
                        <input type="email" value="<?php echo ucfirst($player['description']); ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                  </form>
                </div>

                <div data-pws-tab="user_profile" data-pws-tab-name="User Profile">
                  <form class="form-horizontal">
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Username</label>
                      <div class="col-sm-10">
                        <input type="text" value="<?php echo $user_player['username']; ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">Full Name</label>
                      <div class="col-sm-5">
                        <input type="text" value="<?php echo $user_player['firstname']; ?>" class="form-control" id="inputName" placeholder="Name">
                      </div>
                      <div class="col-sm-5">
                        <input type="text" value="<?php echo $user_player['lastname']; ?>" class="form-control" id="inputName" placeholder="Name">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-10">
                        <input type="email" value="<?php echo $user_player['email']; ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">Phone No.</label>
                      <div class="col-sm-10">
                        <input type="number" value="<?php echo $user_player['contact_info']; ?>" class="form-control" id="inputName2" placeholder="Name">
                      </div>
                    </div>
                  </form>
                </div>

                <div data-pws-tab="videos" data-pws-tab-name="Videos">
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="card px-3">
                        <!-- <div class="card-body"> -->
                          <h4 class="card-title">Recent Video Uploads</h4>
                          <div id="video-gallery" class="row lightGallery text-center">
                            <?php
                            
                            // FETCH VIDEOS
                            $stmt = $conn->prepare("SELECT * FROM videos WHERE player_id = ? ORDER BY id DESC");
                            $stmt->execute([$user_player['id']]);
                            $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);    
                            ?>
                            
                              <?php foreach ($videos as $video): 
                      
                              if($video['status'] == 0){
                                $video_analysis = 'Pending';
                              }elseif($video['status'] == 1){
                                $video_analysis = 'Completed';
                              }elseif($video['status'] == 2){
                                $video_analysis = 'Rejected';
                              }

                              if($video['thumbnail_url'] == null || $video['thumbnail_url'] == ''){
                                $thumbnail = $settings['site_url'] ."assets/images/favicon.png";
                              } else {
                                $thumbnail = $video['thumbnail_url'];
                              }
                              
                              ?>
                              <a class="image-tile col-xl-3 col-lg-3 col-md-3 col-md-4 col-6g glightbox text-decoration-none" data-gallery="videos" data-title="<?php echo $video['description']; ?>, Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?>, Analysis Status:</b> <?php echo $video_analysis; ?>" href="<?php echo $video['file_url']; ?>">
                                <img src="<?php echo $thumbnail; ?>" alt="image" />
                                <div class="demo-gallery-poster">
                                  <img src="../assets/images/lightbox/play-button.png" alt="image">
                                </div>
                                <small class="text-muted">Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?></small>
                                <h6>Analysis Status: <?php echo $video_analysis; ?></h6>
                              </a>
                              <?php endforeach; ?>



                          </div>
                        <!-- </div> -->
                      </div>
                    </div>
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
