<?php include 'includes/head.php'; ?>
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
                  <h3 class="font-weight-bold">Email Settings</h3>
                  <h6 class="font-weight-normal mb-0">Edit everything email pertaining to <?php echo $settings['site_name']; ?></h6>
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
            <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"><?php echo $settings['site_name']; ?> Email Settings</h4>
                  <p class="card-description">
                    <?php echo $settings['site_name']; ?> Email Settings
                  </p>
                  <?php
                  $result = $conn->prepare("SELECT * FROM email_settings WHERE id = 1");
                  $result->execute();
                  for($i=0; $row = $result->fetch(); $i++){
                  ?>
                  <form class="forms-sample" action="email-settings_edit" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="1">
                    <div class="form-group">
                      <label>Host</label>
                        <input type="text" name="stmphost" class="form-control" placeholder="Enter Host" value="<?php echo $row['stmphost'];?>">
                    </div>
                    <div class="form-group">
                      <label>Username</label>
                        <input type="text" name="stmpuser" class="form-control" placeholder="Enter User" value="<?php echo $row['stmpuser'];?>">
                    </div>
                    <div class="form-group">
                      <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter Password" value="<?php echo $row['password'];?>">
                    </div>
                    <div class="form-group">
                      <label>Port No.</label>
                        <input type="text" name="portno" class="form-control" placeholder="Enter Port No" value="<?php echo $row['portno'];?>">
                    </div>
                    <div class="form-group">
                      <label>Set From</label>
                        <input type="email" name="from_email" class="form-control" placeholder="Enter From Email" value="<?php echo $row['from_email'];?>">
                    </div>
                    <div class="form-group">
                      <label>Set Reply To</label>
                        <input type="email" name="replyto" class="form-control" placeholder="Enter Reply To Email" value="<?php echo $row['replyto'];?>">
                    </div>
                    <button type="submit" name="edit" class="btn btn-primary btn-rounded mr-2 btn-icon-text">
                      <i class="ti-save btn-icon-prepend"></i>
                      Save
                    </button>
                    
                    <a href="#emailsend" data-toggle="modal" class="btn btn-warning btn-rounded float-right"><b><i class="mdi mdi-send"></i> Send Test Email</b></a>
                    
                  </form>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <?php include 'includes/footer.php'; ?>
        <!-- Unblock -->
        <div class="modal fade" id="emailsend">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
              <h4 class="modal-title"><b>Send Test Email</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                  <form class="form-horizontal" method="POST" action="email_send">
                    <div class="form-group">
                        <label for="edit_name" class="col-sm-3 control-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-close"></i> Close</button>
                    <button type="submit" class="btn btn-warning btn-rounded" name="send"><i class="mdi mdi-send"></i> Send</button>
                  </form>
                </div>
            </div>
          </div>
        </div>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <?php include 'includes/scripts.php'; ?>
  
</body>

</html>
