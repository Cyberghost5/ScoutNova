<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php
date_default_timezone_set("Africa/Lagos");
$now = date('d F, Y');
?>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title><?php echo $settings['site_name']; ?> | User</title>
  <meta name="description" content="<?php echo $settings['site_desc']; ?>">
  <meta name="keyword" content="<?php echo $settings['site_keyword']; ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="theme-color" content="<?php echo $settings['theme']; ?>">
  <meta name="msapplication-navbutton-color" content="<?php echo $settings['theme']; ?>">
  <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo $settings['theme']; ?>">
  <meta name="language" content="English">
  <meta name="revisit-after" content="1 days">
  <meta name="author" content="Adebisi Covenant">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?php echo $settings['site_url']; ?>" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta property="og:title" content="<?php echo $settings['site_name']; ?>"/>
  <meta property="og:locale" content="en_US"/>
  <meta property="og:url" content="<?php echo $settings['site_url']; ?>"/>
  <meta property="og:type" content="website"/>
  <meta property="og:description" content="<?php echo $settings['site_desc']; ?>">
  <meta property="og:image" content="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">

  <meta property="twitter:card" content="summary"/>
  <meta property="twitter:title" content="<?php echo $settings['site_name']; ?>"/>
  <meta property="twitter:description" content="<?php echo $settings['site_desc']; ?>">
  <meta property="twitter:url" content="<?php echo $settings['site_url']; ?>"/>
  <meta property="twitter:image" content="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">

  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">
  <meta name="msapplication-TileImage" content="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">

  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/feather/feather.css">
  <!--Christmas lights-->
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/datatables.net-bs4/dataTables.bootstrap4.css" type="text/css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/ti-icons/css/themify-icons.css" type="text/css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>js/select.dataTables.min.css" type="text/css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/mdi/css/materialdesignicons.min.css" type="text/css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/select2/select2.min.css" type="text/css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/select2-bootstrap-theme/select2-bootstrap.min.css" type="text/css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/bootstrap-tagsinput/bootstrap-tagsinput.css" type="text/css">
  <!-- End plugin css for this page -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/codemirror/codemirror.css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/codemirror/ambiance.css">
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>vendors/pwstabs/jquery.pwstabs.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <?php if(isset($_COOKIE['mode']) == 'dark'): ?>
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>css/vertical-layout-dark/style.css" type="text/css">
  <?php else: ?>
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>css/vertical-layout-light/style.css" type="text/css">
  <?php endif; ?>
</head>
<?php include 'includes/format.php';?>

