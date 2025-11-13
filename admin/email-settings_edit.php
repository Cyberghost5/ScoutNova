<?php
	include 'include/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$stmphost = $_POST['stmphost'];
		$stmpuser = $_POST['stmpuser'];
		$password = $_POST['password'];
		$portno = $_POST['portno'];
		$from_email = $_POST['from_email'];
		$replyto = $_POST['replyto'];

		$conn = $pdo->open();
		$stmt = $conn->prepare("SELECT * FROM email_settings WHERE id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();

		try{
			$stmt = $conn->prepare("UPDATE email_settings SET stmphost=:stmphost, stmpuser=:stmpuser, password=:password, portno=:portno, from_email=:from_email, replyto=:replyto WHERE id=:id");
			$stmt->execute(['stmphost'=>$stmphost, 'stmpuser'=>$stmpuser, 'password'=>$password, 'portno'=>$portno, 'from_email'=>$from_email, 'replyto'=>$replyto, 'id'=>$id]);
			$_SESSION['success'] = 'Settings updated successfully';

		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}


		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up settings form first';
	}

	header('location: email-settings');

?>
