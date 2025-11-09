<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require '../vendor/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/src/SMTP.php';

	include 'include/session.php';
	include 'includes/slugify.php';
    include '../vendor/autoload.php';

    $cloudinary_cloud_name = $settings['cloudinary_cloud_name'];
    $cloudinary_api_key = $settings['cloudinary_api_key'];
    $cloudinary_api_secret = $settings['cloudinary_api_secret'];

    use Cloudinary\Cloudinary;
    use Cloudinary\Api\Exception\ApiError;

    // Confirm user subscription status, and if the user is not subscribed, and  number of videos uploaded exceeds allowed limit, block upload.
    $conn = $pdo->open();
    $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user['id']]);
    $subscription = $stmt->fetch();

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

    if (!$subscription) {
        // No active subscription, check number of uploaded videos
        $videoCountStmt = $conn->prepare("SELECT COUNT(*) AS video_count FROM videos WHERE player_id = ?");
        $videoCountStmt->execute([$user['id']]);
        $videoCountResult = $videoCountStmt->fetch();
        $uploadedVideos = $videoCountResult ? $videoCountResult['video_count'] : 0;

        $freeUploadLimit = 50; // Set free upload limit

        if ($uploadedVideos >= $freeUploadLimit) {
            $_SESSION['error'] = 'You have reached the free upload limit. Please subscribe to upload more videos.';
            header('location: videos');
            exit;
        }
    }

	if(isset($_POST['add'])){
		$full_link = $_POST['full_link'];
		$detail = $_POST['detail'];
        $player_id = $user['id'];
        $video_id = "#VID_" . uniqid();

        $uuid = generateHexUUID();

		$conn = $pdo->open();

            $upload_type = $settings['upload_type']; //'local';

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
                    'folder' => "scoutnova/users/videos",
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
                $targetDir = "upload/videos/";
                $thumbnailUrl = null;
                $public_id = null;
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['video']['name']);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFilePath)) {
                    // Save relative path for easy access
                    $videoUrl = $settings['site_url']."user/upload/videos/" . $fileName;
                } else {
                    // die("<div style='color:red;'>Error uploading file locally.</div>");
                    $_SESSION['error'] = 'Error uploading file locally.';
                    header('location: videos');
                    exit;
                }
            }

            // --- SAVE TO DATABASE ---
            if ($videoUrl) {

                $stmt = $conn->prepare("INSERT INTO videos (uuid, video_id, full_link, description, file_url, player_id, upload_type, thumbnail_url, cloudinary_public_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$uuid, $video_id, $full_link, $detail, $videoUrl, $player_id, $upload_type, $thumbnailUrl, $public_id]);
                // echo "<div style='color: green;'>✅ Upload successful! Video: <a href='$videoUrl' target='_blank'>$videoUrl</a></ div>";

                // Send email to admin
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
                    $mail->Subject = $settings['site_name']. ' New Video Uploaded';
                    
                    ob_start();
                    include 'emails/video_upload_notification_email.php';
                    $mail->Body = ob_get_clean();
                    
                    // $mail->Body    = "
                    //     <h3>A new video has been uploaded.</h3>
                    //     <p>Video URL: <a href='$videoUrl'>$videoUrl</a></p>
                    //     <br>
                    //     <p>Thank you for using our service!</p>
                    // ";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                    $_SESSION['error'] = 'Notification email could not be sent. ' .$mail->ErrorInfo;
                }

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
