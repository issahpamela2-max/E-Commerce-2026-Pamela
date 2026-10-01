<?php
session_start();
date_default_timezone_set('Africa/Accra');

// Works out the app's base URL automatically, local or live
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$pos = strpos($script, '/shoppn');
define('BASE_URL', $pos !== false ? substr($script, 0, $pos) . '/shoppn' : '');

require_once __DIR__ . '/db_class.php';

// Get the visitor's IP address (used for guest carts later)
function get_ip() {
    return $_SERVER['REMOTE_ADDR'];
}

// Send the browser to another page and stop
function redirect($url) {
    header("Location: $url");
    exit;
}

// Is someone logged in?
function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

// Is the logged-in user an admin?
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

// Block the page if nobody is logged in
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in to continue.';
        redirect(BASE_URL . '/views/login.php');
    }
}

// Block the page if the user is not an admin
function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to view that page.';
        redirect(BASE_URL . '/index.php');
    }
}