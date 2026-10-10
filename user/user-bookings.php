<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        { header("Location: ../login.php"); exit; }
    }

    if($_SESSION['role'] != "user"){
        { header("Location: ../index.php"); exit; }
    }

    $base_dir = __DIR__ . '/../Base';
    $static_url = '/Travosy/assets'; // Ensure this is the correct path
    include '../db.php';
    // Include the common navlink content
    ob_start();
    $navlink_content = ob_get_clean();
    $page= 'nav3';
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT first_name, last_name, username, birthday, phone, created_at, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    // Optionally define the Hero block content
    ob_start();
?>

<!-- Start Hero -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/cta.jpg'; ?>');"></div>
    </div>

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">
            <div class="lg:w-1/4 md:w-1/3 md:px-3">
                <div class="sidebar">
                    <?php include 'user-sidebar.php'; ?>
                </div>
            </div>
            
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <!-- Upcoming Bookings Section -->
                <div class="mb-12">
                    <h5 class="text-xl font-bold mb-5 border-b pb-2" style="color: #059669; border-color: #059669;">Upcoming Bookings</h5>

                    <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                        <?php
                        // Current date for comparison
                        $currentDate = date('Y-m-d');
                        
                        // Get all upcoming bookings for the user (based on package departure date)
                        $upcomingQuery = "SELECT b.*, p.* FROM bookings b 
                                         JOIN packages p ON b.package_id = p.package_id 
                                         WHERE b.user_id = ? 
                                         AND p.departure_date >= ?
                                         ORDER BY p.departure_date ASC";
                        $upcomingStmt = $conn->prepare($upcomingQuery);
                        $upcomingStmt->bind_param("is", $user_id, $currentDate);
                        $upcomingStmt->execute();
                        $upcomingResult = $upcomingStmt->get_result();
                        
                        if ($upcomingResult->num_rows > 0) {
                            while ($booking = $upcomingResult->fetch_assoc()) {
                                // Create URL-friendly title slug
                                $titleSlug = strtolower(str_replace(' ', '-', $booking['name']));
                                
                                // Format dates
                                $departureDate = date('M d, Y', strtotime($booking['departure_date']));
                                $returnDate = date('M d, Y', strtotime($booking['return_date']));
                                
                                // Calculate days until departure
                                $daysUntilDeparture = ceil((strtotime($booking['departure_date']) - time()) / (60 * 60 * 24));
                        ?>
                        <div class="rounded-md shadow hover:shadow-lg dark:shadow-gray-800 bg-white dark:bg-slate-900 transition duration-500 overflow-hidden">
                            <div class="relative">
                                <img src="<?php echo $static_url . $booking['img1']; ?>" alt="<?php echo htmlspecialchars($booking['name']); ?>" class="w-full h-40 object-cover">
                                <div class="absolute top-3 right-3">
                                    <span class="bg-emerald-100 text-emerald-600 text-xs font-medium px-2.5 py-0.5 rounded-full" style="color: #059669;">
                                        <?php if ($daysUntilDeparture <= 7): ?>
                                            Departing soon
                                        <?php else: ?>
                                            Upcoming
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div style="position: absolute; top: 12px; right: 12px; z-index: 10;">
                                    <a href="user-travelers-edit.php?booking_id=<?php echo $booking['booking_id']; ?>">
                                        <i data-feather="eye" class="size-6" color="white"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <a href="../tour-detail-one.php?title=<?php echo $titleSlug; ?>" class="text-lg font-medium hover:text-red-600 duration-500 ease-in-out line-clamp-1">
                                    <?php echo htmlspecialchars($booking['name']); ?>
                                </a>
                                
                                <div class="mt-2 text-sm">
                                    <div class="flex justify-between">
                                        <div>
                                            <span class="text-slate-400">Departure:</span>
                                            <span class="font-medium"><?php echo $departureDate; ?></span>
                                            <?php if ($daysUntilDeparture > 0): ?>
                                                <span class="text-xs text-emerald-600 block">(in <?php echo $daysUntilDeparture; ?> days)</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span class="text-slate-400">ID:</span>
                                            <span class="font-medium">#<?php echo $booking['booking_id']; ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 flex flex-col space-y-2">
                                        <div class="flex items-center">
                                            <i class="uil uil-users-alt text-emerald-600 mr-1"></i>
                                            <span class="text-slate-400">
                                                <?php echo $booking['nr_adults']; ?> Adult<?php echo $booking['nr_adults'] != 1 ? 's' : ''; ?>, 
                                                <?php echo $booking['nr_children']; ?> Child<?php echo $booking['nr_children'] != 1 ? 'ren' : ''; ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="uil uil-calendar-alt text-emerald-600 mr-1"></i>
                                            <span class="text-slate-400"><?php echo $booking['num_nights']; ?> nights</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <div>
                                        <span class="text-slate-400 text-xs">Total:</span>
                                        <p class="text-base font-semibold">$<?php echo number_format($booking['personalised_price'], 2); ?></p>
                                    </div>
                                    
                                    <a href="../tour-detail-one.php?title=<?php echo $titleSlug; ?>" class="px-4 py-2 font-medium bg-emerald-600 hover:bg-emerald-700 border-emerald-600 hover:border-emerald-700 text-white rounded-md shadow-md hover:shadow-lg transition-all" style="background: linear-gradient(to right, #059669, #10b981);">
                                        <i class="uil uil-eye mr-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="md:col-span-2 text-center py-6 bg-white dark:bg-slate-900 rounded-md shadow">';
                            echo '<p class="text-slate-600 dark:text-slate-400">You don\'t have any upcoming bookings.</p>';
                            echo '</div>';
                        }
                        $upcomingStmt->close();
                        ?>
                    </div>
                </div>
                
                <!-- Booking History Section -->
                <div class="mt-10">
                    <h5 class="text-xl font-bold mb-5 text-red-600 border-b pb-2">Booking History</h5>

                    <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                        <?php
                        // Get all past bookings for the user (based on package departure date)
                        $historyQuery = "SELECT b.*, p.* FROM bookings b 
                                        JOIN packages p ON b.package_id = p.package_id 
                                        WHERE b.user_id = ? 
                                        AND p.departure_date < ?
                                        ORDER BY p.departure_date DESC";
                        $historyStmt = $conn->prepare($historyQuery);
                        $historyStmt->bind_param("is", $user_id, $currentDate);
                        $historyStmt->execute();
                        $historyResult = $historyStmt->get_result();
                        
                        if ($historyResult->num_rows > 0) {
                            while ($booking = $historyResult->fetch_assoc()) {
                                // Create URL-friendly title slug
                                $titleSlug = strtolower(str_replace(' ', '-', $booking['name']));
                                
                                // Format dates
                                $departureDate = date('M d, Y', strtotime($booking['departure_date']));
                                $returnDate = date('M d, Y', strtotime($booking['return_date']));
                                
                                // Check if the trip is completed (return date has passed)
                                $isCompleted = strtotime($booking['return_date']) < time();
                                
                                // Has the user reviewed this package?
                                $reviewQuery = "SELECT review_id FROM reviews WHERE user_id = ? AND package_id = ?";
                                $reviewStmt = $conn->prepare($reviewQuery);
                                $reviewStmt->bind_param("ii", $user_id, $booking['package_id']);
                                $reviewStmt->execute();
                                $reviewStmt->store_result();
                                $hasReviewed = $reviewStmt->num_rows > 0;
                                $reviewStmt->close();
                        ?>
                        <div class="rounded-md shadow hover:shadow-lg dark:shadow-gray-800 bg-white dark:bg-slate-900 transition duration-500 overflow-hidden">
                            <div class="relative">
                                <img src="<?php echo $static_url . $booking['img1']; ?>" alt="<?php echo htmlspecialchars($booking['name']); ?>" class="w-full h-40 object-cover">
                                <div class="absolute top-3 right-3">
                                    <?php if ($isCompleted): ?>
                                    <span class="bg-red-100 text-red-600 text-xs font-medium px-2.5 py-0.5 rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="bg-blue-100 text-blue-600 text-xs font-medium px-2.5 py-0.5 rounded-full">In Progress</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <a href="../tour-detail-one.php?title=<?php echo $titleSlug; ?>" class="text-lg font-medium hover:text-red-600 duration-500 ease-in-out line-clamp-1">
                                    <?php echo htmlspecialchars($booking['name']); ?>
                                </a>
                                
                                <div class="mt-2 text-sm">
                                    <div class="flex justify-between">
                                        <div>
                                            <span class="text-slate-400">Trip Date:</span>
                                            <span class="font-medium"><?php echo $departureDate; ?></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400">ID:</span>
                                            <span class="font-medium">#<?php echo $booking['booking_id']; ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 flex flex-col space-y-2">
                                        <div class="flex items-center">
                                            <i class="uil uil-users-alt text-red-600 mr-1"></i>
                                            <span class="text-slate-400">
                                                <?php echo $booking['nr_adults']; ?> Adult<?php echo $booking['nr_adults'] != 1 ? 's' : ''; ?>, 
                                                <?php echo $booking['nr_children']; ?> Child<?php echo $booking['nr_children'] != 1 ? 'ren' : ''; ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="uil uil-map-marker text-red-600 mr-1"></i>
                                            <span class="text-slate-400"><?php echo htmlspecialchars($booking['destination_city']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <div>
                                        <span class="text-slate-400 text-xs">Paid:</span>
                                        <p class="text-base font-semibold">$<?php echo number_format($booking['personalised_price'], 2); ?></p>
                                    </div>
                                    
                                    <?php if ($isCompleted && !$hasReviewed): ?>
                                    <a href="../tour-detail-one.php?title=<?php echo $titleSlug; ?>#review-form" class="px-4 py-2 font-medium bg-amber-500 hover:bg-amber-600 border-amber-500 hover:border-amber-600 text-white rounded-md shadow-md hover:shadow-lg transition-all">
                                        <i class="uil uil-star mr-1"></i> Leave Review
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="md:col-span-2 text-center py-6 bg-white dark:bg-slate-900 rounded-md shadow">';
                            echo '<p class="text-slate-600 dark:text-slate-400">You don\'t have any booking history yet.</p>';
                            echo '</div>';
                        }
                        $historyStmt->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/base-user.php";
?>