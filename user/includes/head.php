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
  <link rel="stylesheet" href="<?php echo $settings['site_url']; ?>css/vertical-layout-light/style.css" type="text/css">
  <!-- Include SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>
<?php include 'format.php'; 
// if($user['verification'] == 0){
//     echo "<script>window.location.assign('kyc')</script>";
// }
?>
