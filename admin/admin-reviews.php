<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        header("Location: ../login.php");
        exit;
    }

    if($_SESSION['role'] != "admin"){
        header("Location: ../index.php");
        exit;
    }

    $base_dir = __DIR__ . '/../'; 
    $static_url = '../assets'; // Ensure this is the correct path
    include '../db.php';

    // Include the common navlink content
    ob_start();
    $navlink_content = ob_get_clean();
    $page = 'nav3';

    // Start Hero content buffer
    ob_start();
?>

<!-- Start Hero -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/cta.jpg'; ?>');"></div>
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">
            
            <?php include 'admin-sidebar.php'; ?>

            <!-- Main Content: Reviews Section -->
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <div class="rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h5 class="text-xl font-semibold">View All Reviews</h5>
                        <p class="text-slate-400 mt-2">See and manage all user reviews</p>
                    </div>
                    <div class="px-6 py-6">
                        <?php
                        // --- BEGIN: Review logic ---

                        // Use the existing database connection from db.php
                        if ($conn) {
                            // Get admin user_id from session
                            $user_id = $_SESSION['user_id'];

                            // Handle review deletion
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
                                $review_id = intval($_POST['delete_review_id']);
                                // Admin can delete any review
                                $deleteStmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
                                $deleteStmt->bind_param("i", $review_id);
                                $deleteStmt->execute();
                                $deleteStmt->close();
                                
                                // Redirect to prevent resubmission
                                header("Location: " . $_SERVER['PHP_SELF']);
                                exit;
                            }

                            // Fetch and display all reviews with user and package info
                            $sql = "SELECT r.*, u.username, u.profile_pic, p.name AS package_name
                                    FROM reviews r
                                    JOIN users u ON r.user_id = u.user_id
                                    JOIN packages p ON r.package_id = p.package_id
                                    ORDER BY r.created_at DESC";
                            $result = $conn->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Use default image if profile_pic is empty
                                    $profile_pic = !empty($row['profile_pic']) ? htmlspecialchars($row['profile_pic']) : ('img/admin.jpg');
                                    ?>
                                    <div class="mb-4 p-4 bg-gray-50 dark:bg-slate-800 rounded shadow relative">
                                        <!-- Admin can delete any review -->
                                        <form method="post" style="position:absolute;top:1rem;right:1rem;z-index:10;" onsubmit="return confirm('Delete this review?');">
                                            <input type="hidden" name="delete_review_id" value="<?php echo $row['review_id']; ?>">
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-base font-semibold">
                                                Delete
                                            </button>
                                        </form>
                                        
                                        <div class="flex items-center mb-2">
                                            <img src="<?php echo "../assets" . $profile_pic; ?>" class="w-8 h-8 rounded-full mr-2" alt="User">
                                            <span class="font-semibold" style="margin-right:5px;"><?php echo htmlspecialchars($row['username']); ?></span>
                                            <span class="ml-2 text-xs text-gray-400"><?php echo htmlspecialchars($row['created_at']); ?></span>
                                        </div>
                                        <div class="mb-1 text-yellow-500">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span><?php echo $i <= $row['rating'] ? '★' : '☆'; ?></span>
                                            <?php endfor; ?>
                                            <span class="ml-2 text-xs text-gray-600">on <?php echo htmlspecialchars($row['package_name']); ?></span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($row['comment']); ?></p>
                                    </div>
                                    <?php
                                }
                                $result->close();
                            } else {
                                ?>
                                <div class="max-w-md mx-auto bg-white dark:bg-slate-900 rounded-xl shadow-md overflow-hidden md:max-w-2xl p-6 text-center">
                                    <div class="mb-4">
                                        <span class="inline-block bg-gray-200 dark:bg-slate-700 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 dark:text-gray-300">No reviews yet</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong class="block text-lg font-semibold text-gray-900 dark:text-gray-100">Review Preview</strong>
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">"This is how a user review will appear once someone submits feedback about their travel experience."</p>
                                    </div>
                                    <div class="flex items-center justify-center mt-4">
                                        <img class="w-10 h-10 rounded-full mr-2" src="img/admin.jpg" alt="User">
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">John Doe</span>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div class='text-red-500'>Failed to connect to database.</div>";
                        }
                        // --- END: Review logic ---
                        ?>
                    </div>
                </div>
            </div>
            <!-- End Main Content -->
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Hero -->

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include $base_dir . "Base/style/base-user.php";
?>