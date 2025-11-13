<?php
// update_consistency.php
require_once __DIR__ . '../include/session.php'; // adjust path to your DB connection

$days = 60;                // rolling window
$recency_decay_days = 15;  // recency weighting
$inactivity_penalty = 5;   // 5% per 7 days
$expected_uploads = 4;     // expected uploads per month

// Fetch all players
$players = $conn->query("SELECT id FROM players")->fetchAll(PDO::FETCH_ASSOC);

foreach ($players as $player) {
    $playerId = $player['id'];

    // Fetch all uploads within the time window
    $stmt = $conn->prepare("
        SELECT created_at 
        FROM videos 
        WHERE player_id = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
        ORDER BY created_at ASC
    ");
    $stmt->execute([$playerId]);
    $uploads = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!$uploads) {
        $consistency = 0;
    } else {
        $total_weight = 0;
        $sum_weight = 0;
        $last_date = null;
        $gap_penalty = 0;

        foreach ($uploads as $uploadDate) {
            $days_since = (new DateTime())->diff(new DateTime($uploadDate))->days;
            $weight = exp(-$days_since / $recency_decay_days);
            $total_weight += $weight;
            $sum_weight++;

            if ($last_date) {
                $gap_days = (new DateTime($uploadDate))->diff(new DateTime($last_date))->days;
                $gap_penalty += floor($gap_days / 7) * $inactivity_penalty;
            }

            $last_date = $uploadDate;
        }

        $avg_weight = $total_weight / max($sum_weight, 1);
        $consistency = $avg_weight * 100;
        $consistency -= $gap_penalty;
        $consistency = max(0, min(100, $consistency));
    }

    
    // Update the playerstats table
    $stmt = $conn->prepare("
    UPDATE playerstats 
    SET average_consistency = ? 
    WHERE player_id = ?
    ");
    $stmt->execute([$consistency, $playerId]);
    
}

// Add to cron table in database along with the response from the script
$stmt = $conn->prepare("
    INSERT INTO cronjobs (task_name, created_at) 
    VALUES (?, NOW())
");
$stmt->execute(['Player consistency scores updated']);

echo "✅ Player consistency scores updated successfully on " . date('Y-m-d H:i:s') . PHP_EOL;
