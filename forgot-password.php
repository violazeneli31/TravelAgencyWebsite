<?php
$base_dir = __DIR__ . '/Base';
$static_url = '/Travosy/assets'; // Ensure this is the correct path

// Include the common navlink content
ob_start();
$navlink_content = ob_get_clean();

// Optionally define the Hero block content
ob_start();
?>

<section class="md:h-screen py-36 flex items-center relative overflow-hidden zoom-image">
    <div class="absolute inset-0 image-wrap z-1 bg-no-repeat bg-center bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/6.jpg'; ?>');"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900 z-2" id="particles-snow"></div>
    <div class="container relative z-3">
        <div class="flex justify-center">
            <div class="max-w-[400px] w-full m-auto p-6 bg-white dark:bg-slate-900 shadow-md dark:shadow-gray-700 rounded-md">
                <a href="index.php"><img src="<?php echo $static_url; ?>/images/logo-icon.png" class="mx-auto" alt=""></a>
                <h5 class="my-6 text-xl font-semibold">Reset Your Password</h5>
                <div class="grid grid-cols-1">
                    <p class="text-slate-400 mb-6">Please enter your email address. You will receive a link to create a new password via email.</p>
                    <form class="text-start">
                        <div class="grid grid-cols-1">
                            <div class="mb-4">
                                <label class="font-semibold" for="LoginEmail">Email Address:</label>
                                <input id="LoginEmail" type="email" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="name@example.com">
                            </div>

                            <div class="mb-4">
                                <input type="submit" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full" value="Send">
                            </div>

                            <div class="text-center">
                                <span class="text-slate-400 me-2">Remember your password ? </span><a href="login.php" class="text-slate-900 dark:text-white font-bold inline-block">Sign in</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section><!--end section -->

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/no-header.php";
?>