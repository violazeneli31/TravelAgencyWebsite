<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(!isset($_SESSION['user_id'])){
        { header("Location: ../login.php"); exit; }
    }

    if($_SESSION['role'] != "admin"){
        { header("Location: ../index.php"); exit; }
    }
    $base_dir = __DIR__ . '/../'; 
    $static_url = '/Travosy/assets';
    include '../config.php';

    $page = 'nav2';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: view-all-users.php');
        exit();
    }

    $id = intval($_GET['id']);
    $error = '';

    $query = "SELECT * FROM users WHERE user_id = $id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        header('Location: view-all-users.php');
        exit();
    }

    $user = mysqli_fetch_assoc($result);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $birthday = $_POST['birthday'];
        $passport_number = mysqli_real_escape_string($conn, $_POST['passport_number']);
        $gender = $_POST['gender'];
        $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $role = $_POST['role'];

        if (empty($username) || empty($email)) {
            $error = 'Username and Email are required.';
        } else {
            $update_query = "UPDATE users SET 
                username='$username', email='$email', first_name='$first_name', last_name='$last_name', 
                birthday='$birthday', passport_number='$passport_number', gender='$gender', 
                nationality='$nationality', phone='$phone', role='$role' 
                WHERE user_id = $id";

            if (mysqli_query($conn, $update_query)) {
                header("Location:view-all-users.php");
                exit();
            } else {
                $error = "Error updating record: " . mysqli_error($conn);
            }
        }
    }

    ob_start();
?>

<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover"
             style="background-image: url('<?php echo $static_url; ?>/images/bg/cta.jpg');"></div>
    </div>

    <div class="container relative md:mt-26 mt-16">
        <div class="md:flex flex-col items-center">
            <h1 class="text-2xl font-bold text-white-700 dark:text-white-300 text-center mb-8">Edit User</h1>

            <?php if ($error): ?>
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6 text-center max-w-md w-full">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="edit-user.php?id=<?= $id ?>" method="post" 
                class="w-full max-w-2xl bg-white dark:bg-gray-900 p-6 border border-blue-300 dark:border-gray-700 
                       rounded shadow-sm space-y-4 text-gray-900 dark:text-gray-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    $fields = [
                        ['username', 'Username*'],
                        ['email', 'Email*', 'email'],
                        ['first_name', 'First Name'],
                        ['last_name', 'Last Name'],
                        ['birthday', 'Birthday', 'date'],
                        ['passport_number', 'Passport Number'],
                        ['nationality', 'Nationality'],
                        ['phone', 'Phone']
                    ];

                    foreach ($fields as $field) {
                        $name = $field[0];
                        $label = $field[1];
                        $type = $field[2] ?? 'text';
                        echo "<div>
                            <label for=\"$name\" class=\"font-semibold\">$label</label>
                            <input type=\"$type\" id=\"$name\" name=\"$name\" value=\"" . htmlspecialchars($user[$name]) . "\"
                                class=\"w-full p-2 border rounded border-gray-300 dark:border-gray-600 
                                       bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100\" />
                        </div>";
                    }
                    ?>
                    <div>
                        <label for="gender" class="font-semibold">Gender</label>
                        <select id="gender" name="gender"
                            class="w-full p-2 border rounded border-gray-300 dark:border-gray-600 
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="" <?= $user['gender'] == '' ? 'selected' : '' ?>>Select Gender</option>
                            <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= $user['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="role" class="font-semibold">Role</label>
                        <select id="role" name="role"
                            class="w-full p-2 border rounded border-gray-300 dark:border-gray-600 
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-3 pt-6">
    <a href="view-all-users.php" 
       class="px-4 py-2 text-sm rounded bg-gray-300 dark:bg-gray-700 
              text-black dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 
              text-center">
       ← Cancel
    </a>
    <input type="submit" value="Update User" 
           class="px-4 py-2 text-sm rounded bg-gray-300 dark:bg-gray-700 
              text-black dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 
              text-center" />
</div>

            </form>

            <a class="btn-add mt-6 text-white-600 dark:text-white-400" href="view-all-users.php">← Go Back</a>
        </div>
    </div>
</section>

<script>
    feather.replace();
    function toggleDarkMode() {
        document.getElementById("body").classList.toggle("dark-mode");
    }
</script>

<?php
$hero_content = ob_get_clean();
include $base_dir . 'Base/style/base.php';
?>
