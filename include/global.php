<?php
// For production environment
define('DB_HOSTNAMEDNS', 'mysql:host=localhost;dbname=trustscr_scoutnova;charset=utf8');
define('DB_USERNAME', 'trustscr_scoutnova');
define('DB_PASSWORD', 'trustscr_scoutnova');

// // For local development, you might want to use the following settings, uncomment when needed
// define('DB_HOSTNAMEDNS', 'mysql:host=localhost;dbname=nova;charset=utf8');
// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', '');

function generateHexUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

 ?>
