<?php
include 'config.php';

// Number of packages per page
$packagesPerPage = 6;

// Current page from URL, default to 1 if not present or invalid
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the offset for SQL LIMIT clause
$offset = ($page - 1) * $packagesPerPage;

// Get total number of active packages to calculate total pages
$totalResult = $conn->query("SELECT COUNT(*) as total FROM packages WHERE status = 'active'");
$totalRow = $totalResult->fetch_assoc();
$totalPackages = $totalRow['total'];

// Fetch only the packages for the current page
$query = "SELECT * FROM packages WHERE status = 'active' ORDER BY created_at DESC LIMIT $packagesPerPage OFFSET $offset";
$result = $conn->query($query);

if ($result->num_rows > 0):
    while ($item = $result->fetch_assoc()):
?>

<div class="group rounded-md shadow-sm dark:shadow-gray-700">
    <div class="relative overflow-hidden rounded-t-md shadow-sm dark:shadow-gray-700 mx-3 mt-3">
        <img src="<?php echo $static_url . $item['img1']; ?>" class="scale-125 group-hover:scale-100 duration-500" alt="">
    </div>

    <div class="p-4">
        <p class="flex items-center text-slate-400 font-medium mb-2">
            <i data-feather="map-pin" class="text-red-500 size-4 me-1"></i> 
            <?php echo htmlspecialchars($item['destination_city']); ?>
        </p>
        <a href="tour-detail-one.php?title=<?php echo urlencode(str_replace(' ', '-', strtolower($item['name']))); ?>" 
           class="text-lg font-medium hover:text-red-500 duration-500 ease-in-out">
           <?php echo htmlspecialchars($item['name']); ?>
        </a>

        <div class="flex items-center mt-2">
            <span class="text-slate-400">Duration:</span>
            <span class="text-sm ml-2 text-slate-600 dark:text-white">
                <?php echo "{$item['num_days']} Days / {$item['num_nights']} Nights"; ?>
            </span>
        </div>
        
        <div class="mt-4 pt-4 flex justify-between items-center border-t border-slate-100 dark:border-gray-800">
            <h5 class="text-lg font-medium text-red-500">$<?php echo number_format($item['base_price'], 2); ?></h5>
            <a href="tour-detail-one.php?title=<?php echo urlencode(str_replace(' ', '-', strtolower($item['name']))); ?>" 
               class="inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300">
               Explore Now <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<?php 
    endwhile;
else:
    echo "<p>No packages found.</p>";
endif;
?>

<?php
// Pagination controls
$totalPages = ceil($totalPackages / $packagesPerPage);

if ($totalPages > 1): ?>
<nav aria-label="Page navigation example">
    <ul class="inline-flex items-center -space-x-px">

        <!-- Previous button -->
        <li>
            <a href="?page=<?php echo max(1, $page - 1); ?>" 
               class="size-[40px] inline-flex justify-center items-center text-slate-400 bg-white dark:bg-slate-900 rounded-s-3xl hover:text-white border border-gray-100 dark:border-gray-800 hover:border-red-500 dark:hover:border-red-500 hover:bg-red-500 dark:hover:bg-red-500">
                <i data-feather="chevron-left" class="size-5 rtl:rotate-180 rtl:-mt-1"></i>
            </a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): 
            $active = ($i == $page); ?>
            <li>
                <a href="?page=<?php echo $i; ?>" 
                   <?php if ($active): ?>
                     aria-current="page"
                     class="z-10 size-[40px] inline-flex justify-center items-center text-white bg-red-500 border border-red-500"
                   <?php else: ?>
                     class="size-[40px] inline-flex justify-center items-center text-slate-400 hover:text-white bg-white dark:bg-slate-900 border border-gray-100 dark:border-gray-800 hover:border-red-500 dark:hover:border-red-500 hover:bg-red-500 dark:hover:bg-red-500"
                   <?php endif; ?>>
                   <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <!-- Next button -->
        <li>
            <a href="?page=<?php echo min($totalPages, $page + 1); ?>" 
               class="size-[40px] inline-flex justify-center items-center text-slate-400 bg-white dark:bg-slate-900 rounded-e-3xl hover:text-white border border-gray-100 dark:border-gray-800 hover:border-red-500 dark:hover:border-red-500 hover:bg-red-500 dark:hover:bg-red-500">
                <i data-feather="chevron-right" class="size-5 rtl:rotate-180 rtl:-mt-1"></i>
            </a>
        </li>

    </ul>
</nav>
<?php
endif;

$conn->close();
?>

