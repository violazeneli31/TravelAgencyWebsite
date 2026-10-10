<?php
include 'config.php'; // make sure the path is correct

// Count total visitors (users)
$userCountQuery = "SELECT COUNT(*) AS total_users FROM users";
$userResult = mysqli_query($conn, $userCountQuery);
$userCount = mysqli_fetch_assoc($userResult)['total_users'] ?? 0;

// Count total travel packages
$packageCountQuery = "SELECT COUNT(*) AS total_packages FROM packages";
$packageResult = mysqli_query($conn, $packageCountQuery);
$packageCount = mysqli_fetch_assoc($packageResult)['total_packages'] ?? 0;
?>


<div class="grid md:grid-cols-12 grid-cols-1 items-center gap-6 relative">
    <div class="md:col-span-5">
        <div class="relative">
            <img src="<?php echo $static_url; ?>/images/about.jpg" class="mx-auto rounded-3xl shadow-sm dark:shadow-gray-700 w-[90%]" alt="">
            

            <div class="absolute flex items-center bottom-16 md:-start-10 -start-5 p-4 rounded-lg shadow-md dark:shadow-gray-800 bg-white dark:bg-slate-900 w-56 m-3">
                <div class="flex items-center justify-center h-[65px] min-w-[65px] bg-red-500/5 text-red-500 text-center rounded-xl me-3">
                    <i data-feather="users" class="size-6"></i>
                </div>
                <div class="flex-1">
                    <span class="text-slate-400">Users</span>
                    <p class="text-xl font-bold"><span class="counter-value" data-target="<?php echo $userCount; ?>"><?php echo $userCount; ?></span></p>
                </div>
            </div>

            <div class="absolute flex items-center top-16 md:-end-10 -end-5 p-4 rounded-lg shadow-md dark:shadow-gray-800 bg-white dark:bg-slate-900 w-60 m-3">
                <div class="flex items-center justify-center h-[65px] min-w-[65px] bg-red-500/5 text-red-500 text-center rounded-xl me-3">
                    <i data-feather="globe" class="size-6"></i>
                </div>
                <div class="flex-1">
                    <span class="text-slate-400">Travel Packages</span>
                    <p class="text-xl font-bold"><span class="counter-value" data-target="<?php echo $packageCount; ?>"><?php echo $packageCount; ?></span>+</p>
                </div>
            </div>
        </div>
    </div>

    <div class="md:col-span-7">
        <div class="lg:ms-8">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Discover the World with Travosy<br>World Best Travel Agency</h3>

            <p class="text-slate-400 max-w-xl mb-6">At Travosy, we believe travel isn’t just about destinations — it’s about unforgettable experiences, meaningful connections, and stories that last a lifetime. As the world’s best travel agency, we’re here to turn your dream journeys into reality. Whether you're chasing sunsets on tropical shores, exploring vibrant cities, or venturing off the beaten path, Travosy is your trusted partner in adventure. Let’s explore the world — together.</p>

            <a href="/Travosy/aboutus.php" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">Read More <i class="mdi mdi-chevron-right align-middle ms-0.5"></i></a>
        </div>
    </div>

    <div class="absolute bottom-0 start-1/3 -z-1">
        <img src="<?php echo $static_url; ?>/images/map-plane-big.png" class="lg:w-[600px] w-96" alt="">
    </div>
</div>