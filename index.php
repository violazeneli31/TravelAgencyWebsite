<?php
$base_dir = __DIR__ . '/Base';
$static_url = '/Travosy/assets'; // Ensure this is the correct path

// Include the common navlink content
ob_start();
$navlink_content = ob_get_clean();
$page= 'nav';

// Optionally define the Hero block content
ob_start();
?>
        
<!-- Start Hero -->
<section class="swiper-slider-hero relative block h-screen" id="home">
    <div class="swiper-container absolute end-0 top-0 w-full h-full">
        <div class="swiper-wrapper">
            <div class="swiper-slide flex items-center overflow-hidden">
                <div class="slide-inner absolute end-0 top-0 w-full h-full slide-bg-image flex items-center bg-center;" data-background="<?php echo $static_url; ?>/images/bg/2.jpg">
                    <div class="absolute inset-0 bg-slate-900/70"></div>
                    <div class="container relative">
                        <div class="grid grid-cols-1">
                            <div class="text-center">
                                <img src="<?php echo $static_url; ?>/images/map-plane.png" class="mx-auto w-[300px]" alt="">
                                <h1 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">Find Your Best <br> Travels Package</h1>
                                <p class="text-white/70 text-xl max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
                                
                                <div class="mt-6">
                                    <a href="./list.php" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">See More</a>
                                </div>
                            </div>
                        </div><!--end grid-->
                    </div><!--end container-->
                </div><!-- end slide-inner --> 
            </div> <!-- end swiper-slide -->

            <div class="swiper-slide flex items-center overflow-hidden">
                <div class="slide-inner absolute end-0 top-0 w-full h-full slide-bg-image flex items-center bg-center;" data-background="<?php echo $static_url; ?>/images/bg/3.jpg">
                    <div class="absolute inset-0 bg-slate-900/70"></div>
                    <div class="container relative">
                        <div class="grid grid-cols-1">
                            <div class="text-center">
                                <img src="<?php echo $static_url; ?>/images/map-plane.png" class="mx-auto w-[300px]" alt="">
                                <h1 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">Find Next Best <br> Place To Visit</h1>
                                <p class="text-white/70 text-xl max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
                                
                                <div class="mt-6">
                                    <a href="./list.php" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">See More</a>
                                </div>
                            </div>
                        </div><!--end grid-->
                    </div><!--end container-->
                </div><!-- end slide-inner --> 
            </div> <!-- end swiper-slide -->
        </div>
        <!-- end swiper-wrapper -->

        <!-- swipper controls -->
        <div class="swiper-pagination"></div>
        <!-- <div class="swiper-button-next bg-transparent w-[35px] h-[35px] leading-[35px] -mt-[30px] bg-none border border-solid border-white/50 text-white hover:bg-red-500 hover:border-red-500 rounded-full text-center"></div>
        <div class="swiper-button-prev bg-transparent w-[35px] h-[35px] leading-[35px] -mt-[30px] bg-none border border-solid border-white/50 text-white hover:bg-red-500 hover:border-red-500 rounded-full text-center"></div> -->
    </div><!--end container-->
</section><!--end section-->
<!-- Hero End -->


<!-- Start -->
<section class="relative overflow-hidden">

    <div class="container relative">
        <div class="grid grid-cols-1 text-center">
            <h3 class="text-2xl md:text-3xl font-semibold leading-normal mb-4 mt-10">LATEST TOUR PACKAGES</h3>
            <p class="text-slate-400 max-w-xl mx-auto">ADVENTURE STARTS WITH THE RIGHT PACKAGE</p>
        </div><!--end grid-->

        <div>                    
            
            <!-- tours-packages code  -->
            <?php
                include "$base_dir/Components/Hero/tours-packages.php";
            ?>

        </div><!--end grid-->
    </div><!--end container-->

    <div class="container relative">
        
        <!-- about code  -->
        <?php
            include "$base_dir/Components/Hero/about.php";
        ?>

    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16" style="margin-bottom:75px;">
        <div class="grid grid-cols-1 pb-6 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold"> ALL USER REVIEWS</h3>

            <p class="text-slate-400 max-w-xl mx-auto">BECAUSE YOUR FEEDBACK FUELS OUR JOURNEY</p>
        </div><!--end grid-->

        <div class="grid grid-cols-1 mt-6">
            <div class="tiny-three-item">
                
                <!-- reviews code  -->
                <?php
                    include "$base_dir/Components/Hero/reviews.php";
                ?>

            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section>
<!-- End -->

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/base.php";
?>