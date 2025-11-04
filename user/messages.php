<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
    echo "<script>window.location.assign('set-profile')</script>"; 
    exit;
    // header('location: set-profile');
}?> 
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
                  <h3 class="font-weight-bold">Messages</h3>
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
              ?>
            </div>
          </div>

          <?php
          $conn = $pdo->open();

          // Suppose the logged-in user ID is 1 (Alexander for now)
          $logged_in_user_id = $user['id'];

          // Get all chats the user is part of
         $sql = "
        SELECT 
          c.id AS chat_id, c.uuid AS chat_uuid,
          IF(c.user1_id = ?, u2.firstname, u1.firstname) AS firstname,
          IF(c.user1_id = ?, u2.lastname, u1.lastname) AS lastname,
          IF(c.user1_id = ?, u2.type, u1.type) AS usertype2,
          IF(c.user1_id = ?, u2.photo, u1.photo) AS photo,
          IF(c.user1_id = ?, p2.id, p1.id) AS userid2,
          m.message,
          m.timestamp
        FROM chats c
        JOIN users u1 ON c.user1_id = u1.id
        JOIN users u2 ON c.user2_id = u2.id
        LEFT JOIN players p1 ON u1.id = p1.user_id
        LEFT JOIN players p2 ON u2.id = p2.user_id
        JOIN (
          SELECT chat_id, message, timestamp
          FROM messages
          WHERE id IN (
            SELECT MAX(id) FROM messages GROUP BY chat_id
          )
        ) AS m ON m.chat_id = c.id
        WHERE (c.user1_id = ? OR c.user2_id = ?)
        ORDER BY m.timestamp DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id, $logged_in_user_id]);
        $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ?>

          <div class="row">
            <div class="col-md-12 grid-margin grid-margin-md-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Chats</h4>
                  <div class="table-responsive">
                    <?php if (count($chats) > 0): ?>
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="pt-1 ps-0">
                              Chat
                            </th>
                            <th class="pt-1">
                              Last Message
                            </th>
                            <th class="pt-1">
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($chats as $chat): 
                            if($chat['usertype2'] == '1'){
                              $profile_avatar = (!empty($chat['photo'])) ? '../admin/images/'.$chat['photo'] : '../admin/images/profile.jpg';
                            }else{
                              $profile_avatar = (!empty($chat['photo'])) ? 'images/'.$chat['photo'] : 'images/profile.jpg';
                            }  
                          ?>
                          <tr>
                            <td class="py-1 ps-0">
                              <div class="d-flex align-items-center">
                                <img src="<?php echo $profile_avatar; ?>" alt="profile" class="mr-3">
                                <div class="ms-3">
                                  <p class="mb-0"><?php echo $chat['firstname'] . ' ' . $chat['lastname']; ?></p>
                                  <p class="mb-0 text-muted text-small">
                                    <?php if (!empty($chat['timestamp'])): ?>
                                      <?= date("M j, g:i a", strtotime($chat['timestamp'])) ?>
                                    <?php endif; ?>
                                  </p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <?php if (!empty($chat['message'])): ?>
                                <?= mb_strimwidth($chat['message'], 0, 30, '...') ?>
                              <?php else: ?>
                                No messages yet.
                              <?php endif; ?>
                            </td>
                            <td>
                              <a class="btn btn-sm btn-outline-success" href="message/<?= $chat['chat_uuid'] ?>"><i class="mdi mdi-chat-outline"></i> View</a>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <li class="list-group-item text-muted w-100 text-center">No chats yet.</li>
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
