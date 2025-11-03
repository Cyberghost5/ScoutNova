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
        // ✅ Payment successful or subscription renewed
        $customer_email = $event['data']['customer']['email'] ?? null;
        $plan_id = $event['meta_data']['payment_plan'] ?? null;
        $subscription_id = $event['data']['id'] ?? null;

        $stmt = $conn->prepare("SELECT * FROM plans WHERE plan_id = ?");
        $stmt->execute([$plan_id]);
        $plan = $stmt->fetch();

        $plan_name = $plan['name'];
        $plan_interval = lcfirst($plan['intervals']);

        // Compute next payment date based on interval
        switch ($plan_interval) {
            case 'hourly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 hour'));
                break;
            case 'daily':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 day'));
                break;
            case 'weekly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 week'));
                break;
            case 'monthly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 month'));
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
                INSERT INTO subscriptions (user_id, user_email, plan_id, plan_name, subscription_id, status, next_payment_date, updated_at)
                VALUES (?, ?, ?, ?, ?, 'active', ?, NOW())
                ON DUPLICATE KEY UPDATE
                    status='active',
                    next_payment_date=VALUES(next_payment_date),
                    updated_at=NOW()
            ");
            $stmt->execute([$user_id, $customer_email, $plan_id, $plan_name, $subscription_id, $next_payment_date]);

            // ✅ Update user table: mark as subscribed
            $stmt = $conn->prepare("UPDATE users SET subscription_status = 'active', subscription_plan_id = ? WHERE id = ?");
            $stmt->execute([$plan_id, $user_id]);

            if($user['role'] == 'user'){
                // Update players table if user is a player
                $stmt = $conn->prepare("UPDATE players SET subscription_status = 'active', subscription_plan_id = ?, featured = 1 WHERE user_id = ?");
                $stmt->execute([$plan_id, $user_id]);
            }elseif($user['role'] == 'agent'){
                // Update agents table if user is a coach
                $stmt = $conn->prepare("UPDATE agent_profiles SET subscription_status = 'active', subscription_plan_id = ? WHERE user_id = ?");
                $stmt->execute([$plan_id, $user_id]);
            }

            // Insert into transactions table
            $stmt = $conn->prepare("
                INSERT INTO transactions (user_id, amount, currency, payment_method, transaction_type, transaction_id, status, created_at)
                VALUES (?, ?, ?, ?, 'Subscription Payment', ?, '1', NOW())
            ");
            $amount = $event['data']['amount'] ?? 0;
            $currency = $event['data']['currency'] ?? 'USD';
            $payment_method = $event['data']['payment_type'] ?? 'unknown';
            $transaction_id = $event['data']['tx_ref'] ?? '';
            $stmt->execute([$user_id, $amount, $currency, $payment_method, $transaction_id]);

        }

        break;
    case 'subscription.charge.completed':
        // ✅ Payment successful or subscription renewed
        $customer_email = $event['data']['customer']['email'] ?? null;
        $plan_id = $event['meta_data']['payment_plan'] ?? null;
        $subscription_id = $event['data']['id'] ?? null;

        $stmt = $conn->prepare("SELECT * FROM plans WHERE plan_id = ?");
        $stmt->execute([$plan_id]);
        $plan = $stmt->fetch();

        $plan_name = $plan['name'];
        $plan_interval = lcfirst($plan['intervals']);

        // Compute next payment date based on interval
        switch ($plan_interval) {
            case 'hourly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 hour'));
                break;
            case 'daily':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 day'));
                break;
            case 'weekly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 week'));
                break;
            case 'monthly':
                $next_payment_date = date('Y-m-d H:i:s', strtotime('+1 month'));
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
                INSERT INTO subscriptions (user_id, user_email, plan_id, plan_name, subscription_id, status, next_payment_date, updated_at)
                VALUES (?, ?, ?, ?, ?, 'active', ?, NOW())
                ON DUPLICATE KEY UPDATE
                    status='active',
                    next_payment_date=VALUES(next_payment_date),
                    updated_at=NOW()
            ");
            $stmt->execute([$user_id, $customer_email, $plan_id, $plan_name, $subscription_id, $next_payment_date]);

            // ✅ Update user table: mark as subscribed
            $stmt = $conn->prepare("UPDATE users SET subscription_status = 'active', subscription_plan_id = ? WHERE id = ?");
            $stmt->execute([$plan_id, $user_id]);

            if($user['role'] == 'user'){
                // Update players table if user is a player
                $stmt = $conn->prepare("UPDATE players SET subscription_status = 'active', subscription_plan_id = ?, featured = 1 WHERE user_id = ?");
                $stmt->execute([$plan_id, $user_id]);
            }elseif($user['role'] == 'agent'){
                // Update agents table if user is a coach
                $stmt = $conn->prepare("UPDATE agent_profiles SET subscription_status = 'active', subscription_plan_id = ? WHERE user_id = ?");
                $stmt->execute([$plan_id, $user_id]);
            }

            // Insert into transactions table
            $stmt = $conn->prepare("
                INSERT INTO transactions (user_id, amount, currency, payment_method, transaction_type, transaction_id, status, created_at)
                VALUES (?, ?, ?, ?, 'Subscription Payment', ?, '1', NOW())
            ");
            $amount = $event['data']['amount'] ?? 0;
            $currency = $event['data']['currency'] ?? 'USD';
            $payment_method = $event['data']['payment_type'] ?? 'unknown';
            $transaction_id = $event['data']['tx_ref'] ?? '';
            $stmt->execute([$user_id, $amount, $currency, $payment_method, $transaction_id]);
        }

        break;

    case 'subscription.cancelled':
        // ❌ Cancelled, expired or failed renewal
        $subscription_id = $event['data']['id'] ?? null;

        // Update subscription record
        $stmt = $conn->prepare("UPDATE subscriptions SET status = 'cancelled', updated_at = NOW() WHERE subscription_id = ?");
        $stmt->execute([$subscription_id]);

        // Fetch the user for this subscription
        $stmt = $conn->prepare("SELECT user_id FROM subscriptions WHERE subscription_id = ? LIMIT 1");
        $stmt->execute([$subscription_id]);
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sub && !empty($sub['user_id'])) {
            // ❌ Update user table: mark as unsubscribed
            $stmt = $conn->prepare("UPDATE users SET subscription_status = 'cancelled', subscription_plan_id = NULL WHERE id = ?");
            $stmt->execute([$sub['user_id']]);

            // Update players table if user is a player
            $stmt = $conn->prepare("UPDATE players SET subscription_status = 'cancelled', subscription_plan_id = NULL, featured = 0 WHERE user_id = ?");
            $stmt->execute([$sub['user_id']]);
        }
        // echo "Handled subscription.cancelled";

        break;
    case 'subscription.expired':
        // ❌ Cancelled, expired or failed renewal
        $subscription_id = $event['data']['id'] ?? null;

        // Update subscription record
        $stmt = $conn->prepare("UPDATE subscriptions SET status = 'expired', updated_at = NOW() WHERE subscription_id = ?");
        $stmt->execute([$subscription_id]);

        // Fetch the user for this subscription
        $stmt = $conn->prepare("SELECT user_id FROM subscriptions WHERE subscription_id = ? LIMIT 1");
        $stmt->execute([$subscription_id]);
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sub && !empty($sub['user_id'])) {
            // ❌ Update user table: mark as unsubscribed
            $stmt = $conn->prepare("UPDATE users SET subscription_status = 'expired', subscription_plan_id = NULL WHERE id = ?");
            $stmt->execute([$sub['user_id']]);

            // Update players table if user is a player
            $stmt = $conn->prepare("UPDATE players SET subscription_status = 'expired', subscription_plan_id = NULL, featured = 0 WHERE user_id = ?");
            $stmt->execute([$sub['user_id']]);
        }

        break;
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
            $stmt = $conn->prepare("UPDATE users SET subscription_status = 'inactive', subscription_plan_id = NULL WHERE id = ?");
            $stmt->execute([$sub['user_id']]);

            // Update players table if user is a player
            $stmt = $conn->prepare("UPDATE players SET subscription_status = 'inactive', subscription_plan_id = NULL WHERE user_id = ?");
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
