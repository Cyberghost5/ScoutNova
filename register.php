<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php
$conn = $pdo->open();
try{
  $stmt = $conn->prepare("SELECT * FROM settings WHERE id = 1");
  $stmt->execute();
  $settings = $stmt->fetch();
}
catch(PDOException $e){
  echo "There is some problem in connection: " . $e->getMessage();
}
$pdo->close();

$now = date('d F, Y');
?>
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo $settings['site_name']; ?> | Register</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>" />
</head>

<body>
<?php
	if(isset($_SESSION['user'])){
		header('location: user/home');
	}
 ?>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <a href="<?php echo $settings['site_url']; ?>"><img src="<?php echo $settings['site_url'];?>assets/images/logo/logo.png" alt="logo"></a>
              </div>
              <h4>Hello! let's get started,</h4>
              <h6 class="font-weight-light">Register an account to continue.</h6>
              <?php
                if(isset($_SESSION['error'])){
                  echo "
                    <div class='alert alert-danger fade show'>
                      <strong>Oops! 😕</strong> <br>".$_SESSION['error']."
                    </div>
                  ";
                  unset($_SESSION['error']);
                }
                if(isset($_SESSION['block'])){
                  echo "
                    <div class='alert alert-warning fade show'>
                      <strong>Oh-Uh! 😒</strong> <br>".$_SESSION['block']."
                    </div>
                  ";
                  unset($_SESSION['block']);
                }
                if(isset($_SESSION['warning'])){
                  echo "
                    <div class='alert alert-warning fade show'>
                      <strong>Woah! 😒</strong> <br>".$_SESSION['warning']."
                    </div>
                  ";
                  unset($_SESSION['warning']);
                }
                if(isset($_SESSION['success'])){
                  echo "
                    <div class='alert alert-success fade show'>
                      <strong>Hurray 🥳</strong><br>".$_SESSION['success']."
                    </div>
                  ";
                  unset($_SESSION['success']);
                }
              ?>
              <form class="pt-3" action="register_action" method="post">
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" name="firstname" id="exampleInputFirstname1" placeholder="Firstname" required>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" name="lastname" id="exampleInputLastname1" placeholder="Lastname" required>
                </div>
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" name="email" id="exampleInputEmail1" placeholder="Email" required>
                </div>
                <div class="form-group">
                  <select class="form-control form-control-lg" name="role" id="role" required>
                    <option value="">Sign up as</option>
                    <option value="user">Player</option>
                    <option value="agent">Scout/Agent</option>
                  </select>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="password" id="exampleInputPassword1" placeholder="Password" required>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="confirm_password" id="exampleInputPassword2" placeholder="Confirm Password" required>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input" required>
                      I agree to all Terms & Conditions
                    </label>
                  </div>
                </div>
                <div class="mt-3">
                  <button type="submit" name="register" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Register</button>
                </div>
                <div class="mb-1 mt-4 text-center">
                  Already have an account? <a href="login" class="auth-link text-black">Login</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="<?php echo $settings['site_url']; ?>vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="<?php echo $settings['site_url']; ?>js/off-canvas.js"></script>
  <script src="<?php echo $settings['site_url']; ?>js/hoverable-collapse.js"></script>
  <script src="<?php echo $settings['site_url']; ?>js/template.js"></script>
  <script src="<?php echo $settings['site_url']; ?>js/settings.js"></script>
  <script src="<?php echo $settings['site_url']; ?>js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>
