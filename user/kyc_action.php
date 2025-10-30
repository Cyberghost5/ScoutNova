<?php
include "include/session.php";
$secret_key = $settings["monnify_sk"];
$monnify_api = $settings["monnify_api"];
$token1 = base64_encode($monnify_api . ":" . $secret_key);
$user_balance = $user['balance'];
$type = 'KYC Charge';
$trx_ref = "TRX".time();
if (isset($_POST["bvn_verify"])) {
    $userid = $user["id"];
    $bvn = $_POST["bvn"];
    // $htmlDate = $_POST["dob"];
    $bank = $_POST['bank'];
		
	$bank_explode = explode('|', $bank);
	$main_bank_name = $bank_explode[0];
	$main_bank_code = $bank_explode[1];
    $bankCode = $main_bank_code;
    $accountNumber = $_POST['account_number'];
    
    // $date = new DateTime($htmlDate);
    // $dob = $date->format('d-M-Y');
    // $user_fullname = $user['firstname'].' '.$user['lastname'];
    // $user_phone = $_POST['phone'];
    $enc_bvn = base64_encode($bvn);

    $stmt = $conn->prepare("SELECT * FROM users_account WHERE userid=:userid");
    $stmt->execute(["userid" => $user["id"]]);
    $account_details = $stmt->fetch();

    $res = json_decode($account_details["account_details"]);

    $accountss = $res->responseBody->accounts;
    foreach ($res->responseBody->accounts as $row) {
        if ($row->bankName === "Wema bank") {
            $wema_aza = $row->accountNumber;
        }
        if ($row->bankName === "Sterling bank") {
            $sterling_aza = $row->accountNumber;
        }
    }

    $stmt = $conn->prepare("SELECT * FROM monnify1 WHERE Wemabank=:Wemabank OR Sterlingbank=:Sterlingbank OR Customeremail=:email");
    $stmt->execute(["Wemabank" => $wema_aza, "Sterlingbank" => $sterling_aza, "email" => $user["email"]]);
    $new_aza_ref = $stmt->fetch();

    $accountref = $new_aza_ref["AccountReference"];

    if ($accountref === null || $accountref === "_") {
        $_SESSION["error"] = "Account reference does not exist, kindly reach out to admin.";
        echo "<script>window.location.assign('kyc')</script>";
    } else {
        $final_balance = $user_balance - 20;
        if($user_balance < 20){
            $_SESSION['error'] = "Insufficient Funds for this transaction, kindly <a href='atm-funding'>fund</a> your wallet and try again.";
            echo "<script>window.location.assign('kyc')</script>";
        }else{
            $verification = 1; // 1 is auto, 2 is manual
    
            $conn = $pdo->open();
            
            $prev = $user['balance'];
        
            $stmt = $conn->prepare("UPDATE users SET balance=:final_balance WHERE id=:id");
            $stmt->execute(['final_balance'=>$final_balance, 'id'=>$userid]);
            
            $curr = $final_balance;
            
            $stmt = $conn->prepare("INSERT INTO transaction_all (userid, trxid, amount, prev, curr, type, status) VALUES (:userid, :trxid, :amount, :prev, :curr, :type, :status)");
            $stmt->execute(['userid'=>$user['id'], 'trxid'=>$trx_ref, 'amount'=>20, 'prev'=>$prev, 'curr'=>$curr, 'type'=>$type, 'status'=>1]);
    
            try {
                $request = [
                    "bvn" => $bvn,
                ];
                
                $requestverify = [
                    "bvn" => $bvn,
                    "bankCode" => $bankCode,
                    "accountNumber" => $accountNumber
                    // "name" => $user_fullname,
                    // "dateOfBirth" => $dob,
                    // "mobileNo" => $user_phone
                ];
    
                $curl = curl_init();
                curl_setopt_array($curl, [
                    // CURLOPT_URL => 'https://api.flutterwave.com/v3/payments', // Live
                    CURLOPT_URL => "https://api.monnify.com/api/v1/auth/login", // Test
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Basic {$token1}",
                        "Content-Type: application/json",
                    ],
                ]);
    
                $init_response = curl_exec($curl);
    
                curl_close($curl);
    
                // echo "<pre>";
                // echo $init_response;
                // echo "</pre>";
    
                $init_res = json_decode($init_response);
                $real_token = $init_res->responseBody->accessToken;
    
                // Verify BVN details
                $curl = curl_init();
                curl_setopt_array($curl, [
                    // CURLOPT_URL => "https://api.monnify.com/api/v1/vas/bvn-details-match",
                    CURLOPT_URL => "https://api.monnify.com/api/v1/vas/bvn-account-match",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($requestverify),
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Authorization: Bearer {$real_token}",
                    ],
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
    
                // echo "<pre>";
                // echo $response;
                // echo "</pre>";
                
                $api = "BVN Account Match";
                
                $stmt = $conn->prepare("INSERT INTO api_response (body, userid, signature, api) VALUES (:body, :userid, :signature, :api)");
                $stmt->execute(['body'=>$response, 'userid'=>$user['id'], 'signature'=>$type, 'api'=>$api]);
                    
                $pathToFile = 'api_response.log';
                    
                file_put_contents($pathToFile, $response, FILE_APPEND);
                
                $res = json_decode($response);
                $resmessage1 = $res->responseMessage;
                $name_status = $res->responseBody->matchStatus;
                // $dob_status = $res->responseBody->dateOfBirth;
                // $phone_status = $res->responseBody->mobileNo;
                if ($res->requestSuccessful) {
                    // BVN maches
                    if($name_status === 'FULL_MATCH' || $name_status === 'PARTIAL_MATCH'){
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => "https://api.monnify.com/api/v1/bank-transfer/reserved-accounts/$accountref/kyc-info",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "PUT",
                            CURLOPT_POSTFIELDS => json_encode($request),
                            CURLOPT_HTTPHEADER => [
                                "Content-Type: application/json",
                                "Authorization: Bearer {$real_token}",
                            ],
                        ]);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        
                        $api = "BVN KYC Info";
                
                        $stmt = $conn->prepare("INSERT INTO api_response (body, userid, signature, api) VALUES (:body, :userid, :signature, :api)");
                        $stmt->execute(['body'=>$response, 'userid'=>$user['id'], 'signature'=>$type, 'api'=>$api]);
                            
                        $pathToFile = 'api_response.log';
                            
                        file_put_contents($pathToFile, $response, FILE_APPEND);
            
                        // echo "<pre>";
                        // echo $response;
                        // echo "</pre>";
            
                        $res = json_decode($response);
                        $resmessage = $res->responseMessage;
                        if ($res->requestSuccessful) {
                            $stmt = $conn->prepare("UPDATE users SET verification=:verification, bvn=:bvn WHERE id=:id");
                            $stmt->execute(["verification" => $verification, "bvn" => $enc_bvn, "id" => $userid]);
            
                            $_SESSION["success"] = "KYC verifed successfully";
                            echo "<script>window.location.assign('kyc')</script>";
                        } else {
                            $_SESSION["error"] = "An error occured! [BVN Update Records] ".$resmessage;
                            echo "<script>window.location.assign('kyc')</script>";
                        }
                        
                    }else{
                        $_SESSION["error"] = "BVN details don't match!";
                        echo "<script>window.location.assign('kyc')</script>";
                    }
                    
                    
                } else {
                    $_SESSION["error"] = "An error occured! [BVN verification] ".$resmessage1;
                    echo "<script>window.location.assign('kyc')</script>";
                }
                
            } catch (PDOException $e) {
                $_SESSION["error"] = $e->getMessage();
                echo "<script>window.location.assign('kyc')</script>";
            }
        }
    }

    $pdo->close();
} 

