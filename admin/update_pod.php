<?php
include 'include/session.php';
header('Content-Type: application/json');

try {
    $player_id = $_POST['player_id'];
    $video_id = $_POST['video_id'];
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

    if ($existing) {
        // Update existing record
        $history = json_decode($existing['historical_data'], true) ?? [];
        $history[date('Y-m-d H:i:s')] = $total_score;

        // Compute consistency index
        $scores = array_values($history);
        $mean = array_sum($scores) / count($scores);
        $variance = array_sum(array_map(fn($s) => pow($s - $mean, 2), $scores)) / count($scores);
        $consistency_index = 100 - min(100, sqrt($variance) * 10);

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

    // 🔁 Update PlayerStats (aggregate)
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

    // Check if PlayerStats exists
    $check = $conn->prepare("SELECT * FROM PlayerStats WHERE player_id = ?");
    $check->execute([$player_id]);

    if ($check->fetch()) {
        $updateStats = $conn->prepare("
            UPDATE PlayerStats 
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
            INSERT INTO PlayerStats 
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

    $_SESSION['success'] = "POD rating updated successfully.";
    header('Location: video?id=' . $video_id);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit();
}
?>
