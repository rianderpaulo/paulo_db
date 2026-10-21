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