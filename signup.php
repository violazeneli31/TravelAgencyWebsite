<?php
session_start();
//$base_dir = DIR'/Base'; // REMOVED this line as requested
$static_url = '/Travosy/assets';

// Include the common navlink content
ob_start();
$navlink_content = ob_get_clean();

// Optionally define the Hero block content
ob_start();
?>

<section class="md:m-screen py-36 flex items-center relative -hidden zoom-image">
    <div class="absolute inset-0 image-wrap z-1 bg-no-repeat bg-center bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/6.jpg'; ?>');"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900 z-2" id="particles-snow"></div>
    <div class="container min-h-screen flex items-center justify-center relative z-3">

        <div class="w-full max-w-[400px] m-4 p-6 bg-white dark:bg-slate-900 shadow-md dark:shadow-gray-700 rounded-md">
            <a href="index.php"><img src="<?php echo $static_url; ?>/images/logo-icon.png" class="mx-auto" alt=""></a>
            <h5 class="my-6 text-xl font-semibold text-center">Signup</h5>
            <form class="text-start" action="signupback.php" method="POST">
                <div class="grid grid-cols-1">
                    <div class="mb-4">
                        <label class="font-semibold" for="RegisterName">Your Name:</label>
                        <input id="RegisterName" name="RegisterName" type="text" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Name" required>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="RegisterSurname">Your Surname:</label>
                        <input id="RegisterSurname" name="RegisterSurname" type="text" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Surname" required>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="RegisterBirth">Your Birthday:</label>
                        <input id="RegisterBirth" name="RegisterBirth" type="date" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" required>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="RegisterPassport">Your Passport Number:</label>
                        <input id="RegisterPassport" name="RegisterPassport" type="text" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="L12345678N" required>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="RegisterNationality">Your Nationality:</label>
                        <input id="RegisterNationality" name="RegisterNationality" type="text" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Nationality" required>
                    </div>

                    <div class="mb-2 font-semibold">Your Gender:</div>
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center gap-1">
                            <input id="GenderMale" name="gender" value="Male" type="radio" style="margin-right: 5px;">
                            <span>Male</span>
                        </label>
                        <label class="flex items-center gap-1">
                            <input id="GenderFemale" name="gender" value="Female" type="radio" style="margin-right: 5px;">
                            <span>Female</span>
                        </label>
                        <label class="flex items-center gap-1">
                            <input id="GenderOther" name="gender" value="Other" type="radio" style="margin-right: 5px;">
                            <span>Other</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="LoginEmail">Email Address:</label>
                        <input id="LoginEmail" name="LoginEmail" type="email" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="name@example.com" required>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="username">Username:</label>
                        <input id="username" name="username" type="text" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Username" required>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="RegisterPhoneNr">Phone Number:</label>
                        <input id="RegisterPhoneNr" name="RegisterPhoneNr" type="number" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="+1234567890" required>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold" for="LoginPassword">Password:</label>
                        <input id="LoginPassword" name="LoginPassword" type="password" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Password" required>
                    </div>

                    <div class="mb-4">
                        <div class="flex items-center w-full mb-0">
                            <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="1" name="AcceptTnC" id="AcceptTnC" required>
                            <label class="form-check-label text-slate-400" for="AcceptTnC">I Accept <a href="" class="text-red-500">Terms And Conditions</a></label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <input type="submit" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full" value="Register">
                    </div>

                    <div class="text-center">
                        <span class="text-slate-400 me-2">Already have an account?</span>
                        <a href="login.php" class="text-slate-900 dark:text-white font-bold inline-block">Sign in</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
$hero_content = ob_get_clean();

// Include the base template
include './Base/style/no-header.php';
?>