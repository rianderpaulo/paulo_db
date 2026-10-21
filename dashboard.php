<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$username = htmlspecialchars(
    (string) ($_SESSION["username"] ?? "there"),
    ENT_QUOTES | ENT_SUBSTITUTE,
    "UTF-8"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <main class="dashboard-container">
        <h2>Dashboard</h2>
        <p class="dashboard-welcome">Welcome, <?= $username ?>!</p>
        <p class="dashboard-message">You are logged in.</p>
        <form action="logout.php" method="POST">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>"
            >
            <button class="logout-button" type="submit">Log out</button>
        </form>
    </main>
</body>
</html>