<body class="sidebar-dark">
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php include 'includes/navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <?php include 'includes/settings.php'; ?>
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <?php include 'includes/sidebar.php'; ?>
      <!-- partial -->
      <div class="main-panel" id="rand_0903">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">KYC</h3>
                  <h6 class="font-weight-normal mb-0">Complete your KYC Verification!</h6>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <?php
                if(isset($_SESSION['error'])){
                  echo "
                    <div class='alert alert-danger alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-close'></i>Error!</h4>
                      ".$_SESSION['error']."
                    </div>
                  ";
                  unset($_SESSION['error']);
                }
                if(isset($_SESSION['success'])){
                  echo "
                    <div class='alert alert-success alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-check'></i> Success!</h4>
                      ".$_SESSION['success']."
                    </div>
                  ";
                  unset($_SESSION['success']);
                }
                if(isset($_SESSION['cancelled'])){
                  echo "
                    <div class='alert alert-secondary alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-delete-forever'></i> Cancelled!</h4>
                      ".$_SESSION['cancelled']."
                    </div>
                  ";
                  unset($_SESSION['cancelled']);
                }
              ?>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">KYC (Know Your Customer) Section</h4>
                  <?php if($user['verification'] == 0): ?>
                  <p class="display-4">Welcome to <?php echo $settings['site_name']; ?> KYC System.</p>
                  <p>
                    The Central Bank of Nigeria, released a circular recently, regarding virtual accounts issued to customers, as follows:

                    <ul>
                        <li>Every virtual account must be linked with either a Bank Verification Number (BVN) or National Identification Number (NIN).</li>
                        <li>For accounts that wish to enjoy the maximum transaction limit, both BVN and NIN must be linked to the Account Number.</li>
                    </ul>
                  </p>
                  <p>   
                    Following this update, we would be requiring you to provide either your BVN or NIN. Failure to do so would lead to your <?php echo $settings['site_name']; ?> virtual accounts rejecting 
                    funds.
                  </p>
                  <p>Account reference - <?php echo $accountref.' '.$refactive; ?></p>
                  <div class="form-group row mb-0">
                     <label class="col-sm-6 col-form-label"><h3>Verification Type *</h3></label>
                     <div class="col-sm-3">
                       <div class="form-check">
                         <label class="form-check-label">
                           <input type="radio" class="form-check-input" onclick="bvnVerification();" name="verifytype" value="BVN" id="verifytype" required>
                           BVN (this will cost 20 Naira)
                         </label>
                       </div>
                     </div>
                      <div class="col-sm-3">
                       <div class="form-check">
                         <label class="form-check-label">
                           <input type="radio" class="form-check-input" onclick="ninVerification();" name="verifytype" value="NIN" id="verifytype" required>
                           NIN (this will cost 80 Naira)
                         </label>
                       </div>
                     </div>
                   </div>
                   
                  <form id="kyc-form" class="bvn_verify" action="kyc_action" method="POST" enctype="multipart/form-data" style="display:none;">
                    <div>
                        <input type="hidden" name="bvn_verify" value="BVN" required>
                      
                      <h3>BVN</h3>
                      <section>
                        <h3>BVN Verification</h3>
                        <div class="form-group">
                          <label for="bvn">BVN *</label>
                          <input id="bvn" name="bvn" type="number" class="form-control" placeholder="Enter BVN" required>
                        </div>
                        
                        <div class="form-group">
                          <label for="main_bank_name" class="control-label">Bank Name *</label>
                          <select class="form-control w-100" name="bank" id="bank1" required>
        
                            <?php
                            $user_bank_name = $user['main_bank_name'];
        
                            $secret_key = $settings["monnify_sk"];
                            $monnify_api = $settings["monnify_api"];
                            $token1 = base64_encode($monnify_api . ":" . $secret_key);
                            
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
                            
                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => "https://api.monnify.com/api/v1/banks",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET",
                                CURLOPT_HTTPHEADER => [
                                    "Content-Type: application/json",
                                    "Authorization: Bearer {$real_token}",
                                ],
                            ]);
        
                            $response = curl_exec($curl);
        
                            curl_close($curl);
        
                            $res = json_decode($response);
        
                            ?>
        
                            <option value="">Select</option>
                            <?php
                            foreach($res->responseBody as $value) {
                              if($user_bank_name === $value->name){
                                  $selected = 'Selected';
                              }
                              else{
                                $selected = '';
                              }
                              echo "<option value='$value->name|$value->code' $selected>$value->name</option>";
                            }
                            ?>
        
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="main_account_number123" class="control-label">Account No. *</label>
                          <input type="number" id="main_account_number123" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10" value="<?php echo $user['main_account_number']; ?>" name="account_number" placeholder="Account Number" required>
                          <span id="tey"></span>
                        </div>
                        
                        <!--<div class="form-group">-->
                        <!--  <label for="dob">DOB *</label>-->
                        <!--  <input id="dob" name="dob" type="date" class="form-control" placeholder="Enter DOB" required>-->
                        <!--</div>-->
                        
                        <!--<div class="form-group">-->
                        <!--  <label for="phone">Phone No. *</label>-->
                        <!--  <input id="phone" name="phone" type="number" value="<?php echo $user['contact_info']; ?>" class="form-control" placeholder="Enter Phone No." required>-->
                        <!--</div>-->
                      </section>
                      
                      <h3>Finish</h3>
                      <section>
                        <h3>Finish</h3>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="checkbox" type="checkbox">
                            I agree with the Terms and Conditions.
                          </label>
                        </div>
                      </section>
                      
                    </div>
                  </form>
                  
                  <form id="kyc-form1" class="nin_verify" action="kyc_action" method="POST" enctype="multipart/form-data" style="display:none;">
                    <div>
                        <input type="hidden" name="nin_verify" value="Business Type" required>
                        
                      <h3>NIN</h3>
                      <section>
                        <h3>NIN Verification</h3>
                        <div class="form-group">
                          <label for="nin">NIN *</label>
                          <input id="nin" name="nin" type="number" class="form-control" placeholder="Enter NIN" required>
                        </div>
                      </section>
                      
                      
                      <h3>Finish</h3>
                      <section>
                        <h3>Finish</h3>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="checkbox" type="checkbox">
                            I agree with the Terms and Conditions.
                          </label>
                        </div>
                      </section>
                      
                    </div>
                  </form>
                  
                  <?php elseif($user['verification'] == 2): ?>
                  
                  <div class='alert alert-info alert-dismissible'>
                  <!--<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>-->
                  <h4><i class='icon mdi mdi-check'></i> Pending!</h4>
                  Your KYC is currently in review, you'll receive an email upon completion.
                  </div>
                  
                  <?php elseif($user['verification'] == 3): ?>
                  
                  <div class='alert alert-danger alert-dismissible'>
                  <!--<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>-->
                  <h4><i class='icon mdi mdi-alert'></i> Danger!</h4>
                  Your KYC was rejected. Try again by using your correct BVN/NIN Details.
                  </div>
                  
                  <p class="display-4">Welcome to <?php echo $settings['site_name']; ?> KYC System. Do you wish to use BVN or NIN?</p>
                  <div class="form-group row mb-0">
                     <label class="col-sm-6 col-form-label">Verication Type *</label>
                     <div class="col-sm-3">
                       <div class="form-check">
                         <label class="form-check-label">
                           <input type="radio" class="form-check-input" onclick="bvnVerification();" name="verifytype" value="Indivudual" id="verifytype" required>
                           BVN
                         </label>
                       </div>
                     </div>
                     <div class="col-sm-3">
                       <div class="form-check">
                         <label class="form-check-label">
                           <input type="radio" class="form-check-input" onclick="ninVerification();" name="verifytype" value="Business" id="verifytype" required>
                           NIN
                         </label>
                       </div>
                     </div>
                   </div>
                   
                  <form id="kyc-form" class="bvn_verify" action="kyc_action" method="POST" enctype="multipart/form-data" style="display:none;">
                    <div>
                        <input type="hidden" name="bvn_verify" value="Individual Type" required>
                      
                      <h3>BVN Veification</h3>
                      <section>
                        <h3>BVN Verification</h3>
                        <div class="form-group">
                          <label for="bvn">BVN *</label>
                          <input id="bvn" name="bvn" type="number" class="form-control" placeholder="Enter BVN" required>
                        </div>
                        
                        <div class="form-group">
                          <label for="dob">DOB *</label>
                          <input id="dob" name="dob" type="date" class="form-control" placeholder="Enter DOB" required>
                        </div>
                        
                        <div class="form-group">
                          <label for="phone">Phone No. *</label>
                          <input id="phone" name="phone" type="number" value="<?php echo $user['contact_info']; ?>" class="form-control" placeholder="Enter Phone No." required>
                        </div>
                      </section>
                      
                      <h3>Finish</h3>
                      <section>
                        <h3>Finish</h3>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="checkbox" type="checkbox">
                            I agree with the Terms and Conditions.
                          </label>
                        </div>
                      </section>
                      
                    </div>
                  </form>
                  
                  <form id="kyc-form1" class="nin_verify" action="kyc_action" method="POST" enctype="multipart/form-data" style="display:none;">
                    <div>
                        <input type="hidden" name="nin_verify" value="Business Type" required>
                        
                      <h3>NIN Veification</h3>
                      <section>
                        <h3>NIN Verification</h3>
                        <div class="form-group">
                          <label for="nin">NIN *</label>
                          <input id="nin" name="nin" type="number" class="form-control" placeholder="Enter NIN" required>
                        </div>
                      </section>
                      
                      
                      <h3>Finish</h3>
                      <section>
                        <h3>Finish</h3>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="checkbox" type="checkbox">
                            I agree with the Terms and Conditions.
                          </label>
                        </div>
                      </section>
                      
                    </div>
                  </form>
                  
                  <?php else: ?>
                  
                  <div class='alert alert-success alert-dismissible'>
                  <!--<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>-->
                  <h4><i class='icon mdi mdi-check'></i> Success!</h4>
                  You have Already completed your KYC Verification. 
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <?php include 'includes/footer.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <?php include 'includes/scripts.php'; ?>
  <script src="<?php echo $settings['site_url']; ?>vendors/jquery-steps/jquery.steps.min.js"></script>
  <script src="<?php echo $settings['site_url']; ?>vendors/jquery-validation/jquery.validate.min.js"></script>
  <script src="<?php echo $settings['site_url']; ?>js/wizard.js"></script>
  <script>
    
    let bvn_verify = document.querySelector('.bvn_verify');
    let nin_verify = document.querySelector('.nin_verify');
    
    let registered_business123 = document.getElementById('rand_0903'); 
    
    console.log("This " + bvn_verify);

    function ninVerification(){
      nin_verify.style.display = "block";
      bvn_verify.style.display = "none";
    }
    
    function bvnVerification(){
      nin_verify.style.display = "none";
      bvn_verify.style.display = "block";
    }
    
    
  
    let registered_business = document.getElementById('registered_business');

    function registeredBusiness(){
      registered_business.style.display = "block";
    }
    
    function starterBusiness(){
      registered_business.style.display = "none";
    }
  </script>
