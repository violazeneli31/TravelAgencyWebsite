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
    include '../db.php';
    // Include the common navlink content
    ob_start();
    $navlink_content = ob_get_clean();
    $page= 'nav3';
    $user_id = 1;
    
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

            <?php include 'admin-sidebar.php'; ?>
            
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <!-- Add Package Button at the top -->
                <div class="mb-6 text-right">
                    <a href="admin-add-packages.php" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg hover:shadow-xl transition-all text-lg font-bold" title="Add Package" style="width: 45px; height: 45px; border-radius: 50%;">
                        +
                    </a>
                </div>

                <!-- Success message for package added -->
                <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Package has been added successfully.</span>
                </div>
                <?php endif; ?>

                <!-- Upcoming Packages Section -->
                <div class="mb-12">
                    <h5 class="text-xl font-bold mb-5 border-b pb-2" style="color: #059669; border-color: #059669;">Upcoming Packages</h5>

                    <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                        <?php
                        $currentDate = date('Y-m-d');

                        $packageQuery = "SELECT * FROM packages WHERE departure_date > ? ORDER BY departure_date ASC";
                        $packageStmt = $conn->prepare($packageQuery);
                        $packageStmt->bind_param("s", $currentDate);
                        $packageStmt->execute();
                        $packageResult = $packageStmt->get_result();
                            if($packageResult->num_rows > 0){
                                while ($package = $packageResult->fetch_assoc()) {
                                    $titleSlug = strtolower(str_replace(' ', '-', $package['name']));
                                    $departureDate = date('M d, Y', strtotime($package['departure_date']));
                                    $returnDate = date('M d, Y', strtotime($package['return_date']));
                                    $daysUntilDeparture = ceil((strtotime($package['departure_date']) - time()) / (60 * 60 * 24));
                            ?>
                            <div class="rounded-md shadow hover:shadow-lg dark:shadow-gray-800 bg-white dark:bg-slate-900 transition duration-500 overflow-hidden">
                                <div class="relative">
                                    <img src="<?php echo $static_url . $package['img1']; ?>" alt="<?php echo htmlspecialchars($package['name']); ?>" class="w-full h-40 object-cover" style="height:300px;">
                                </div>
                                
                                <div class="p-4">
                                    <a href="/Travosy/tour-detail-one.php?title=<?php echo $titleSlug; ?>" class="text-lg font-medium hover:text-red-600 duration-500 ease-in-out line-clamp-1">
                                        <?php echo htmlspecialchars($package['name']); ?>
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
                                                <span class="text-slate-400">Days:</span>
                                                <span class="font-medium"><?php echo $package['num_days']; ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <i class="uil uil-moon text-emerald-600 mr-1"></i>
                                            <span class="text-slate-400"><?php echo $package['num_nights']; ?> nights</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <div>
                                            <span class="text-slate-400 text-xs">From</span>
                                            <p class="text-base font-semibold">$<?php echo number_format($package['base_price'], 2); ?></p>
                                        </div>
                                        <a href="admin-packages-edit.php?package_id=<?php echo $package['package_id']; ?>" class="px-4 py-2 font-medium bg-emerald-600 hover:bg-emerald-700 border-emerald-600 hover:border-emerald-700 text-white rounded-md shadow-md hover:shadow-lg transition-all" style="background: linear-gradient(to right, #059669, #10b981);">
                                            <i class="uil uil-eye mr-1"></i> Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            } }else {
                                echo '<div class="md:col-span-2 text-center py-6 bg-white dark:bg-slate-900 rounded-md shadow">';
                                echo '<p class="text-slate-600 dark:text-slate-400">No packages found.</p>';
                                echo '</div>';
                            }
                        $packageResult->close();
                        ?>
                    </div>
                </div>
                <!-- Previous Packages Section -->
                <div class="mb-12" style="margin-top:60px;">
                    <h5 class="text-xl font-bold mb-5 border-b pb-2" style="color:rgb(150, 5, 5); border-color:rgb(150, 5, 5);">Previous Packages</h5>

                    <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                        <?php
                        // Current date for comparison
                        $currentDate = date('Y-m-d');
                        
                        // Get all upcoming packages for the user (based on package departure date)
                        $packageQuery = "SELECT * FROM packages WHERE departure_date <= ? ORDER BY departure_date ASC";
                        $packageStmt = $conn->prepare($packageQuery);
                        $packageStmt->bind_param("s", $currentDate);
                        $packageStmt->execute();
                        $packageResult = $packageStmt->get_result();
                            if($packageResult->num_rows > 0){
                                while ($package = $packageResult->fetch_assoc()) {
                                    $titleSlug = strtolower(str_replace(' ', '-', $package['name']));
                                    $departureDate = date('M d, Y', strtotime($package['departure_date']));
                                    $returnDate = date('M d, Y', strtotime($package['return_date']));
                                    $daysUntilDeparture = ceil((strtotime($package['departure_date']) - time()) / (60 * 60 * 24));
                            ?>
                            <div class="rounded-md shadow hover:shadow-lg dark:shadow-gray-800 bg-white dark:bg-slate-900 transition duration-500 overflow-hidden">
                                <div class="relative">
                                    <img src="<?php echo $static_url . $package['img1']; ?>" alt="<?php echo htmlspecialchars($package['name']); ?>" class="w-full h-40 object-cover" style="height:300px;">
                                </div>
                                
                                <div class="p-4">
                                    <a href="/Travosy/tour-detail-one.php?title=<?php echo $titleSlug; ?>" class="text-lg font-medium hover:text-red-600 duration-500 ease-in-out line-clamp-1">
                                        <?php echo htmlspecialchars($package['name']); ?>
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
                                                <span class="text-slate-400">Days:</span>
                                                <span class="font-medium"><?php echo $package['num_days']; ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <i class="uil uil-moon text-emerald-600 mr-1"></i>
                                            <span class="text-slate-400"><?php echo $package['num_nights']; ?> nights</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <div>
                                            <span class="text-slate-400 text-xs">From</span>
                                            <p class="text-base font-semibold">$<?php echo number_format($package['base_price'], 2); ?></p>
                                        </div>
                                        <a href="admin-packages-edit.php?package_id=<?php echo $package['package_id']; ?>" class="px-4 py-2 font-medium bg-emerald-600 hover:bg-emerald-700 border-emerald-600 hover:border-emerald-700 text-white rounded-md shadow-md hover:shadow-lg transition-all" style="background: linear-gradient(to right, #059669, #10b981);">
                                            <i class="uil uil-eye mr-1"></i> Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            } }else {
                                echo '<div class="md:col-span-2 text-center py-6 bg-white dark:bg-slate-900 rounded-md shadow">';
                                echo '<p class="text-slate-600 dark:text-slate-400">No packages found.</p>';
                                echo '</div>';
                            }
                        $packageResult->close();
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
include $base_dir . 'Base/style/base-user.php';
?>