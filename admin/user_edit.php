<?php
	include 'include/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$username = $_POST['username'];
		$role = $_POST['role'];
		$email = $_POST['email'];
		$address = $_POST['address'];

		$conn = $pdo->open();
		$stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();

		try{
			$stmt = $conn->prepare("UPDATE users SET firstname=:firstname, lastname=:lastname, username=:username, role=:role, email=:email, address=:address WHERE id=:id");
			$stmt->execute(['firstname'=>$firstname, 'lastname'=>$lastname, 'username'=>$username, 'role'=>$role, 'email'=>$email, 'address'=>$address, 'id'=>$id]);
			$_SESSION['success'] = 'User profile updated successfully';

		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}


		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit user form first';
	}

	header('location: users');

?>
