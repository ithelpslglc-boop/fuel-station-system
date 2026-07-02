<?php

require_once ROOT_PATH . '/config/config.php';
session_start();

/**
 * LOGIN USER
 */
function loginUser($email, $password)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 1 LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // update last login time
        $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->execute([$user['id']]);

        return true;
    }

    return false;
}

/**
 * CHECK LOGIN (protect pages)
 */
function checkAuth()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . APP_URL . "/login.php");
        exit;
    }
}

/**
 * LOGOUT USER
 */
function logoutUser()
{
    session_destroy();
    header("Location: " . APP_URL . "/login.php");
    exit;
}