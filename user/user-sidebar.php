<div class="relative md:-mt-48 -mt-32">
    <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
        <div class="profile-pic text-center mb-5">
            <input id="pro-img" name="profile-image" type="file" class="hidden" onchange="loadFile(event)" />
            <div>
                <div class="relative h-28 w-28 mx-auto">
                    <img src="<?php echo $static_url; ?>/images/client/16.jpg" class="rounded-full shadow-sm dark:shadow-gray-800 ring-4 ring-slate-50 dark:ring-slate-800" id="profile-image" alt="">
                    <label class="absolute inset-0 cursor-pointer" for="pro-img"></label>
                </div>

                <div class="mt-4">
                    <h5 class="font-semibold text-lg text-black dark:text-white">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </h5>
                    <p class="text-sm text-slate-400"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700 p-4">
            <ul class="list-none sidebar-nav mb-0 pb-0" id="navmenu-nav">
                <li class="navbar-item account-menu">
                    <a href="user-profile.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                        <span class="me-2 mb-0"><i data-feather="airplay" class="size-4"></i></span>
                        <h6 class="mb-0 font-medium">Profile</h6>
                    </a>
                </li>

                <li class="navbar-item account-menu">
                    <a href="user-packages.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                        <span class="me-2 mb-0"><i data-feather="heart" class="size-4"></i></span>
                        <h6 class="mb-0 font-medium">Wishlist</h6>
                    </a>
                </li>

                <li class="navbar-item account-menu">
                    <a href="user-bookings.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
                        <span class="me-2 mb-0"><i data-feather="briefcase" class="size-4"></i></span>
                        <h6 class="mb-0 font-medium">Bookings</h6>
                    </a>
                </li>

                <li class="navbar-item account-menu">
                    <a href="user-setting.php" class="navbar-link text-slate-400 flex items-center py-2 rounded">
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