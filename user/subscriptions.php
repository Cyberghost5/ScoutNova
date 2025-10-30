<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php';
if($user['profile_set'] == 0){
    echo "<script>window.location.assign('set-profile')</script>"; 
    // header('location: set-profile');
} ?> 
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
                  <h3 class="font-weight-bold">Subscription</h3>
                  <h6 class="font-weight-normal mb-0">This page is coming soon.</h6>
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
                    <div class='alert alert-warning alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-information-outline'></i> Uh Oh!</h4>
                      ".$_SESSION['cancelled']."
                    </div>
                  ";
                  unset($_SESSION['cancelled']);
                }
              ?>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="container text-center pt-5">
                    <h4 class="mb-3 mt-5">Start up your football/basketball career today</h4>
                    <p class="w-75 mx-auto mb-5">Choose a plan that suits you the best. If you are not fully satisfied,
                      we offer 30-day money-back guarantee no questions asked!!</p>

                    <?php
                    // Check if user has an active subscription
                    $conn = $pdo->open();
                    $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_email = ? AND status = 'active' LIMIT 1");
                    $stmt->execute([$user['email']]);
                    $active_subscription = $stmt->fetch(PDO::FETCH_ASSOC);

                    // var_dump($active_subscription);
                    
                    if(!$active_subscription):
                    ?>
                    
                    <div class="row pricing-table">
                      <?php
                      $conn = $pdo->open();

                      $stmt = $conn->prepare("SELECT * FROM plans WHERE role=:role OR role = 'all' AND status = 1 ORDER BY id ASC");
                      $stmt->execute(['role' => $user['role']]);
                      $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      $i = 0;
                      ?>
                      <?php foreach($plans as $plan):
                        $i++; 
                      ?>
                      <div class="col-md-6 col-xl-6 grid-margin stretch-card pricing-card">
                        <div class="card border-primary border pricing-card-body">
                          <div class="text-center pricing-card-head">
                            <h3><?php echo $plan['name']; ?></h3>
                            <p><?php echo $plan['intervals']; ?></p>
                            <h1 class="font-weight-normal mb-4"> <?php echo $plan['currency_short']; ?><?php echo number_format($plan['amount'], 2); ?></h1>
                          </div>
                          <ul class="list-unstyled plan-features">
                            <li><?php echo $plan['details']; ?></li>
                          </ul>
                          <div class="wrapper">
                            <?php if($user['subscription_plan_id'] == $plan['plan_id'] || $user['subscription_plan_id'] == $plan['id']): ?>
                            <a href="#" class="btn btn-outline-primary active btn-block">Subscribed</a>
                            <?php else: ?>
                            <a href="#" class="btn btn-outline-primary btn-block" data-toggle="modal" data-target="#methodModal<?php echo $i; ?>">Upgrade</a>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>

                      <div class="modal fade" id="methodModal<?php echo $i; ?>" tabindex="-1" aria-labelledby="methodModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header bg-light">
                              <h5 class="modal-title" id="methodModalLabel"><?php echo $plan['name']; ?></h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body text-center">
                              <p>You are about to upgrade your subscription to the <?php echo $plan['name']; ?> plan and you will be charged <?php echo $plan['currency_short']; ?><?php echo number_format($plan['amount'], 2); ?> at a <?php echo $plan['intervals']; ?> basis.</p>
                              <p>By upgrading to a <?php echo $settings['site_name']; ?> plan, you agree to the <?php echo $settings['site_name']; ?> Terms of Service and the Offer Terms and Conditions. Note: The <?php echo $settings['site_name']; ?> Privacy Policy describes how data is handled in this service.</p>
                              <div class="d-flex justify-content-center gap-3 mt-4">
                                <form action="subscription" method="POST">
                                  <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                  <button type="submit" class="btn btn-outline-success mr-3">Agree</button>
                                </form>
                                <button id="rejectBtn" data-dismiss="modal" class="btn btn-outline-danger mr-3">Cancel</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <?php endforeach; ?>
                      
                    </div>

                    <?php else: ?>
                    <div class="alert alert-info">
                      <h4 class="alert-heading">You already have an active subscription plan.</h4>
                      <p>Your current subscription plan is <strong><?php echo htmlspecialchars($active_subscription['plan_name']); ?></strong> which is set to renew on <strong><?php echo htmlspecialchars(date('F j, Y - g:i A', strtotime($active_subscription['next_payment_date']))); ?></strong>.</p>
                      <p>If you wish to change your subscription plan, please cancel your current plan before selecting a new one.</p>
                      <p><a href="subscription?action=cancel" class="btn btn-outline-danger">Cancel Current Plan</a></p>
                    </div>
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
</body>

</html>
