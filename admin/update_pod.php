<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/phpmailer/src/Exception.php';
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';

include 'include/session.php';

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

header('Content-Type: application/json');

try {
    $player_id = $_POST['player_id'];
    $video_id = $_POST['video_id'];
    $video_uuid = $_POST['video_uuid'];
    $category = $_POST['category'];
    $source = $_POST['source'];

    // Capture rating breakdown
    $rating_breakdown = json_encode([
        "stamina" => (float)$_POST['stamina'],
        "passing" => (float)$_POST['passing'],
        "speed"   => (float)$_POST['speed'],
        "agility" => (float)$_POST['agility']
    ], JSON_UNESCAPED_SLASHES);

    $ai_confidence = $_POST['ai_confidence'] ?? null;

    $breakdown = json_decode($rating_breakdown, true);
    $total_score = array_sum($breakdown) / count($breakdown);

    // ✅ Check if this video already has a rating
    $stmt = $conn->prepare("SELECT * FROM podratings WHERE player_id = ? AND video_id = ?");
    $stmt->execute([$player_id, $video_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    // function calculateConsistency($conn, $player_id, $days = 30, $expectedUploads = 4) {
    //     $stmt = $conn->prepare("
    //         SELECT COUNT(*) AS total 
    //         FROM videos 
    //         WHERE player_id = ? 
    //         AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    //     ");
    //     $stmt->execute([$player_id, $days]);
    //     $count = $stmt->fetchColumn();

    //     $consistency_index = min(100, ($count / $expectedUploads) * 100);

    //     return round($consistency_index, 2);
    // }

    // $consistency_index = calculateConsistency($conn, $player_id);

    function calculateSmartConsistency($conn, $player_id, $days = 60) {
        // Fetch recent uploads by category
        $stmt = $conn->prepare("
            SELECT DATE(created_at) AS upload_date
            FROM videos 
            WHERE player_id = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            ORDER BY created_at ASC
        ");
        $stmt->execute([$player_id, $days]);
        $uploads = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($uploads)) {
            return 0; // No uploads = zero consistency
        }

        $total_weight = 0;
        $weighted_sum = 0;
        $last_upload_date = null;
        $gap_penalty = 0;

        foreach ($uploads as $upload_date) {
            $days_since = (time() - strtotime($upload_date)) / 86400;
            $weight = exp(-$days_since / 15); // exponential decay, 15-day half-life
            $weighted_sum += $weight;
            $total_weight += 1;

            // Check inactivity gap
            if ($last_upload_date) {
                $gap_days = (strtotime($upload_date) - strtotime($last_upload_date)) / 86400;
                if ($gap_days > 7) {
                    $gap_penalty += floor($gap_days / 7) * 5; // 5% penalty per week gap
                }
            }
            $last_upload_date = $upload_date;
        }

        // Normalize score
        $consistency = ($weighted_sum / $total_weight) * 100;

        // Apply penalties
        $consistency -= $gap_penalty;
        $consistency = max(0, min(100, $consistency)); // clamp between 0 and 100

        return round($consistency, 2);
    }

    $consistency_index = calculateSmartConsistency($conn, $player_id);

    if ($existing) {
        // Update existing record
        $history = json_decode($existing['historical_data'], true) ?? [];
        $history[date('Y-m-d H:i:s')] = $total_score;

        // Compute consistency index
        $scores = array_values($history);
        $mean = array_sum($scores) / count($scores);
        $variance = array_sum(array_map(fn($s) => pow($s - $mean, 2), $scores)) / count($scores);
        // $consistency_index = 100 - min(100, sqrt($variance) * 10);

        $update = $conn->prepare("
            UPDATE podratings 
            SET total_score=?, consistency_index=?, rating_breakdown=?, category=?, ai_confidence=?, source=?, historical_data=?, updated_at=NOW()
            WHERE id=?
        ");
        $update->execute([
            $total_score,
            $consistency_index,
            $rating_breakdown,
            $category,
            $ai_confidence,
            $source,
            json_encode($history),
            $existing['id']
        ]);
    } else {
        // Insert new record
        $history = [date('Y-m-d H:i:s') => $total_score];
        $insert = $conn->prepare("
            INSERT INTO podratings 
            (player_id, video_id, total_score, video_count, consistency_index, rating_breakdown, category, ai_confidence, source, historical_data, created_at, updated_at)
            VALUES (?, ?, ?, 1, 100, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $insert->execute([
            $player_id,
            $video_id,
            $total_score,
            $rating_breakdown,
            $category,
            $ai_confidence,
            $source,
            json_encode($history)
        ]);
    }

    // 🔁 Update playerstats (aggregate)
    $stats = $conn->prepare("
        SELECT 
            AVG(total_score) AS avg_score, 
            AVG(consistency_index) AS avg_consistency, 
            COUNT(*) AS total_videos 
        FROM podratings 
        WHERE player_id = ?
    ");
    $stats->execute([$player_id]);
    $summary = $stats->fetch(PDO::FETCH_ASSOC);

    // Compute best skill overall
    $skills = ['stamina' => 0, 'passing' => 0, 'speed' => 0, 'agility' => 0];
    $skillQuery = $conn->prepare("SELECT rating_breakdown FROM podratings WHERE player_id = ?");
    $skillQuery->execute([$player_id]);

    while ($row = $skillQuery->fetch(PDO::FETCH_ASSOC)) {
        $data = json_decode($row['rating_breakdown'], true);
        foreach ($skills as $k => $v) {
            $skills[$k] += $data[$k] ?? 0;
        }
    }

    $best_skill = array_keys($skills, max($skills))[0];

    // ✅ Calculate POD (Performance on Demand)
    // Simple formula: average of average_score and average_consistency
    $pod_score = ($summary['avg_score'] + $summary['avg_consistency']) / 2;

    // Check if playerstats exists
    $check = $conn->prepare("SELECT * FROM playerstats WHERE player_id = ?");
    $check->execute([$player_id]);

    if ($check->fetch()) {
        $updateStats = $conn->prepare("
            UPDATE playerstats 
            SET total_videos=?, 
                average_score=?, 
                average_consistency=?, 
                best_skill=?, 
                pod_score=?, 
                updated_at=NOW() 
            WHERE player_id=?
        ");
        $updateStats->execute([
            $summary['total_videos'],
            $summary['avg_score'],
            $summary['avg_consistency'],
            $best_skill,
            $pod_score,
            $player_id
        ]);
    } else {
        $insertStats = $conn->prepare("
            INSERT INTO playerstats 
            (player_id, total_videos, average_score, average_consistency, best_skill, pod_score, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $insertStats->execute([
            $player_id,
            $summary['total_videos'],
            $summary['avg_score'],
            $summary['avg_consistency'],
            $best_skill,
            $pod_score
        ]);
    }

    // ✅ Update video status
    $updateVideo = $conn->prepare("UPDATE videos SET status = 1, ai_score = ?, updated_at = NOW() WHERE id = ?");
    $updateVideo->execute([$total_score, $video_id]);

    // Send notification email
    $userStmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $userStmt->execute([$player_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    $player_email = $user ? $user['email'] : null; 
    $consistency_index_new = $consistency_index ?? 100;

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

        // Add recipient - assuming $player_email is defined in the calling context
        $mail->addAddress($player_email);
        $mail->addAddress($email_to, $settings['site_name']);
        $mail->addReplyTo($email_reply, $settings['site_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $settings['site_name']. ' POD Rating Updated';
        
        // Get message content from external file
        ob_start();
        include 'emails/podrating_notification_email.php';
        $mail->Body = ob_get_clean();
        
        // $mail->Body    = "
        //     <h3>Your pod rating has been updated.</h3>
        //     <p>Total Score: {$total_score}</p>
        //     <p>Consistency Index: {$consistency_index}</p>
        //     <p>Category: {$category}</p>
        //     <p>AI Confidence: {$ai_confidence}</p>
        //     <br>
        //     <p>Thank you for using our service!</p>
        // ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        $_SESSION['error'] = 'Notification email could not be sent. ' .$mail->ErrorInfo;
    }

    $_SESSION['success'] = "POD rating updated successfully.";
    header('Location: video/' . $video_uuid);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit();
}
?>
