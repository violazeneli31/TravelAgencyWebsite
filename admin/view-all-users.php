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

  // Handle data fetching based on pagination
  if (isset($_GET['all']) && $_GET['all'] == 1) {
      // Show all users without pagination
      $query = "SELECT * FROM users ORDER BY user_id DESC";
      $result = mysqli_query($conn, $query);
  } else {
      // Show limited or paginated view
      $per_page = 3;
      $result_total = mysqli_query($conn, "SELECT * FROM users");
      $total_results = mysqli_num_rows($result_total);
      $total_pages = ceil($total_results / $per_page);

      if (isset($_GET['page']) && is_numeric($_GET['page'])) {
          $show_page = $_GET['page'];
          if ($show_page > 0 && $show_page <= $total_pages) {
              $start = ($show_page - 1) * $per_page;
          } else {
              $start = 0;
          }
      } else {
          $start = 0;
      }

      $query = "SELECT * FROM users ORDER BY user_id DESC LIMIT $start, $per_page";
      $result = mysqli_query($conn, $query);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User List</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #3#1e293b;
      color: #333;
    }
    h1 {
      color: rgb(24, 92, 97);
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      table-layout: auto;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
      font-size: 14px; /* Smaller font size */
      white-space: nowrap; /* Prevent text from wrapping */
      word-wrap: break-word; /* Break long words but keep content in one row */
      overflow: hidden; /* Ensure no overflow */
      text-overflow: ellipsis; /* Show ellipsis if the content is too long */
    }
    th {
      background-color: rgba(24, 92, 97, 0.77);
      color: white;
    }

    /* Light Theme Table Styling */
    .table-light tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .table-light tr:nth-child(odd) {
      background-color: #ffffff;
    }
    .table-light tr:hover {
      background-color: rgb(184, 225, 228);
    }

    /* Dark Theme Table Styling */
    body.dark-mode {
      background-color: #212530; /* Dark background for body in dark mode */
      color: white;
    }

    .table-dark th {
      background-color: #2c3e50; /* Darker blue for header */
      color: white;
    }
    .table-dark tr:nth-child(even) {
      background-color: #34495e; /* Dark background for even rows */
    }
    .table-dark tr:nth-child(odd) {
      background-color: #2a3e50; /* Dark background for odd rows */
    }
    .table-dark tr:hover {
      background-color: rgba(87, 110, 185, 0.3); /* Lighter background on hover */
    }

    /* Text color in Dark Mode */
    .table-dark td, .table-dark th {
      color: white;
    }

    a {
      color: rgba(24, 92, 97, 0.77);
      text-decoration: none;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }

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
    .btn-delete {
      background-color: red;
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 3px;
    }
    .btn-delete:hover {
      background-color: darkred;
    }
    .table-container {
      max-width: 130%;
      margin-bottom: 5px;
    }
  </style>
</head>

<body class="bg-white" id="body">

<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover"
             style="background-image: url('<?php echo $static_url; ?>/images/bg/cta.jpg');"></div>
    </div>

    <div class="container relative md:mt-26 mt-16 ">
        <div class="md:flex flex-col items-center">
        
            <h1 class="text-2xl font-bold text-blue-700 dark:text-blue-300 text-center mb-8">User List</h1>

            <!-- "View Paginated" link before the table -->
            <?php if (isset($_GET['all']) && $_GET['all'] == 1): ?>
                <p><a href="view-all-users.php">&laquo; Go back to paginated view</a></p>
            <?php endif; ?>

            <div class="table-container">
                <table class="table-light dark:table-dark">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Birthday</th>
                            <th>Passport Number</th>
                            <th>Gender</th>
                            <th>Nationality</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    
                    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['first_name']; ?></td>
            <td><?php echo $row['last_name']; ?></td>
            <!-- Format Birthdate -->
            <td>
                <?php
                    $birthday = $row['birthday']; // Get the birthday from the database
                    // Format the date to "DD-MM-YYYY"
                    echo date("d-m-Y", strtotime($birthday)); 
                ?>
            </td>
            <td><?php echo $row['passport_number']; ?></td>
            <td><?php echo $row['gender']; ?></td>
            <td><?php echo $row['nationality']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['role']; ?></td>
            <td><a href="edit-user.php?id=<?php echo $row['user_id']; ?>">Edit</a></td>
            <td>
                <form action="delete-user.php?id=<?php echo $row['user_id']; ?>" method="post" onsubmit="return ConfirmDelete()">
                    <input type="submit" class="btn-delete" value="Delete" />
                </form>
            </td>
        </tr>
    <?php } ?>
</tbody>


                </table>
            </div>

            <!-- Pagination Section -->
            <?php if (!isset($_GET['all']) || $_GET['all'] != 1): ?>
                <div class="pagination">
                    <a href="view-all-users.php?all=1">View All</a> |
                    <strong>View Page:</strong>
                    <?php
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<a href='view-all-users.php?page=$i'>$i</a> ";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Go Back Button -->
            <a class="btn-add" href="admin-users.php">Go Back</a>
        </div>
    </div>
</section>

<script>
    function ConfirmDelete() {
        return confirm('Are you sure you want to delete this user?');
    }

    // Toggle Dark Mode
    function toggleDarkMode() {
        var body = document.getElementById("body");
        body.classList.toggle("dark-mode");
    }

    // You can call toggleDarkMode() when switching themes
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