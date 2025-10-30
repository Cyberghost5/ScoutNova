<?php
	include 'include/session.php';

	if(isset($_POST['add'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$username = $_POST['username'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$address = $_POST['address'];
		$contact = $_POST['contact'];
		$type = $_POST['type'];

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email=:email");
		$stmt->execute(['email'=>$email]);
		$row = $stmt->fetch();

		$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE username=:username");
		$stmt->execute(['username'=>$username]);
		$iow = $stmt->fetch();

		if($row['numrows'] > 0){
			$_SESSION['error'] = 'Email already taken';
		}elseif ($iow['numrows'] > 0) {
			$_SESSION['error'] = 'Username already taken';
		}
		else{
			$password = password_hash($password, PASSWORD_DEFAULT);
			$filename = $_FILES['photo']['name'];
			$now = date('Y-m-d');
			if(!empty($filename)){
				move_uploaded_file($_FILES['photo']['tmp_name'], 'images/'.$filename);
			}
			try{
				$stmt = $conn->prepare("INSERT INTO users (username, email, password, type, firstname, lastname, address, contact_info, photo, status, created_on) VALUES (:username, :email, :password, :type, :firstname, :lastname, :address, :contact, :photo, :status, :created_on)");
				$stmt->execute(['username'=>$username, 'email'=>$email, 'password'=>$password, 'type'=>$type, 'firstname'=>$firstname, 'lastname'=>$lastname, 'address'=>$address, 'contact'=>$contact, 'photo'=>$filename, 'status'=>1, 'created_on'=>$now]);
				$_SESSION['success'] = 'Admin added successfully';

			}
			catch(PDOException $e){
				$_SESSION['error'] = $e->getMessage();
			}
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up admin form first';
	}

	header('location: admins');

?>
