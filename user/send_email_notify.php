<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/phpmailer/src/Exception.php';
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';

include 'include/session.php';

$conn = $pdo->open();

$stmt = $conn->prepare("SELECT * FROM email_settings WHERE id = 1");
$stmt->execute();
$email_settings = $stmt->fetch();

$email_host = $email_settings['stmphost'];
$email_username = $email_settings['stmpuser'];
$email_password = $email_settings['password'];
$email_port = $email_settings['portno'];
$email_from = $email_settings['from_email'];
$email_reply = $email_settings['replyto'];

$email_message = "
                
                					<!doctype html>
                					<html lang='en-US'>
                
                					<head>
                						<meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
                						<title>Test Email</title>
                						<meta name='description' content='Test Email.'>
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
                            					          <img width='300' src='".$settings['site_url']."assets/images/".$settings['logo_line']."'>
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
                												<h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Low Balance Notification</h1>
                													<span
                													style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                													<p style='color:#455056; font-size:15px;line-height:24px; margin:0; text-align:left;'>
                														Hello ".$user['username'].",
                													</p>
                													<br>
                													<p style='color:#455056; font-size:15px;line-height:24px; margin:0; text-align:left;'>
                														This is to notify you that your current balance which is ".$settings['currency']."".$user['balance']." is currently low. <br><br> Please top up using any of the payment method available.
                													</p>
                													<br>
                													<p style='color:#455056; font-size:15px;line-height:24px; margin:0; text-align:left;'>
                														Thank you for using ".$settings['site_name'].".
                													</p>
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

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                            // Send using SMTP
    $mail->CharSet = 'UTF-8';
    $mail->Host       = $email_host;                     // SMTP server
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username = $email_username;
    $mail->Password = $email_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mail->Port       = $email_port;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($email_from, $settings['site_name']);
    $mail->addAddress($user['email']);
    $mail->addReplyTo($email_reply, $settings['site_name']);      // Recipient's email address and name

    // Content
    $mail->isHTML(true);                                       // Set email format to HTML
    $mail->Subject = 'Low Account Balance';
    $mail->Body    = $email_message;
    
    $mail->send();
    echo json_encode(array('success' => true));
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'error' => $mail->ErrorInfo));
}
?>
