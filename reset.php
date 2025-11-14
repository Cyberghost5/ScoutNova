<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

include 'include/session.php';

	if(isset($_POST['reset'])){
		$email = $_POST['email'];

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT * FROM settings WHERE id = 1");
		$stmt->execute();
		$settings = $stmt->fetch();

		$stmt = $conn->prepare("SELECT * FROM email_settings WHERE id = 1");
		$stmt->execute();
		$email_settings = $stmt->fetch();

		$email_host = $email_settings['stmphost'];
		$email_username = $email_settings['stmpuser'];
		$email_password = $email_settings['password'];
		$email_port = $email_settings['portno'];
		$email_from = $email_settings['from_email'];
		$email_reply = $email_settings['replyto'];

		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email=:email");
		$stmt->execute(['email'=>$email]);
		$row = $stmt->fetch();

		if($row['numrows'] > 0){

			if ($row['status'] == 1 or $row['status'] == 0) {
				//generate code
				$set='123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$code=substr(str_shuffle($set), 0, 15);
				try{
					$stmt = $conn->prepare("UPDATE users SET reset_code=:code WHERE id=:id");
					$stmt->execute(['code'=>$code, 'id'=>$row['id']]);

					$message = "

					<!doctype html>
					<html lang='en-US'>

					<head>
						<meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
						<title>Reset Password Email Template</title>
						<meta name='description' content='Reset Password Email Template.'>
						<style type='text/css'>
						a:hover {text-decoration: underline !important;}
						</style>
					</head>

					<body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
						<table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
						style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
						<tr>
							<td>
								<table style='background-color: #f2f3f8; max-width:670px;  margin:0 auto;' width='100%' border='0'
								align='center' cellpadding='0' cellspacing='0'>
								<tr>
									<td style='height:80px;'>&nbsp;</td>
								</tr>
								<tr>
									<td style='text-align:center;'>
										<a href='".$settings['site_url']."' title='logo' target='_blank'>
            					          <img width='300' src='".$settings['site_url']."assets/images/".$settings['favicon']."'>
            					        </a>
									</td>
								</tr>
								<tr>
									<td style='height:20px;'>&nbsp;</td>
								</tr>
								<tr>
									<td>
										<table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
										style='max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
										<tr>
											<td style='height:40px;'>&nbsp;</td>
										</tr>
										<tr>
											<td style='padding:0 35px;'>
												<h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>You have
													requested to reset your password</h1>
													<span
													style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
													<p style='color:#455056; font-size:15px;line-height:24px; margin:0;'>
														We cannot simply send you your old password. A unique link to reset your
														password has been generated for you. To reset your password, click the
														following link and follow the instructions.
													</p>
													<a href='".$settings['site_url']."password-reset?code=".$code."&user=".$row['id']."'
													style='background:#62296b;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;'>Reset
													Password</a>
												</td>
											</tr>
											<tr>
												<td style='height:40px;'>&nbsp;</td>
											</tr>
										</table>
									</td>
									<tr>
										<td style='height:20px;'>&nbsp;</td>
									</tr>
									<tr>
										<td style='text-align:center;'>
											<p style='font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;'>&copy; ".date('Y')." <strong>".$settings['site_name']."</strong></p>
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

					// <p>Hello</p>,
					// <p>We received a request for a change of your password</p>
					// <p>Click on the button bellow to reset it.</p>
					// <p>Your Account:</p>
					// <p>Email: ".$email."</p>
					// <p>Please click the link below to reset your password.</p>
					// <a href='".$settings['site_url']."password_reset?code=".$code."&user=".$row['id']."'><button class='btn btn-primary btn-block btn-flat'>Change Password</button></a>
					// <p>If you didn't request a password reset link, kindly ignore this message and we'll forget this ever happened.</p>

					//Load phpmailer

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
								// $mail->addAddress($settings['admin_email'], 'Forgotten Password');
								$mail->addAddress($email);
								$mail->addReplyTo($email_reply, $settings['site_name']);

								//Content
								$mail->isHTML(true);
								$mail->Subject = $settings['site_name']." Password Reset";
								$mail->Body    = $message;

								$mail->send();

				        $_SESSION['success'] = 'Password reset link sent. Please check your email inbox/spam folder';

				    }
				    catch (Exception $e) {
				        $_SESSION['error'] = 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
				    }
				}
				catch(PDOException $e){
					$_SESSION['error'] = $e->getMessage();
				}
			}
			elseif ($row['status'] == 2) {
				$_SESSION['error'] = 'This account has been blocked for violating our <a href="terms-conditions">Terms & Conditions</a> and cannot be used anymore!';
			}

		}
		else{
			$_SESSION['error'] = 'Email not found';
		}

		$pdo->close();

	}
	else{
		$_SESSION['warning'] = 'No shortcuts, Fill up the form first';
	}

	// header('location: forgot-password');
	echo "<script>window.location.assign('forgot-password')</script>";

?>
