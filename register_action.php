<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

include 'include/session.php';

$link = 'register';

try {

	if(isset($_GET['return'])){
		$return = $_GET['return'];

	}
	else{
		$return = 'register';
	}

	if(isset($_POST['register'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$username = substr($firstname, 0, 3).''.substr($lastname, 0, 3).''.substr(rand(), 0, 3);
		$role = $_POST['role'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		$password = $_POST['password'];
		$confirm_password = $_POST['confirm_password'];

		// If password length is less than 6
		if(strlen($password) < 6){
			$_SESSION['error'] = 'Password must be 6 character or more.';
			echo "<script>window.location.assign('$link')</script>";
		}
		// elseif($role !== "agent" || $role !== "user"){
		// 	$_SESSION['error'] = 'User type does not exist.';
		// 	echo "<script>window.location.assign('$link')</script>";
		// }
		else{
			$conn = $pdo->open();

			$stmt = $conn->prepare("SELECT * FROM email_settings WHERE id = 1");
			$stmt->execute();
			$email_settings = $stmt->fetch();

			$stmt = $conn->prepare("SELECT * FROM settings WHERE id = 1");
			$stmt->execute();
			$settings = $stmt->fetch();

			$email_host = $email_settings['stmphost'];
			$email_username = $email_settings['stmpuser'];
			$email_password = $email_settings['password'];
			$email_port = $email_settings['portno'];
			$email_from = $email_settings['from_email'];
			$email_reply = $email_settings['replyto'];

			// try {
			$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			// var_dump($stmt);
			// } catch (\Exception $e1){
			// 	$_SESSION['error'] = $e1->getMessage();
			// 	echo "<script>window.location.assign('$link')</script>";
			// }

			// try {
			$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE username=:username");
			$stmt->execute(['username'=>$username]);
			$iow = $stmt->fetch();
			// var_dump($stmt->execute(['username'=>$username]));
			// } catch (\Exception $e2){
			// 	$_SESSION['error'] = $e2->getMessage();
			// 	echo "<script>window.location.assign('$link')</script>";
			// }


			if($row['numrows'] > 0){
				$_SESSION['error'] = 'Email already taken.';
				echo "<script>window.location.assign('$link')</script>";
			}elseif ($iow['numrows'] > 0){
				$_SESSION['error'] = 'Username already taken.';
				echo "<script>window.location.assign('$link')</script>";
			}elseif ($password !== $confirm_password){
				$_SESSION['error'] = 'Password and confirm password do not match.';
				echo "<script>window.location.assign('$link')</script>";
			}
			else{
				$now = date('Y-m-d');
				$password = password_hash($password, PASSWORD_DEFAULT);

				//generate code
				$set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$code = substr(str_shuffle($set), 0, 12);
				
				$uuid = generateHexUUID();

				if(true)
				{
					try{
						// Insert new user into DB
						$stmt = $conn->prepare("INSERT INTO users (uuid, username, firstname, lastname, role, email, contact_info, password, activate_code, created_on) VALUES (:uuid, :username, :firstname, :lastname, :role, :email, :phone, :password, :code, :now)");
						$stmt->execute(['uuid'=>$uuid, 'username'=>$username, 'firstname'=>$firstname, 'lastname'=>$lastname, 'role'=>$role, 'email'=>$email, 'phone'=>$phone, 'password'=>$password, 'code'=>$code, 'now'=>$now]);
						$userid = $conn->lastInsertId();

						$message = "
						<!doctype html>
						<html lang='en-US'>

						<head>
						<meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
						<title>New Account on ".$settings['site_name']."</title>
						<meta name='description' content='New Account Email Template.'>
						<style type='text/css'>
						a:hover {text-decoration: underline !important;}
						</style>
						</head>

						<body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
						<table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
						style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
						<tr>
						<td>
						<table style='background-color: #f2f3f8; max-width:670px; margin:0 auto;' width='100%' border='0'
						align='center' cellpadding='0' cellspacing='0'>
						<tr>
						<td style='height:80px;'>&nbsp;</td>
						</tr>
						<tr>
						<td style='text-align:center;'>
						<a href='".$settings['site_url']."' title='logo' target='_blank'>
						<img width='100' src='".$settings['site_url']."assets/images/".$settings['favicon']."'>
						</a>
						</td>
						</tr>
						<tr>
						<td style='height:20px;'>&nbsp;</td>
						</tr>
						<tr>
						<td>
						<table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
						style='max-width:670px; background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
						<tr>
						<td style='height:40px;'>&nbsp;</td>
						</tr>
						<tr>
						<td style='padding:0 35px;'>
						<h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Get started <br> ".$username."</h1>
						<p style='font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;'> Your account has been created on ".$settings['site_name'].". <br> Below are your registered credentials, <br><strong>Click on the button below to activate your account</strong>.</p>
						<span style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
						<p style='color:#455056; font-size:18px;line-height:20px; margin:0; font-weight: 500;'>
						<strong style='display: block;font-size: 13px; margin: 0 0 4px; color:rgba(0,0,0,.64); font-weight:normal;'>Username</strong>".$username."
						<strong style='display: block; font-size: 13px; margin: 24px 0 4px 0; font-weight:normal; color:rgba(0,0,0,.64);'>Password</strong>".$_POST['password']."
						</p>

						<a href='".$settings['site_url']."activate?code=".$code."&user=".$userid."'
						style='background:#1e3a8ae6;text-decoration:none !important; display:inline-block; font-weight:500; margin-top:24px; color:#fff; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;'>Activate Your Account</a>
						</td>
						</tr>
						<tr>
						<td style='height:40px;'>&nbsp;</td>
						</tr>
						</table>
						</td>
						</tr>
						<tr>
						<td style='height:20px;'>&nbsp;</td>
						</tr>
						<tr>
						<td style='text-align:center;'>
						<p style='font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;'>&copy; ".date('Y')." <strong>".$settings['site_name']."</strong> </p>
						</td>
						</tr>
						<tr>
						<td style='height:80px;'>&nbsp;</td>
						</tr>
						</table>
						</td>
						</tr>
						</table>
						</body>
						</html>
						";

						$mail = new PHPMailer(true);
						try {
							//Server settings
							// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
							$mail->isSMTP();
							$mail->Host = $email_host;
							$mail->SMTPAuth = true;
							$mail->Username = $email_username;
							$mail->Password = $email_password;
							$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
							$mail->Port = $email_port;

							$mail->setFrom($email_from, $settings['site_name']);

							//Recipients
							// $mail->addAddress($settings['admin_email'], 'New Signup');
							$mail->addAddress($email, $username);
							$mail->addReplyTo($email_reply, $settings['site_name']);

							//Content
							$mail->isHTML(true);
							$mail->Subject = $settings['site_name']." Sign Up";
							$mail->Body    = $message;

							$mail->send();

							$_SESSION['success'] = 'Account created. Check your email to activate. If you cant find the email, check your spam or <a href="contact">reach</a> out to us.';
							
							echo "<script>window.location.assign('login')</script>";

							// unset($_SESSION['username']);
							// unset($_SESSION['email']);
						}
						catch (Exception $e) {
							$_SESSION['error'] = 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
							echo "<script>window.location.assign('$link')</script>";
						}


					}
					catch(PDOException $e){
						$_SESSION['error'] = $e->getMessage();
						echo "<script>window.location.assign('$link')</script>";
					}

				}else{
					// echo "$link";
					$_SESSION['error'] = 'Opps, an error occcured. Try again later.';
					echo "<script>window.location.assign('$link')</script>";
				}

				$pdo->close();

			}

		}

	}
	else{
		$_SESSION['warning'] = 'No shortcuts, Fill up registration form first';
		echo "<script>window.location.assign('$link')</script>";
	}

}
catch(PDOException $e){
	$_SESSION['error'] = $e->getMessage();
	header('location:'.$link);
	// echo "<script>window.location.assign('$link')</script>";
}

?>
