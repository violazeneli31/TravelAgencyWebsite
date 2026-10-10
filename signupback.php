<?php
session_start(); // Always start the session at the top, before any output

include("config.php");
$conn = new mysqli($SERVER, $USERNAME, $PASSWORD, $DATABASE);

if ($conn->connect_errno) {
    die("Database connection failed: " . $conn->connect_error);
}

// Collect form data
$first_name = $_POST['RegisterName'] ?? '';
$last_name = $_POST['RegisterSurname'] ?? '';
$birthday = $_POST['RegisterBirth'] ?? null;
$passport_number = $_POST['RegisterPassport'] ?? '';
$nationality = $_POST['RegisterNationality'] ?? '';
$gender = $_POST['gender'] ?? null;
$email = $_POST['LoginEmail'] ?? '';
$username = $_POST['username'] ?? '';
$phone = $_POST['RegisterPhoneNr'] ?? '';
$password = $_POST['LoginPassword'] ?? '';
$accept_tnc = isset($_POST['AcceptTnC']) ? 1 : 0;

// Validate required fields
if (!$first_name || !$last_name || !$email || !$username || !$password || !$accept_tnc) {
    header("Location: signup-fail.php");
    exit;
}

// Check for duplicate username or email
$check = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    header("Location: signup-fail.php");
    exit;
}
$check->close();

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, birthday, passport_number, gender, nationality, phone, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param(
    "ssssssssss",
    $username,
    $email,
    $password_hash,
    $first_name,
    $last_name,
    $birthday,
    $passport_number,
    $gender,
    $nationality,
    $phone
);

if ($stmt->execute()) {
    $stmt->close();

    // Retrieve inserted user info
    $getUser = $conn->prepare("SELECT user_id, role FROM users WHERE username = ?");
    $getUser->bind_param("s", $username);
    $getUser->execute();
    $getUser->bind_result($user_id, $role);
    if ($getUser->fetch()) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
    }
    $getUser->close();
    $conn->close();

    header("Location: signup-success.php");
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: signup-fail.php");
    exit;
}
?>