<?php
// $base_dir = __DIR__ . '/Base';
$static_url = '/Travosy/assets';

// Define the content for the navlink block
ob_start();
?>

<!DOCTYPE html>
<html lang="en" class="light scroll-smooth" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <title>Travosy - Tour & Travels Agency </title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="Tour & Travels Agency Tailwind CSS Template" name="description">
        <meta content="Tour, Travels, agency, business, corporate, tour packages, journey, trip, tailwind css, Admin, Landing" name="keywords">
        <meta name="author" content="Shreethemes">
        <meta name="website" content="https://shreethemes.in">
        <meta name="email" content="support@shreethemes.in">
        <meta name="version" content="1.5.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- favicon -->
        <link rel="shortcut icon" href="<?php echo $static_url; ?>/images/favicon.ico">

        <!-- Css -->
        <link href="<?php echo $static_url; ?>/libs/swiper/css/swiper.min.css" rel="stylesheet">
        <link href="<?php echo $static_url; ?>/libs/tobii/css/tobii.min.css" rel="stylesheet">
        <link href="<?php echo $static_url; ?>/libs/tiny-slider/tiny-slider.css" rel="stylesheet">
        <link href="<?php echo $static_url; ?>/libs/js-datepicker/datepicker.min.css" rel="stylesheet">
        <!-- Main Css -->
        <link href="<?php echo $static_url; ?>/libs/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo $static_url; ?>/css/output.css">
        <link href="<?php echo $static_url; ?>/css/tailwind.css" rel="stylesheet" type="text/css">

    </head>
    
    <body class="dark:bg-slate-900">

        <?php
            // Define an associative array for mapping page values to navbar files
            $navbarFiles = [
                'nav' => 'navbar-user.php',
                'nav2' => 'navbar-user.php',
                'nav3' => 'navbar-user.php',
                'nav4' => 'navbar-user.php',
            ];

            // Check if the page exists in the array, and include the corresponding file
            if (array_key_exists($page, $navbarFiles)) {
                include $navbarFiles[$page];
            } else {
                include 'navbar-user.php'; // Default navbar
            }
        ?>

        <!-- Main Content -->
        <main>
            <?php echo $hero_content ?? '<!-- Default hero content here -->'; ?>
        </main>

        <?php
            include 'footer.php'; // Include the footer
        ?>

        <!-- Switcher -->
        <div class="fixed top-1/4 -left-2 z-50">
            <span class="relative inline-block rotate-90">
                <input type="checkbox" class="checkbox opacity-0 absolute" id="chk">
                <label class="label bg-slate-900 dark:bg-white shadow-sm dark:shadow-gray-800 cursor-pointer rounded-full flex justify-between items-center p-1 w-14 h-8" for="chk">
                    <i data-feather="moon" class="w-[18px] h-[18px] text-yellow-500"></i>
                    <i data-feather="sun" class="w-[18px] h-[18px] text-yellow-500"></i>
                    <span class="ball bg-white dark:bg-slate-900 rounded-full absolute top-[2px] left-[2px] w-7 h-7"></span>
                </label>
            </span>
        </div>

        <!-- <div class="fixed top-1/2 -right-11 z-50 hidden sm:block">
            <a href="https://1.envato.market/travosy" target="_blank" class="py-1 px-3 relative inline-block rounded-t-md -rotate-90 bg-white dark:bg-slate-900 shadow-md dark:shadow-sm dark:shadow-gray-800 font-semibold"><i class="mdi mdi-cart-outline me-1"></i> Download</a>
        </div> -->
        <!-- Switcher -->

        <!-- LTR & RTL Mode Code -->
        <div class="fixed top-[40%] -left-3 z-50">
            <a href="" id="switchRtl">
                <span class="py-1 px-3 relative inline-block rounded-b-md -rotate-90 bg-white dark:bg-slate-900 shadow-md dark:shadow-sm dark:shadow-gray-800 font-semibold rtl:block ltr:hidden" >LTR</span>
                <span class="py-1 px-3 relative inline-block rounded-b-md -rotate-90 bg-white dark:bg-slate-900 shadow-md dark:shadow-sm dark:shadow-gray-800 font-semibold ltr:block rtl:hidden">RTL</span>
            </a>
        </div>
        <!-- LTR & RTL Mode Code -->

        <!-- Back to top -->
        <a href="#" onclick="topFunction()" id="back-to-top" class="back-to-top fixed hidden text-lg rounded-md z-10 bottom-5 end-5 size-8 text-center bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white justify-center items-center"><i class="mdi mdi-arrow-up"></i></a>
        <!-- Back to top --> 

        <!-- JAVASCRIPTS -->
        <script src="<?php echo $static_url; ?>/libs/swiper/js/swiper.min.js"></script>
        <script src="<?php echo $static_url; ?>/libs/gumshoejs/gumshoe.polyfills.min.js"></script>
        <script src="<?php echo $static_url; ?>/libs/tobii/js/tobii.min.js"></script>
        <script src="<?php echo $static_url; ?>/libs/tiny-slider/min/tiny-slider.js"></script>
        <script src="<?php echo $static_url; ?>/libs/jarallax/jarallax.min.js"></script>
        <script src="<?php echo $static_url; ?>/libs/js-datepicker/datepicker.min.js"></script>
        <script src="<?php echo $static_url; ?>/js/easy_background.js"></script>
        <script src="<?php echo $static_url; ?>/libs/feather-icons/feather.min.js"></script>
        <script src="<?php echo $static_url; ?>/js/plugins.init.js"></script>
        <script src="<?php echo $static_url; ?>/js/app.js"></script>
        <!-- JAVASCRIPTS -->
    </body>
</html>