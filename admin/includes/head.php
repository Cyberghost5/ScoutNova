<?php include 'include/session.php'; ?>
<?php
date_default_timezone_set("Africa/Lagos");
$now = date('d F, Y');
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title><?php echo $settings['site_name']; ?> | Admin</title>
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
  <meta property="og:image" content="<?php echo $settings['site_url']; ?>assets/images/logo/logo.png">

  <meta property="twitter:card" content="summary"/>
  <meta property="twitter:title" content="<?php echo $settings['site_name']; ?>"/>
  <meta property="twitter:description" content="<?php echo $settings['site_desc']; ?>">
  <meta property="twitter:url" content="<?php echo $settings['site_url']; ?>"/>
  <meta property="twitter:image" content="<?php echo $settings['site_url']; ?>assets/images/logo/logo.png">

  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $settings['site_url']; ?>assets/images/logo/logo.png">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $settings['site_url']; ?>assets/images/logo/logo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $settings['site_url']; ?>assets/images/logo/logo.png">
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo $settings['site_url']; ?>assets/images/logo/logo.png">
  <meta name="msapplication-TileImage" content="<?php echo $settings['site_url']; ?>assets/images/logo/logo.png">

  <link rel="stylesheet" href="../vendors/feather/feather.css">
  <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="../vendors/datatables.net-bs4/dataTables.bootstrap4.css" type="text/css" >
  <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css" type="text/css" >
  <link rel="stylesheet" href="../js/select.dataTables.min.css" type="text/css" >
  <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css" type="text/css" >
  <link rel="stylesheet" href="../vendors/select2/select2.min.css" type="text/css" >
  <link rel="stylesheet" href="../vendors/select2-bootstrap-theme/select2-bootstrap.min.css" type="text/css" >
  <link rel="stylesheet" href="../vendors/bootstrap-tagsinput/bootstrap-tagsinput.css" type="text/css" >
  <!-- End plugin css for this page -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="../vendors/codemirror/codemirror.css">
  <link rel="stylesheet" href="../vendors/codemirror/ambiance.css">
  <link rel="stylesheet" href="../vendors/pwstabs/jquery.pwstabs.min.css">
  <link rel="stylesheet" href="../vendors/dropzone/dropzone.css">

  <link rel="stylesheet" href="../vendors/quill/quill.snow.css">
  <link rel="stylesheet" href="../vendors/simplemde/simplemde.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="../css/vertical-layout-light/style.css">
</head>
<?php include 'format.php'; ?>
