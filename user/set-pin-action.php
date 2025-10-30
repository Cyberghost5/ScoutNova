<?php
include 'include/session.php';

if (isset($_POST['set'])) {
  $conn = $pdo->open();

  $userid = $user['id'];
  $pin = $_POST['pin'];
  $confirm_pin = $_POST['confirm_pin'];

  if ($user['pin_set'] == 1) {
    $_SESSION['error'] = "PIN already set";
    echo "<script>window.location.assign('set-profile')</script>";
  }
  elseif ($pin !== $confirm_pin) {
    $_SESSION['error'] = "The two PIN do not tally.";
    echo "<script>window.location.assign('set-profile')</script>";
  }
  else {
    $stmt = $conn->prepare("UPDATE users SET pin=:pin, pin_set=:pin_set WHERE id=:id");
    $stmt->execute(['pin'=>$pin, 'pin_set'=>1, 'id'=>$userid]);
    
    $_SESSION['success'] = "PIN set successfully";
    echo "<script>window.location.assign('home')</script>";
  }


}else{
    $_SESSION['error'] = "An error occured";
    echo "<script>window.location.assign('set-profile')</script>";
}



?>
