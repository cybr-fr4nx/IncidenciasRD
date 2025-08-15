<?php
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function is_validator()
{
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'validador';
}

function require_login()
{
    global $basePath;
    if (!is_logged_in()) {
        header('Location: ' . $basePath . '/login.php');
        exit();
    }
}

function require_validator()
{
    global $basePath;
    if (!is_validator()) {
        header('Location: ' . $basePath . '/super/login.php');
        exit();
    }
}
