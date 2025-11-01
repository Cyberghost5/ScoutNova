<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
    echo "<script>window.location.assign('set-profile')</script>"; 
    // header('location: set-profile');
};
$stmt = $conn->prepare("SELECT * FROM scout_verifications WHERE scout_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user['id']]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);
?> 
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
                  <h3 class="font-weight-bold">Scout/Agent Verification</h3>
                  <h6 class="font-weight-normal mb-0">Verification for all scouts/agents.</h6>
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
            <div class="col-lg-12 grid-margin strech-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Scout/Agent Verification</h4>
                  <p class="card-description">
                  Let's began your verification process.
                  </p>

                  <?php if ($existing): ?>
                    <div class="alert alert-info">
                    Your last submission: <strong><?= htmlspecialchars($existing['status']) ?></strong>
                    <?php if ($existing['status'] === 'rejected' && $existing['review_notes']): ?>
                      <div class="mt-2"><strong>Review notes:</strong> <?= htmlspecialchars($existing['review_notes']) ?></div>
                    <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  
                    <?php 
                      // 0 is not started verify, 1 is verified, 2 is rejected, 3 is pending approval
                      if($user['verified'] === 2 || $user['verified'] === 0): ?>  
                      <form method="post" action="scout-verification-submit.php" enctype="multipart/form-data" class="card p-4 mt-4">

                        <!-- Professional Certification -->
                        <div class="mb-3">
                          <label class="form-label">Professional Certification (CSA, PSC, etc.)</label>
                          <input type="file" name="certification" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                          <small class="text-muted">Upload a clear image or PDF of your certification. Max 10MB.</small>
                        </div>

                        <!-- Experience Proof -->
                        <div class="mb-3">
                          <label class="form-label">Proof of Experience (contract, testimonial, reference letter)</label>
                          <input type="file" name="experience" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                          <small class="text-muted">Max 10MB file.</small>
                        </div>

                        <!-- Business Registration -->
                        <div class="mb-3">
                          <label class="form-label">Business Registration Document (CAC or equivalent)</label>
                          <input type="file" name="business_doc" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                          <small class="text-muted">Max 10MB file.</small>
                        </div>

                        <!-- Social Media -->
                        <div class="mb-3">
                          <label class="form-label">Social Media Handles (for online reputation check)</label>
                          <div class="row g-2">
                            <div class="col-md-6 mt-2"><input name="instagram" placeholder="Instagram" class="form-control"></div>
                            <div class="col-md-6 mt-2"><input name="twitter" placeholder="Twitter/X" class="form-control"></div>
                            <div class="col-md-6 mt-2"><input name="facebook" placeholder="Facebook" class="form-control"></div>
                            <div class="col-md-6 mt-2"><input name="linkedin" placeholder="LinkedIn" class="form-control"></div>
                          </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                          <label class="form-label">Extra Notes (optional)</label>
                          <textarea name="notes" rows="3" class="form-control"></textarea>
                        </div>

                        <button class="btn btn-primary" type="submit">Submit Verification</button>
                      </form>
                    <?php endif; ?>
                      
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
    document.getElementById('team_affiliated').addEventListener('change', function() {
        document.getElementById('teamProofGroup').style.display = this.checked ? 'block' : 'none';
    });
  </script>
</body>

</html>
