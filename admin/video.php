<?php include 'includes/head.php'; 
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
$user = $stmt->fetch();

?>
<!-- Plugin css for this page -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css" integrity="" crossorigin="anonymous" />
<!-- End plugin css for this page -->
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
                <div class="col-12 col-xl-12 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Video <?php echo $video['video_id']; ?></h3>
                  <h6 class="font-weight-normal mb-0"><?php echo $video['description']; ?></h6>
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

                $public_status = '';
                if($video['public_status'] == 0){
                  $public_status = '<div class="badge badge-warning">No</div>';
                }elseif($video['public_status'] == 1){
                  $public_status = '<div class="badge badge-success">Yes</div>';
                }elseif($video['public_status'] == 2){
                  $public_status = '<div class="badge badge-danger">Rejected</div>';
                }

                if($video['thumbnail_url'] == null || $video['thumbnail_url'] == ''){
                  $thumbnail = $settings['site_url'] ."assets/images/favicon.png";
                } else {
                  $thumbnail = $video['thumbnail_url'];
                }
              ?>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Video Details</h4>

                  <p>Video ID: <br> <?php echo $video['video_id']; ?></p><br>
                  
                  <p>Uploaded by: <br> <a href="player?id=<?php echo $player['id']; ?>"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></a></p><br>
                  
                  <p>Video Description: <br> <?php echo $video['description']; ?></p><br>

                  <p>Public Status: <br> <?php echo $public_status; ?>
                  <div class="dropdown d-inline-block">
                    <a href="#" class="text-secondary" data-toggle="dropdown"><i class="mdi mdi-pencil"></i></a>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="update_video_status?id=<?= $video['id'] ?>&status=1">Public</a>
                      <a class="dropdown-item" href="update_video_status?id=<?= $video['id'] ?>&status=0">Private</a>
                    </div>
                  </div>
                  </p><br>

                  <p>Full Link: <br> <?php echo $video['full_link']; ?></p><br>

                  <p>Video: <br> 
                  <div id="video-gallery" class="row lightGallery text-center">
                    <a class="image-tile col-xl-3 col-lg-3 col-md-3 col-md-4 col-6g glightbox" data-gallery="videos" data-title="<?php echo $video['description']; ?>, Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?>" href="<?php echo $video['file_url']; ?>">
                      <img src="<?php echo $thumbnail; ?>" alt="image" />
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
                      <h2>Video Analysis</h2>
                      <p><strong>Category:</strong> <?php echo htmlspecialchars($video['category']); ?></p>
                      <p><strong>Uploaded:</strong> <?php echo date('M d, Y - h:i A', strtotime($video['created_at'])); ?></p>
                      <p><strong>Analysised:</strong> <?php echo date('M d, Y - h:i A', strtotime($video['updated_at'])); ?></p>
  
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
                      <p class="text-center">There is currently no Player Overtime Development (POD) data available for this video. Please check back later or submit performance data.</p>
                      <p class="text-center">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#methodModal">
                          Submit Rating
                        </button>
                      </p>
                    </div>
  
                    <!-- Choose Method Modal -->
                    <div class="modal fade" id="methodModal" tabindex="-1" aria-labelledby="methodModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-light">
                            <h5 class="modal-title" id="methodModalLabel">Select Submission Method</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          </div>
                          <div class="modal-body text-center">
                            <p>How would you like to process this player's performance?</p>
                            <div class="d-flex justify-content-center gap-3 mt-4">
                              <button id="manualBtn" data-toggle="modal" data-target="#manualModal" class="btn btn-outline-primary mr-3">Manual Method</button>
                              <button id="aiBtn" data-toggle="modal" data-target="#aiModal" class="btn btn-outline-success mr-3">Submit to AI</button>
                              <button id="rejectBtn" data-toggle="modal" data-target="#rejectModal" class="btn btn-outline-danger mr-3">Reject</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
  
                    <!-- Manual Entry Modal -->
                    <div class="modal fade" id="manualModal" tabindex="-1" aria-labelledby="manualModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-light">
                            <h5 class="modal-title" id="manualModalLabel">Manual Rating Entry</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          </div>
                          <form id="manualForm" class="form-horizontal" method="POST" action="update_pod">
                            <div class="modal-body">
                              <div class="g-3">
                                <div class="form-group">
                                  <label class="form-label">Player ID</label>
                                  <input type="number" class="form-control" name="player_id" value="<?php echo $player['id']; ?>" readonly required>
                                </div>
                                <div class="form-group">
                                  <label class="form-label">Video ID</label>
                                  <input type="text" class="form-control" name="video_#id" value="<?php echo $video['video_id']; ?>" readonly required>
                                </div>
                                <div class="form-group">
                                  <label class="form-label">Category</label>
                                  <input type="text" class="form-control" name="category" value="<?php echo $player['game_type']; ?>" readonly required>
                                </div>
  
                                <hr class="my-3">
  
                                <h6>Performance Breakdown (0/100)</h6>
  
                                <div class="form-group">
                                  <label class="form-label">Stamina</label>
                                  <input type="number" name="stamina" class="form-control" min="0" max="100" required>
                                </div>
                                <div class="form-group">
                                  <label class="form-label">Passing</label>
                                  <input type="number" name="passing" class="form-control" min="0" max="100" required>
                                </div>
                                <div class="form-group">
                                  <label class="form-label">Speed</label>
                                  <input type="number" name="speed" class="form-control" min="0" max="100" required>
                                </div>
  
                                <div class="form-group">
                                  <label class="form-label">Agility</label>
                                  <input type="number" name="agility" class="form-control" min="0" max="100" required>
                                </div>
                              </div>
                            </div>
  
                            <div class="modal-footer">
                              <input type="hidden" name="source" value="Manual">
                              <input type="hidden" name="ai_confidence" value="">
                              <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                              <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Submit Rating</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!-- Reject Video Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-light">
                            <h5 class="modal-title" id="rejectModalLabel">Reject Video Submission</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          </div>

                          <div class="modal-body text-center">
                            <p>Are you sure you want to reject this video submission?</p>

                            <div class="d-flex justify-content-center g-3 mt-4">
                              <!-- Option 1: Reject only -->
                              <button type="button" class="btn btn-outline-warning mr-3" onclick="rejectVideo(false)">
                                Yes
                              </button>

                              <!-- Option 2: Reject and Delete -->
                              <button type="button" class="btn btn-outline-danger mr-3" onclick="rejectVideo(true)">
                                Yes, delete video
                              </button>

                              <!-- Option 3: Cancel -->
                              <button type="button" class="btn btn-outline-success" data-dismiss="modal">
                                No
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <script>
                    function rejectVideo(deleteVideo) {
                      const videoId = <?php echo json_encode($video_id ?? null); ?>;
                      if (!videoId) return alert("Missing video ID.");

                      // Send AJAX request to backend
                      fetch('reject_video.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                          video_id: videoId,
                          delete: deleteVideo ? 1 : 0
                        })
                      })
                      .then(res => res.json())
                      .then(data => {
                        if (data.success) {
                          alert(data.message);
                          location.reload(); // Refresh page after success
                        } else {
                          alert(data.error || "Something went wrong.");
                        }
                      })
                      .catch(err => console.error(err));
                    }
                    </script>
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
    $(function () {
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
  </script>
  <script>

  document.getElementById("aiBtn").addEventListener("click", function() {
    // var methodModal = document.getElementById('methodModal');
    // methodModal.hide();

    // Example: Send to AI endpoint
    fetch('submit_to_ai.php', {
      method: 'POST',
      body: JSON.stringify({ video_id: 123 }),
      headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => alert(data.message))
    .catch(err => alert('Error connecting to AI service, use manual method or try again later.'));
  });
  </script>
  
           

</body>

</html>
