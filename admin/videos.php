<?php include 'includes/head.php'; ?>
<!-- Plugin css for this page -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css" integrity="" crossorigin="anonymous" />
<!-- End plugin css for this page -->
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
                  <h3 class="font-weight-bold">Videos</h3>
                  <h6 class="font-weight-normal mb-0">All videos on <?php echo $settings['site_name']; ?>.</h6>
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

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Recent Video Uploads</h4>
                  <p class="card-description">
                    All videos for your profile.
                  </p>

                  <?php
                    // FETCH VIDEOS
                    $stmt = $conn->prepare("SELECT * FROM videos ORDER BY id DESC");
                    $stmt->execute();
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
                              Player
                            </th>
                            <th class="pt-1">
                              Analysis Status
                            </th>
                            <th class="pt-1">
                              Public Status
                            </th>
                            <th class="pt-1">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($videos as $video): 

                          $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
                          $stmt->execute(['user_id' => $video['player_id']]);
                          $player = $stmt->fetch();

                          $stmt = $conn->prepare("SELECT * FROM players WHERE user_id = :user_id");
                          $stmt->execute(['user_id' => $video['player_id']]);
                          $user_player = $stmt->fetch();

                          $video_analysis = '';
                          if($video['status'] == 0){
                            $video_analysis = '<div class="badge badge-warning">Pending</div>';
                            $video_analysis2 = 'Pending';
                          }elseif($video['status'] == 1){
                            $video_analysis = '<div class="badge badge-success">Completed</div>';
                            $video_analysis2 = 'Completed';
                          }elseif($video['status'] == 2){
                            $video_analysis = '<div class="badge badge-danger">Rejected</div>';
                            $video_analysis2 = 'Rejected';
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
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <a class="image-tile col-xl-3 col-lg-3 col-md-3 col-md-4 col-6g glightbox" data-gallery="videos" data-title="<?php echo $video['description']; ?>, Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?>, Analysis Status:</b> <?php echo $video_analysis2; ?>" href="<?php echo $video['file_url']; ?>">
                                  <img src="<?php echo $thumbnail; ?>" alt="image" />
                                </a>
                                <p><?php echo $video['video_id']; ?></p>
                              </div>
                            </td>
                            <td class="py-1 ps-0">
                              <p class="mb-0"><a href="player?id=<?php echo $user_player['id']; ?>"><?php echo $player['firstname'] . ' ' . $player['lastname']; ?></a></p>
                              <p class="mb-0 text-muted text-small">
                                <?php if (!empty($video['created_at'])): ?>
                                  <?= date("M j, g:i a", strtotime($video['created_at'])) ?>
                                <?php endif; ?>
                              </p>
                            </td>
                            <td>
                              <?php echo $video_analysis; ?>
                            </td>
                            <td>
                              <?php echo $public_status; ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="video?id=<?= $video['id'] ?>"><i class="mdi mdi-eye"></i> View</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <li class="list-group-item text-muted text-center">No videos yet.</li>
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

</body>

</html>
