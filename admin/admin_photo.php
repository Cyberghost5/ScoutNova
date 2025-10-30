<?php
	include 'include/session.php';

	if(isset($_POST['upload'])){
		$id = $_POST['id'];
		$filename = $_FILES['photo']['name'];
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/user/'.$filename);
		}

		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("UPDATE users SET photo=:photo WHERE id=:id");
			$stmt->execute(['photo'=>$filename, 'id'=>$id]);
			$_SESSION['success'] = 'Admin photo updated successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();

	}
	else{
		$_SESSION['error'] = 'Select admin to update photo first';
	}

	header('location: admins');
?>
