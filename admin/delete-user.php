<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(!isset($_SESSION['user_id'])){
        { header("Location: ../login.php"); exit; }
    }

    if($_SESSION['role'] != "admin"){
        { header("Location: ../index.php"); exit; }
    }
    include('../config.php');

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = intval($_GET['id']);
        $query = "DELETE FROM users WHERE user_id = $id";
        mysqli_query($conn, $query);
    }

    header('Location: view-all-users.php');
    exit();
?>
