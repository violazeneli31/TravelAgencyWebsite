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

  // Handle data fetching based on pagination and filters
  $search = isset($_GET['search']) ? $_GET['search'] : '';
  $package_filter = isset($_GET['package']) ? $_GET['package'] : '';
  $search_sql = mysqli_real_escape_string($conn, $search);
  $package_filter_sql = mysqli_real_escape_string($conn, $package_filter);

  // Get the total results
  $per_page = 10;
  $result_total = mysqli_query($conn, "SELECT COUNT(*) FROM bookings WHERE (booking_id LIKE '%$search_sql%' OR user_id LIKE '%$search_sql%' OR package_id LIKE '%$search_sql%') AND (package_id LIKE '%$package_filter_sql%' OR '' = '$package_filter_sql')");
  $total_results = mysqli_fetch_row($result_total)[0];
  $total_pages = ceil($total_results / $per_page);

  // Get the current page
  $page_num = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
  $start = ($page_num - 1) * $per_page;

  // Build the query with filters
  $query = "SELECT b.*, u.first_name, u.last_name, u.email, p.name AS package_name, p.description AS package_description,
            CONCAT(u.first_name, ' ', u.last_name) AS full_name
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.user_id
            LEFT JOIN packages p ON b.package_id = p.package_id
            WHERE (b.booking_id LIKE '%$search_sql%' OR u.first_name LIKE '%$search_sql%' OR u.last_name LIKE '%$search_sql%' OR p.name LIKE '%$search_sql%')
            AND (b.package_id LIKE '%$package_filter_sql%' OR '' = '$package_filter_sql')
            ORDER BY b.booking_id DESC LIMIT $start, $per_page";

  $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Bookings</title>
  <style>
    /* General Page Styles */
    /* General Page Styles */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 20px;
  background-color: #3#1e293b; /* Light background color */
  color: #333; /* Default text color for light mode */
}

/* Dark Mode Body */
body.dark-mode {
  background-color: #3#1e293b;/* Dark background */
  color: #fff; /* White text in dark mode */
}

/* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Table Headers */
th,
td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
  font-size: 14px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

/* Table Header Color */
th {
  background-color: rgba(24, 92, 97, 0.77);
  color: white;
}

/* Table Rows */
td {
  color: #333; /* Default row color for light mode */
}

/* Dark Mode Table Rows */
body.dark-mode td {
  color: #fff; /* White text color for table rows in dark mode */
}

/* Filter Section */
.filter-container {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.filter-container div {
  flex: 1;
  margin-right: 10px;
}

#search,
#package {
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #ddd;
  font-size: 14px;
  width: 100%;
  background-color: #fff;
}

#search:focus,
#package:focus {
  outline: none;
  border-color: #5e75c2;
}

/* Button Styling */
.btn-add {
  display: inline-block;
  padding: 10px 15px;
  background-color: rgba(24, 92, 97, 0.77);
  color: white;
  border-radius: 5px;
  text-decoration: none;
  margin-top: 20px;
}

.btn-add:hover {
  background-color: #5e75c2;
}

/* Pagination Section */
.pagination {
  text-align: center;
  margin-top: 20px;
}

.pagination a {
  margin: 0 5px;
  padding: 5px 10px;
  border: 1px solid rgba(24, 92, 97, 0.77);
  border-radius: 4px;
  color: rgba(24, 92, 97, 0.77);
}

.pagination a:hover {
  background-color: rgba(24, 92, 97, 0.77);
  color: white;
}

/* Responsive Table Styling */
@media (max-width: 768px) {
  table {
    font-size: 12px;
  }

  th,
  td {
    padding: 8px;
  }

  .pagination a {
    font-size: 12px;
    padding: 3px 7px;
  }
}

  </style>
</head>

<body id="body">
  <!-- Header Section -->
  <section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
      <div class="relative overflow-hidden md:rounded-md shadow-sm dark:white-800 h-52 bg-center bg-no-repeat bg-cover"
        style="background-image: url('<?php echo $static_url; ?>/images/bg/cta.jpg');"></div>
    </div>
    <div class="container relative md:mt-24 mt-16">
      <div class="md:flex justify-center">
        <!-- Main Content Section -->
        <div class="w-full md:w-4/5 px-3 mt-6 md:mt-0">
          <!-- Main Card -->
          <div class="p-8 rounded-xl shadow-xl dark:white-800 bg-white dark:bg-slate-900 transition-all duration-300">
            <!-- Title and Description -->
            <div class="mb-6">
              <h2 class="text-2xl font-bold text-black-800 dark:text-black mb-2">Booking Details</h2>
              <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed">
                Here you can see all the details for the selected booking.
              </p>
            </div>

            <!-- Filter Form -->
            <div class="filter-container">
              <form action="admin-viewBookings.php" method="GET">
                <div>
                  <label for="search">Search</label>
                  <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search by booking ID, user, or package">
                </div>
                <div>
                  <label for="package">Filter by Package</label>
                  <select id="package" name="package">
                    <option value="">All Packages</option>
                    <?php
                    $package_query = "SELECT * FROM packages WHERE status = 'active'";
                    $package_result = mysqli_query($conn, $package_query);
                    while ($package = mysqli_fetch_assoc($package_result)) {
                        $selected = ($package['package_id'] == $package_filter) ? 'selected' : '';
                        echo "<option value='{$package['package_id']}' $selected>{$package['name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div>
                  <button type="submit" class="btn-add">Apply Filters</button>
                </div>
              </form>
            </div>

            <!-- "View Paginated" link before the table -->
            <?php if (isset($_GET['all']) && $_GET['all'] == 1): ?>
              <p><a href="admin-viewBookings.php">&laquo; Go back to paginated view</a></p>
            <?php endif; ?>

            <table>
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>User Full Name</th>
                  <th>Package ID</th>
                  <th>Date Booked</th>
                  <th>Adults</th>
                  <th>Children</th>
                  <th>Total Price</th>
                  <th>View</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                  <tr>
                    <td><?php echo $row['booking_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['package_id']; ?></td>
                    <td><?php echo date("d-m-Y", strtotime($row['date_booked'])); ?></td>
                    <td><?php echo $row['nr_adults']; ?></td>
                    <td><?php echo $row['nr_children']; ?></td>
                    <td>$<?php echo number_format($row['personalised_price'], 2); ?></td>
                    <td><a href="view-booking-details.php?id=<?php echo $row['booking_id']; ?>" class="btn-add">View</a></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

            <!-- Pagination Section -->
            <?php if (!isset($_GET['all']) || $_GET['all'] != 1): ?>
              <div class="pagination">
                <a href="admin-viewBookings.php?all=1">View All</a> |
                <strong>View Page:</strong>
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='admin-viewBookings.php?page=$i'>$i</a> ";
                }
                ?>
              </div>
            <?php endif; ?>

            <!-- Go Back Button -->
            <a class="btn-add" href="admin-dashboard.php">Go Back</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    // Toggle Dark Mode
    function toggleDarkMode() {
      var body = document.getElementById("body");
      body.classList.toggle("dark-mode");
    }

    
  </script>

</body>
</html>

<script>
  feather.replace();
</script>

<?php
$hero_content = ob_get_clean();
include $base_dir . 'Base/style/base-user.php';
?>
