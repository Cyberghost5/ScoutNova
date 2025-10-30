<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
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
                  <h3 class="font-weight-bold">Set your PIN</h3>
                  <h6 class="font-weight-normal mb-0">Set a PIN for all your transactions on <?php echo $settings['site_name'];?></h6>
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
                if(isset($_SESSION['cancelled'])){
                  echo "
                    <div class='alert alert-secondary alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-delete-forever'></i> Cancelled!</h4>
                      ".$_SESSION['cancelled']."
                    </div>
                  ";
                  unset($_SESSION['cancelled']);
                }
              ?>
            </div>
          </div>
          <div class="row">
            <?php if($user['pin_set'] == 0): ?>
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
  								<div class="card-body">
  									<h4 class="card-title">Set Transaction PIN</h4>
                    <form class="form" action="set-profile-action" method="POST">
                      <div class="form-group">
                        <label>PIN</label>
                        <input type="number" name="pin" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control text-dark" placeholder="Enter PIN" required>
                      </div>
                      <div class="form-group">
                        <label>Confirm PIN</label>
                        <input type="number" name="confirm_pin" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" class="form-control text-dark" placeholder="Confirm PIN" required>
                      </div>

                      <button type="submit" name="set" class="btn btn-primary btn-rounded btn-icon-text mb-4 mt-4">
                        <i class="ti-save btn-icon-prepend"></i>
                        Set PIN
                      </button>
                    </form>
  								</div>
  							</div>
              </div>
            <?php else: ?>
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class='alert alert-success alert-dismissible'>
                          <h4><i class='icon mdi mdi-check'></i> Success!</h4>
                          PIN has already been set
                        </div>
                    </div>
                </div>
              </div>
            <?php endif; ?>
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
</body>

</html>
