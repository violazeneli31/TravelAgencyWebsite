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
    
    // Process wishlist removal if form submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_remove_wishlist']) && isset($_POST['package_id'])) {
        $packageId = $_POST['package_id'];
        $removeStmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND package_id = ?");
        $removeStmt->bind_param("ii", $user_id, $packageId);
        $removeStmt->execute();
        $removeStmt->close();
        
        // Redirect to prevent form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
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
                <div class="">
                    <h5 class="text-xl font-bold mb-5 border-b pb-2" style="color: #000000; border-color: #000000;">My Wishlist</h5>

                    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 mt-6 gap-6">
                        
                        <?php
                        // Get all packages in user's wishlist
                        $wishlistQuery = "SELECT p.* FROM packages p 
                                         JOIN wishlist w ON p.package_id = w.package_id 
                                         WHERE w.user_id = ? AND p.status = 'active'";
                        $wishlistStmt = $conn->prepare($wishlistQuery);
                        $wishlistStmt->bind_param("i", $user_id);
                        $wishlistStmt->execute();
                        $wishlistResult = $wishlistStmt->get_result();
                        
                        if ($wishlistResult->num_rows > 0) {
                            while ($package = $wishlistResult->fetch_assoc()) {
                                // Create URL-friendly title slug
                                $titleSlug = strtolower(str_replace(' ', '-', $package['name']));
                                
                                // Calculate days and nights
                                $days = $package['num_days'];
                                $nights = $package['num_nights'];
                                
                                // Format the price
                                $price = number_format($package['base_price'], 2);
                                
                                // Get package ratings
                                $ratingQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                                               FROM reviews 
                                               WHERE package_id = ?";
                                $ratingStmt = $conn->prepare($ratingQuery);
                                $ratingStmt->bind_param("i", $package['package_id']);
                                $ratingStmt->execute();
                                $ratingResult = $ratingStmt->get_result();
                                $ratingData = $ratingResult->fetch_assoc();
                                $ratingStmt->close();
                                
                                // Format the rating
                                $avgRating = !empty($ratingData['avg_rating']) ? number_format($ratingData['avg_rating'], 1) : "0.0";
                                $reviewCount = !empty($ratingData['review_count']) ? $ratingData['review_count'] : 0;

                                // Get latest booking for this package to show adult/children counts
                                $bookingQuery = "SELECT nr_adults, nr_children FROM bookings 
                                                WHERE user_id = ? AND package_id = ? 
                                                ORDER BY date_booked DESC LIMIT 1";
                                $bookingStmt = $conn->prepare($bookingQuery);
                                $bookingStmt->bind_param("ii", $user_id, $package['package_id']);
                                $bookingStmt->execute();
                                $bookingResult = $bookingStmt->get_result();
                                
                                $nrAdults = 1;  // Default values
                                $nrChildren = 0;
                                
                                if ($bookingResult->num_rows > 0) {
                                    $bookingData = $bookingResult->fetch_assoc();
                                    $nrAdults = $bookingData['nr_adults'];
                                    $nrChildren = $bookingData['nr_children'];
                                }
                                $bookingStmt->close();
                        ?>
                        <div class="group rounded-md shadow hover:shadow-lg dark:shadow-gray-800 duration-500 ease-in-out overflow-hidden">
                            <div class="relative">
                                <img src="<?php echo $static_url . $package['img1']; ?>" alt="<?php echo htmlspecialchars($package['name']); ?>" class="h-48 w-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/20"></div>
                            </div>

                            <div class="p-4 pb-0">
                                <div class="flex justify-between mb-2">
                                    <span class="text-slate-400 text-sm"><i class="uil uil-map-marker text-red-600"></i> <?php echo htmlspecialchars($package['destination_city']); ?></span>
                                    <span class="text-slate-400 text-sm flex items-center">
                                        <i class="uil uil-star text-amber-400 mr-1"></i> 
                                        <span><?php echo $avgRating; ?> (<?php echo $reviewCount; ?>)</span>
                                    </span>
                                </div>
                                
                                <a href="../tour-detail-one.php?title=<?php echo $titleSlug; ?>" class="text-lg font-medium hover:text-red-600 duration-500 ease-in-out"><?php echo htmlspecialchars($package['name']); ?></a>
                                
                                <p class="text-slate-400 text-sm mt-2 truncate"><?php echo htmlspecialchars(substr($package['description'], 0, 100)) . '...'; ?></p>
                                
                                <ul class="py-5 flex items-center list-none border-t border-gray-100 dark:border-gray-800 mt-4">
                                    <li class="flex items-center mr-3">
                                        <i class="uil uil-clock text-lg leading-none mr-2 text-slate-900 dark:text-white"></i>
                                        <span class="text-slate-400 text-sm"><?php echo $days; ?> days <?php echo $nights; ?> nights</span>
                                    </li>
                                </ul>
                                
                                <div class="py-5 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400">Price</span>
                                        <p class="text-lg font-semibold">$<?php echo $price; ?></p>
                                    </div>
                                    
                                    <a href="../tour-detail-one.php?title=<?php echo $titleSlug; ?>" class="px-4 py-2 font-medium bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 text-white rounded-md shadow-md hover:shadow-lg transition-all" style="background: linear-gradient(to right, #dc2626, #e11d48);">
                                        <i class="uil uil-compass mr-1"></i> Explore Now
                                    </a>
                                </div>
                                
                                <!-- Remove from Wishlist button -->
                                <div class="py-3 border-t border-gray-100 dark:border-gray-800 text-center">
                                    <button onclick="showRemoveWishlistDialog(<?php echo $package['package_id']; ?>, '<?php echo htmlspecialchars(addslashes($package['name'])); ?>')" class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-red-600 rounded transition-all">
                                        <i class="uil uil-heart-break mr-1"></i> Remove from Wishlist
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="col-span-full text-center py-8">';
                            echo '<p class="text-slate-600">You have not added any tours to your wishlist yet.</p>';
                            echo '</div>';
                        }
                        $wishlistStmt->close();
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Confirmation Dialog Modal -->
<div id="confirmRemoveWishlistDialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-lg shadow-lg w-full max-w-sm text-center">
        <h2 class="text-lg font-semibold mb-4">Are you sure you want to remove <span id="packageNameToRemove"></span> from your wishlist?</h2>
        <div class="flex justify-center gap-4">
            <form method="POST">
                <input type="hidden" name="confirm_remove_wishlist" value="yes">
                <input type="hidden" name="package_id" id="packageIdToRemove">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md">OK</button>
            </form>
            <button onclick="hideRemoveWishlistDialog()" class="bg-gray-300 dark:bg-gray-700 text-black dark:text-white px-4 py-2 rounded-md">✕</button>
        </div>
    </div>
</div>

<!-- JavaScript for confirmation popup -->
<script>
    function showRemoveWishlistDialog(packageId, packageName) {
        document.getElementById('packageIdToRemove').value = packageId;
        document.getElementById('packageNameToRemove').textContent = '"' + packageName + '"';
        document.getElementById('confirmRemoveWishlistDialog').classList.remove('hidden');
    }

    function hideRemoveWishlistDialog() {
        document.getElementById('confirmRemoveWishlistDialog').classList.add('hidden');
    }
</script>

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/base-user.php";
?>