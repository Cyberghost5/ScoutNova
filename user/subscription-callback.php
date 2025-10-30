<?php
include 'include/session.php';
$secret_key = $settings['flutterwave_secret_key'];

if (isset($_GET['status'])) {

    // 🔹 Cancelled
    if ($_GET['status'] === 'cancelled') {
        $_SESSION['cancelled'] = 'Payment was cancelled!';
        echo "<script>window.location.assign('subscriptions');</script>";
        exit;
    }

    // 🔹 Successful or Completed
    if (in_array($_GET['status'], ['successful', 'completed'])) {
        $txid = $_GET['tr_ref'] ?? $_GET['transaction_id'];

        // Verify Transaction
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txid}/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$secret_key}",
                "Content-Type: application/json"
            ]
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        
        // Insert into api_responses table for logging
        $stmt = $conn->prepare("
            INSERT INTO api_responses (user_id, response, service)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user['id'], $response, 'Flutterwave Transaction Verification']);

        $res = json_decode($response);


        if ($res && $res->status === 'success' && $res->data->status === 'successful') {

            $amountPaid = $res->data->charged_amount ?? 0;
            $amountToPay = $res->data->meta->price ?? 0;

            if ($amountPaid >= $amountToPay) {

                // ✅ Transaction confirmed
                $plan_id = $res->data->plan ?? null;
                $plan_id_from_db = $res->data->meta->payment_plan_id;
                $customer_email = $res->data->customer->email ?? null;
                $transaction_id = $res->data->id ?? null;

                $user_id = $res->data->meta->user_id ?? $user['id'] ?? null;
                $customer_id = $res->data->customer->id ?? null;

                // Optional: Fetch Subscription Details
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.flutterwave.com/v3/subscriptions?email={$customer_email}",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer {$secret_key}",
                        "Content-Type: application/json"
                    ]
                ]);
                $subResponse = curl_exec($curl);
                curl_close($curl);

                // Insert into api_responses table for logging
                $stmt = $conn->prepare("
                    INSERT INTO api_responses (user_id, response, service)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$user_id, $subResponse, 'Flutterwave Subscription Verification']);

                $subRes = json_decode($subResponse);

                // If a subscription exists for the customer
                if ($subRes && !empty($subRes->data)) {

                    $subscription_id = $subRes->data[0]->id ?? null;
                    $plan_token = $subRes->data[0]->plan->id ?? null;
                    $status = $subRes->data[0]->status ?? 'inactive';
                    $next_payment_date = $subRes->data[0]->next_payment_date ?? null;

                    // Calculate the next payment date
                    if(empty($next_payment_date)){
                        // Get the interval from the plans table
                        $stmt = $conn->prepare("SELECT * FROM plans WHERE id = ?");
                        $stmt->execute([$plan_id_from_db]);
                        $plan = $stmt->fetch();

                        $plan_name = $plan['name'];
                        $plan_interval = $plan['intervals'];

                        switch (strtolower($plan_interval)){
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
                                break;
                        }
                    }

                    // ✅ Store in your database
                    $stmt = $conn->prepare("
                        INSERT INTO subscriptions (user_id, user_email, subscription_id, plan_id, plan_name, status, next_payment_date, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE
                        status = VALUES(status), next_payment_date = VALUES(next_payment_date)
                    ");
                    $stmt->execute([$user_id, $customer_email, $subscription_id, $plan_id, $plan_name, $status, $next_payment_date]);

                    // Update users table too
                    $stmt = $conn->prepare("
                        UPDATE users
                        SET subscription_status = 'active', subscription_plan_id = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$plan_id, $user_id]);

                    $_SESSION['success'] = "Payment successful! Subscription is now active.";
                    echo "<script>window.location.assign('subscriptions');</script>";
                    exit;

                } else {
                    $_SESSION['error'] = "Payment verified, but no active subscription found.";
                    echo "<script>window.location.assign('subscriptions');</script>";
                    exit;
                }

            } else {
                $_SESSION['error'] = '⚠️ Fraudulent transaction detected (amount mismatch)';
                echo "<script>window.location.assign('subscriptions');</script>";
                exit;
            }

        } else {
            $_SESSION['error'] = 'Unable to verify payment with Flutterwave.';
            echo "<script>window.location.assign('subscriptions');</script>";
            exit;
        }
    }

} else {
    $_SESSION['error'] = '⚠️ Invalid payment callback data received.';
    echo "<script>window.location.assign('subscriptions');</script>";
    exit;
}
?>
