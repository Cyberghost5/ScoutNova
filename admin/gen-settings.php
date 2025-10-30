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
                  <h3 class="font-weight-bold">General Settings</h3>
                  <h6 class="font-weight-normal mb-0">Edit everything pertaining to <?php echo $settings['site_name']; ?></h6>
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
                  <h4 class="card-title"><?php echo $settings['site_name']; ?> General Settings</h4>
                  <p class="card-description">
                    <?php echo $settings['site_name']; ?> General Settings
                  </p>
                  <?php
                  $result = $conn->prepare("SELECT * FROM settings WHERE id = 1");
                  $result->execute();
                  for($i=0; $row = $result->fetch(); $i++){
                  ?>
                  <form class="forms-sample"  action="settings_edit" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="1">
                    <div class="form-group">
                      <label>Site Name</label>
                      <input type="text" name="site_name" class="form-control" placeholder="Enter site_name" value="<?php echo $row['site_name'];?>">
                    </div>
                    <div class="form-group">
                      <label>Site  URL</label>
                      <input type="text" name="site_url" class="form-control" placeholder="Enter site_url" value="<?php echo $row['site_url'];?>">
                    </div>
                    <div class="form-group">
                      <label>Site Description</label>
                      <textarea name="site_desc" class="form-control" rows="4" cols="80"><?php echo $row['site_desc'];?></textarea>
                    </div>
                    <div class="form-group">
                      <label>Site Keywords</label>
                        <textarea name="site_keyword" class="form-control" rows="4" cols="80"><?php echo $row['site_keyword'];?></textarea>
                    </div>

                    <button type="submit" name="edit" class="btn btn-primary btn-rounded mr-2 btn-icon-text">
                      <i class="ti-save btn-icon-prepend"></i>
                      Save
                    </button>
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
        <?php include 'includes/settings_modal.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <?php include 'includes/scripts.php'; ?>
  <script>
  $(function(){
  $(document).on('click', '.edit3', function(e){
    e.preventDefault();
    $('#edit3').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
  $(document).on('click', '.edit4', function(e){
    e.preventDefault();
    $('#edit4').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
  });
  </script>
</body>

</html>
