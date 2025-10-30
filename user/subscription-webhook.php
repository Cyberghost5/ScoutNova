<?php
include 'include/session.php';

// Get the raw POST data
$input = @file_get_contents("php://input");
$event = json_decode($input, true);

// Log the received webhook for debugging in api_responses table
$conn = $pdo->open();
$stmt = $conn->prepare("
    INSERT INTO webhooks (webhook, service)
    VALUES (?, ?)
");
$stmt->execute([$input, 'Flutterwave Webhook']);

// Flutterwave signature validation
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';
$secret_hash = $settings['flutterwave_hash']; // set this from your Flutterwave dashboard

if (!$signature || $signature !== $secret_hash) {
    http_response_code(401);
    echo "Invalid signature";
    exit;
}

if (!isset($event['event'])) {
    http_response_code(400);
    echo "Invalid event structure";
    exit;
}

$eventType = $event['event'];

switch ($eventType) {
    case 'charge.completed':
    case 'subscription.charge.completed':
        // ✅ Payment successful or subscription renewed
        $customer_email = $event['data']['customer']['email'] ?? null;
        $plan_id = $event['data']['plan'] ?? null;
        $subscription_id = $event['data']['subscription_id'] ?? null;
        $interval = strtolower($event['data']['plan_object']['interval'] ?? 'monthly');

        // Compute next payment date based on interval
        switch ($interval) {
            case 'hourly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 hour'));
                break;
            case 'daily':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 day'));
                break;
            case 'weekly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 week'));
                break;
            case 'quarterly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+3 months'));
                break;
            case 'yearly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 year'));
                break;
            default:
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 month'));
        }

        // Find the user from the email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$customer_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = $user['id'];

            // Insert or update the subscription record
            $stmt = $conn->prepare("
                INSERT INTO subscriptions (user_id, customer_email, plan_id, subscription_id, status, next_payment_date, updated_at)
                VALUES (?, ?, ?, ?, 'active', ?, NOW())
                ON DUPLICATE KEY UPDATE
                    status='active',
                    next_payment_date=VALUES(next_payment_date),
                    updated_at=NOW()
            ");
            $stmt->execute([$user_id, $customer_email, $plan_id, $subscription_id, $next_payment_date]);

            // ✅ Update user table: mark as subscribed
            $stmt = $conn->prepare("UPDATE users SET is_subscribed = 1, subscription_expires_at = ? WHERE id = ?");
            $stmt->execute([$next_payment_date, $user_id]);
        }

        break;

    case 'subscription.cancelled':
    case 'subscription.expired':
    case 'subscription.not_renewed':
        // ❌ Cancelled, expired or failed renewal
        $subscription_id = $event['data']['id'] ?? null;

        // Update subscription record
        $stmt = $conn->prepare("UPDATE subscriptions SET status = 'inactive', updated_at = NOW() WHERE subscription_id = ?");
        $stmt->execute([$subscription_id]);

        // Fetch the user for this subscription
        $stmt = $conn->prepare("SELECT user_id FROM subscriptions WHERE subscription_id = ? LIMIT 1");
        $stmt->execute([$subscription_id]);
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sub && !empty($sub['user_id'])) {
            // ❌ Update user table: mark as unsubscribed
            $stmt = $conn->prepare("UPDATE users SET is_subscribed = 0 WHERE id = ?");
            $stmt->execute([$sub['user_id']]);
        }

        break;

    default:
        // Ignore other events
        break;
}

http_response_code(200);
echo "Webhook handled successfully";
?>
