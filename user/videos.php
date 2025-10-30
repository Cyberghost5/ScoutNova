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
                  <h6 class="font-weight-normal mb-0">Your videos on <?php echo $settings['site_name']; ?>.</h6>
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

                $stmt = $conn->prepare("SELECT * FROM players WHERE user_id=:userid");
                $stmt->execute(['userid'=>$user['id']]);
                $player = $stmt->fetch();

                $player_id = $user['id'];
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
                  <a href="#addnew1" data-toggle="modal" class="btn btn-rounded btn-primary btn-sm mb-3"><i class="mdi mdi-library-plus"></i> New</a>

                  
                    <?php
                    // FETCH VIDEOS
                    $stmt = $conn->prepare("SELECT * FROM videos WHERE player_id = ? ORDER BY id DESC");
                    $stmt->execute([$user['id']]);
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
                                  Action
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($videos as $video): 

                              $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
                              $stmt->execute(['user_id' => $video['player_id']]);
                              $player = $stmt->fetch();

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

                              // Display thumbnail from Cloudinary if available
                              if(!empty($video['thumbnail_url'])){
                                  $thumbnail = $video['thumbnail_url'];
                              } else {
                                  $thumbnail = $settings['site_url'] . 'assets/images/favicon.png'; // default thumbnail
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
                                  <p class="mb-0"><a href="player?id=<?php echo $player['id']; ?>"><?php echo $player['firstname'] . ' ' . $player['lastname']; ?></a></p>
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
  

<!-- Add -->
<div class="modal fade" id="addnew1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><b>Add New Video</b></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="videos_add" enctype="multipart/form-data">
            <div class="form-group">
              <label for="instruction">Details</label>
              <textarea name="detail" class="form-control" rows="8" cols="80" placeholder="Enter Video Summary" required></textarea>
            </div>
            <div class="form-group">
              <label class="col-sm-6 control-label">Longer Video Link</label>
              <input type="url" class="form-control" name="full_link" placeholder="Enter URL" required>
            </div>
            <div class="form-group">
              <label for="validity" class="col-sm-6 control-label">Video</label>
              <input type="file" class="form-control" name="video" required accept="video/*">
            </div>
            <small>Videos must capture: Impressive key passes, Good defensive actions, Prolific goals (e.g., overhead kick for striker profiles). And for basketball: Skillful dunking , Successful dribbles, Completion of drills, Sprinting ability, Shooting power, Agility, balance, ball control.</small><br>
            <small class="text-danger">Note: Video to be uploaded has to be at least 60 seconds only and 10 MB in size, a link to full video preferally on Youtube can also be submnitted.</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
            <button type="submit" class="btn btn-primary btn-rounded" name="add"><i class="ti-save"></i> Save</button>
          </form>
      </div>
    </div>
  </div>
</div>


</body>

</html>
