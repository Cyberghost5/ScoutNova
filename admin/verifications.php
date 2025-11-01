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
                  <h3 class="font-weight-bold">Pending Verifications</h3>
                  <h6 class="font-weight-normal mb-0">Approve or reject verification submissions from players and scouts.</h6>
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

                // Fetch pending verifications for both players and scouts
                $playerStmt = $conn->query("
                  SELECT pv.*, u.firstname, u.lastname, 'player' AS type
                  FROM player_verifications pv
                  JOIN users u ON u.id = pv.player_id
                  WHERE pv.status = 'pending'
                  ORDER BY pv.created_at DESC
                ");

                $scoutStmt = $conn->query("
                  SELECT sv.*, u.firstname, u.lastname, 'scout' AS type
                  FROM scout_verifications sv
                  JOIN users u ON u.id = sv.scout_id
                  WHERE sv.status = 'pending'
                  ORDER BY sv.created_at DESC
                ");

                $verifications = array_merge($playerStmt->fetchAll(PDO::FETCH_ASSOC), $scoutStmt->fetchAll(PDO::FETCH_ASSOC));

                // var_dump($verifications);

              ?>
            </div>
          </div>

          <?php if (empty($verifications)): ?>
            <div class="alert alert-info">No pending verifications found.</div>
          <?php else: ?>
            <div class="row">
              <?php foreach ($verifications as $v): ?>
                <div class="col-md-6 mb-4">
                  <div class="card shadow-sm border-0">
                    <div class="card-body">
                      <h5><?= htmlspecialchars($v['firstname'].' '.$v['lastname']) ?> <span class="badge bg-secondary"><?= ucfirst($v['type']) ?></span></h5>
                      <p class="mb-2"><b>Submitted:</b> <?= date('M d, Y', strtotime($v['created_at'])) ?></p>

                      <?php if ($v['type'] == 'player'): ?>
                        <ul class="list-unstyled">
                          <li><b>ID Document:</b> <a href="<?= $v['official_id_url'] ?>" target="_blank">View</a></li>
                          <li><b>Intro Video:</b> <a href="<?= $v['intro_video_url'] ?>" target="_blank">Watch</a></li>
                          <?php if ($v['team_affiliated']): ?>
                            <li><b>Team Proof:</b> <a href="<?= $v['team_proof_url'] ?>" target="_blank">View</a></li>
                          <?php endif; ?>
                        </ul>
                      <?php else: ?>
                        <ul class="list-unstyled">
                          <li><b>Certification:</b> <a href="<?= $v['certification_url'] ?>" target="_blank">View</a></li>
                          <li><b>Experience Proof:</b> <a href="<?= $v['experience_proof_url'] ?>" target="_blank">View</a></li>
                          <li><b>Business Registration:</b> <a href="<?= $v['business_registration_url'] ?>" target="_blank">View</a></li>
                        </ul>
                      <?php endif; ?>

                      <p class="mb-1"><b>Socials:</b> 
                        <?php
                          $socials = json_decode($v['social_handles'], true);
                          if ($socials) {
                            foreach ($socials as $key => $link) {
                              if ($link) echo "<a href='".htmlspecialchars($link)."' target='_blank' class='me-2 text-decoration-none text-primary'>".ucfirst($key)."</a> ";
                            }
                          }
                        ?>
                      </p>

                      <form action="verification-action" method="POST" class="mt-3">
                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                        <input type="hidden" name="type" value="<?= $v['type'] ?>">
                        <textarea name="notes" rows="2" class="form-control mb-2" placeholder="Optional review notes..."></textarea>
                        <div class="d-flex p-2">
                          <button name="action" value="approve" class="btn btn-success btn-sm flex-fill mr-2">Approve</button>
                          <button name="action" value="reject" class="btn btn-danger btn-sm flex-fill mr-2">Reject</button>
                        </div>
                      </form>

                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

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
