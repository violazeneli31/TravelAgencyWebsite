<?php
session_start();
if(!isset($_SESSION['user_id'])){
    { header("Location: ../login.php"); exit; }
}

if($_SESSION['role'] != "admin"){
    { header("Location: ../index.php"); exit; }
}
$base_dir = __DIR__ . '/../'; 
$static_url = '/Travosy/assets';
include '../config.php';

// Start capturing output to later include in the layout
ob_start();
$navlink_content = ob_get_clean();
$page = 'nav2';

ob_start();
include '../config.php';

// Get total users, bookings, and revenue (previously included queries)
$total_users_query = "SELECT COUNT(*) FROM users";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_row($total_users_result)[0];

$total_bookings_query = "SELECT COUNT(*) FROM bookings";
$total_bookings_result = mysqli_query($conn, $total_bookings_query);
$total_bookings = mysqli_fetch_row($total_bookings_result)[0];

$total_revenue_query = "SELECT SUM(personalised_price) FROM bookings";
$total_revenue_result = mysqli_query($conn, $total_revenue_query);
$total_revenue = mysqli_fetch_row($total_revenue_result)[0];

// Get monthly revenue for the last 5 months
$query = "
    SELECT 
        MONTH(date_booked) AS month, 
        SUM(personalised_price) AS revenue
    FROM bookings
    WHERE date_booked >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY MONTH(date_booked)
    ORDER BY MONTH(date_booked);
";

$result = mysqli_query($conn, $query);

// Prepare arrays for the months and revenue data
$months = [];
$revenues = [];
$current_month = date('n'); // Get the current month as a number

// Calculate the last 5 months
$last_five_months = [];
for ($i = 4; $i >= 0; $i--) {  // Start from the 4th previous month (last 5 months)
    $last_five_months[] = ($current_month - $i + 11) % 12 + 1;  // Ensure month wraps correctly (1-12)
}

// Format months for the chart (January, February, etc.)
$months = array_map(function ($month) {
    return date('M', mktime(0, 0, 0, $month, 1)); // Convert month number to short month name
}, $last_five_months);

// Initialize revenue array with 0s for the last 5 months
$revenues = array_fill(0, 5, 0);

// Populate the revenue array with actual data from the query
while ($row = mysqli_fetch_assoc($result)) {
    $month_index = array_search($row['month'], $last_five_months);
    if ($month_index !== false) {
        $revenues[$month_index] = $row['revenue'];  // Fill the corresponding month with actual revenue
    }
}

// Convert PHP arrays to JavaScript variables
$months_json = json_encode($months);
$revenues_json = json_encode($revenues);

?>

<!-- Start Hero Section -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover"
             style="background-image: url('<?php echo $static_url; ?>/images/bg/cta.jpg');"></div>
    </div>

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">

            <!-- Sidebar Include -->
            <?php include 'admin-sidebar.php'; ?> 

            <!-- Main Content Section -->

            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
                    <h5 class="text-xl font-semibold mb-4">Admin Dashboard</h5>
                    <p class="text-slate-400 ">Welcome to the admin panel. Here's a quick overview of key statistics:</p>
                </div>

                <!-- Overview Stats Section -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-6">
                    <!-- Total Users -->
                    <div class="bg-white p-6 rounded-md shadow-sm dark:shadow-gray-800 dark:bg-slate-800">
                        <h6 class="text-lg font-medium text-slate-600 dark:text-slate-200">Total Users</h6>
                        <p class="text-3xl font-semibold text-indigo-600 dark:text-indigo-400"><?php echo $total_users; ?></p>
                    </div>

                    <!-- Total Bookings -->
                    <div class="bg-white p-6 rounded-md shadow-sm dark:shadow-gray-800 dark:bg-slate-800">
                        <h6 class="text-lg font-medium text-slate-600 dark:text-slate-200">Total Bookings</h6>
                        <p class="text-3xl font-semibold text-teal-600 dark:text-teal-400"><?php echo $total_bookings; ?></p>
                    </div>

                    <!-- Total Revenue -->
                    <div class="bg-white p-6 rounded-md shadow-sm dark:shadow-gray-800 dark:bg-slate-800">
                        <h6 class="text-lg font-medium text-slate-600 dark:text-slate-200">Total Revenue</h6>
                        <p class="text-3xl font-semibold text-green-600 dark:text-green-400"><?php echo '$' . number_format($total_revenue, 2); ?></p>
                    </div>
                </div>

                <!-- Revenue Chart Section -->
                <div class="p-6 mt-6 bg-white rounded-md shadow-sm dark:shadow-gray-800 dark:bg-slate-800">
                    <h6 class="text-lg font-medium text-slate-600 dark:text-slate-200">Revenue Trend</h6>
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Create the chart for revenue trends
    var months = <?php echo $months_json; ?>;
    var revenues = <?php echo $revenues_json; ?>;

    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,  // Set the months as labels
            datasets: [{
                label: 'Revenue($)',
                data: revenues,  // Set the revenue data
                borderColor: 'rgb(15, 136, 136)',
                fill: false,  // Set to true if you want to fill the area under the line
                tension: 0.1  // Smooths the line
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Revenue Trend Over the Last 5 Months'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            }
        }
    });
</script>

<script>
    feather.replace();
</script>

<?php
$hero_content = ob_get_clean();
include $base_dir . 'Base/style/base-user.php';
?>
