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

// Get the booking ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $booking_id = $_GET['id'];
} else {
    header("Location: admin-viewBookings.php");
    exit();
}

// Fetch booking details from the database
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
$traveller_query = "SELECT * FROM other_travellers WHERE booking_id = $booking_id";
$traveller_result = mysqli_query($conn, $traveller_query);

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    // Delete the booking from the database
    $delete_query = "DELETE FROM bookings WHERE booking_id = $booking_id";
    if (mysqli_query($conn, $delete_query)) {
        // Redirect back to the bookings list after successful deletion
        header("Location: admin-viewBookings.php");
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
        /* Enhance the table styling and layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        
        .heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .heading h2 {
            font-size: 36px;
            font-weight: bold;
        }

        .heading p {
            font-size: 16px;
            color: #6c757d;
        }

        .detail-box {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .detail-box h3 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .detail-box p {
            font-size: 16px;
            line-height: 1.6;
        }

        .detail-box .label {
            font-weight: bold;
            margin-right: 10px;
        }

        .button {
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        /* Delete Button */
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

        .delete-button:hover {
            background-color: darkred;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        table th {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body class="bg-white" id="body">

<!-- Header Section -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover"
             style="background-image: url('<?php echo $static_url; ?>/images/bg/cta.jpg');"></div>
    </div>

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex justify-center">
            <!-- Main Content Section (Now full width) -->
            <div class="w-full md:w-4/5 px-3 mt-6 md:mt-0">
                <!-- Main Card -->
                <div class="p-8 rounded-xl shadow-xl dark:shadow-gray-800 bg-white dark:bg-slate-900 transition-all duration-300">
                    <!-- Title and Description -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-black-800 dark:text-black mb-2">Booking Details</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed">
                            Here you can see all the details for the selected booking.
                        </p>
                    </div>

                    <!-- Booking Information Section -->
                    <div class="detail-box">
                        <h3>Booking Information</h3>
                        <p><span class="label">Booking ID:</span> <?php echo $booking['booking_id']; ?></p>
                        <p><span class="label">User Name:</span> <?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></p>
                        <p><span class="label">User Email:</span> <?php echo $booking['email']; ?></p>
                        <p><span class="label">Package Name:</span> <?php echo $booking['package_name']; ?></p>
                        <p><span class="label">Package Description:</span> <?php echo $booking['package_description']; ?></p>
                        <p><span class="label">Booking Date:</span> <?php echo date('d-m-Y', strtotime($booking['date_booked'])); ?></p>
                    </div>

                    <!-- Traveler Details Section -->
                    <div class="detail-box">
                        <h3>Traveler Details</h3>
                        <p><span class="label">Adults:</span> <?php echo $booking['nr_adults']; ?></p>
                        <p><span class="label">Children:</span> <?php echo $booking['nr_children']; ?></p>
                        <p><span class="label">Total Price:</span> $<?php echo number_format($booking['personalised_price'], 2); ?></p>
                    </div>

                    <!-- Other Travellers Section -->
                    <div class="detail-box">
                        <h3>Other Travellers</h3>
                        <?php if (mysqli_num_rows($traveller_result) > 0): ?>
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
                                    <?php $i = 1; while ($traveller = mysqli_fetch_assoc($traveller_result)): ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($traveller['name'] . ' ' . $traveller['surname']); ?></td>
                                            <td><?php echo $traveller['birthday'] ? date('d-m-Y', strtotime($traveller['birthday'])) : '-'; ?></td>
                                            <td><?php echo htmlspecialchars($traveller['nationality']); ?></td>
                                            <td><?php echo htmlspecialchars($traveller['passport_number']); ?></td>
                                            <td><?php echo ucfirst($traveller['traveller_type']); ?></td>
                                            <td><?php echo ucfirst($traveller['gender']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No other travellers found for this booking.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Delete Button -->
                    <form action="view-booking-details.php?id=<?php echo $booking['booking_id']; ?>" method="POST">
                        <input type="submit" name="delete" value="Delete Booking" class="delete-button" onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">
                    </form>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <a href="admin-viewBookings.php" class="button">Back to Bookings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    feather.replace();
</script>

<script>
    function toggleDarkMode() {
        var body = document.getElementById("body");
        body.classList.toggle("dark-mode");
    }

    function ConfirmDelete() {
        return confirm('Jeni i sigurtë që doni ta fshini?');
    }
</script>

</body>
</html>

<?php
$hero_content = ob_get_clean();
include $base_dir . 'Base/style/base.php';
?>
