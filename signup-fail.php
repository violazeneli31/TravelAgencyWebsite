<?php
$base_dir = __DIR__ . '/Base';
$static_url = '/Travosy/assets'; // Ensure this is the correct path

// Include the common navlink content
ob_start();
$navlink_content = ob_get_clean();

// Optionally define the Hero block content
ob_start();
?>
        
<!-- Start Hero -->
<section class="relative h-screen flex justify-center items-center bg-slate-50 dark:bg-slate-800">
    <div class="container relative">
        <div class="md:flex justify-center">
            <div class="lg:w-2/5">
                <div class="relative overflow-hidden rounded-md bg-white dark:bg-slate-900 shadow-sm dark:shadow-gray-800">
                    <div class="px-6 py-12 bg-red-600 text-center">
                        <i class="mdi mdi-close-circle text-white text-6xl"></i>
                        <h5 class="text-white text-xl tracking-wide uppercase font-semibold mt-2">Fail: Sign Up Not Complete</h5>
                    </div>

                    <div class="px-6 py-12 text-center">
                        <p class="text-slate-900 font-semibold text-xl dark:text-white">Caution!</p> 
                        <p class="text-slate-400 mt-4">Your account could not be created. Please fill out all required fields.<br> Try again and enjoy your journey. Thank you!</p>
                        
                        <div class="mt-6">
                            <a href="signup.php" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">Try Again</a>
                        </div>
                    </div>

                    <div class="text-center p-6 border-t border-gray-100 dark:border-gray-700">
                        <p class="mb-0 text-slate-400">© <script>document.write(new Date().getFullYear())</script> Travosy. Design & Develop with <i class="mdi mdi-heart text-red-600"></i> by <a href="https://shreethemes.in/" target="_blank" class="text-reset">Shreethemes</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end container-->
</section><!--end section-->
<!-- End Hero -->

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/no-header.php";
?>
