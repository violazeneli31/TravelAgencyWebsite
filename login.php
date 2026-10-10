<?php
$base_dir = 'Base';
$static_url = '/Travosy/assets';

session_start();

$error = "";
$user_profile_pic = $static_url . "/images/logo-icon.png"; // Default logo
$show_profile_pic = false;

$redirectParam = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("config.php");
    $con = new mysqli($SERVER, $USERNAME, $PASSWORD, $DATABASE);

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    $username = isset($_POST['username']) ? $con->real_escape_string($_POST['username']) : '';
    $password = isset($_POST['LoginPassword']) ? $_POST['LoginPassword'] : '';
    $redirectFromPost = isset($_POST['redirect']) ? $_POST['redirect'] : '';

    if ($username !== '' && $password !== '') {
        $stmt = $con->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                // Show profile picture if exists
                if (!empty($user['profile_pic'])) {
                    if (strpos($user['profile_pic'], '/') === 0 || strpos($user['profile_pic'], 'http') === 0) {
                        $user_profile_pic = $user['profile_pic'];
                    } else {
                        $user_profile_pic = $static_url . '/images/client/' . $user['profile_pic'];
                    }
                }

                $show_profile_pic = true;

                // Set session
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];

                // Handle redirect
                $redirectUrl = '';
                if (!empty($redirectFromPost)) {
                    $redirectUrl = $redirectFromPost;
                } elseif (!empty($_SESSION['redirect_after_login'])) {
                    $redirectUrl = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                }

                if (!empty($redirectUrl)) {
                    { header("Location: " . $redirectUrl); exit; }
                } else {
                    if ($user['role'] === 'admin') {
                        { header("Location: admin/admin-dashboard.php"); exit; }
                    } else {
                        { header("Location: user/user-profile.php"); exit; }
                    }
                }
                exit;
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }

        $stmt->close();
    } else {
        $error = "Please enter both username and password.";
    }

    $con->close();
}

// Layout and form rendering
ob_start();
?>

<section class="md:h-screen py-36 flex items-center relative overflow-hidden zoom-image">
    <div class="absolute inset-0 image-wrap z-1 bg-no-repeat bg-center bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/6.jpg'; ?>');"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900 z-2" id="particles-snow"></div>
    <div class="container relative z-3">
        <div class="flex justify-center">
            <div class="max-w-[400px] w-full m-auto p-6 bg-white dark:bg-slate-900 shadow-md dark:shadow-gray-700 rounded-md">
                <a href="index.php">
                    <img src="<?php echo htmlspecialchars($show_profile_pic ? $user_profile_pic : ($static_url . '/images/logo-icon.png')); ?>" class="mx-auto mb-4" alt="Logo or Profile">
                </a>
                <h5 class="my-6 text-xl font-semibold">Login</h5>
                <?php if ($error): ?>
                    <div style="color: red; margin-bottom: 12px; text-align: center;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form class="text-start" action="login.php<?php echo !empty($redirectParam) ? '?redirect=' . urlencode($redirectParam) : ''; ?>" method="POST">
                    <?php if (!empty($redirectParam)): ?>
                        <input type="hidden" name="redirect" value="<?php echo $redirectParam; ?>">
                    <?php endif; ?>
                    <div class="grid grid-cols-1">
                        <div class="mb-4">
                            <label class="font-semibold" for="username">Username:</label>
                            <input id="username" name="username" type="text" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Username" required>
                        </div>
                        <div class="mb-4">
                            <label class="font-semibold" for="LoginPassword">Password:</label>
                            <input id="LoginPassword" name="LoginPassword" type="password" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Password" required>
                        </div>
                        <div class="flex justify-between mb-4">
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 me-2 accent-red-600" type="checkbox" id="RememberMe">
                                <label class="text-slate-400" for="RememberMe">Remember me</label>
                            </div>
                            <p class="text-slate-400 mb-0"><a href="forgot-password.php" class="text-slate-400">Forgot password?</a></p>
                        </div>
                        <div class="mb-4">
                            <input type="submit" class="py-2 px-5 inline-block tracking-wide duration-500 text-base bg-red-500 text-white rounded-md w-full" value="Login">
                        </div>
                        <div class="text-center">
                            <span class="text-slate-400 me-2">Don't have an account?</span>
                            <a href="signup.php" class="text-slate-900 dark:text-white font-bold">Sign Up</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$hero_content = ob_get_clean();
include "$base_dir/style/no-header.php";
?>