<script type="text/javascript">
// Function to handle AJAX request
function makeAJAXRequest152(data) {
  // Your AJAX logic here
  console.log('AJAX request triggered with data:', data);
  // Example: Send an AJAX request using jQuery
  $.ajax({
    url: 'apis/validate-account-number-ft.php',
    method: 'POST',
    data: data,
    success: function(response) {
      console.log('AJAX request successful!');
      var content = JSON.parse(response);
      console.log(content);
      if(content.status == 'success'){
        document.getElementById('name_on_account152').value = content.data.account_name;
        document.getElementById("tey152").innerHTML = "";
      }else {
        document.getElementById("tey152").innerHTML = "<span class='text-danger'>Incorrect Details</span>";
      } 
      //document.getElementById("tey152").innerHTML = "";
      // Handle the response here
    },
    error: function(xhr, status, error) {
      console.error('AJAX request failed:', error);
      // Handle the error here
      document.getElementById("tey152").innerHTML = "<span class='text-danger'>An error occured</span>";
    }
  });
}

// Function to handle input change event
function handleInputChange152(event) {
  const inputValue = event.target.value;
  const inputLength = inputValue.length;
  
  const account_bank = document.getElementById('bank152').value;
  const account_number = document.getElementById('main_account_number152').value;

  if (inputLength === 10) {
      const data = {
      account_bank: account_bank,
      account_number: account_number
    };
    makeAJAXRequest152(data);
    // hideError(); // Hide the error message if it was previously shown
    document.getElementById("tey152").innerHTML = "Resolving account <i class='mdi mdi-refresh mdi-spin mt-4 menu-icon'> </i>";
    // console.log(account_bank);
    // console.log(account_number);
  } else {
    // showError(); // Show the error message
    document.getElementById("tey152").innerHTML = "";
  }
}

// Attach input change event listener
const inputElement152 = document.getElementById('main_account_number152');
// inputElement152.addEventListener('input', handleInputChange152);
</script>
</body>

</html>
