<?php
include 'include/session.php';

    if (isset($_POST['save'])) {
    $curr_password = $_POST['curr_password'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $conn = $pdo->open();
    
    if (password_verify($curr_password, $user['password'])) {
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Password and Confirm Password do not match';
            echo "<script>window.location.assign('settings.php');</script>";
        } else {
            if (password_verify($password, $user['password'])) {
                $new_password = $user['password'];
            } else {
                $new_password = password_hash($password, PASSWORD_DEFAULT);
            }

            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $new_password, 'id' => $user['id']]);
            $_SESSION['success'] = "Password successfully changed";
            echo "<script>window.location.assign('settings.php');</script>";
        }
    } else {
        $_SESSION['error'] = 'Incorrect password';
        echo "<script>window.location.assign('settings.php');</script>";
    }

    $pdo->close();
} else {
    $_SESSION['warning'] = 'No shortcuts, Fill up the form first';
    echo "<script>window.location.assign('settings.php');</script>";
}
