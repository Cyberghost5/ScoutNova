<?php
	include 'include/session.php';

	if(isset($_POST['resetpass'])){
		$id = $_POST['id'];

		$conn = $pdo->open();
		
		$pass = '12345678';
		$password = password_hash($pass, PASSWORD_DEFAULT);

		try{
			$stmt = $conn->prepare("UPDATE users SET password=:status WHERE id=:id");
			$stmt->execute(['status'=>$password, 'id'=>$id]);
			$_SESSION['success'] = 'User password reset successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();

	}
	else{
		$_SESSION['error'] = 'Select user to block first';
	}

// 	header('location: users');
header('location: user?id='.$id);
?>
