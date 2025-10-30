<?php
date_default_timezone_set("Africa/Lagos");
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
  <link rel="manifest" href="<?php echo $settings['site_url']; ?>assets/img/favicons/manifest.php">
  <meta name="msapplication-TileImage" content="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>">
  <link href="css/theme.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- <link href="vendors/fonts-awesome/css/font-awesome.min.css" rel="stylesheet" /> -->
  <script src="https://kit.fontawesome.com/2cfbc63d1f.js" crossorigin="anonymous"></script>
    <!-- <link rel="manifest" href="../manifest.json"> -->
    <!-- jquery file ----->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    
<!-- iziToast section -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">

    <link rel="stylesheet" href="sweetalert/sweetalert.css">
  <!-- endinject -->
  <!-- inject:css -->
  <?php if(isset($_COOKIE['mode']) == 'dark'): ?>
  <!--<link rel="stylesheet" href="<?php echo $settings['site_url']; ?>css/vertical-layout-dark/style.css" type="text/css">-->
  <?php else: ?>
  <!--<link rel="stylesheet" href="<?php echo $settings['site_url']; ?>css/vertical-layout-light/style.css" type="text/css">-->
  <?php endif; ?>
</head>
<?php include 'format.php'; 
// if($user['verification'] == 0){
//     echo "<script>window.location.assign('kyc')</script>";
// }
?>
