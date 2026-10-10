<?php
//session_start();
include 'config.php';

header('Content-Type: application/json');

/*if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}*/

//$user_id = (int)$_SESSION['user_id'];
$user_id = 1;

$stmt = $conn->prepare("SELECT first_name, last_name, birthday, passport_number, gender, nationality FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();

echo json_encode($user);
exit;
?>