<?php

// include 'include/session.php';
include 'include/conn.php';
  session_start();

// echo "Logout";

    $conn = $pdo->open();

		try{
			$stmt = $conn->prepare("DELETE FROM sessions WHERE user_id=:id");
			$stmt->execute(['id'=>$_SESSION['user']]);

			$_SESSION['error'] = 'Contact admin. Error code: 162.';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();
		
	session_start();
	session_destroy();
	setcookie("username", "", time() - 3600, "/");
	setcookie("password", "", time() - 3600, "/");

	header('location: ../login');
?>
