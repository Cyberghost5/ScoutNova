<?php
include 'include/session.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/phpmailer/src/Exception.php';
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';

$conn = $pdo->open();

// Fetch email settings
$stmt = $conn->prepare("SELECT * FROM email_settings WHERE id = 1");
$stmt->execute();
$email_settings = $stmt->fetch();

$email_host = $email_settings['stmphost'];
$email_username = $email_settings['stmpuser'];
$email_password = $email_settings['password'];
$email_port = $email_settings['portno'];
$email_from = $email_settings['from_email'];
$email_reply = $email_settings['replyto'];
$email_to = $settings['admin_email'];

if (!isset($admin['id'])) {
  header("Location: admin-login.php");
  exit;
}

$admin_id = $admin['id'];
$id       = $_POST['id'];
$type     = $_POST['type']; // player | scout
$action   = $_POST['action'];
$user_email = trim($_POST['email']);
$notes    = trim($_POST['notes']);

$status = ($action === 'approve') ? 'approved' : 'rejected';
$now    = date('Y-m-d H:i:s');

try {
  if ($type === 'player') {
    // Fetch the record
    $stmt = $conn->prepare("SELECT player_id FROM player_verifications WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update status
    $update = $conn->prepare("
      UPDATE player_verifications 
      SET status=?, reviewed_by=?, review_notes=?, updated_at=? 
      WHERE id=?
    ");
    $update->execute([$status, $admin_id, $notes, $now, $id]);

    // Update player table if approved
    if ($status === 'approved') {
      $conn->prepare("UPDATE users SET verified=1 WHERE id=?")
            ->execute([$record['player_id']]);
    }
    if ($status === 'rejected') {
      $conn->prepare("UPDATE users SET verified=2 WHERE id=?")
            ->execute([$record['player_id']]);
    }

  } elseif ($type === 'scout/agent') {
    $stmt = $conn->prepare("SELECT scout_id FROM scout_verifications WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    $update = $conn->prepare("
      UPDATE scout_verifications 
      SET status=?, reviewed_by=?, review_notes=?, updated_at=? 
      WHERE id=?
    ");
    $update->execute([$status, $admin_id, $notes, $now, $id]);

    if ($status === 'approved') {
      $conn->prepare("UPDATE users SET verified=1 WHERE id=?")
           ->execute([$record['scout_id']]);
    }
    if ($status === 'rejected') {
      $conn->prepare("UPDATE users SET verified=2 WHERE id=?")
            ->execute([$record['scout_id']]);
    }
  }

  $_SESSION['success'] = ucfirst($type) . " verification " . $status . " successfully.";

  // Send notification email to user
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $email_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $email_username;
        $mail->Password   = $email_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $email_port;
        
        //Recipients
        $mail->setFrom($email_from, $settings['site_name']);
        $mail->addAddress($user_email, $settings['site_name']);
        $mail->addAddress($email_to, $settings['site_name']);
        $mail->addReplyTo($email_reply, $settings['site_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verification Notification - ' . $settings['site_name'];
        
        ob_start();
        include 'emails/verification_notification_email.php';
        $mail->Body = ob_get_clean();
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        $_SESSION['error'] = 'Notification email could not be sent. ' .$mail->ErrorInfo;
    }

  header("Location: verifications");
  exit;

} catch (Exception $e) {
  error_log($e->getMessage());
  $_SESSION['error'] = "Something went wrong: " . $e->getMessage();
  header("Location: verifications");
  exit;
}
