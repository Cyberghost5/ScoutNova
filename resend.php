<?php 
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

// Resend verification email logic goes here
include 'include/session.php';


if(isset($_GET['email'])) {
    // Call email settings from database
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
    
    $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = substr(str_shuffle($set), 0, 12);
    
    $pdo->close();
    
    $email = $_GET['email'];
    
    $conn = $pdo->open();
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email OR username = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        $userid = $user['id'];
        $username = $user['username'];
        
        // Update the code column in the database
        $update_stmt = $conn->prepare("UPDATE users SET activate_code = :code WHERE id = :id");
        $update_stmt->execute(['code' => $code, 'id' => $userid]);
        
        if($user) {
            if($user['status'] == 0) {
                // Resend verification email logic
                
                try{
                    
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
                        $mail->addAddress($user['email'], $user['username']);
                        $mail->addReplyTo($email_reply, $settings['site_name']);
                        
                        //Content
                        $mail->isHTML(true);
                        $mail->Subject = $settings['site_name']." Verification Email";
                        $mail->Body    = $message;
                        
                        $mail->send();
                        
                        $_SESSION['success'] = "Verification email resent. Please check your inbox.";
                        echo "<script>window.location.assign('login')</script>";
                        exit();
                    }
                    catch (Exception $e) {
                        $_SESSION['error'] = 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
                        echo "<script>window.location.assign('login')</script>";
                        exit();
                    }   
                }
                catch(PDOException $e){
                    $_SESSION['error'] = $e->getMessage();
                    echo "<script>window.location.assign('login')</script>";
                    exit();
                }
            } else {
                $_SESSION['error'] = "Account is already verified.";
            }
        } else {
            $_SESSION['error'] = "No account found with that email address.";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "There was a problem: " . $e->getMessage();
    }
    
    $pdo->close();
    header('location: login');
    exit();
}
else {
    $_SESSION['error'] = "No email address provided.";
    header('location: login');
    exit();
}

?>