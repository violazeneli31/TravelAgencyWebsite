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
    include '../config.php';

    // Get the booking ID from the URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $booking_id = $_GET['id'];

        // Delete the booking from the database
        $query = "DELETE FROM bookings WHERE booking_id = $booking_id";
        if (mysqli_query($conn, $query)) {
            // Redirect to the bookings list after deletion
            header("Location: admin-viewBookings.php");
            exit();
        } else {
            echo "Error deleting booking: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid booking ID.";
    }
?>