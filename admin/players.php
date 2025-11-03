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
                  <h3 class="font-weight-bold">Players</h3>
                  <h6 class="font-weight-normal mb-0">Check out all players on <?php echo $settings['site_name']; ?>.</h6>
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
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Players</h4>
                  <p class="card-description">
                    All Players on <?php echo $settings['site_name']; ?>
                  </p>
                  <div class="table-responsive">
                    <table class="table" id="example2">
                      <thead>
                        <tr>
                          <th class="pt-1 ps-0">
                            Player
                          </th>
                          <th class="pt-1">
                            Sport
                          </th>
                          <th class="pt-1">
                            Status
                          </th>
                          <th class="pt-1">
                            Date Registered
                          </th>
                          <th class="pt-1">
                            Action
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php 
                          
                          $sql = "
                          SELECT u.*, p.*
                          FROM players p
                          JOIN users u ON u.id = p.user_id ORDER BY p.id DESC
                          ";

                          $stmt = $conn->prepare($sql);
                          $stmt->execute();
                          $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                          foreach ($players as $player): 
                            $player_age = (new DateTime())->diff(new DateTime(($player['dob'])))->y;
                            if ($player['status'] == 0) {
                              $status = '<div class="badge badge-warning">Inactive</div>';
                              // echo '<div class="badge badge-warning">Pending</div>';
                            }
                            if ($player['status'] == 1) {
                              $status = '<div class="badge badge-success">Active</div>';
                              // echo '<div class="badge badge-success">Successfull</div>';
                            }
                            if ($player['status'] == 2) {
                              $status = '<div class="badge badge-danger">Blocked</div>';
                              // echo '<div class="badge badge-danger">Rejected</div>';
                            }
                          ?>
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <img src="<?php echo (!empty($player['photo'])) ? '../user/images/'.$player['photo'] : '../user/images/profile.jpg'; ?>" alt="profile" class="mr-3">
                                <div class="ms-3">
                                  <p class="mb-0"><a href="player/<?php echo $player['uuid']; ?>"><?php echo $player['firstname'] . ' ' . $player['lastname']; ?></p>
                                  <p class="mb-0 text-muted text-small">
                                    <?= htmlspecialchars($player['country'] . ' | ' . (new DateTime())->diff(new DateTime(($player['dob'])))->y); ?> years old
                                  </p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <?php echo htmlspecialchars(ucfirst($player['game_type'])); ?>
                            </td>
                            <td>
                              <?php echo $status; ?>
                            </td>
                            <td>
                              <?php if (!empty($player['created_at'])): ?>
                                <?= date("M j, g:i a", strtotime($player['created_at'])) ?>
                              <?php else: ?>
                                Not Found
                              <?php endif; ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="player/<?= $player['uuid'] ?>"><i class="mdi mdi-eye"></i> View</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                    </table>
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
    $(function () {
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
  </script>
</body>

</html>
