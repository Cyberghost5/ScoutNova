<?php include 'include/session.php'; 
$chat_id = $_GET['chat_id'] ?? 0;

$query = "
  SELECT c.*, 
  u1.firstname AS user1_firstname, 
  u1.lastname AS user1_lastname, 
  u1.photo AS user1_photo, 
  u2.firstname AS user2_firstname, 
  u2.lastname AS user2_lastname, 
  u2.photo AS user2_photo
  FROM chats c
  JOIN users u1 ON c.user1_id = u1.id
  JOIN users u2 ON c.user2_id = u2.id
  WHERE c.id = ?
  ORDER BY c.id ASC
";

$result = $conn->prepare($query);
$result->execute([$chat_id]);
$row = $result->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  $_SESSION['error'] = 'Somethong is wrong!';
  header('location: messages');
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; 
if($user['profile_set'] == 0){
  echo "<script>window.location.assign('set-profile')</script>"; 
  exit;
    // header('location: set-profile');
};?> 
<link rel="stylesheet" href="../css/chat.css">
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
            <div class="col-md-12">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Messages</h3>
                  <?php if($user['id'] == $row['user2_id']): ?>
                  <h6 class="font-weight-normal mb-0">Messages with <?php echo $row['user2_firstname'] . ' ' . $row['user2_lastname']; ?></h6>
                  <?php elseif($user['id'] == $row['user1_id']): ?>
                  <h6 class="font-weight-normal mb-0">Messages with <?php echo $row['user1_firstname'] . ' ' . $row['user1_lastname']; ?></h6>
                  <?php endif; ?>
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
            <!-- <div class="col-md-12 grid-margin stretch-card"> -->
              <!-- <div class="card"> -->
                <div class="card-body">                  
                  <div class="row">
                    <div class="col-md-12 bg-white p-3 rounded">

                      <div class="card direct-chat direct-chat-primary">
                        <div class="card-body">
                          <?php if($user['id'] == $row['user2_id']): ?>
                          <p>Chat with <?php echo $row['user2_firstname'] . ' ' . $row['user2_lastname']; ?></p>
                          <?php else: ?>
                          <p>Chat with <?php echo $row['user1_firstname'] . ' ' . $row['user1_lastname']; ?></p>
                          <?php endif; ?>
                          <!-- Conversations are loaded here -->
                          <div class="direct-chat-messages" id="chat-messages"></div>
                          <!--/.direct-chat-messages-->
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                          <form id="chat-form">
                            <div class="input-group">
                              <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                              <input type="hidden" name="chat_id" value="<?= htmlspecialchars($chat_id) ?>">
                              <input type="text" name="message" id="message" placeholder="Type Message ..." class="form-control">
                              <span class="input-group-append">
                                <button type="submit" class="btn btn-primary">Send</button>
                              </span>
                            </div>
                          </form>
                        </div>
                        <!-- /.card-footer-->
                      </div>

                    </div>
                  </div>
                </div>
              <!-- </div> -->
            <!-- </div> -->
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
function formatTimestamp(timestamp) {
  const date = new Date(timestamp);

  // Day with ordinal suffix
  const day = date.getDate();
  const suffix =
    day % 10 === 1 && day !== 11 ? "st" :
    day % 10 === 2 && day !== 12 ? "nd" :
    day % 10 === 3 && day !== 13 ? "rd" : "th";

  // Month abbreviations
  const months = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
  const month = months[date.getMonth()];

  // Format time
  let hours = date.getHours();
  const minutes = date.getMinutes().toString().padStart(2, "0");
  const ampm = hours >= 12 ? "PM" : "AM";
  hours = hours % 12 || 12;

  const year = date.getFullYear();

  return `${day}${suffix} ${month} ${year} - ${hours.toString().padStart(2, "0")}:${minutes} ${ampm}`;
}

function loadMessages() {
  $.get('fetch_messages', { chat_id: <?= (int)$chat_id ?> }, function(data) {
    let messages = JSON.parse(data);
    let html = '';
    // console.log(messages); // Debugging line
    messages.forEach(msg => {
      // Determine alignment: right if Sarah (id=2), left otherwise
      let isRight = msg.user_id == <?php echo $user['id']; ?>;
      const isAdmin = msg.role === 'admin'; // from backend if joined

      let role = '';
      let imagepath = '';
      if (msg.role === 'admin') {
        role = 'Admin';
        imagepath = '../admin/images/'; 
      }else if (msg.role === 'user') {
        role = 'Player';
        imagepath = 'images/'; 
      } else if (msg.role === 'agent') {
        role = 'Agent';
        imagepath = 'images/'; 
      }

      html += `
        <div class="direct-chat-msg ${isRight ? 'right' : ''} ${isAdmin ? 'admin-message' : ''}">
          <div class="direct-chat-infos clearfix">
            <span class="direct-chat-name ${isRight ? 'float-right' : 'float-left'}">${msg.firstname} ${msg.lastname} (${role})</span>
            <span class="direct-chat-timestamp ${isRight ? 'float-left' : 'float-right'}">${formatTimestamp(msg.timestamp)}</span>
          </div>
          <img class="direct-chat-img" src="${imagepath}${msg.photo ? msg.photo : 'profile.jpg'}" alt="message user image">
          <div class="direct-chat-text rounded-2 p-3">${msg.message}</div>
        </div>
      `;
    });
    $('#chat-messages').html(html);
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
  });
}

$('#chat-form').on('submit', function(e) {
  e.preventDefault();
  $.post('send_message', $(this).serialize(), function(data) {
    $('#message').val('');
    loadMessages();
  });
});

setInterval(loadMessages, 2000);
loadMessages();
</script>
</body>

</html>
