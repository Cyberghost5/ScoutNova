<?php include 'includes/head.php';
 
$agent_id = $_GET['id'] ?? 0;
// $stmt = $conn->prepare("SELECT * FROM players WHERE id=:userid");
// $stmt = $conn->prepare("SELECT * FROM agent_profiles WHERE id=:userid");
// $stmt->execute(['userid'=>$agent_id]);
// $agent = $stmt->fetch();

$stmt = $conn->prepare("SELECT * FROM users WHERE id=:userid");
$stmt->execute(['userid'=>$agent_id]);
$user_agent = $stmt->fetch();

if (!$user_agent) {
    $_SESSION['error'] = 'User not found.';
    header('location: users');
    exit();
}
?>
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
                  <h3 class="font-weight-bold">Users</h3>
                  <h6 class="font-weight-normal mb-0">User Profile for ID: <?php echo $user_agent['id']; ?></h6>
                  <p>Date joined: <?php echo $user_agent['created_on']; ?></p>
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

                if ($user_agent['status'] == 0) {
                  $mark = 'mdi mdi-checkbox-marked-circle-outline text-warning';
                  $status = '<span class="badge badge-warning">Not Verified</span>';
                  $active = '<span class="float-right"><a href="#activate1" class="status" data-toggle="modal" data-id="'.$user_agent['id'].'"><i class="mdi mdi-check"></i></a></span>';
                  $block = '<a href="#activate1" data-toggle="modal" data-id="'.$user_agent['id'].'" class="btn btn-success btn-rounded btn-block"><b>erify User</b></a>';
                  // echo '<span class="badge badge-warning">Pending</span>';
                }
                if ($user_agent['status'] == 1) {
                  $mark = 'mdi mdi-checkbox-marked-circle-outline text-success';
                  $status = '<span class="badge badge-success">Active</span>';
                  $block = '<a href="#block" data-toggle="modal" data-id="'.$user_agent['id'].'" class="btn btn-danger btn-rounded btn-block"><b>Block User</b></a>';
                  // echo '<span class="badge badge-success">Successfull</span>';
                }
                if ($user_agent['status'] == 2) {
                  $mark = 'mdi mdi-checkbox-marked-circle-outline text-danger';
                  $status = '<span class="badge badge-danger">Blocked</span>';
                  $active = '<span class="float-right"><a href="#unblock" class="status" data-toggle="modal" data-id="'.$user_agent['id'].'"><i class="mdi mdi-check"></i></a></span>';
                  $block = '<a href="#unblock" data-toggle="modal" data-id="'.$user_agent['id'].'" class="btn btn-warning btn-rounded btn-block"><b>Unblock User</b></a>';
                  // echo '<span class="badge badge-danger">Rejected</span>';
                }
                // if ($user_agent['user_type'] == 1) {
                //   $type = '<span class="badge badge-success">Agent</span>';
                //   $action = '<a href="#downgrade" data-toggle="modal" data-id="'.$user_agent['id'].'" class="btn btn-warning btn-rounded btn-block"><b><i class="mdi mdi-download"></i> Downgrade User</b></a>';
                // }
                // if ($user_agent['user_type'] == 0) {
                //   $type = '<span class="badge badge-info">Normal</span>';
                //   $action = '<a href="#upgrade" data-toggle="modal" data-id="'.$user_agent['id'].'" class="btn btn-success btn-rounded btn-block"><b><i class="mdi mdi-upload"></i> Upgrade User</b></a>';
                // }
                
                if ($user_agent['verification'] == 1) {
                    $kyc = '<span class="badge badge-success">Completed</span>';
                }
                if ($user_agent['verification'] == 0) {
                    $kyc = '<span class="badge badge-danger">Pending</span>';
                }


                // if ($user_agent['verification'] == 1) {
                //     $kyc = '<span class="badge badge-success">Passed</span>';
                // }
                // if ($user_agent['verification'] == 0) {
                //     $kyc = '<span class="badge badge-danger">Pending</span>';
                // }
                if ($user_agent['role'] == 'agent') {
                    $user_agenttype = '<span class="badge badge-primary">Agent</span>';
                    $action = '<a href="#downgrade" data-toggle="modal" data-id="'.$user_agent['id'].'" class="btn btn-warning btn-rounded btn-block"><b>Change Account to Player</b></a>';
                }
                if ($user_agent['role'] == 'user') {
                    $user_agenttype = '<span class="badge badge-info">Player</span>';
                    $action = '<a href="#upgrade" data-toggle="modal" data-id="'.$user_agent['id'].'" class="btn btn-success btn-rounded btn-block"><b>Change Account to Agent</b></a>';
                }                
              ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-body box-profile">
                  <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle mb-4" src="<?php echo (!empty($user_agent['profile_image'])) ? '../user/images/'.$user_agent['profile_image'] : '../user/images/profile.jpg'; ?>" alt="User profile picture">
                  </div>

                  <h3 class="profile-username text-center"><?php echo $user_agent['username'];?><sup><i class="mdi mdi-checkbox-marked-circle-outline text-success" style="font-size:10px;"></i></sup> </h3>

                  <p class="text-muted text-center"><?php echo $user_agent['firstname'].' '.$user_agent['lastname']; ?></p>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>Verification</b> <span class="float-right"> <?php echo $kyc; ?></span>
                    </li>
                    <li class="list-group-item">
                      <b>Status</b> <span class="float-right"> <?php echo $status; ?></span>
                    </li>
                    <li class="list-group-item">
                      <b>User Type</b> <span class="float-right"> <?php echo $user_agenttype; ?></span>
                    </li>
                  </ul>

                  <?php echo $block; ?>
                  <?php echo $action; ?>
                  
                  <a href="#resetpass" data-toggle="modal" data-id="<?php $user_agent['id']; ?>" class="btn btn-info btn-rounded btn-block"><b>Reset Password</b></a>
                  
                  <a href="#del_account" data-toggle="modal" class="btn btn-danger btn-rounded btn-block"><b>Delete Account</b></a>
                </div>
              </div>
            </div>
            <div class="col-md-8 grid-margin transparent">
              <div class="demo-tabs">

                <div data-pws-tab="user_profile" data-pws-tab-name="User Profile">
                  <form class="form-horizontal">
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Username</label>
                      <div class="col-sm-10">
                        <input type="text" value="<?php echo $user_agent['username']; ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">Full Name</label>
                      <div class="col-sm-5">
                        <input type="text" value="<?php echo $user_agent['firstname']; ?>" class="form-control" id="inputName" placeholder="Name">
                      </div>
                      <div class="col-sm-5">
                        <input type="text" value="<?php echo $user_agent['lastname']; ?>" class="form-control" id="inputName" placeholder="Name">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-10">
                        <input type="email" value="<?php echo $user_agent['email']; ?>" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">Phone No.</label>
                      <div class="col-sm-10">
                        <input type="number" value="<?php echo $user_agent['contact_info']; ?>" class="form-control" id="inputName2" placeholder="Name">
                      </div>
                    </div>
                  </form>
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

    // Events: useful if you want to pause other agents when a slide changes
    lightbox.on('open', ({index, slide}) => {
      // console.log('opened slide', index, slide);
    });
  </script>

  <!-- Unblock -->
  <div class="modal fade" id="unblock">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><b>Unblocking</b></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="user_unblock">
          <input type="hidden" value="<?php echo $user_agent['id']; ?>"  name="id">
          <div class="text-center">
          <p>UNBLOCK USER</p>
          <h2 class="bold"><?php echo $user_agent['username']; ?></h2>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-close"></i> Close</button>
          <button type="submit" class="btn btn-warning btn-rounded" name="unblock"><i class="mdi mdi-block-helper"></i> Unblock</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Block -->
  <div class="modal fade" id="block">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><b>Blocking</b></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="user_block">
          <input type="hidden" value="<?php echo $user_agent['id']; ?>"  name="id">
          <div class="text-center">
            <p>BLOCK USER</p>
            <h2 class="bold"><?php echo $user_agent['username']; ?></h2>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-close"></i> Close</button>
          <button type="submit" class="btn btn-warning btn-rounded" name="block"><i class="mdi mdi-block-helper"></i> Block</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Upgrade -->
  <div class="modal fade" id="upgrade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><b>Changing</b></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="user_upgrade">
          <input type="hidden" value="<?php echo $user_agent['id']; ?>"  name="id">
          <div class="text-center">
            <p>CHANGE USER ACCOUNT TO SCOUT/AGENT</p>
            <h2 class="bold"><?php echo $user_agent['username']; ?></h2>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-close"></i> Close</button>
          <button type="submit" class="btn btn-success btn-rounded" name="upgrade"><i class="mdi mdi-upload"></i> Upgrade</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Downgrade -->
  <div class="modal fade" id="downgrade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><b>Changing</b></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="user_downgrade">
          <input type="hidden" value="<?php echo $user_agent['id']; ?>"  name="id">
          <div class="text-center">
          <p>CHANGE USER ACCOUNT TO PLAYER</p>
          <h2 class="bold"><?php echo $user_agent['username']; ?></h2>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-close"></i> Close</button>
          <button type="submit" class="btn btn-warning btn-rounded" name="downgrade"><i class="mdi mdi-upload"></i> Upgrade</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Reset Pass -->
  <div class="modal fade" id="resetpass">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
          <h4 class="modal-title"><b>Reset Password</b></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="user_resetpass">
          <input type="hidden" value="<?php echo $user_agent['id']; ?>"  name="id">
          <div class="text-center">
            <p>RESET USER PASSWORD TO '12345678'</p>
            <h2 class="bold"><?php echo $user_agent['username']; ?></h2>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-close"></i> Close</button>
          <button type="submit" class="btn btn-warning btn-rounded" name="resetpass"><i class="mdi mdi-reload"></i> Reset</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" id="del_account">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
              <h4 class="modal-title">Delete Account</h4>
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true">&times;</span>
              </button>
          	</div>
          	<div class="modal-body">
              <div class="row">
                <div class="col-sm-12 mb-4 mb-lg-0 stretch-card transparent">
                  <div class="card">
                    <div class="card-body text-center">
                        <p class="card-title">Are you sure you want to delete this account?</p>
                        <h2><?php echo $user_agent['username']; ?></h2>
                        <small>Note: This action is not revertable!</small>
                    </div>
                  </div>
                </div>
              </div>
            <form action="user_delete" method="post">
          	</div>
          	<div class="modal-footer justify-content-between">
              <input type="hidden" value="<?php echo $user_agent['id']; ?>"  name="id">
            	<button type="button" class="btn btn-success btn-rounded btn-flat" data-dismiss="modal"><i class="mdi mdi-window-close"></i> No, don't delete</button>
            	<button type="submit" class="btn btn-danger btn-rounded btn-icon-text mb-4 mt-4" name="del_account"><i class="mdi mdi-delete"></i>Delete</button>
          	</div>
          	</form>
        </div>
    </div>
  </div>
</body>

</html>
