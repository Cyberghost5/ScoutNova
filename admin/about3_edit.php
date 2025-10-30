<?php
	include 'include/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$phone = $_POST['phone'];
		$facebook = $_POST['facebook'];
		$youtube = $_POST['youtube'];
		$instagram = $_POST['instagram'];
		$email = $_POST['email'];

		$conn = $pdo->open();
		$stmt = $conn->prepare("SELECT * FROM about WHERE id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();

		try{
			$stmt = $conn->prepare("UPDATE about SET phone=:phone, youtube=:youtube, facebook=:facebook, instagram=:instagram, email=:email WHERE id=:id");
			$stmt->execute(['phone'=>$phone, 'youtube'=>$youtube, 'facebook'=>$facebook, 'instagram'=>$instagram, 'email'=>$email, 'id'=>$id]);
			$_SESSION['success'] = 'Social Media Account updated successfully';

		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}


		$pdo->close();
	}

	header('location: gen-settings');

?>
