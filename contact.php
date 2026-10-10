<?php
$base_dir = __DIR__ . '/Base';
$static_url = '/Travosy/assets'; // Ensure this is the correct path

// Include the common navlink content
ob_start();
$navlink_content = ob_get_clean();
$page= 'nav3';

// Optionally define the Hero block content
ob_start();
?>
       
<!-- Start Hero -->
<section class="relative table w-full items-center py-36 bg-top bg-no-repeat bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/cta.jpg'; ?>');">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
    <div class="container relative">
        <div class="grid grid-cols-1 pb-8 text-center mt-10">
            <h3 class="text-4xl leading-normal tracking-wider font-semibold text-white">Travosy Travel Agency</h3>
        </div><!--end grid-->
    </div><!--end container-->
    
    <div class="absolute text-center z-10 bottom-5 start-0 end-0 mx-3">
        <ul class="tracking-[0.5px] mb-0 inline-block">
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white/50 hover:text-white"><a href="index.php">Travosy</a></li>
            <li class="inline-block text-base text-white/50 mx-0.5 ltr:rotate-0 rtl:rotate-180"><i class="mdi mdi-chevron-right"></i></li>
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white" aria-current="page">Contact Us</li>
        </ul>
    </div>
</section><!--end section-->
<!-- End Hero -->       

<!-- Google Map -->
<div class="container-fluid relative mt-20">
    <div class="grid grid-cols-1">
        <div class="w-full leading-[0] border-0">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3088.037123100703!2d19.8057493!3d41.3127516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1350311df73f9aaf%3A0x6a2bff90fa51b370!2sKomuna%20e%20Parisit%2C%20Tirana%2C%20Albania!5e0!3m2!1sen!2s!4v1715954054200!5m2!1sen!2s" style="border:0" class="w-full h-[500px]" allowfullscreen></iframe>
        </div>
    </div><!--end grid-->
</div><!--end container-->
<!-- Google Map -->

<!-- Start Section-->
<section class="relative lg:py-24 py-16">
    <div class="container">
        <div class="grid md:grid-cols-12 grid-cols-1 items-center gap-6">
            <div class="lg:col-span-7 md:col-span-6">
                <img src="<?php echo $static_url; ?>/images/travel-train-station.svg" class="w-full max-w-[500px] mx-auto" alt="">
            </div>  
            <div class="lg:col-span-5 md:col-span-6">
                <div class="lg:ms-5">
                    <div class="bg-white dark:bg-slate-900 rounded-md shadow-sm dark:shadow-gray-800 p-6">
                        <h3 class="mb-6 text-2xl leading-normal font-semibold">Get in touch !</h3>

                        <form method="post" action="submit_contact.php"name="myForm" id="myForm" onsubmit="return validateForm()">
                            <p class="mb-0" id="error-msg"></p>
                            <div id="simple-msg"><?php if (isset($_GET['sent'])): ?><p class="mb-3 font-semibold <?php echo $_GET['sent'] === '1' ? 'text-green-600' : 'text-red-500'; ?>"><?php echo $_GET['sent'] === '1' ? 'Thank you! Your message has been sent.' : 'Please fill in your name, a valid email and a message.'; ?></p><?php endif; ?></div>
                            <div class="grid lg:grid-cols-12 grid-cols-1 gap-3">
                                <div class="lg:col-span-6">
                                    <label for="name" class="font-semibold">Your Name:</label>
                                    <input name="name" id="name" type="text" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Name :">
                                </div>

                                <div class="lg:col-span-6">
                                    <label for="email" class="font-semibold">Your Email:</label>
                                    <input name="email" id="email" type="email" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Email :">
                                </div>

                                <div class="lg:col-span-12">
                                    <label for="subject" class="font-semibold">Your Question:</label>
                                    <input name="subject" id="subject" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Subject :">
                                </div>

                                <div class="lg:col-span-12">
                                    <label for="comments" class="font-semibold">Your Comment:</label>
                                    <textarea name="comments" id="comments" class="mt-2 w-full py-2 px-3 h-28 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Message :"></textarea>
                                </div>
                            </div>
                            <button type="submit" id="submit" name="send" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-2">Send Message</button>
                        </form>
                    </div>
                </div>
                </div>
        </div>
    </div><!--end container-->
    
    <div class="container lg:mt-24 mt-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-6">
            
            <!-- contacts code  -->
            <?php
                include "$base_dir/Components/Contact/contacts.php";
            ?>

        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Section-->

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/base.php";
?>