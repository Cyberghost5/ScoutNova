<?php
include 'include/conn.php';
session_start();

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
  $ip_address = $_SERVER['HTTP_CLIENT_IP'];
}elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
  $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
}else {
  $ip_address = $_SERVER['REMOTE_ADDR'];
}

$device_info = $_SERVER['HTTP_USER_AGENT'];
$today = date('Y-m-d');



if (isset($_SESSION['admin'])) {
  header('location: admin/home');
}

 ?>
