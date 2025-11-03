<?php include 'includes/head.php'; ?>
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
                  <h3 class="font-weight-bold">Welcome <?php echo $admin['firstname'];?>,</h3>
                  <h6 class="font-weight-normal mb-0">This is the Admin Panel, You're the boss! 😎🙌</h6>
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
            <!-- <div class="col-md-12 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-people mt-auto">
                  <img src="<?php echo $settings['site_url']; ?>admin/images/dashboard/people.svg" alt="people">
                  <div class="weather-info">
                    <div class="d-flex">
                      <img src="<?php echo $settings['site_url']; ?>admin/images/dashboard/shape-2.svg" class="img-fluid mb-0" alt="img" height="10" width="10">
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
            <div class="col-md-12 grid-margin transparent">
              <div class="row">
                <div class="col-md-3 mb-5 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="card-title text-white">Admins</p>
                      <div class="row">
                        <div class="col-8 text-white">
                          <?php
                            $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE role = 'admin'");
                            $stmt->execute();
                            $urow =  $stmt->fetch();

                            echo "<h3>".number_format_short($urow['numrows'])."</h3>";
                          ?>
                        </div>
                        <div class="col-4">
                          <i class="icon-lg mdi mdi mdi-account-star"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-5 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="card-title text-white">Users</p>
                      <div class="row">
                        <div class="col-8 text-white">
                          <?php
                            $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE role = 'user' OR role = 'agent'");
                            $stmt->execute();
                            $urow =  $stmt->fetch();
      
                            echo "<h3>".number_format_short($urow['numrows'])."</h3>";
                          ?>
                        </div>
                        <div class="col-4">
                          <i class="icon-lg mdi mdi-account-multiple"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-5 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="card-title text-white">Players</p>
                      <div class="row">
                        <div class="col-8 text-white">
                          <?php
                            $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM players"); // Might add WHERE status=1 later
                            $stmt->execute();
                            $urow =  $stmt->fetch();

                            echo "<h3>".number_format_short($urow['numrows'])."</h3>";
                          ?>
                        </div>
                        <div class="col-4">
                          <i class="icon-lg mdi mdi-account-multiple-outline"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-5 stretch-card transparent">
                  <div class="card card-primary">
                    <div class="card-body">
                      <p class="card-title text-white">Agents</p>
                      <div class="row">
                        <div class="col-8 text-white">
                          <?php
                            $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM agent_profiles"); // Might add WHERE status=1 later
                            $stmt->execute();
                            $urow =  $stmt->fetch();
      
                            echo "<h3>".number_format_short($urow['numrows'])."</h3>";
                          ?>
                        </div>
                        <div class="col-4">
                          <i class="icon-lg mdi mdi-account-switch"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php
          $conn = $pdo->open();

          // Suppose the logged-in user ID is 1 (Alexander for now)
          $logged_in_user_id = $admin['id'];

          // Get all chats the user is part of
          $sql = "
          SELECT u.*, p.*
          FROM players p
          JOIN users u ON u.id = p.user_id ORDER BY p.id DESC LIMIT 5 
          ";

          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

          // Get all chats the user is part of
          $sql = "
          SELECT u.*, p.*
          FROM agent_profiles p
          JOIN users u ON u.id = p.user_id ORDER BY p.id DESC LIMIT 5 
          ";

          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

          ?>

          <div class="row">
            <div class="col-md-12 grid-margin grid-margin-md-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Recent Player Signups</h4>
                  <div class="table-responsive">
                    <?php if (count($players) > 0): ?>
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="pt-1 ps-0">
                              Player
                            </th>
                            <th class="pt-1">
                              Sport
                            </th>
                            <th class="pt-1">
                             Status
                            </th>
                            <th class="pt-1">
                              Date Registered
                            </th>
                            <th class="pt-1">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($players as $player): 
                            $player_age = (new DateTime())->diff(new DateTime(($player['dob'])))->y;
                            if ($player['status'] == 0) {
                              $status = '<div class="badge badge-warning">Inactive</div>';
                              // echo '<div class="badge badge-warning">Pending</div>';
                            }
                            if ($player['status'] == 1) {
                              $status = '<div class="badge badge-success">Active</div>';
                              // echo '<div class="badge badge-success">Successfull</div>';
                            }
                            if ($player['status'] == 2) {
                              $status = '<div class="badge badge-danger">Blocked</div>';
                              // echo '<div class="badge badge-danger">Rejected</div>';
                            }
                          ?>
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <img src="<?php echo (!empty($player['photo'])) ? '../user/images/'.$player['photo'] : '../user/images/profile.jpg'; ?>" alt="profile" class="mr-3">
                                <div class="ms-3">
                                  <p class="mb-0"><a href="player/<?php echo $player['uuid']; ?>"><?php echo $player['firstname'] . ' ' . $player['lastname']; ?></p>
                                  <p class="mb-0 text-muted text-small">
                                    <?= htmlspecialchars($player['country'] . ' | ' . (new DateTime())->diff(new DateTime(($player['dob'])))->y); ?> years old
                                  </p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <?php echo htmlspecialchars(ucfirst($player['game_type'])); ?>
                            </td>
                            <td>
                              <?php echo $status; ?>
                            </td>
                            <td>
                              <?php if (!empty($player['created_at'])): ?>
                                <?= date("M j, g:i a", strtotime($player['created_at'])) ?>
                              <?php else: ?>
                                Not Found
                              <?php endif; ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="player/<?= $player['uuid'] ?>"><i class="mdi mdi-eye"></i> View</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <li class="list-group-item text-muted text-center w-100">No players found.</li>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 mt-4 grid-margin grid-margin-md-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Scouts/Agents</h4>
                  <div class="table-responsive">
                    <?php if (count($agents) > 0): ?>
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="pt-1 ps-0">
                              Scout/Agent
                            </th>
                            <th class="pt-1">
                              Sport
                            </th>
                            <th class="pt-1">
                             Status
                            </th>
                            <th class="pt-1">
                              Date Registered
                            </th>
                            <th class="pt-1">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($agents as $agent): 
                            if ($agent['status'] == 0) {
                              $status = '<div class="badge badge-warning">Inactive</div>';
                              // echo '<div class="badge badge-warning">Pending</div>';
                            }
                            if ($agent['status'] == 1) {
                              $status = '<div class="badge badge-success">Active</div>';
                              // echo '<div class="badge badge-success">Successfull</div>';
                            }
                            if ($agent['status'] == 2) {
                              $status = '<div class="badge badge-danger">Blocked</div>';
                              // echo '<div class="badge badge-danger">Rejected</div>';
                            }
                          ?>
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <img src="<?php echo (!empty($agent['photo'])) ? '../user/images/'.$agent['photo'] : '../user/images/profile.jpg'; ?>" alt="profile" class="mr-3">
                                <div class="ms-3">
                                  <p class="mb-0"><a href="<?php echo $settings['site_url']; ?>admin/agent/<?php echo $agent['uuid']; ?>"><?php echo $agent['firstname'] . ' ' . $agent['lastname']; ?></p>
                                  <p class="mb-0 text-muted text-small">
                                    <?= htmlspecialchars($agent['country'] . ' | ' . ($agent['organization'])); ?> years old
                                  </p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <?php echo htmlspecialchars(ucfirst($agent['game_type'])); ?>
                            </td>
                            <td>
                              <?php echo $status; ?>
                            </td>
                            <td>
                              <?php if (!empty($agent['created_at'])): ?>
                                <?= date("M j, g:i a", strtotime($agent['created_at'])) ?>
                              <?php else: ?>
                                Not Found
                              <?php endif; ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="agent/<?= $agent['uuid'] ?>"><i class="mdi mdi-eye"></i> View</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <li class="list-group-item text-muted text-center w-100">No Agents found.</li>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 mt-4">
              <div class="card px-3">
                <div class="card-body">
                  <h4 class="card-title">Recent Video Uploads</h4>
                  <div id="video-gallery" class="row lightGallery text-center">
                    <?php
                    
                    // FETCH VIDEOS
                    $stmt = $conn->prepare("SELECT * FROM videos ORDER BY id DESC LIMIT 4");
                    $stmt->execute();
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
                        <img src="<?php echo $thumbnail; ?>" alt="image" />
                        <div class="demo-gallery-poster">
                          <img src="../assets/images/lightbox/play-button.png" alt="image">
                        </div>
                        <small class="text-muted">Uploaded: <?php echo date('d, M Y', strtotime($video['created_at'])); ?></small>
                        <p class="mb-0"><b>Analysis Status:</b> <?php echo $video_analysis; ?></p>
                      </a>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <li class="list-group-item text-muted text-center w-100">No Videos found.</li>
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
