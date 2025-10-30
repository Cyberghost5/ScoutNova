<?php
include 'include/session.php';
include '../vendor/autoload.php';

$cloudinary_cloud_name = $settings['cloudinary_cloud_name'];
$cloudinary_api_key = $settings['cloudinary_api_key'];
$cloudinary_api_secret = $settings['cloudinary_api_secret'];

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;

// Configure Cloudinary SDK
Configuration::instance([
    "cloud" => [
        "cloud_name" => $cloudinary_cloud_name,
        "api_key"    => $cloudinary_api_key,
        "api_secret" => $cloudinary_api_secret
    ],
    "url" => ["secure" => true]
]);

header('Content-Type: application/json');

try {
    $video_id = $_POST['video_id'];
    $stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $delete = isset($_POST['delete']) && $_POST['delete'] == 1;
    
    if (!$video_id) {
        echo json_encode(["error" => "Missing video ID."]);
        $_SESSION['error'] = "Missing video ID.";
        exit;
    }
    
    if ($delete) {
        
        if ($video['upload_type'] === 'cloudinary') {
            if ($video && $video['cloudinary_public_id']) {
                $publicId = $video['cloudinary_public_id'];
                
                try {
                    $api = new AdminApi();
                    $api->deleteAssets([$publicId], ['resource_type' => 'video']);
                    
                    // Then delete record from DB
                    $del = $conn->prepare("DELETE FROM videos WHERE id = ?");
                    $del->execute([$video_id]);
                    
                    echo json_encode(["success" => true, "message" => "Video deleted from Cloudinary and database."]);
                    $_SESSION['success'] = "Video deleted from Cloudinary and database.";
                } catch (Exception $e) {
                    echo json_encode(["error" => $e->getMessage()]);
                    $_SESSION['error'] = $e->getMessage();
                }
            } else {
                echo json_encode(["error" => "Video not found or missing Cloudinary public_id."]);
                $_SESSION['error'] = "Video not found or missing Cloudinary public_id.";
            }
        } elseif ($video['upload_type'] === 'manual') {
            if (!empty($video['file_path']) && file_exists($video['file_path'])) {
                unlink($video['file_path']); // delete from server
                
                $del = $conn->prepare("DELETE FROM videos WHERE id = ?");
                $del->execute([$video_id]);
                
                echo json_encode(["success" => true, "message" => "Video deleted from Cloudinary and database."]);
                $_SESSION['success'] = "Video deleted from Cloudinary and database.";
            }
        }
    } else {
        // Just mark as rejected
        $stmt = $conn->prepare("UPDATE videos SET status = 2, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$video_id]);
        echo json_encode(["success" => true, "message" => "Video rejected successfully."]);
        $_SESSION['success'] = "Video rejected successfully.";
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
    $_SESSION['error'] = $e->getMessage();
}
?>
