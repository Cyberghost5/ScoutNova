<?php
	include 'include/session.php';
	include 'includes/slugify.php';
    include '../vendor/autoload.php';

    $cloudinary_cloud_name = $settings['cloudinary_cloud_name'];
    $cloudinary_api_key = $settings['cloudinary_api_key'];
    $cloudinary_api_secret = $settings['cloudinary_api_secret'];

    use Cloudinary\Cloudinary;
    use Cloudinary\Api\Exception\ApiError;

	if(isset($_POST['add'])){
		$full_link = $_POST['full_link'];
		$detail = $_POST['detail'];
        $player_id = $user['id'];
        $video_id = "#VID_" . uniqid();

		$conn = $pdo->open();

            $upload_type = 'cloudinary';

            $videoUrl = null; // will store the final video path or URL

            // --- CHECK UPLOAD TYPE ---
            if ($upload_type === 'cloudinary') {
                // CLOUDINARY UPLOAD
                $cloudinary = new Cloudinary([
                    "cloud" => [
                        "cloud_name" => $cloudinary_cloud_name,
                        "api_key"    => $cloudinary_api_key,
                        "api_secret" => $cloudinary_api_secret
                    ]
                ]);

                $fileTmpPath = $_FILES['video']['tmp_name'];

                $logFile = __DIR__ . '/cloudinary_log.txt'; // path to your log file

                $uploadResult = $cloudinary->uploadApi()->upload($fileTmpPath, [
                    'resource_type' => 'video'
                ]);

                $stmt = $conn->prepare("
                    INSERT INTO api_responses (user_id, response, service)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$user['id'], json_encode($uploadResult), 'Cloudinary Video Upload']);
                
                // Log success message
                // $message = date('Y-m-d H:i:s') . " ✅ Upload Success: " . json_encode($uploadResult) . PHP_EOL;
                // file_put_contents($logFile, $message, FILE_APPEND);

                $videoUrl = $uploadResult['secure_url'];
                $public_id = $uploadResult['public_id'] ?? null;
                $thumbnailUrl = $uploadResult['public_id'] ?? "https://res.cloudinary.com/$cloudinary_cloud_name/video/upload/so_3,w_300,h_200,c_fill/$public_id.jpg";

            } elseif ($upload_type === 'local') {
                // LOCAL UPLOAD
                $targetDir = "videos/";
                $thumbnailUrl = null;
                $public_id = null;
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['video']['name']);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFilePath)) {
                    // Save relative path for easy access
                    $videoUrl = "videos/" . $fileName;
                } else {
                    // die("<div style='color:red;'>Error uploading file locally.</div>");
                    $_SESSION['error'] = 'Error uploading file locally.';
                    header('location: videos');
                    exit;
                }
            }

            // --- SAVE TO DATABASE ---
            if ($videoUrl) {

                // Send email notification to admin (optional)

                $stmt = $conn->prepare("INSERT INTO videos (video_id, full_link, description, file_url, player_id, upload_type, thumbnail_url, cloudinary_public_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$video_id, $full_link, $detail, $videoUrl, $player_id, $upload_type, $thumbnailUrl, $public_id]);
                // echo "<div style='color: green;'>✅ Upload successful! Video: <a href='$videoUrl' target='_blank'>$videoUrl</a></ div>";
                $_SESSION['success'] = 'Video added successfully';
                header('location: videos');
                exit;
            }

			// $filename = $_FILES['image']['name'];
			// if(!empty($filename)){
			// 	$ext = pathinfo($filename, PATHINFO_EXTENSION);
			// 	$new_filename = $link.'_'.time().'.'.$ext;
			// 	move_uploaded_file($_FILES['image']['tmp_name'], '../img/bg-img/'.$new_filename);
			// }
			// try{
			// 	$stmt = $conn->prepare("INSERT INTO events (title, venue, time, datetime, detail, postedby, link, file) VALUES (:title, :venue, :time, :datetime, :detail, :postedby, :link, :image)");
			// 	$stmt->execute(['title'=>$title, 'venue'=>$venue, 'time'=>$time, 'datetime'=>$datetime, 'detail'=>$detail, 'postedby'=>$postedby, 'link'=>$link, 'image'=>$new_filename]);
			// 	$_SESSION['success'] = 'Event added successfully';

			// }
			// catch(PDOException $e){
			// 	$_SESSION['error'] = $e->getMessage();
			// }

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up form first';
	}

	header('location: videos');

?>
