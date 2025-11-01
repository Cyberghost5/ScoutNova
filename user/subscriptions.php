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
                  <h6 class="font-weight-normal mb-0">Pay for subscription here.</h6>
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
                    <p class="w-75 mx-auto mb-3">Choose a plan that suits you the best. If you are not fully satisfied,
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

                    <div class="d-flex justify-content-center align-items-center mb-4">
                      <label class="mr-2 font-weight-bold">Monthly</label>
                      <label class="switch mb-0">
                        <input type="checkbox" id="intervalToggle">
                        <span class="slider round"></span>
                      </label>
                      <label class="ml-2 font-weight-bold">Yearly</label>
                    </div>

                    <style>
                    /* Toggle switch styling */
                    .switch {
                      position: relative;
                      display: inline-block;
                      width: 60px;
                      height: 30px;
                    }
                    .switch input {display:none;}
                    .slider {
                      position: absolute;
                      cursor: pointer;
                      top: 0; left: 0; right: 0; bottom: 0;
                      background-color: #ccc;
                      transition: .4s;
                      border-radius: 30px;
                    }
                    .slider:before {
                      position: absolute;
                      content: "";
                      height: 22px; width: 22px;
                      left: 4px; bottom: 4px;
                      background-color: white;
                      transition: .4s;
                      border-radius: 50%;
                    }
                    input:checked + .slider {
                      background-color: #9f04c8;
                    }
                    input:checked + .slider:before {
                      transform: translateX(30px);
                    }
                    </style>
                    
                    <div class="row pricing-table">
                      <?php
                      $conn = $pdo->open();
                      
                      $stmt = $conn->prepare("SELECT * FROM plans WHERE (role=:role OR role='all') AND status=1 ORDER BY id ASC");
                      $stmt->execute(['role' => $user['role']]);
                      $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      $i = 0;
                      ?>
                      <?php foreach($plans as $plan): $i++; ?>
                        <div class="col-md-4 col-xl-4 grid-margin stretch-card pricing-card plan-card"
                            data-interval="<?php echo strtolower($plan['intervals']); ?>">
                          <div class="card border-primary border pricing-card-body">
                            <div class="text-center pricing-card-head">
                              <h3><?php echo $plan['name']; ?></h3>
                              <p><?php echo ucfirst($plan['intervals']); ?></p>
                              <h2 class="font-weight-normal mb-4">
                                <?php echo $plan['currency_short']; ?><?php echo number_format($plan['amount'], 2); ?>
                              </h2>
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

                        <!-- Modal -->
                        <div class="modal fade" id="methodModal<?php echo $i; ?>" tabindex="-1" aria-labelledby="methodModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header bg-light">
                                <h5 class="modal-title" id="methodModalLabel"><?php echo $plan['name']; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              </div>
                              <div class="modal-body text-center">
                                <p>You are about to upgrade your subscription to the <strong><?php echo $plan['name']; ?></strong> plan and you will be charged <strong><?php echo $plan['currency_short']; ?><?php echo number_format($plan['amount'], 2); ?></strong> on a <strong><?php echo $plan['intervals']; ?></strong> basis.</p>
                                <p>By upgrading, you agree to the <?php echo $settings['site_name']; ?> Terms of Service and Privacy Policy.</p>
                                <div class="d-flex justify-content-center gap-3 mt-4">
                                  <form action="subscription" method="POST">
                                    <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                    <button type="submit" class="btn btn-outline-success mr-3">Agree</button>
                                  </form>
                                  <button data-dismiss="modal" class="btn btn-outline-danger mr-3">Cancel</button>
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
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.getElementById('intervalToggle');
    const plans = document.querySelectorAll('.plan-card');

    // Default to Monthly
    plans.forEach(p => p.style.display = p.dataset.interval === 'monthly' ? 'block' : 'none');

    toggle.addEventListener('change', function() {
      const showInterval = toggle.checked ? 'yearly' : 'monthly';
      plans.forEach(plan => {
        plan.style.display = plan.dataset.interval === showInterval ? 'block' : 'none';
      });
    });
  });
  </script>
</body>

</html>
