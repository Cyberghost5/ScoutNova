<?php
include 'include/session.php';

if (isset($_POST['del_account'])) {
  $conn = $pdo->open();

    $stmt = $conn->prepare("DELETE FROM users WHERE id=:id");
	$stmt->execute(['id'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM players WHERE user_id=:userid");
    $stmt->execute(['userid'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM agent_profiles WHERE user_id=:userid");
    $stmt->execute(['userid'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM PODRatings WHERE player_id=:playerid");
    $stmt->execute(['playerid'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM PlayerStats WHERE player_id=:playerid");
    $stmt->execute(['playerid'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM chats WHERE user1_id=:userid OR user2_id=:userid");
    $stmt->execute(['userid'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM messages WHERE user_id=:userid");
    $stmt->execute(['userid'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM videos WHERE player_id=:userid");
    $stmt->execute(['userid'=>$user['id']]);

    $stmt = $conn->prepare("DELETE FROM watchlist WHERE player_id=:userid OR agent_id=:userid");
    $stmt->execute(['userid'=>$user['id']]);

    $pdo->close();
    
    session_start();
	session_destroy();
	setcookie("username", "", time() - 3600, "/");
	setcookie("password", "", time() - 3600, "/");
	
	$_SESSION['success'] = "Account Deleted successfully";
	
    echo "<script>window.location.assign('../login')</script>";


}else{
    $_SESSION['error'] = "An error occured";
    echo "<script>window.location.assign('settings')</script>";
}



?>
