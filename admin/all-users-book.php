<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        { header("Location: ../login.php"); exit; }
    }

    if($_SESSION['role'] != "admin"){
        { header("Location: ../index.php"); exit; }
    }
    $base_dir = __DIR__ . '/../'; 
    $static_url = '/Travosy/assets';
    include '../config.php';

    ob_start();
    $navlink_content = ob_get_clean();
    $page = 'nav2';

    ob_start();
    include '../config.php';

    // Get the booking ID
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $booking_id = $_GET['id'];
    } else {
        header("Location: admin-viewBookings.php");
        exit();
    }

    // Fetch booking details
    $query = "SELECT b.*, u.first_name, u.last_name, u.email, p.name AS package_name, p.description AS package_description
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.user_id
            LEFT JOIN packages p ON b.package_id = p.package_id
            WHERE b.booking_id = $booking_id";
    $result = mysqli_query($conn, $query);
    $booking = mysqli_fetch_assoc($result);

    if (!$booking) {
        echo "Booking not found!";
        exit();
    }

    // Fetch other travellers
    $travellers_query = "SELECT * FROM other_travellers WHERE booking_id = $booking_id";
    $travellers_result = mysqli_query($conn, $travellers_query);

    // Handle delete
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
        $delete_query = "DELETE FROM bookings WHERE booking_id = $booking_id";
        if (mysqli_query($conn, $delete_query)) {
            header("Location: admin/admin-viewBookings.php");
            exit();
        } else {
            echo "Error deleting booking: " . mysqli_error($conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        .container { width: 80%; margin: auto; padding: 20px; }

        .detail-box {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .detail-box h3 { font-size: 24px; margin-bottom: 15px; }

        .label { font-weight: bold; margin-right: 10px; }

        .delete-button {
            padding: 12px 24px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .delete-button:hover { background-color: darkred; }

        .button {
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            display: inline-block;
        }

        .button:hover { background-color: #0056b3; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        table th {
            background-color: #f8f8f8;
        }
    </style>
</head>
<body id="body">

<div class="container">
    <div class="detail-box">
        <h3>Booking Information</h3>
        <p><span class="label">Booking ID:</span> <?= $booking['booking_id'] ?></p>
        <p><span class="label">User Name:</span> <?= $booking['first_name'] . ' ' . $booking['last_name'] ?></p>
        <p><span class="label">User Email:</span> <?= $booking['email'] ?></p>
        <p><span class="label">Package Name:</span> <?= $booking['package_name'] ?></p>
        <p><span class="label">Package Description:</span> <?= $booking['package_description'] ?></p>
        <p><span class="label">Booking Date:</span> <?= date('d-m-Y', strtotime($booking['date_booked'])) ?></p>
    </div>

    <div class="detail-box">
        <h3>Traveler Details</h3>
        <p><span class="label">Adults:</span> <?= $booking['nr_adults'] ?></p>
        <p><span class="label">Children:</span> <?= $booking['nr_children'] ?></p>
        <p><span class="label">Total Price:</span> $<?= number_format($booking['personalised_price'], 2) ?></p>
    </div>

    <div class="detail-box">
        <h3>Other Travellers Information</h3>
        <?php if (mysqli_num_rows($travellers_result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Birthday</th>
                        <th>Nationality</th>
                        <th>Passport No</th>
                        <th>Type</th>
                        <th>Gender</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($traveller = mysqli_fetch_assoc($travellers_result)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $traveller['name'] . ' ' . $traveller['surname'] ?></td>
                            <td><?= $traveller['birthday'] ? date('d-m-Y', strtotime($traveller['birthday'])) : '-' ?></td>
                            <td><?= $traveller['nationality'] ?></td>
                            <td><?= $traveller['passport_number'] ?></td>
                            <td><?= ucfirst($traveller['traveller_type']) ?></td>
                            <td><?= ucfirst($traveller['gender']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No other travellers found for this booking.</p>
        <?php endif; ?>
    </div>

    <form action="view-booking-details.php?id=<?= $booking['booking_id'] ?>" method="POST">
        <input type="submit" name="delete" value="Delete Booking" class="delete-button" onclick="return confirm('Are you sure you want to delete this booking?');">
    </form>

    <div class="text-center">
        <a href="admin-viewBookings.php" class="button">Back to Bookings</a>
    </div>
</div>

</body>
</html>

<?php
$hero_content = ob_get_clean();
include $base_dir . 'Base/style/base.php';
?>
