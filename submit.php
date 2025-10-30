<?php
include 'include/session.php';
if(isset($_GET['return'])){
  $return = $_GET['return'];

}
else{
  $return = 'index#register';
}

// print_r($_POST);

if(isset($_POST['submit'])) {
  $name = $_POST['name'];
  $phoneno = $_POST['phoneno'];
  $address = $_POST['address'];
  $invited = $_POST['invited'];

  $conn = $pdo->open();

  $stmt = $conn->prepare("SELECT * FROM registration WHERE name=:name");
  $stmt->execute(['name'=>$name]);
  $row = $stmt->fetch();

  if(empty($phoneno)){ //Replace with phoneno validation code here.
    $_SESSION['error'] = 'Phone number is required!';
    header('location:'.$return); 
  }
  if($row['name'] == $name && $row['phoneno'] == $phoneno){ //Replace with phoneno validation code here.
    $_SESSION['error'] = 'You have already registered!';
    header('location:'.$return); 
  }
  else{
    $now = date('Y-m-d');

  try{
    $stmt = $conn->prepare("INSERT INTO registration (name, phoneno, address, invited) VALUES (:name, :phoneno, :address, :invited)");
    $stmt->execute(['name'=>$name, 'phoneno'=>$phoneno, 'address'=>$address, 'invited'=>$invited]);

    // unset($_SESSION['name']);
    // unset($_SESSION['phoneno']);

    $_SESSION['success'] = 'Hurray 🎉';
    header('location:'.$return);
    }
      catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
        header('location:'.$return);

        $pdo->close();
      }
    }
}else{
  $_SESSION['error'] = 'Fill up form first';
}

header('location:'.$return);
?>
