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
  <title><?php echo $settings['site_name']; ?> | Activate</title>
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
              <h4>Account Activation</h4>
              <h6 class="font-weight-light">Here, we know if you're real.</h6>
              
              <?php
				$output = '';
				if(!isset($_GET['code']) OR !isset($_GET['user'])){
					$output .= '
					<div class="alert alert-danger">
					<h4>😕 Error!</h4>
					Code to activate account not found.
					</div>
					<h4 class="font-weight-light">You may <a href="register">Signup</a> or back to <a href="./">Homepage</a>.</h4>
					';
				}
				else{
					$conn = $pdo->open();

					$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE activate_code=:code AND id=:id");
					$stmt->execute(['code'=>$_GET['code'], 'id'=>$_GET['user']]);
					$row = $stmt->fetch();

					if($row['numrows'] > 0){
						if($row['status']){
							$output .= '
							<div class="alert alert-danger">
							<h4>😕 Error!</h4>
							Account already activated.
							</div>
							<h4 class="font-weight-light">You may <a href="login">Login</a> or back to <a href="index">Homepage</a>.</h4>
							';
						}
						else{
							try{
                                $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				                $code = substr(str_shuffle($set), 0, 12);

								$stmt = $conn->prepare("UPDATE users SET status=:status, activate_code=:activate_code WHERE id=:id");
								$stmt->execute(['status'=>1, 'activate_code'=>$code, 'id'=>$row['id']]); 

								$output .= '
								<div class="alert alert-success">
								<h4>🥳 Success!</h4>
								Account activated - Email: <b>'.$row['email'].'</b>.
								</div>
								<h4>You can <a href="login">Login</a> now or back to the <a href="./">Homepage</a>.</h4>
								';
								// header('location: login');
							}
							catch(PDOException $e){
								$output .= '
								<div class="alert alert-danger">
								<h4>😕 Error!</h4>
								'.$e->getMessage().'
								</div>
								';
							}

						}

					}
					else{
						$output .= '
						<div class="alert alert-danger">
						<h4>😕 Error!</h4>
						Cannot activate account. Wrong code.
						</div>
						<h4 class="font-weight-light">Check your email again or <a href="register">Signup</a> again.</h4>
						';
					}

					$pdo->close();
				}
				?>
				<div class="pt-3">
				    <?php echo $output; ?>
				</div>
              
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
