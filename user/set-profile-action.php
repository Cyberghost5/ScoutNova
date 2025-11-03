<?php

include 'include/session.php';

if (isset($_POST['save'])) {
  $conn = $pdo->open();

  $uploadDir = 'images/'; // make sure 'images' folder exists and is writable
  if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  if($user['role'] == 'user'){
    $game_type = $_POST['game_type'] ?? '';
    $country = $_POST['country'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $club = $_POST['club'] ?? '';
    $positions = isset($_POST['positions']) ? implode(', ', $_POST['positions']) : '';
    $weight = $_POST['weight'] ?? '';
    $description = $_POST['description'] ?? '';
    $height = $_POST['height'] ?? '';
    $footedness = $_POST['footedness'] ?? '';
    $academy_status = $_POST['academy_status'] ?? '';
    $academy_name = $_POST['academy_name'] ?? '';
    $set = $_POST['set'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $userid = $user['id'];
    $uuid = $user['uuid'];
    
    
    if ($user['profile_set'] == 1) {
        $_SESSION['error1'] = "Profile already set";
        echo "<script>window.location.assign('set-profile')</script>";
      }
      else {
        $imagePath = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
          $fileTmpPath = $_FILES['profile_image']['tmp_name'];
          $fileName = basename($_FILES['profile_image']['name']);
          $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
      
          $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
          $fileSize = $_FILES['profile_image']['size'];
      
          if (!in_array($fileExtension, $allowedExtensions)) {
              $_SESSION['error1'] = "❌ Invalid file type. Only JPG, PNG, or GIF allowed.";
              echo "<script>window.location.assign('set-profile')</script>";
          }
      
          if ($fileSize > 2 * 1024 * 1024) { // 2MB limit
              $_SESSION['error1'] = "❌ File size exceeds 2MB.";
              echo "<script>window.location.assign('set-profile')</script>";
          }
      
          $newFileName = uniqid('player_', true) . '.' . $fileExtension;
          $destPath = $uploadDir . $newFileName;

          // var_dump($positions);
      
          if (move_uploaded_file($fileTmpPath, $destPath)) {
              $imagePath = 'images/' . $newFileName; // relative path for storage/display

              $stmt = $conn->prepare("INSERT INTO players (user_id, uuid, game_type, country, gender, club, positions, weight, description, height, footedness, academy_status, academy_name, profile_image, dob) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $stmt->execute([$userid, $uuid, $game_type, $country, $gender, $club, $positions, $weight, $description, $height, $footedness, $academy_status, $academy_name, $newFileName, $dob]);

              $stmt = $conn->prepare("UPDATE users SET profile_set=:profile_set, subscription_status=:subscription_status, subscription_plan_id=:subscription_plan_id, photo=:photo WHERE id=:id");
              $stmt->execute(['profile_set'=>1, 'subscription_status'=>1, 'subscription_plan_id'=>1, 'photo'=>$newFileName, 'id'=>$userid]);
              
              $_SESSION['success'] = "Profile set successfully";
              unset($_SESSION['error1']);
              unset($_SESSION['error']);
              echo "<script>window.location.assign('home')</script>";
      
          } else {
              $_SESSION['error1'] = "❌ Failed to move uploaded file.";
              echo "<script>window.location.assign('set-profile')</script>";
          }
        }else{
          $_SESSION['error1'] = "You need to upload an image.";
          echo "<script>window.location.assign('set-profile')</script>";
        }
    }
  }elseif($user['role'] == 'agent'){
    $game_type = $_POST['game_type'] ?? '';
    $country = $_POST['country'] ?? '';
    $license_number = $_POST['license_number'] ?? '';
    $organization = $_POST['organization'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $userid = $user['id'];
    $uuid = $user['uuid'];
    
    
    if ($user['profile_set'] == 1) {
        $_SESSION['error1'] = "Profile already set";
        echo "<script>window.location.assign('set-profile')</script>";
      }
      else {
        $imagePath = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
          $fileTmpPath = $_FILES['profile_image']['tmp_name'];
          $fileName = basename($_FILES['profile_image']['name']);
          $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
      
          $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
          $fileSize = $_FILES['profile_image']['size'];
      
          if (!in_array($fileExtension, $allowedExtensions)) {
              $_SESSION['error1'] = "❌ Invalid file type. Only JPG, PNG, or GIF allowed.";
              echo "<script>window.location.assign('set-profile')</script>";
          }
      
          if ($fileSize > 2 * 1024 * 1024) { // 2MB limit
              $_SESSION['error1'] = "❌ File size exceeds 2MB.";
              echo "<script>window.location.assign('set-profile')</script>";
          }
      
          $newFileName = uniqid('agent_', true) . '.' . $fileExtension;
          $destPath = $uploadDir . $newFileName;

      
          if (move_uploaded_file($fileTmpPath, $destPath)) {
              $imagePath = 'images/' . $newFileName; // relative path for storage/display

              $stmt = $conn->prepare("INSERT INTO agent_profiles (user_id, uuid, game_type, country, license_number, organization, bio, profile_image) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
              $stmt->execute([$userid, $uuid, $game_type, $country, $license_number, $organization, $bio, $newFileName]);

              $stmt = $conn->prepare("UPDATE users SET profile_set=:profile_set, photo=:photo WHERE id=:id");
              $stmt->execute(['profile_set'=>1, 'photo'=>$newFileName, 'id'=>$userid]);
              
              $_SESSION['success'] = "Profile set successfully";
              unset($_SESSION['error1']);
              unset($_SESSION['error']);
              echo "<script>window.location.assign('home')</script>";
      
          } else {
              $_SESSION['error1'] = "❌ Failed to move uploaded file.";
              echo "<script>window.location.assign('set-profile')</script>";
          }
        }else{
          $_SESSION['error1'] = "You need to upload an image.";
          echo "<script>window.location.assign('set-profile')</script>";
        }
    }
  }


}else{
    $_SESSION['error1'] = "An error occured";
    echo "<script>window.location.assign('set-profile')</script>";
}

// var_dump($_POST);
// var_dump($_FILES);



?>