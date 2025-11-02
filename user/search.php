<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
    echo "<script>window.location.assign('set-profile')</script>"; 
    exit;
    // header('location: set-profile');
}
$search_parameter = $_POST['search'] ?? '';
?>
<!-- Plugin css for this page -->
 <style>
  .card {
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .player-card:hover {
      transform: translateY(-3px);
      transition: 0.3s ease;
    }
 </style>
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
                  <h3 class="font-weight-bold">Search</h3>
                  <h6 class="font-weight-normal mb-0">Results relating to <?php echo $search_parameter
                  ; ?>.</h6>
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
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Search Results</h4>
                  <!-- <form method="GET" id="sortForm" class="mb-3">
                    <label for="sort" class="form-label fw-bold">Sort by:</label>
                    <select name="sort" id="sort" class="form-select" onchange="document.getElementById('sortForm').submit();">
                      <option value="">Relevance</option>
                      <option value="age_asc" <?= ($_GET['sort'] ?? '') === 'age_asc' ? 'selected' : '' ?>>Age (youngest first)</option>
                      <option value="age_desc" <?= ($_GET['sort'] ?? '') === 'age_desc' ? 'selected' : '' ?>>Age (oldest first)</option>
                      <option value="region" <?= ($_GET['sort'] ?? '') === 'region' ? 'selected' : '' ?>>Region</option>
                      <option value="position" <?= ($_GET['sort'] ?? '') === 'position' ? 'selected' : '' ?>>Position</option>
                      <option value="rating_desc" <?= ($_GET['sort'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>Rating (highest first)</option>
                      <option value="rating_asc" <?= ($_GET['sort'] ?? '') === 'rating_asc' ? 'selected' : '' ?>>Rating (lowest first)</option>
                    </select>
                  </form> -->
                  <!-- Filter Button -->
                  <button type="button" class="btn btn-outline-primary mb-3" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="bi bi-funnel"></i> Filter
                  </button>
                  <?php foreach ($_GET as $key => $val): ?>
                    <?php if (!empty($val)): ?>
                      <span class="badge bg-info text-dark"><?= ucfirst($key) ?>: <?= htmlspecialchars($val) ?></span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                  <div class="row">
                    <?php
                    
                    $where = [];
                    $params = [];

                    if (!empty($_POST['search'])) {
                        $search = "%{$_POST['search']}%";
                        $where[] = "(u.firstname LIKE ? OR u.lastname LIKE ? OR p.country LIKE ? OR p.positions LIKE ? OR p.club LIKE ?)";
                        array_push($params, $search, $search, $search, $search, $search);
                    }

                    // Build WHERE conditions dynamically
                    if (!empty($_GET['country'])) {
                        $where[] = "p.country = ?";
                        $params[] = $_GET['country'];
                    }
                    if (!empty($_GET['game_type'])) {
                        $where[] = "p.game_type = ?";
                        $params[] = $_GET['game_type'];
                    }
                    if (!empty($_GET['position'])) {
                        $where[] = "p.positions LIKE ?";
                        $params[] = "%" . $_GET['position'] . "%";
                    }
                    if (!empty($_GET['footedness'])) {
                        $where[] = "p.footedness = ?";
                        $params[] = $_GET['footedness'];
                    }
                    if (!empty($_GET['gender'])) {
                        $where[] = "p.gender = ?";
                        $params[] = $_GET['gender'];
                    }
                    
                    // 🔹 AGE FILTER
                    if (!empty($_GET['age'])) {
                        $age = $_GET['age'];
                        if ($age === 'Below 18') {
                            $where[] = "TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) < 18";
                        } elseif ($age === '18 - 26') {
                            $where[] = "TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 26";
                        } elseif ($age === '26 - 45') {
                            $where[] = "TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 26 AND 45";
                        } elseif ($age === 'Above 45') {
                            $where[] = "TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) > 45";
                        }
                    }

                    // 🔹 SCORE FILTER
                    if (!empty($_GET['score'])) {
                        if ($_GET['score'] === 'Above 50') {
                            $where[] = "plstats.average_score > 50";
                        } elseif ($_GET['score'] === 'Below 50') {
                            $where[] = "plstats.average_score <= 50";
                        }
                    }

                    // 🔹 CONSISTENCY FILTER
                    if (!empty($_GET['consistency'])) {
                        if ($_GET['consistency'] === 'Above 50') {
                            $where[] = "plstats.average_consistency > 50";
                        } elseif ($_GET['consistency'] === 'Below 50') {
                            $where[] = "plstats.average_consistency <= 50";
                        }
                    }

                    $sql = "
                      SELECT u.firstname, u.lastname, p.country, p.positions, p.dob, p.user_id, p.id, p.game_type, p.footedness, p.gender, plstats.average_score AS average_score, plstats.average_consistency AS average_consistency,
                      plstats.pod_score AS pod_score, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age
                      FROM players p
                      JOIN users u ON u.id = p.user_id
                      LEFT JOIN playerstats plstats ON plstats.player_id = p.id
                    ";

                    if ($where) {
                        $sql .= " WHERE " . implode(" AND ", $where);
                    }

                    $sql .= " ORDER BY p.featured DESC";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if(count($players) > 0):
                    foreach ($players as $player): 

                    // FETCH Player
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? ORDER BY id DESC");
                    $stmt->execute([$player['user_id']]);
                    $player_user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $roles = explode(',', $player['positions']);
                    ?>
                    <div class="col-md-4 mb-3">
                      <div class="card mt-3 p-3 player-card">
                        <img src="<?php echo (!empty($player['profile_image'])) ? 'images/'.$player['profile_image'] : 'images/profile.jpg'; ?>" style="width: 100px;" class="img-fluid rounded mb-2" alt="">
                        <h6 class="fw-bold mb-0"><?php echo $player_user['firstname']; ?> <?php echo $player_user['lastname']; ?></h6>
                        <small class="text-muted"><?php echo trim($roles[0]); ?> • <?php echo $player['country']; ?> • Age <?php echo (new DateTime())->diff(new DateTime(($player['dob'])))->y; ?></small>
                        <hr>
                        <p class="mb-1"><b>POD Score:</b> <?= $player['pod_score'] ?: 'N/A' ?></p>
                        <p class="mb-1"><b>Overall Score:</b> <?= $player['average_score'] ?: 'N/A' ?></p>
                        <p class="mb-1"><b>Consistency:</b> <?= !empty($player['average_consistency']) ? $player['average_consistency'] . '%' : 'N/A' ?></p>
                        <a class="btn btn-sm btn-outline-primary mt-2 w-100" href="player?id=<?php echo $player['id']; ?>"><i class="bi bi-eye me-1"></i>View Profile</a>
                      </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                      <li class="list-group-item text-muted w-100 p-3 m-3 text-center">No players found.</li>
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
<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="GET" action="search">
        <div class="modal-header">
          <h5 class="modal-title" id="filterModalLabel">Filter Players</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <!-- Region -->
            <div class="col-md-6">
              <label for="region" class="form-label">Region (Country)</label>
              <select name="country" id="region" class="form-select">
                <option value="">All</option>
                <option value="Nigeria">Nigeria</option>
                <option value="Ghana">Ghana</option>
                <option value="England">England</option>
                <option value="Spain">Spain</option>
                <!-- You can dynamically load from DB -->
              </select>
            </div>

            <!-- Game Type -->
            <div class="col-md-6">
              <label for="game_type" class="form-label">Game Type</label>
              <select name="game_type" id="game_type" class="form-select">
                <option value="">All</option>
                <option value="Football">Football</option>
                <option value="Futsal">Futsal</option>
                <option value="Beach Soccer">Beach Soccer</option>
              </select>
            </div>

            <!-- Position -->
            <div class="col-md-6">
              <label for="position" class="form-label">Position</label>
              <select name="position" id="position" class="form-select">
                <option value="">All</option>
                <option value="Striker">Striker</option>
                <option value="Midfielder">Midfielder</option>
                <option value="Defender">Defender</option>
                <option value="Goalkeeper">Goalkeeper</option>
              </select>
            </div>

            <!-- Footedness -->
            <div class="col-md-6">
              <label for="footedness" class="form-label">Footedness</label>
              <select name="footedness" id="footedness" class="form-select">
                <option value="">All</option>
                <option value="Right">Right</option>
                <option value="Left">Left</option>
                <option value="Both">Both</option>
              </select>
            </div>

            <!-- Gender (optional) -->
            <div class="col-md-6">
              <label for="gender" class="form-label">Gender</label>
              <select name="gender" id="gender" class="form-select">
                <option value="">All</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>


</html>
