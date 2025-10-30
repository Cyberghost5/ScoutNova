<?php
	include 'include/session.php';

	if(isset($_POST['upgrade'])){
		$id = $_POST['id'];

		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("UPDATE users SET role=:status WHERE id=:id");
			$stmt->execute(['status'=>'agent', 'id'=>$id]);
			$_SESSION['success'] = 'User changed to agent successfully';
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
