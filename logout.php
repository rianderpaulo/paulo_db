<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . (isset($_SESSION["user_id"]) ? "dashboard.php" : "login.php"));
    exit;
}

$submittedToken = $_POST["csrf_token"] ?? "";
$sessionToken = $_SESSION["csrf_token"] ?? "";

if (
    !is_string($submittedToken)
    || !is_string($sessionToken)
    || $sessionToken === ""
    || !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(400);
    exit("Invalid request. Please return to the dashboard and try again.");
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $cookie = session_get_cookie_params();
    setcookie(
        session_name(),
        "",
        time() - 42000,
        $cookie["path"],
        $cookie["domain"],
        $cookie["secure"],
        $cookie["httponly"]
    );
}

session_destroy();
header("Location: login.php");
exit;