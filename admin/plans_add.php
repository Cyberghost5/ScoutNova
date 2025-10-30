<?php
	include 'include/session.php';
	$flutterwave_secret_key = $settings['flutterwave_secret_key'];

	if(isset($_POST['add'])){
		$intervals = $_POST['intervals'];
		$details = $_POST['details'];
		$name = $_POST['name'];
		$plan_id = $_POST['plan_id'];
		$amount = $_POST['amount'];
		$currency = $_POST['currency'];

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM plans WHERE plan_id=:plan_id");
		$stmt->execute(['plan_id'=>$plan_id]);
		$row = $stmt->fetch();

		$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM plans WHERE name=:name");
		$stmt->execute(['name'=>$name]);
		$iow = $stmt->fetch();

		if($row['numrows'] > 0){
			$_SESSION['error'] = 'Plan ID already taken';
		}elseif ($iow['numrows'] > 0) {
			$_SESSION['error'] = 'Plan name already taken';
		}
		else{

			$curl = curl_init();

			$data = [
				"name" => $name. " " .$settings['site_name']. " " .$intervals." Plan",
				"currency" => $currency,
				"amount" => $amount,
				"interval" => lcfirst($intervals), // can be: daily, weekly, monthly, quarterly, yearly
				"duration" => 0 // 0 = indefinite (until cancelled)
			];

			curl_setopt_array($curl, [
			CURLOPT_URL => "https://api.flutterwave.com/v3/payment-plans",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => [
				"Authorization: Bearer $flutterwave_secret_key",
				"Content-Type: application/json"
			],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);

			if ($err) {
				echo "cURL Error #: " . $err;
			} else {
				$result = json_decode($response, true);
				print_r($response);
			}

			if($result['status'] == 'success'){
				$plan_id = $result['data']['id'];
				try{
					$stmt = $conn->prepare("INSERT INTO plans (name, plan_id, amount, currency, intervals, details) VALUES (:name, :plan_id, :amount, :currency, :intervals, :details)");
					$stmt->execute(['name'=>$name, 'plan_id'=>$plan_id, 'amount'=>$amount, 'currency'=>$currency, 'intervals'=>$intervals, 'details'=>$details]);
					$_SESSION['success'] = 'Plan added successfully';

				}
				catch(PDOException $e){
					$_SESSION['error'] = $e->getMessage();
				}
			}
			else{
				$_SESSION['error'] = 'Flutterwave API Error: '.$result['message'];
			}
			
			// try{
			// 	$stmt = $conn->prepare("INSERT INTO plans (name, plan_id, amount, currency, intervals, details) VALUES (:name, :plan_id, :amount, :currency, :intervals, :details)");
			// 	$stmt->execute(['name'=>$name, 'plan_id'=>$plan_id, 'amount'=>$amount, 'currency'=>$currency, 'intervals'=>$interval, 'details'=>$details]);
			// 	$_SESSION['success'] = 'Plan added successfully';

			// }
			// catch(PDOException $e){
			// 	$_SESSION['error'] = $e->getMessage();
			// }
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up admin form first';
	}

	header('location: plans');

?>
