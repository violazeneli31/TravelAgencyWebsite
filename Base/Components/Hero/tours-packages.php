<?php
include 'config.php';

// Initialize filter values
$destination = $_GET['destination'] ?? '';
$trip_date = $_GET['trip_date'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$meals = isset($_GET['meals']) ? 1 : '';
$tours = isset($_GET['tours']) ? 1 : '';

// Build dynamic query
$sql = "SELECT * FROM packages WHERE status = 'active'";
$params = [];

if (!empty($destination)) {
    $sql .= " AND destination_city LIKE ?";
    $params[] = "%$destination%";
}

if (!empty($trip_date)) {
    $sql .= " AND departure_date = ?";
    $params[] = $trip_date;
}

if (!empty($max_price)) {
    $sql .= " AND base_price <= ?";
    $params[] = $max_price;
}

if ($meals !== '') {
    $sql .= " AND includes_meals = 1";
}

if ($tours !== '') {
    $sql .= " AND includes_tour_guide = 1";
}

$sql .= " ORDER BY created_at DESC LIMIT 6";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Search Form -->
<section class="relative py-10 bg-gray-50 dark:bg-slate-800">
  <div class="container mx-auto px-4">
    <form method="get" class="p-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-gray-700">
      <div class="registration-form text-slate-900 text-start">
        <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-4">
          
          <!-- Trip Destination -->
          <div>
            <label class="form-label font-medium text-slate-900 dark:text-white">Trip Destination:</label>
            <div class="relative mt-2">
              <input name="destination" type="text" value="<?= htmlspecialchars($destination) ?>" class="w-full py-2 px-3 pl-14 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-200 dark:border-gray-800" placeholder="Enter destination">
            </div>
          </div>

          <!-- Trip Date -->
          <div>
            <label class="form-label font-medium text-slate-900 dark:text-white">Trip Date:</label>
            <div class="relative mt-2">
              <input name="trip_date" type="date" value="<?= htmlspecialchars($trip_date) ?>" class="w-full py-2 px-3 pl-14 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-200 dark:border-gray-800">
            </div>
          </div>

          <!-- Max Price -->
          <div>
            <label class="form-label font-medium text-slate-900 dark:text-white">Max Price ($):</label>
            <div class="relative mt-2">
              <input name="max_price" type="number" value="<?= htmlspecialchars($max_price) ?>" class="w-full py-2 px-3 pl-14 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-200 dark:border-gray-800" placeholder="e.g. $1000">
            </div>
          </div>

          <!-- Meals + Tours -->
          <div class="lg:col-span-3">
            <label class="form-label font-medium text-slate-900 dark:text-white">Additional Options:</label>
            <div class="flex flex-wrap gap-12 mt-2">
              <label class="flex items-center space-x-2" style="margin-right:10px;">
                <input type="checkbox" name="meals" value="1" <?= $meals ? 'checked' : '' ?> class="form-checkbox h-4 w-4 text-red-500" style="margin-right:5px;">
                <span class="text-slate-800 dark:text-white">Include Meals</span>
              </label>
              <label class="flex items-center space-x-2">
                <input type="checkbox" name="tours" value="1" <?= $tours ? 'checked' : '' ?> class="form-checkbox h-4 w-4 text-red-500" style="margin-right:5px;">
                <span class="text-slate-800 dark:text-white">Include Tours</span>
              </label>
            </div>
          </div>

          <!-- Search Button -->
          <div class="lg:col-span-3">
            <input type="submit" value="Search" class="mt-4 w-full py-2 px-5 bg-red-500 text-white rounded-md cursor-pointer hover:bg-red-600 transition duration-300">
          </div>

        </div>
      </div>
    </form>
  </div>
</section>

<!-- Packages Display -->
<section class="relative py-10 bg-gray-100 dark:bg-slate-900">
  <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($item = $result->fetch_assoc()): ?>
        <div class="group rounded-md shadow-sm dark:shadow-gray-700">
          <div class="relative overflow-hidden rounded-t-md mx-3 mt-3">
            <img src="<?= $static_url . $item['img1'] ?>" class="scale-125 group-hover:scale-100 duration-500" alt="">
          </div>

          <div class="p-4">
            <p class="flex items-center text-slate-400 font-medium mb-2">
              <i data-feather="map-pin" class="text-red-500 size-4 me-1"></i> 
              <?= htmlspecialchars($item['destination_city']) ?>
            </p>
            <a href="tour-detail-one.php?title=<?= urlencode(str_replace(' ', '-', strtolower($item['name']))) ?>" class="text-lg font-medium hover:text-red-500 duration-500 ease-in-out">
              <?= htmlspecialchars($item['name']) ?>
            </a>
            <div class="flex items-center mt-2">
              <span class="text-slate-400">Duration:</span>
              <span class="text-sm ml-2 text-slate-600 dark:text-white"><?= "{$item['num_days']} Days / {$item['num_nights']} Nights" ?></span>
            </div>
            <div class="mt-4 pt-4 flex justify-between items-center border-t border-slate-100 dark:border-gray-800">
              <h5 class="text-lg font-medium text-red-500">$<?= number_format($item['base_price'], 2) ?></h5>
              <a href="tour-detail-one.php?title=<?= urlencode(str_replace(' ', '-', strtolower($item['name']))) ?>" class="inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300">
                Explore Now <i class="mdi mdi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="col-span-3 text-center text-gray-600 dark:text-white">No packages found.</p>
    <?php endif; ?>
  </div>
</section>

<?php $stmt->close(); $conn->close(); ?>





