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

$flutterwave_secret_key = $settings['flutterwave_secret_key'];

// if user is not verrified, redirect to verification page
if(!$user['verified']){
    $_SESSION['error'] = 'Please verify your account to manage subscriptions.';
    if($user['role'] == 'agent'){
        header('location: scout-verification');
    }elseif($user['role'] == 'user'){
        header('location: player-verification');
    }
    exit;
}


if(isset($_GET['action']) == 'cancel'){
  $conn = $pdo->open();

  $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_email = ? AND status = 'active' LIMIT 1");
  $stmt->execute([$user['email']]);
  $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

  if($subscription){
      // Cancel the payment plan via Flutterwave API
      $curl = curl_init();
      curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.flutterwave.com/v3/subscriptions/".$subscription['subscription_id']."/cancel",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "PUT",
          CURLOPT_HTTPHEADER => [
              "Authorization: Bearer $flutterwave_secret_key",
              "Content-Type: application/json",
              "accept: application/json"
          ],
      ]);
      $response = curl_exec($curl);
      curl_close($curl);

      // var_dump($response);
      
      // Log the API response
      $stmt = $conn->prepare("
          INSERT INTO api_responses (user_id, response, service)
          VALUES (?, ?, ?)
      ");
      $stmt->execute([$user['id'], $response, 'Flutterwave Subscription Cancellation']);

      $result = json_decode($response, true);

      if($result['status'] != 'success'){
          $_SESSION['error'] = 'Failed to cancel subscription via payment gateway. '.$result['message'];
          header('location: subscriptions');
          exit;
      }
      
      // Cancel the subscription
      $stmt = $conn->prepare("UPDATE subscriptions SET status = 'cancelled' WHERE id = ?");
      $stmt->execute([$subscription['id']]);

      // Update the users table to remove subscription info
      $stmt = $conn->prepare("UPDATE users SET subscription_plan_id = 1, subscription_status = 'cancelled' WHERE id = ?");
      $stmt->execute([$user['id']]);

      if($user['role'] == 'user'){
        // Update players table if user is a player
        $stmt = $conn->prepare("UPDATE players SET subscription_status = 'cancelled', subscription_plan_id = 1, featured = 1 WHERE user_id = ?");
        $stmt->execute([$user['id']]);
      }elseif($user['role'] == 'agent'){
        // Update agents table if user is a coach
        $stmt = $conn->prepare("UPDATE agent_profiles SET subscription_status = 'cancelled', subscription_plan_id = 1 WHERE user_id = ?");
        $stmt->execute([$user['id']]);
      }

      $_SESSION['success'] = 'Subscription cancelled successfully.';

      // Send email notification to user
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
        $mail->addAddress($user['email'], $settings['site_name']);
        // $mail->addAddress($email_to, $settings['site_name']);
        $mail->addReplyTo($email_reply, $settings['site_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Subscription Cancellation - ' . $settings['site_name'];
        
        ob_start();
        include 'emails/subscription_cancelled_notification_email.php';
        $mail->Body = ob_get_clean();
        
        $mail->send();
      } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        $_SESSION['error'] = 'Notification email could not be sent. ' .$mail->ErrorInfo;
      }


      header('location: subscriptions');
      exit;
  }else{
      $_SESSION['error'] = 'No active subscription found.';
      header('location: subscriptions');
      exit;
  }

}else{
  $plan_id = $_POST['plan_id'];

  $conn = $pdo->open();

  $stmt = $conn->prepare("SELECT * FROM plans WHERE id = :id");
  $stmt->execute(['id' => $plan_id]);
  $plan = $stmt->fetch();

  if(!$plan){
      $_SESSION['error'] = 'Selected plan not found.';
      header('location: subscriptions');
      exit;
  }

  // Make sure the role user is subscribing to a plan meant for their role
  if($plan['role'] != 'all' && $plan['role'] != $user['role']){
      $_SESSION['error'] = 'You cannot subscribe to this plan.';
      header('location: subscriptions');
      exit;
  }

  // Check if user already has active subscription
  $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_email = ? AND status = '1' LIMIT 1");
  $stmt->execute([$user['email']]);
  $existing = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($existing) {
      $_SESSION['cancelled'] = 'You already have an active subscription plan.';
      header('location: subscriptions');
      exit;
  }

  $pdo->close();

  $curl = curl_init();

  $data = [
    "tx_ref" => "SNOVA-" . uniqid(),
    "amount" => $plan['amount'],
    "currency" => $plan['currency'],
    "redirect_url" => $settings['site_url']."user/subscription-callback",
    "customer" => [
      "email" => $user['email'],
      "name" => $user['firstname']. " " .$user['lastname'],
      "phone" => $user['contact_info']
    ],
    "meta" => [
      "payment_plan" => $plan['plan_id'],
      "payment_plan_id" => $plan['id'],
      "price" => $plan['amount'],
      "user_id" => $user['id']
    ],
    "customizations" => [
      "title" => $settings['site_name']. " ".$plan['name']." Subscription",
      "description" => $settings['site_name']. " ".$plan['name']." Subscription",
      "logo" => $settings['site_url']."assets/images/".$settings['favicon']
    ],
    "payment_plan" => $plan['plan_id']
  ];

  curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.flutterwave.com/v3/payments",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
      "Authorization: Bearer $flutterwave_secret_key",
      "Content-Type: application/json"
    ],
  ]);

  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  $stmt = $conn->prepare("
      INSERT INTO api_responses (user_id, response, service)
      VALUES (?, ?, ?)
  ");
  $stmt->execute([$user['id'], $response, 'Flutterwave Payment Initialization']);

  if ($err) {
    $_SESSION['error'] = "cURL Error #: " . $err;
    header('location: subscriptions');
    exit;
  } else {
    $result = json_decode($response, true);

    if($result['status'] != 'success'){
      $_SESSION['error'] = 'Payment initialization failed. Please try again.';
      header('location: subscriptions');
      exit;
    }
    header("Location: " . $result['data']['link']); // redirect to Flutterwave checkout page
    exit;
  }
}

?>