<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
  echo "<script>window.location.assign('set-profile')</script>"; 
  exit;
    // header('location: set-profile');
};?>
<!-- Plugin css for this page -->
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
                  <h3 class="font-weight-bold">Watchlist Players</h3>
                  <h6 class="font-weight-normal mb-0">Your Watchlisted Players on <?php echo $settings['site_name']; ?>.</h6>
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

          <?php
          $conn = $pdo->open();

          // Suppose the logged-in user ID is 1 (Alexander for now)
          $logged_in_user_id = $user['id'];
          $stmt = $conn->prepare("SELECT * FROM watchlist WHERE agent_id = ? ORDER BY created_at DESC");
          $stmt->execute([$logged_in_user_id]);
          $watchlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

          ?>

          <div class="row">
            <div class="col-md-12 grid-margin grid-margin-md-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">My Watchlist Players</h4>
                  <div class="table-responsive">
                    <?php if (count($watchlists) > 0): ?>
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="pt-1 ps-0">
                              Player
                            </th>
                            <th class="pt-1">
                              Overall Score
                            </th>
                            <th class="pt-1">
                              Category
                            </th>
                            <th class="pt-1">
                              Consistency
                            </th>
                            <th class="pt-1">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($watchlists as $watchlist): 
                          
                          $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                          $stmt->execute([$watchlist['player_id']]);
                          $user_player = $stmt->fetch(PDO::FETCH_ASSOC);
                          
                          $stmt = $conn->prepare("SELECT * FROM players WHERE user_id = ?");
                          $stmt->execute([$user_player['id']]);
                          $player = $stmt->fetch(PDO::FETCH_ASSOC);

                          $stmt = $conn->prepare("SELECT * FROM playerstats WHERE player_id = ?");
                          $stmt->execute([$player['id']]);
                          $playerstats = $stmt->fetch(PDO::FETCH_ASSOC);


                          ?>
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <img src="<?php echo (!empty($user['photo'])) ? 'images/'.$user['photo'] : 'images/profile.jpg'; ?>" alt="profile" class="mr-3">
                                <div class="ms-3">
                                  <p class="mb-0"><a href="<?php echo $settings['site_url']; ?>user/player/<?php echo $player['uuid']; ?>"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></p>
                                  <p class="mb-0 text-muted text-small">
                                    <?= htmlspecialchars($player['country'] . ' | ' . (new DateTime())->diff(new DateTime(($player['dob'])))->y); ?> years old
                                  </p>
                                  <small class="text-muted">Added to watchlist on <?= date('M j, Y', strtotime($watchlist['created_at'])) ?></small>
                                </div>
                              </div>
                            </td>
                            <td>
                              <?= $playerstats['average_score'] ?: 'N/A' ?>
                            </td>
                            <td>
                              <?= ucfirst($player['game_type'] ?? 'N/A') ?>
                            </td>
                            <td>
                              <?= $playerstats['average_consistency'] ? $playerstats['average_consistency'].'%' : 'N/A' ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="<?php echo $settings['site_url']; ?>user/player/<?php echo $player['uuid']; ?>"><i class="mdi mdi-eye"></i> View</a>
                              <a class="btn btn-sm btn-outline-danger" href="watchlist_delete_action?player_id=<?= $watchlist['player_id'] ?>"><i class="mdi mdi-delete"></i> Remove</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <li class="list-group-item text-muted text-center">No players added to your watchlist yet.</li>
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
