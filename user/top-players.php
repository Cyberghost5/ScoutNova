<?php include 'include/session.php';?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
    echo "<script>window.location.assign('set-profile')</script>"; 
    exit;
}?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
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
                  <h3 class="font-weight-bold">Top Players</h3>
                  <h6 class="font-weight-normal mb-0">Highest rated players on <?php echo $settings['site_name']; ?> based on POD scores.</h6>
                </div>
              </div>
            </div>
          </div>

          <?php
          // Fetch top players with highest POD scores
          $stmt = $conn->prepare("
              SELECT u.firstname, u.lastname, u.photo, 
                     p.id, p.uuid, p.country, p.positions, p.dob, p.game_type, p.profile_image, p.club,
                     ps.average_score, ps.pod_score, ps.average_consistency
              FROM users u 
              JOIN players p ON u.id = p.user_id 
              LEFT JOIN playerstats ps ON ps.player_id = p.id
              WHERE ps.pod_score IS NOT NULL
              ORDER BY ps.pod_score DESC
              LIMIT 20
          ");
          $stmt->execute();
          $top_players = $stmt->fetchAll(PDO::FETCH_ASSOC);
          ?>

          <!-- Top 3 Featured Cards -->
          <div class="row mb-4">
          <?php 
          $podium = ['🥇 Champion', '🥈 Runner-up', '🥉 Third Place'];
          $colors = ['success', 'warning', 'info'];
          for($i = 0; $i < min(3, count($top_players)); $i++): 
              $player = $top_players[$i];
              $full_name = trim($player['firstname'] . ' ' . $player['lastname']);
              $position = explode(',', $player['positions'])[0] ?? 'Player';
              $age = (new DateTime())->diff(new DateTime($player['dob']))->y;                $player_image = '';
                if (!empty($player['profile_image'])) {
                    $player_image = 'images/' . $player['profile_image'];
                } elseif (!empty($player['photo'])) {
                    $player_image = 'images/' . $player['photo'];
                } else {
                    $player_image = 'images/profile.jpg';
                }
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card h-100 border-<?php echo $colors[$i]; ?> shadow-lg">
                <div class="card-body text-center">
                  <div class="position-relative d-inline-block mb-3">
                    <img src="<?php echo $player_image; ?>" 
                         alt="<?php echo $full_name; ?>" 
                         class="rounded-circle"
                         style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--bs-<?php echo $colors[$i]; ?>);">
                    <div class="badge bg-<?php echo $colors[$i]; ?> position-absolute" style="top: -10px; right: -10px; font-size: 24px;">
                      <?php echo $i + 1; ?>
                    </div>
                  </div>
                  
                  <h4 class="card-title font-weight-bold text-<?php echo $colors[$i]; ?>"><?php echo $podium[$i]; ?></h4>
                  <h5 class="font-weight-bold"><?php echo $full_name; ?></h5>
                  <p class="text-muted mb-2">
                    <?php echo trim($position); ?> • <?php echo $player['country']; ?> • <?php echo $age; ?> years
                  </p>
                  
                  <div class="row text-center mt-3">
                    <div class="col-4">
                      <div class="border-end">
                        <h3 class="font-weight-bold text-<?php echo $colors[$i]; ?>"><?php echo $player['pod_score']; ?></h3>
                        <small class="text-muted">POD Score</small>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="border-end">
                        <h5 class="font-weight-bold"><?php echo $player['average_score'] ?: 'N/A'; ?></h5>
                        <small class="text-muted">Avg Score</small>
                      </div>
                    </div>
                    <div class="col-4">
                      <h5 class="font-weight-bold"><?php echo $player['average_consistency'] ?: 'N/A'; ?>%</h5>
                      <small class="text-muted">Consistency</small>
                    </div>
                  </div>
                  
                  <div class="mt-3">
                    <a href="player-card/<?php echo $player['uuid']; ?>" 
                       class="btn btn-<?php echo $colors[$i]; ?> btn-sm me-2">
                      View Card
                    </a>
                    <a href="player/<?php echo $player['uuid']; ?>" 
                       class="btn btn-outline-<?php echo $colors[$i]; ?> btn-sm">
                      Full Profile
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <?php endfor; ?>
          </div>

          <!-- All Players Table -->
          <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Complete Rankings</h4>
                  </div>
                  
                  <!-- Table View -->
                  <div id="tableView" class="table-responsive">
                    <?php if (count($top_players) > 0): ?>
                      <table class="table table-hover">
                        <thead class="table-light">
                          <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Position</th>
                            <th>Country</th>
                            <th>Age</th>
                            <th>POD Score</th>
                            <th>Avg Score</th>
                            <th>Consistency</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                          $rank = 1;
                          foreach ($top_players as $player): 
                            $full_name = trim($player['firstname'] . ' ' . $player['lastname']);
                            $position = explode(',', $player['positions'])[0] ?? 'Player';
                            $age = (new DateTime())->diff(new DateTime($player['dob']))->y;
                            
                            $player_image = '';
                            if (!empty($player['profile_image'])) {
                                $player_image = 'images/' . $player['profile_image'];
                            } elseif (!empty($player['photo'])) {
                                $player_image = 'images/' . $player['photo'];
                            } else {
                                $player_image = 'images/profile.jpg';
                            }
                            
                            $rank_badge = '';
                            if ($rank <= 3) {
                                $badges = ['🥇', '🥈', '🥉'];
                                $colors = ['warning', 'secondary', 'info'];
                                $rank_badge = '<span class="badge bg-' . $colors[$rank-1] . '">' . $badges[$rank-1] . ' ' . $rank . '</span>';
                            } else {
                                $rank_badge = '<span class="badge bg-light text-dark">' . $rank . '</span>';
                            }
                          ?>
                          <tr>
                            <td><?php echo $rank_badge; ?></td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="<?php echo $player_image; ?>" 
                                     alt="<?php echo $full_name; ?>" 
                                     class="rounded-circle me-3"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                  <h6 class="mb-0 font-weight-bold"><?php echo $full_name; ?></h6>
                                  <small class="text-muted"><?php echo $player['club'] ?: 'Free Agent'; ?></small>
                                </div>
                              </div>
                            </td>
                            <td><span class="badge badge-primary"><?php echo trim($position); ?></span></td>
                            <td><?php echo $player['country']; ?></td>
                            <td><?php echo $age; ?></td>
                            <td>
                              <span class="font-weight-bold text-success" style="font-size: 1.1em;">
                                <?php echo $player['pod_score']; ?>
                              </span>
                            </td>
                            <td><?php echo $player['average_score'] ?: 'N/A'; ?></td>
                            <td>
                              <?php if ($player['average_consistency']): ?>
                                <div class="progress" style="height: 8px;">
                                  <div class="progress-bar bg-success" style="width: <?php echo $player['average_consistency']; ?>%"></div>
                                </div>
                                <small><?php echo $player['average_consistency']; ?>%</small>
                              <?php else: ?>
                                <small class="text-muted">N/A</small>
                              <?php endif; ?>
                            </td>
                            <td>
                              <div class="btn-group btn-group-sm">
                                <a href="player-card/<?php echo $player['uuid']; ?>" 
                                   class="btn btn-outline-success">
                                  Card
                                </a>
                                <a href="player/<?php echo $player['uuid']; ?>" 
                                   class="btn btn-outline-primary">
                                  Profile
                                </a>
                              </div>
                            </td>
                          </tr>
                          <?php 
                          $rank++;
                          endforeach; 
                          ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <div class="alert alert-info text-center">
                        <h5>📊 No Rated Players Yet</h5>
                        <p>Players need to have POD scores to appear in the rankings.</p>
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
    // Auto-refresh every 30 seconds to show updated rankings
    setInterval(() => {
      // Uncomment to enable auto-refresh
      // location.reload();
    }, 30000);
  </script>

</body>
</html>