<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
  echo "<script>window.location.assign('set-profile')</script>"; 
  exit;
  // header('location: set-profile');
};
if($user['role'] != 'user'){
  echo "<script>window.location.assign('scout-verification')</script>";
  exit;
  // header('location: index');
};
$stmt = $conn->prepare("SELECT * FROM player_verifications WHERE player_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user['id']]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM players WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user['id']]);
$player = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$player) {
    echo "<script>window.location.assign('set-profile')</script>";
    exit;
}
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
                  <h3 class="font-weight-bold">Player Verification</h3>
                  <h6 class="font-weight-normal mb-0">Verification for all players.</h6>
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
                  <h4 class="card-title">Player Verification</h4>
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
                      <form method="post" action="player-verification-submit.php" enctype="multipart/form-data" class="card p-4">
                        
                        <div class="mb-3">
                          <label class="form-label">Full Name <small class="text-muted">(Surname first)</small></label>
                          <input type="text" name="full_name" class="form-control" value="<?php echo $user['firstname'].' '.$user['lastname']; ?>" placeholder="Enter your full name as appear on your Official ID" required>
                          <small class="form-text text-muted">As appreared on Official ID.</small>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Date of Birth <small class="text-muted">(Enter your real age)</small></label>
                          <input type="date" name="dob" class="form-control" value="<?php echo $player['dob']; ?>" required>
                          <small class="form-text text-muted">As appreared on Official ID.</small>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Gender</label>
                          <select name="gender" class="form-control" >
                            <option value="male" <?php if($player['gender'] == 'male'){ echo 'selected'; }; ?>>Male</option>
                            <option value="female" <?php if($player['gender'] == 'female'){ echo 'selected'; }; ?>>Female</option>
                          </select>
                          <small class="form-text text-muted">As appreared on Official ID.</small>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Official ID (NIN, Passport, Driver's license) <small class="text-muted">(Image or PDF)</small></label>
                          <input type="file" name="official_id" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                          <small class="form-text text-muted">Max 10MB.</small>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                          <div class="form-check">
                            <label class="form-check-label text-muted" for="team_affiliated">
                            <input type="checkbox" class="form-check-input" id="team_affiliated" name="team_affiliated" value="1">
                            I am affiliated with a team
                            <i class="input-helper"></i></label>
                          </div>
                        </div>
                        
                        <div class="mb-3" id="teamProofGroup" style="display:none;">
                          <label class="form-label">Team Affiliation Proof (Letter, Certificate)</label>
                          <input type="file" name="team_proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control">
                          <small class="form-text text-muted">Optional if not affiliated. Max 10MB.</small>
                        </div>

                        <?php
                        
                        // if DOB is less than 18 years, show guardian info
                        $dob = new DateTime($player['dob']);
                        $now = new DateTime();
                        $ageInterval = $now->diff($dob);
                        if ($ageInterval->y < 18):
                        ?>
                        
                        <div class="mb-3">
                          <label class="form-label">Parent/Guardian Consent <small class="text-muted">(if under 18)</small></label>
                          <input type="file" name="parent_consent" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                          <small class="form-text text-muted">Optional if not affiliated. Max 10MB.</small>
                        </div>

                        <?php endif; ?>
                        
                        <div class="mb-3">
                          <label class="form-label">Social Media Handles (optional)</label>
                          <div class="row g-2">
                            <div class="col-md-6 mt-2"><input name="instagram" placeholder="Instagram" class="form-control"></div>
                            <div class="col-md-6 mt-2"><input name="twitter" placeholder="Twitter/X" class="form-control"></div>
                            <div class="col-md-6 mt-2"><input name="facebook" placeholder="Facebook" class="form-control"></div>
                            <div class="col-md-6 mt-2"><input name="linkedin" placeholder="LinkedIn" class="form-control"></div>
                          </div>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label">Anything to tell the admin (optional)</label>
                          <textarea name="notes" class="form-control" rows="3"></textarea>
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
