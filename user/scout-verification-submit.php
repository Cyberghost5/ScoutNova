<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/phpmailer/src/Exception.php';
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';

// scout-verification-submit.php
include 'include/session.php'; // must provide $conn and session

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

require '../vendor/autoload.php'; // cloudinary SDK

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Exception\ApiError;

header('Content-Type: text/html; charset=utf-8');

// Basic auth check
if (!isset($user['id'])) {
    http_response_code(403);
    header("Location: scout-verification");
    exit;
}

$scout_id = intval($user['id'] ?? 0);
if ($scout_id <= 0 || $scout_id !== (int)$user['id']) {
    $_SESSION['error'] = "Invalid scout.";
    header("Location: scout-verification");
    exit;
}

// Cloudinary config (set these in your settings or env)
$cloudName = $settings['cloudinary_cloud_name'] ?? '';
$apiKey = $settings['cloudinary_api_key'] ?? '';
$apiSecret = $settings['cloudinary_api_secret'] ?? '';

if (!$cloudName || !$apiKey || !$apiSecret) {
    $_SESSION['error'] = "Cloudinary is not configured.";
    header("Location: scout-verification");
    exit;
}

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key'    => $apiKey,
        'api_secret' => $apiSecret
    ]
]);

// Helper: validate upload
function validate_file($file, $allowed_exts, $max_bytes) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return "Upload failed or no file.";
    if ($file['size'] > $max_bytes) return "File too large. Max " . ($max_bytes/1024/1024) . " MB.";
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts)) return "Invalid file type: $ext";
    return true;
}

$cert = $_FILES['certification'];
$exp  = $_FILES['experience'];
$biz  = $_FILES['official_id'];

foreach ([$cert,$exp,$biz] as $file) {
  $ok = validate_file($file, ['jpg','jpeg','png','pdf'], 10*1024*1024);
  if ($ok !== true) {
    $_SESSION['error'] = $ok;
    header("Location: scout-verification");
    exit;
  }
}
$upload_type = $settings['upload_type']; //'local';

// Proceed with uploads to Cloudinary
if($upload_type === 'cloudinary'){
    try {
        // Cerifications
        $res1 = $cloudinary->uploadApi()->upload($cert['tmp_name'], [
            'folder' => "scoutnova/verifications/scouts/certifications"
        ]);
        $certUrl = $res1['secure_url'] ?? $res1['url'] ?? null;

        // Experience
        $res2 = $cloudinary->uploadApi()->upload($exp['tmp_name'], [
            'folder' => "scoutnova/verifications/scouts/experience"
        ]);
        $expUrl = $res2['secure_url'] ?? $res2['url'] ?? null;

        // Business document
        $res3 = $cloudinary->uploadApi()->upload($biz['tmp_name'], [
            'folder' => "scoutnova/verifications/scouts/official_ids"
        ]);
        $bizUrl = $res3['secure_url'] ?? $res3['url'] ?? null;


    } catch (ApiError $e) {
        error_log("Cloudinary error: " . $e->getMessage());
        $_SESSION['error'] = "File upload failed. Try again later.";
        header("Location: scout-verification");
        exit;
    }
} elseif ($upload_type === 'local'){
    try {
        // certification
        $targetDir = "upload/verifications/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['certification']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['certification']['tmp_name'], $targetFilePath)) {
            // Save relative path for easy access
            $certUrl = $settings['site_url']."user/upload/verifications/" . $fileName;
        } else {
            // die("<div style='color:red;'>Error uploading file locally.</div>");
            $_SESSION['error'] = 'Error uploading certification file locally.';
            header('location: scout-verification');
            exit;
        }

        // experience document
        $targetDir = "upload/verifications/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['experience']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['experience']['tmp_name'], $targetFilePath)) {
            // Save relative path for easy access
            $expUrl = $settings['site_url']."user/upload/verifications/" . $fileName;
        } else {
            // die("<div style='color:red;'>Error uploading file locally.</div>");
            $_SESSION['error'] = 'Error uploading experience document file locally.';
            header('location: scout-verification');
            exit;
        }

        // business doc (resource_type video)
        $targetDir = "upload/verifications/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['official_id']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['official_id']['tmp_name'], $targetFilePath)) {
            // Save relative path for easy access
            $bizUrl = $settings['site_url']."user/upload/verifications/" . $fileName;
        } else {
            // die("<div style='color:red;'>Error uploading file locally.</div>");
            $_SESSION['error'] = 'Error uploading business doc file locally.';
            header('location: scout-verification');
            exit;
        }

        
    } catch (ApiError $e) {
        error_log("Upload error: " . $e->getMessage());
        $_SESSION['error'] = "File upload failed. Try again later.";
        header("Location: scout-verification");
        exit;
    }
}
// social handles
$social = [
    'instagram' => trim($_POST['instagram'] ?? ''),
    'twitter' => trim($_POST['twitter'] ?? ''),
    'facebook' => trim($_POST['facebook'] ?? ''),
    'linkedin' => trim($_POST['linkedin'] ?? '')
];

// notes
$notes = trim($_POST['notes'] ?? '');

// Insert into DB
try {
    $stmt = $conn->prepare("
        INSERT INTO scout_verifications
        (scout_id, certification_url, experience_proof_url, business_registration_url, social_handles, status, review_notes, created_at)
        VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())
    ");
    $stmt->execute([
        $scout_id,
        $certUrl,
        $expUrl,
        $bizUrl,
        json_encode($social, JSON_UNESCAPED_SLASHES),
        $notes
    ]);

    // Optional: update user's verified flag removed (still pending)
    $upd = $conn->prepare("UPDATE users SET verified = 3 WHERE id = ?");
    $upd->execute([$user['id']]);

    $_SESSION['success'] = "Verification submitted. An admin will review your documents within 48 hours.";

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
        $mail->addAddress($user['email'], $settings['site_name']);
        $mail->addAddress($email_to, $settings['site_name']);
        $mail->addReplyTo($email_reply, $settings['site_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Scout Verification Request Submitted - ' . $settings['site_name'];
        
        ob_start();
        include 'emails/scout_verification_notification_email.php';
        $mail->Body = ob_get_clean();
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        $_SESSION['error'] = 'Notification email could not be sent. ' .$mail->ErrorInfo;
    }

    header("Location: scout-verification");
    exit;

} catch (Exception $e) {
    error_log("DB error (player verification): " . $e->getMessage());
    $_SESSION['error'] = "Could not save verification. Try again later.";
    header("Location: scout-verification");
    exit;
}
