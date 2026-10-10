<?php
include('../config.php');  // Ensure the database connection is included

// Fetch user information from the database
$user_id = $_SESSION['user_id']; // Get the logged-in user ID

$query = "SELECT first_name, last_name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // Bind user_id to the prepared statement
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); // Fetch the user data

// Static profile image (same for all users)
$profile_image = 'img/admin.jpg';  // Adjust the image path to your static image
?>

<!-- === Begin Sidebar === -->
<div class="lg:w-1/4 md:w-1/3 md:px-3">
    <div class="relative md:-mt-48 -mt-32">
        <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
            <div class="profile-pic text-center mb-5">
                <!-- Static Profile Picture -->
                <div>
                    <div class="relative h-28 w-28 mx-auto">
                        <!-- Display the static profile image -->
                        <img src="<?php echo $profile_image; ?>" class="rounded-full shadow-sm dark:shadow-gray-800 ring-4 ring-slate-50 dark:ring-slate-800" id="profile-image" alt="Profile Image">
                    </div>

                    <div class="mt-4">
                        <!-- Display the user's name dynamically -->
                        <h5 class="text-lg font-semibold"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h5>
                        <!-- Display the user's email dynamically -->
                        <p class="text-slate-400"><?php echo $user['email']; ?></p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700">
                <ul class="list-none sidebar-nav mb-0 pb-0" id="navmenu-nav">
                    <li class="navbar-item account-menu">
                        <a href="admin-dashboard.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                            <span class="me-2 mb-0"><i data-feather="airplay" class="size-4"></i></span>
                            <h6 class="mb-0 font-medium">Dashboard</h6>
                        </a>
                    </li>

                    <li class="navbar-item account-menu">
                        <a href="admin-packages.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                            <span class="me-2 mb-0"><i data-feather="credit-card" class="size-4"></i></span>
                            <h6 class="mb-0 font-medium">Packages</h6>
                        </a>
                    </li>

                    <li class="navbar-item account-menu">
                        <a href="admin-users.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                            <span class="me-2 mb-0"><i data-feather="users" class="size-4"></i></span>
                            <h6 class="mb-0 font-medium">Users</h6>
                        </a>
                    </li>

                    <li class="navbar-item account-menu">
                        <a href="admin-viewBookings.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                            <span class="me-2 mb-0"><i data-feather="calendar" class="size-4"></i></span>
                            <h6 class="mb-0 font-medium">Bookings</h6>
                        </a>
                    </li>

                    <li class="navbar-item account-menu">
                        <a href="admin-reviews.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                            <span class="me-2 mb-0">
                                <i data-feather="star" class="size-4"></i>
                            </span>
                            <h6 class="mb-0 font-medium">Reviews</h6>
                        </a>
                    </li>

                    <li class="navbar-item account-menu">
                        <a href="admin-setting.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                            <span class="me-2 mb-0"><i data-feather="settings" class="size-4"></i></span>
                            <h6 class="mb-0 font-medium">Settings</h6>
                        </a>
                    </li>

                    <li class="navbar-item account-menu">
                        <a href="#" onclick="showLogoutDialog()" class="navbar-link text-slate-400 flex items-center py-2 rounded" style="cursor: pointer;">
                            <span class="me-2 mb-0"><i data-feather="log-out" class="size-4"></i></span>
                            <h6 class="mb-0 font-medium">Sign Out</h6>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Logout Confirmation Dialog Modal -->
<div id="logoutDialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-lg shadow-lg w-full max-w-sm text-center">
        <h2 class="text-lg font-semibold mb-4">Are you sure you want to sign out?</h2>
        <p class="text-sm text-gray-500 mb-4">You will be redirected to the home page.</p>
        <div class="flex justify-center gap-4">
            <button onclick="confirmLogout()" class="bg-red-500 text-white px-4 py-2 rounded-md">Sign Out</button>
            <button onclick="hideLogoutDialog()" class="bg-gray-300 dark:bg-gray-700 text-black dark:text-white px-4 py-2 rounded-md">Cancel</button>
        </div>
    </div>
</div>

<script>
    function showLogoutDialog() {
        document.getElementById('logoutDialog').classList.remove('hidden');
    }

    function hideLogoutDialog() {
        document.getElementById('logoutDialog').classList.add('hidden');
    }

    function confirmLogout() {
        // Redirect to index.php
        window.location.href = '../logout.php';
    }
</script>
