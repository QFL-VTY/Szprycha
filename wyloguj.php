<?php
require 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Użyj formularza wylogowania.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parametry = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $parametry['path'], $parametry['domain'], $parametry['secure'], $parametry['httponly']);
}
session_destroy();
header('Location: login.php');
exit;
