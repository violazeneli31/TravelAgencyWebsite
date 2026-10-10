<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        { header("Location: ../login.php"); exit; }
    }

    if($_SESSION['role'] != "user"){
        { header("Location: ../index.php"); exit; }
    }
    $base_dir = __DIR__ . '/../Base';
    $static_url = '/Travosy/assets'; // Ensure this is the correct path

    // Include the common navlink content
    ob_start();
    $navlink_content = ob_get_clean();
    $page= 'nav3';
    include '../db.php';

    $user_id = $_SESSION['user_id'];
    $success = "";
    $error = "";

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['send'])) {  // Form 1: Personal details
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $nationality = $_POST['nationality'];
            $username = $_POST['username'];

            $sql = "UPDATE users SET first_name=?, last_name=?, nationality=?, username=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $first_name, $last_name, $nationality, $username, $user_id);

            $stmt->execute();

            if ($stmt->affected_rows >= 0) {
                $success_personal = "Personal details updated successfully.";
            }

            $stmt->close();
        }

        if (isset($_POST['update_contact'])) {  // Form 2: Contact info
            $phone = $_POST['phone'];
            $email = $_POST['email'];

            $sql = "UPDATE users SET phone=?, email=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $phone, $email, $user_id);

            $stmt->execute();

            if ($stmt->affected_rows >= 0) {
                $success_contact = "Contact details updated successfully.";
            }

            $stmt->close();
        }
        
        // CHANGE PASSWORD
        if (isset($_POST['change_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
                $error = "All password fields are required.";
            } else if ($new_password !== $confirm_password) {
                $error = "New passwords do not match.";
            } else if (strlen($new_password) < 6) {
                $error = "Password must be at least 6 characters long.";
            } else {
                // Get the current password hash from database
                $checkStmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
                $checkStmt->bind_param("i", $user_id);
                $checkStmt->execute();
                $checkStmt->bind_result($current_hash);
                $checkStmt->fetch();
                $checkStmt->close();
                
                // Encrypt old password with salt and verify
                $salt = "poci";
                $old_password_encrypted = md5($old_password . $salt);
                
                // Debug: Check what we're comparing (remove this after testing)
                // echo "Database hash: " . $current_hash . "<br>";
                // echo "Entered password encrypted: " . $old_password_encrypted . "<br>";
                
                if ($old_password_encrypted !== $current_hash) {
                    $error = "Your current password is incorrect.";
                } else {
                    // Encrypt new password with salt
                    $new_password_encrypted = md5($new_password . $salt);
                    
                    // Update password in database
                    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                    $updateStmt->bind_param("si", $new_password_encrypted, $user_id);
                    $updateStmt->execute();
                    
                    if ($updateStmt->affected_rows > 0) {
                        $success = "Password has been updated successfully.";
                    } else {
                        $error = "Failed to update password. Please try again.";
                    }
                    
                    $updateStmt->close();
                }
            }
        }

        // Handle account deletion
        if (isset($_POST['confirm_delete'])) {
            try {
                // Start transaction to ensure all related data is deleted
                $conn->begin_transaction();
                
                // Delete user's wishlist items
                $deleteWishlist = $conn->prepare("DELETE FROM wishlist WHERE user_id = ?");
                $deleteWishlist->bind_param("i", $user_id);
                $deleteWishlist->execute();
                $deleteWishlist->close();
                
                // Delete user's reviews
                $deleteReviews = $conn->prepare("DELETE FROM reviews WHERE user_id = ?");
                $deleteReviews->bind_param("i", $user_id);
                $deleteReviews->execute();
                $deleteReviews->close();
                
                // Delete the user account
                $deleteUser = $conn->prepare("DELETE FROM users WHERE user_id = ?");
                $deleteUser->bind_param("i", $user_id);
                $deleteUser->execute();
                $deleteUser->close();
                
                // Commit the transaction
                $conn->commit();
                
                // Redirect to the index page
                echo "<script>alert('Your account has been successfully deleted.'); window.location.href = 'index.php';</script>";
                exit;
            } catch (Exception $e) {
                // If any error occurs, roll back the transaction
                $conn->rollback();
                echo "<p class='text-red-500 mt-2'>Failed to delete the account. Please try again.</p>";
            }
        }
    }

    // Get user info to pre-fill the form
    $sql = "SELECT first_name, last_name, nationality, username, phone, email FROM users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    ob_start();
?>

