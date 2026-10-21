<?php
session_start();
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Please fill in all fields.";
    } elseif (strlen($username) > 50) {
        $message = "Username must not exceed 50 characters.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ?"
        );
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Username already exists.";
        } else {
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (username, password) VALUES (?, ?)"
            );
            $stmt->bind_param(
                "ss",
                $username,
                $hashed_password
            );

            if ($stmt->execute()) {
                $message = "Registration successful! You can now log in.";
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            max-width: calc(100% - 40px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 15px;
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Account</h2>

    <form method="POST" action="register.php">
        <input
            type="text"
            name="username"
            placeholder="Choose a username"
            maxlength="50"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Choose a password (8+ characters)"
            minlength="8"
            required
        >

        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm password"
            minlength="8"
            required
        >

        <button type="submit">Register</button>
    </form>

    <p class="message">
        <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
    </p>

    <p class="link">
        Already have an account?
        <a href="login.php">Log in</a>
    </p>
</div>

</body>
</html>