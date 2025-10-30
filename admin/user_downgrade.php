<?php
	include 'include/session.php';

	if(isset($_POST['downgrade'])){
		$id = $_POST['id'];

		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("UPDATE users SET role=:status WHERE id=:id");
			$stmt->execute(['status'=>'user', 'id'=>$id]);
			$_SESSION['success'] = 'User changed to player successfully';
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
