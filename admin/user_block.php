<?php
	include 'include/session.php';

	if(isset($_POST['block'])){
		$id = $_POST['id'];

		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("UPDATE users SET status=:status WHERE id=:id");
			$stmt->execute(['status'=>2, 'id'=>$id]);
			$_SESSION['success'] = 'User blocked successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();

	}
	else{
		$_SESSION['error'] = 'Select user to block first';
	}

	header('location: user?id='.$id);
?>
