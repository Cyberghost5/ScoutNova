<?php
include 'include/session.php';
$conn = $pdo->open();
try{
  $stmt = $conn->prepare("SELECT * FROM settings WHERE id = 1");
  $stmt->execute();
  $settings = $stmt->fetch();
}
catch(PDOException $e){
  echo "There is some problem in connection: " . $e->getMessage();
}
$pdo->close();
?>
<?php
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		$ip_address = $_SERVER['REMOTE_ADDR'];
	}
	$deviceinfo = $_SERVER['HTTP_USER_AGENT'];

	if(isset($_GET['return'])){
		$return = $_GET['return'];

	}
	else{
		$return = 'login';
	}

	$conn = $pdo->open();

	if(isset($_POST['login'])){

		$username = $_POST['username'];
		$password = $_POST['password'];

		try{

			$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE username = :username OR email = :username");
			$stmt->execute(['username'=>$username]);
			$row = $stmt->fetch();

			if($row['numrows'] > 0){
				if($row['status'] == 1){
					if(password_verify($password, $row['password'])){
						if($row['type']){
							$_SESSION['admin'] = $row['id'];
							$stmt = $conn->prepare("INSERT INTO userslog (userid, userip, deviceinfo) VALUES (:userid, :userip, :deviceinfo)");
							$stmt->execute(['userid'=>$row['id'], 'userip'=>$ip_address, 'deviceinfo'=>$deviceinfo]);
						}
						else{
						    unset($_SESSION['username']);
							$_SESSION['user'] = $row['id'];
							// $_SESSION['message'] = $settings['gen_notification'];
							$stmt = $conn->prepare("INSERT INTO userslog (userid, userip, deviceinfo) VALUES (:userid, :userip, :deviceinfo)");
							$stmt->execute(['userid'=>$row['id'], 'userip'=>$ip_address, 'deviceinfo'=>$deviceinfo]);
						}
					}
					else{
						$_SESSION['error'] = 'Incorrect Password';
						$_SESSION['username'] = $username;
					}
				}
				elseif ($row['status'] == 2) {
					$_SESSION['block'] = 'This account has been blocked for violating our <a href="terms">Terms & Conditions</a> and cannot be used anymore! If you think otherwise 
					do <a href="contact">write</a> to us providing your username and we could help resolve this.';
					$_SESSION['username'] = $username;
				}
				else{
					$_SESSION['error'] = 'Account not activated. Check your email for activation link.';
					$_SESSION['username'] = $username;
					// echo $_SESSION['error'] .'jeek';
					var_dump($_SESSION);
				}
			}
			else{
				$_SESSION['error'] = 'Username not found';
				$_SESSION['username'] = $username;
			}
		}
		catch(PDOException $e){
			echo "There is some problem in connection: " . $e->getMessage();
		}
		
	}
	else{
		$_SESSION['error'] = 'Input login credentails first';
	}
	
	$pdo->close();

	var_dump($_SESSION);
	// var_dump($_SESSION['block']);

	header('location:'.$return);
?>
