<?php
	include 'include/session.php';

	if(isset($_GET['return'])){
		$return = $_GET['return'];

	}
	else{
		$return = 'home';
	}

	if(isset($_POST['save'])){
		$userid = $user['id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$contact_info = $_POST['contact_info'];


		$photo = $_FILES['photo']['name'];

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT * FROM users WHERE id=:userid");
		$stmt->execute(['userid'=>$userid]);
		$row = $stmt->fetch();

			if(!empty($photo)){
				$allowed =  array("jpeg", "jpg", "png", "JPEG", "JPG", "PNG");
				$ext = pathinfo($photo, PATHINFO_EXTENSION);
				if(!in_array($ext,$allowed)){
				    $_SESSION['error'] = 'Invalid Image type submitted';
				    header('location:'.$return);
				    //exit();
				}else{
				    $new_filename = $row['username'].'_'.time().'.'.$ext;
				    move_uploaded_file($_FILES['photo']['tmp_name'], 'images/'.$new_filename);
				    $filename = $new_filename;
				}
			}
			else{
				$filename = $user['photo'];
			}

			try{
				$stmt = $conn->prepare("UPDATE users SET firstname=:firstname, lastname=:lastname, contact_info=:contact_info, photo=:photo WHERE id=:id");
				$stmt->execute(['firstname'=>$firstname, 'lastname'=>$lastname, 'contact_info'=>$contact_info, 'photo'=>$filename, 'id'=>$user['id']]);

				$_SESSION['success'] = 'Account updated successfully';
			}
			catch(PDOException $e){
				$_SESSION['error'] = $e->getMessage();
			}

			$pdo->close();

	}
	else{
		$_SESSION['error'] = 'Fill up required details first';
	}

	header('location:'.$return);

?>
