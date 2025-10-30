<?php
	include 'include/session.php';
	$flutterwave_secret_key = $settings['flutterwave_secret_key'];

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$intervals = $_POST['intervals'];
		$details = $_POST['details'];
		$name = $_POST['name'];
		$plan_id = $_POST['plan_id'];
		$amount = $_POST['amount'];
		$currency = $_POST['currency'];

		$conn = $pdo->open();
		$stmt = $conn->prepare("SELECT * FROM plans WHERE id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();

		if(!$row){
			$_SESSION['error'] = 'Plan not found';
			header('location: plans');
			exit();
		}

		$curl = curl_init();

		$data = [
			"name" => $name. " " .$settings['site_name']. " " .$intervals." Plan",
			"currency" => $currency,
			"amount" => $amount,
			"interval" => lcfirst($intervals), // can be: daily, weekly, monthly, quarterly, yearly
			"duration" => 0, // 0 = indefinite (until cancelled)
			"status" => "active"
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://api.flutterwave.com/v3/payment-plans/".$plan_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => "PUT",
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

		// try{
		// 	$stmt = $conn->prepare("UPDATE plans SET name=:name, plan_id=:plan_id, intervals=:intervals, details=:details, amount=:amount, currency=:currency WHERE id=:id");
		// 	$stmt->execute(['name'=>$name, 'plan_id'=>$plan_id, 'intervals'=>$intervals, 'details'=>$details, 'amount'=>$amount, 'currency'=>$currency, 'id'=>$id]);
		// 	$_SESSION['success'] = 'Plan updated successfully';

		// }
		// catch(PDOException $e){
		// 	$_SESSION['error'] = $e->getMessage();
		// }


		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit admin form first';
	}

	// header('location: plans');

?>
