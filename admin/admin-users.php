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
?>

<!-- Start Hero Section -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover"
             style="background-image: url('<?php echo $static_url; ?>/images/bg/cta.jpg');"></div>
    </div>

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">

            <!-- Sidebar Include -->
            <?php include 'admin-sidebar.php'; ?>

            <!-- Main Content Section -->
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <!-- Main Card -->
                <div class="p-8 rounded-xl shadow-xl dark:shadow-gray-800 bg-white dark:bg-slate-900 transition-all duration-300">
                    <!-- Title and Description -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-black mb-2">Manage Users</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed">
                            Welcome to the administrative user panel. Here you can monitor, review, and manage all registered users of the platform.
                        </p>
                    </div>

                    <!-- Highlight Card (Clickable) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
                        <a href="view-all-users.php"
                           class="block p-6 bg-gradient-to-br from-blue-50 to-white dark:from-slate-800 dark:to-slate-900 border border-blue-100 dark:border-slate-700 rounded-lg shadow hover:shadow-md transition duration-300 group">

                            <div class="flex items-center">
                                <!-- Text Info -->
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-black mb-1 group-hover:underline">
                                        View All Users
                                    </h4>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">
                                        Browse the complete list of registered users. Manage roles, edit or remove profiles.
                                    </p>
                                </div>

                                <!-- Optional Right Arrow Icon -->
                                <div class="ml-auto text-blue-600 group-hover:translate-x-1 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function ConfirmDelete() {
        return confirm('Are you sure you want to delete this user?');
    }
</script>


<?php
$hero_content = ob_get_clean();
include $base_dir . 'Base/style/base-user.php';
?>