<?php
  include 'include/conn.php';
  session_start();

  if(isset($_SESSION['user'])){
    header('location: ../user/home');
  }

  if(isset($_SESSION['admin'])){

    $conn = $pdo->open();

    try{
      $stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
      $stmt->execute(['id'=>$_SESSION['admin']]);
      $admin = $stmt->fetch();
      // header('location: profile.php');
    }
    catch(PDOException $e){
      echo "There is some problem in connection: " . $e->getMessage();
    }

    $pdo->close();
  }
  else {
    header('location: ../login');
  }

$conn = $pdo->open();
try{
  $stmt = $conn->prepare("SELECT * FROM settings WHERE id = 1");
  $stmt->execute();
  $settings = $stmt->fetch();
}
catch(PDOException $e){
  echo "There is some problem in connection: " . $e->getMessage();
}
$pdo->close();
?>
