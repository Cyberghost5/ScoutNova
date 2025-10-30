<?php
	include 'include/session.php';
	$flutterwave_secret_key = $settings['flutterwave_secret_key'];

	if(isset($_POST['delete'])){
		$id = $_POST['id'];

		$conn = $pdo->open();

		try{

			$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => "https://api.flutterwave.com/v3/payment-plans/".$id."/cancel",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "PUT",
				CURLOPT_HTTPHEADER => [
					"Authorization: Bearer $flutterwave_secret_key",
					"Content-Type: application/json",
					"accept: application/json"
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

			if(!$result['status'] == 'success'){
				$_SESSION['error'] = 'Failed to delete plan from Flutterwave';
				header('location: plans');
				exit();
			}

			if($result['status'] == 'success'){

				$stmt = $conn->prepare("DELETE FROM plans WHERE plan_id=:id");
				$stmt->execute(['id'=>$id]);

				$_SESSION['success'] = 'Plan deleted successfully';
				header('location: plans');
				exit();
			}
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Select Plan to delete first';
	}

	header('location: plans');

?>
