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
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">
            <div class="lg:w-1/4 md:w-1/3 md:px-3">
                <div class="sidebar">
                    <?php include 'user-sidebar.php'; ?>
                </div>
            </div>
            
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
                    <h5 class="text-lg font-semibold mb-4">My personal info</h5>
                    
                    <div class="grid lg:grid-cols-2 grid-cols-1 gap-5">
                        <div>
                            <label class="form-label font-medium">First Name :</label>
                            <div class="form-icon relative mt-2">
                                <i data-feather="user" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" value="<?= htmlspecialchars($user['first_name']) ?>" readonly class="ps-12 w-full py-2 px-3 h-10 bg-gray-100 dark:bg-gray-700 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="form-label font-medium">Last Name :</label>
                            <div class="form-icon relative mt-2">
                                <i data-feather="user-check" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" value="<?= htmlspecialchars($user['last_name']) ?>" readonly class="ps-12 w-full py-2 px-3 h-10 bg-gray-100 dark:bg-gray-700 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="form-label font-medium">Username :</label>
                            <div class="form-icon relative mt-2">
                                <i data-feather="user" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly class="ps-12 w-full py-2 px-3 h-10 bg-gray-100 dark:bg-gray-700 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="form-label font-medium">Birthday :</label>
                            <div class="form-icon relative mt-2">
                                <i data-feather="calendar" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" value="<?= htmlspecialchars($user['birthday']) ?>" readonly class="ps-12 w-full py-2 px-3 h-10 bg-gray-100 dark:bg-gray-700 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="form-label font-medium">Phone :</label>
                            <div class="form-icon relative mt-2">
                                <i data-feather="phone" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" value="<?= htmlspecialchars($user['phone']) ?>" readonly class="ps-12 w-full py-2 px-3 h-10 bg-gray-100 dark:bg-gray-700 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="form-label font-medium">Email :</label>
                            <div class="form-icon relative mt-2">
                                <i data-feather="mail" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" value="<?= htmlspecialchars($user['email']) ?>" readonly class="ps-12 w-full py-2 px-3 h-10 bg-gray-100 dark:bg-gray-700 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="form-label font-medium">Created At :</label>
                            <div class="form-icon relative mt-2">
                                <i data-feather="clock" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" value="<?= htmlspecialchars($user['created_at']) ?>" readonly class="ps-12 w-full py-2 px-3 h-10 bg-gray-100 dark:bg-gray-700 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800">
                            </div>
                        </div>
                    </div><!--end grid-->
                </div>
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Hero -->
  
<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/base-user.php";
?>