<!-- Start Hero -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/cta.jpg'; ?>');"></div>
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">
            <div class="lg:w-1/4 md:w-1/3 md:px-3">
                <div class="sidebar">
                    <?php include 'user-sidebar.php'; ?>
                </div>
            </div>
            
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
            
                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
                    <h5 class="text-lg font-semibold mb-4">Edit your personal details:</h5>
                    
                    <?php if(isset($success_personal)): ?>
                        <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
                            <?php echo $success_personal; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                    
                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5">
                            <div>
                                <label class="form-label font-medium">First Name : <span class="text-red-600"></span></label>
                                <div class="form-icon relative mt-2">
                                <i data-feather="user" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                </div>
                            </div>
                            <div>
                                <label class="form-label font-medium">Last Name : <span class="text-red-600"></span></label>
                                <div class="form-icon relative mt-2">
                                <i data-feather="user-check" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                </div>
                            </div>
                            <div>
                                <label class="form-label font-medium">Nationality : <span class="text-red-600"></span></label>
                                <div class="form-icon relative mt-2">
                                <i data-feather="flag" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" name="nationality" value="<?php echo htmlspecialchars($user['nationality']); ?>" required class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                </div>
                                
                            </div>
                            <div>
                                <label class="form-label font-medium">Username:</label>
                                <div class="form-icon relative mt-2">
                                <i data-feather="user" class="w-4 h-4 absolute top-3 start-4"></i>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                </div>
                            </div>
                        </div><!--end grid-->

                        <input type="submit" id="submit" name="send" class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-5" value="Save Changes">
                    </form><!--end form-->
                </div>

                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900 mt-6">
                    <h5 class="text-lg font-semibold mb-4">Edit your contact details:</h5>
                    
                    <?php if(isset($success_contact)): ?>
                        <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
                            <?php echo $success_contact; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                            
                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5">
                            <div>
                                <label class="form-label font-medium">Phone No. :</label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="phone" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input name="phone" id="phone" type="text" value="<?php echo htmlspecialchars($user['phone']); ?>" required class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                </div>
                            </div>

                            <div>
                                <label class="form-label font-medium">Your Email : <span class="text-red-600"></span></label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="mail" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                </div>
                            </div>
                        </div>

                        <input type="submit" id="submit" name="update_contact" class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-5" value="Save Changes">
                    </form>
                </div>

                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900 mt-6">
                    <h5 class="text-lg font-semibold mb-4">Change password :</h5>
                    
                    <?php if (!empty($error)): ?>
                        <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="form-label font-medium">Old password :</label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="key" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input type="password" name="old_password" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Old password" required>
                                </div>
                            </div>

                            <div>
                                <label class="form-label font-medium">New password :</label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="key" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input type="password" name="new_password" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="New password" required>
                                </div>
                            </div>

                            <div>
                                <label class="form-label font-medium">Re-type New password :</label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="key" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input type="password" name="confirm_password" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Re-type New password" required>
                                </div>
                            </div>
                        </div><!--end grid-->

                        <button type="submit" name="change_password" class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-5">Save password</button>
                    </form>
                </div>

                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900 mt-6">
                    <h5 class="text-lg font-semibold mb-5 text-red-600">Delete Account :</h5>

                    <p class="text-slate-400 mb-4">Do you want to delete the account? Please press below "Delete" button</p>

                    <button onclick="showDeleteDialog()" class="py-2 px-5 font-semibold text-white bg-red-600 rounded-md mt-5">Delete Account
                    </button>
                </div>
                
                <!-- Confirmation Dialog Modal -->
                <div id="confirmDialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-lg shadow-lg w-full max-w-sm text-center">
                        <h2 class="text-lg font-semibold mb-4">Are you sure you want to delete your account?</h2>
                        <p class="text-sm text-gray-500 mb-4">This action cannot be undone. All your data, including your wishlist and reviews, will be permanently deleted.</p>
                        <div class="flex justify-center gap-4">
                            <form method="POST">
                                <input type="hidden" name="confirm_delete" value="yes">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md">Delete Account</button>
                            </form>
                            <button onclick="hideDeleteDialog()" class="bg-gray-300 dark:bg-gray-700 text-black dark:text-white px-4 py-2 rounded-md">Cancel</button>
                        </div>
                    </div>
                </div>
                
                <script>
                    function showDeleteDialog() {
                        document.getElementById('confirmDialog').classList.remove('hidden');
                    }

                    function hideDeleteDialog() {
                        document.getElementById('confirmDialog').classList.add('hidden');
                    }
                </script>
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Hero -->

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/base-user.php";
?>