elseif (isset($_POST["nin_verify"])) {
    $userid = $user["id"];
    $nin = $_POST["nin"];
    $enc_nin = base64_encode($nin);

    $stmt = $conn->prepare("SELECT * FROM users_account WHERE userid=:userid");
    $stmt->execute(["userid" => $user["id"]]);
    $account_details = $stmt->fetch();

    $res = json_decode($account_details["account_details"]);

    $accountss = $res->responseBody->accounts;
    foreach ($res->responseBody->accounts as $row) {
        if ($row->bankName === "Wema bank") {
            $wema_aza = $row->accountNumber;
        }
        if ($row->bankName === "Sterling bank") {
            $sterling_aza = $row->accountNumber;
        }
    }

    $stmt = $conn->prepare("SELECT * FROM monnify1 WHERE Wemabank=:Wemabank OR Sterlingbank=:Sterlingbank OR Customeremail=:email");
    $stmt->execute(["Wemabank" => $wema_aza, "Sterlingbank" => $sterling_aza, "email" => $user["email"]]);
    $new_aza_ref = $stmt->fetch();

    $accountref = $new_aza_ref["AccountReference"];

    if ($accountref === null || $accountref === "_") {
        $_SESSION["error"] = "Account reference does not exist, kindly reach out to admin.";
        echo "<script>window.location.assign('kyc')</script>";
    } else {
        $final_balance = $user_balance - 80;
        if($user_balance < 80){
            $_SESSION['error'] = "Insufficient Funds for this transaction, kindly <a href='atm-funding'>fund</a> your wallet and try again.";
            echo "<script>window.location.assign('kyc')</script>";
        }else{
            $verification = 1; // 1 is auto, 2 is manual
            $conn = $pdo->open();
            
            $prev = $user['balance'];
        
            $stmt = $conn->prepare("UPDATE users SET balance=:final_balance WHERE id=:id");
            $stmt->execute(['final_balance'=>$final_balance, 'id'=>$userid]);
            
            $curr = $final_balance;
            
            $stmt = $conn->prepare("INSERT INTO transaction_all (userid, trxid, amount, prev, curr, type, status) VALUES (:userid, :trxid, :amount, :prev, :curr, :type, :status)");
            $stmt->execute(['userid'=>$user['id'], 'trxid'=>$trx_ref, 'amount'=>80, 'prev'=>$prev, 'curr'=>$curr, 'type'=>$type, 'status'=>1]);
    
            try {
                $request = [
                    "nin" => $nin,
                ];
    
                $curl = curl_init();
                curl_setopt_array($curl, [
                    // CURLOPT_URL => 'https://api.flutterwave.com/v3/payments', // Live
                    CURLOPT_URL => "https://api.monnify.com/api/v1/auth/login", // Test
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Basic {$token1}",
                        "Content-Type: application/json",
                    ],
                ]);
    
                $init_response = curl_exec($curl);
    
                curl_close($curl);
    
                // echo "<pre>";
                // echo $init_response;
                // echo "</pre>";
    
                $init_res = json_decode($init_response);
                $real_token = $init_res->responseBody->accessToken;
    
                // Verify NIN details
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.monnify.com/api/v1/vas/nin-details",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($request),
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Authorization: Bearer {$real_token}",
                    ],
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
    
                // echo "<pre>";
                // echo $response;
                // echo "</pre>";
                
                $api = "NIN Account Match";
                
                $stmt = $conn->prepare("INSERT INTO api_response (body, userid, signature, api) VALUES (:body, :userid, :signature, :api)");
                $stmt->execute(['body'=>$response, 'userid'=>$user['id'], 'signature'=>$type, 'api'=>$api]);
                
                $pathToFile = 'api_response.log';
                
                file_put_contents($pathToFile, $response, FILE_APPEND);
    
                $res = json_decode($response);
                $respond_firstname = strtolower($res->responseBody->firstName);
                $respond_lastname = strtolower($res->responseBody->lastName);
                
                $user_firstname = strtolower($user['firstname']);
                $user_lastname = strtolower($user['lastname']);
                if ($res->requestSuccessful) {
                    // NIN maches
                    if($respond_firstname === $user_firstname || $respond_firstname === $user_lastname || $respond_lastname === $user_firstname || $respond_lastname === $user_lastname){
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => "https://api.monnify.com/api/v1/bank-transfer/reserved-accounts/$accountref/kyc-info",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "PUT",
                            CURLOPT_POSTFIELDS => json_encode($request),
                            CURLOPT_HTTPHEADER => [
                                "Content-Type: application/json",
                                "Authorization: Bearer {$real_token}",
                            ],
                        ]);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        
                        $api = "NIN KYC Info";
                
                        $stmt = $conn->prepare("INSERT INTO api_response (body, userid, signature, api) VALUES (:body, :userid, :signature, :api)");
                        $stmt->execute(['body'=>$response, 'userid'=>$user['id'], 'signature'=>$type, 'api'=>$api]);
                            
                        $pathToFile = 'api_response.log';
                            
                        file_put_contents($pathToFile, $response, FILE_APPEND);
            
                        // echo "<pre>";
                        // echo $response;
                        // echo "</pre>";
            
                        $res = json_decode($response);
                        if ($res->requestSuccessful) {
                            $stmt = $conn->prepare("UPDATE users SET verification=:verification, nin=:nin WHERE id=:id");
                            $stmt->execute(["verification" => $verification, "nin" => $enc_nin, "id" => $userid]);
            
                            $_SESSION["success"] = "KYC verified successfully";
                            echo "<script>window.location.assign('kyc')</script>";
                        } else {
                            $_SESSION["error"] = "An error occured!";
                            echo "<script>window.location.assign('kyc')</script>";
                        }
                        
                    }else{
                        $_SESSION["error"] = "NIN details don't match with your details!";
                        echo "<script>window.location.assign('kyc')</script>";
                    }
                    
                    
                } else {
                    $_SESSION["error"] = "An error occured!";
                    echo "<script>window.location.assign('kyc')</script>";
                }
                
            } catch (PDOException $e) {
                $_SESSION["error"] = $e->getMessage();
                echo "<script>window.location.assign('kyc')</script>";
            }
        }
    }

    $pdo->close();
} else {
    $_SESSION["error"] = "Fill up required details first";
    echo "<script>window.location.assign('kyc')</script>";
}

// echo $_SESSION["error"];
// echo $_SESSION["success"];
?>