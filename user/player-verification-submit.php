<?php
// player-verification-submit.php
include 'include/session.php'; // must provide $conn and session
require '../vendor/autoload.php'; // cloudinary SDK

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Exception\ApiError;

header('Content-Type: text/html; charset=utf-8');

// Basic auth check
if (!isset($user['id'])) {
    http_response_code(403);
    header("Location: player-verification");
    exit;
}

$player_id = intval($_POST['player_id'] ?? 0);
if ($player_id <= 0 || $player_id !== (int)$user['id']) {
    $_SESSION['error'] = "Invalid player.";
    header("Location: player-verification");
    exit;
}

// Cloudinary config (set these in your settings or env)
$cloudName = $settings['cloudinary_cloud_name'] ?? '';
$apiKey = $settings['cloudinary_api_key'] ?? '';
$apiSecret = $settings['cloudinary_api_secret'] ?? '';

if (!$cloudName || !$apiKey || !$apiSecret) {
    $_SESSION['error'] = "Cloudinary is not configured.";
    header("Location: player-verification");
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

// Validate official ID
$official = $_FILES['official_id'] ?? null;
$ok = validate_file($official, ['jpg','jpeg','png','pdf'], 10 * 1024 * 1024);
if ($ok !== true) {
    $_SESSION['error'] = $ok;
    header("Location: player-verification"); 
    exit;
}

// validate parent consent form (optional)
$parent_consent = null;
if (!empty($_FILES['parent_consent']) && $_FILES['parent_consent']['error'] !== UPLOAD_ERR_NO_FILE) {
    $parent_consent = $_FILES['parent_consent'];
    $ok2 = validate_file($parent_consent, ['jpg','jpeg','png','pdf'], 10 * 1024 * 1024);
    if ($ok2 !== true) {
        $_SESSION['error'] = $ok2;
        header("Location: player-verification"); 
        exit;
    }
}

// Team proof optional
$team_proof_url = null;
if (!empty($_FILES['team_proof']) && $_FILES['team_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
    $teamProof = $_FILES['team_proof'];
    $ok3 = validate_file($teamProof, ['jpg','jpeg','png','pdf'], 10 * 1024 * 1024);
    if ($ok3 !== true) {
        $_SESSION['error'] = $ok3;
        header("Location: player-verification"); 
        exit;
    }
}

$upload_type = $settings['upload_type']; //'local';

// Proceed with uploads to Cloudinary
if($upload_type === 'cloudinary'){
    try {
        // Official ID
        $res1 = $cloudinary->uploadApi()->upload($_FILES['official_id']['tmp_name'], [
            'folder' => "scoutnova/verifications/ids",
            'resource_type' => 'image' // pdf may still work; cloudinary auto-detect
        ]);
        $official_url = $res1['secure_url'] ?? $res1['url'] ?? null;

        // consent form if provided
        if (isset($parent_consent) && $parent_consent['error'] === UPLOAD_ERR_OK) {
            $res2 = $cloudinary->uploadApi()->upload($parent_consent['tmp_name'], [
                'folder' => "scoutnova/verifications/parent_consent",
                'resource_type' => 'image'
            ]);
            $parent_consent_url = $res2['secure_url'] ?? $res2['url'] ?? null;
        }

        // Team proof if provided
        if (isset($teamProof) && $teamProof['error'] === UPLOAD_ERR_OK) {
            $res3 = $cloudinary->uploadApi()->upload($teamProof['tmp_name'], [
                'folder' => "scoutnova/verifications/team_proofs",
                'resource_type' => 'image'
            ]);
            $team_proof_url = $res3['secure_url'] ?? $res3['url'] ?? null;
        }

    } catch (ApiError $e) {
        error_log("Cloudinary error: " . $e->getMessage());
        $_SESSION['error'] = "File upload failed. Try again later.";
        header("Location: player-verification");
        exit;
    }
} elseif ($upload_type === 'local'){
    try {
        // Official ID
        $targetDir = "upload/verifications/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['official_id']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['official_id']['tmp_name'], $targetFilePath)) {
            // Save relative path for easy access
            $official_url = $settings['site_url']."user/upload/verifications/" . $fileName;
        } else {
            // die("<div style='color:red;'>Error uploading file locally.</div>");
            $_SESSION['error'] = 'Error uploading official ID file locally.';
            header('location: player-verification');
            exit;
        }

        // Parent consent
        if (isset($parent_consent) && $parent_consent['error'] === UPLOAD_ERR_OK) {

            $targetDir = "upload/parent_consent/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($parent_consent['name']);
            $targetFilePath = $targetDir . $fileName;
            
            if (move_uploaded_file($parent_consent['tmp_name'], $targetFilePath)) {
                // Save relative path for easy access
                $parent_consent_url = $settings['site_url']."user/upload/parent_consent/" . $fileName;
            } else {
                // die("<div style='color:red;'>Error uploading file locally.</div>");
                $_SESSION['error'] = 'Error uploading parent consent file locally.';
                header('location: player-verification');
                exit;
            }
        }

        // Team proof if provided
        if (isset($teamProof) && $teamProof['error'] === UPLOAD_ERR_OK) {

            $targetDir = "upload/verifications/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($teamProof['name']);
            $targetFilePath = $targetDir . $fileName;
            
            if (move_uploaded_file($teamProof['tmp_name'], $targetFilePath)) {
                // Save relative path for easy access
                $team_proof_url = $settings['site_url']."user/upload/verifications/" . $fileName;
            } else {
                // die("<div style='color:red;'>Error uploading file locally.</div>");
                $_SESSION['error'] = 'Error uploading team proof file locally.';
                header('location: player-verification');
                exit;
            }
        }

    } catch (ApiError $e) {
        error_log("Upload error: " . $e->getMessage());
        $_SESSION['error'] = "File upload failed. Try again later.";
        header("Location: player-verification");
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
        INSERT INTO player_verifications
        (player_id, official_id_url, team_affiliated, team_proof_url, parent_consent, social_handles, status, review_notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, NOW())
    ");
    $team_aff = isset($_POST['team_affiliated']) && $_POST['team_affiliated'] == '1' ? 1 : 0;
    $stmt->execute([
        $player_id,
        $official_url,
        $team_aff,
        $team_proof_url,
        $parent_consent,
        json_encode($social, JSON_UNESCAPED_SLASHES),
        $notes
    ]);

    // Optional: update user's verified flag removed (still pending)
    $upd = $conn->prepare("UPDATE users SET verified = 3 WHERE id = ?");
    $upd->execute([$user['id']]);

    $_SESSION['success'] = "Verification submitted. An admin will review your documents within 48 hours.";
    header("Location: player-verification");
    exit;

} catch (Exception $e) {
    error_log("DB error (player verification): " . $e->getMessage());
    $_SESSION['error'] = "Could not save verification. Try again later.";
    header("Location: player-verification");
    exit;
}
