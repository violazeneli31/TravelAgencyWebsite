<?php
include 'config.php';

$reviews = [];

$sql = "SELECT 
            r.comment, 
            r.rating, 
            u.username, 
            p.name AS package_name
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        JOIN packages p ON r.package_id = p.package_id
        ORDER BY r.review_id DESC";

$result = $conn->query($sql);

if (!$result) {
    echo "<p class='text-red-500'>Query error: " . $conn->error . "</p>";
} elseif ($result->num_rows === 0) {
    echo "<p class='text-slate-400'>No reviews found.</p>";
} else {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}
?>

<?php foreach ($reviews as $item): ?>
    <div class="tiny-slide text-center swiper-slide">
        <div class="cursor-e-resize">
            <div class="content relative rounded shadow-sm dark:shadow-gray-700 m-2 p-6 bg-white dark:bg-slate-900 before:content-[''] before:absolute before:start-1/2 before:-bottom-[4px] before:box-border before:border-8 before:rotate-[45deg] before:border-t-transparent before:border-e-white dark:before:border-e-slate-900 before:border-b-white dark:before:border-b-slate-900 before:border-s-transparent before:shadow-testi dark:before:shadow-gray-700 before:origin-top-left">
                <i class="mdi mdi-format-quote-open mdi-48px text-red-500"></i>
                <p class="text-slate-400">"<?php echo htmlspecialchars($item['comment']); ?>"</p>
                <ul class="list-none mb-0 text-amber-400 mt-3">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <li class="inline">
                            <i class="mdi mdi-star<?= $i < (int)$item['rating'] ? '' : '-outline'; ?>"></i>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>

            <div class="text-center mt-5">
                <h6 class="mt-2 font-semibold">Username: <?php echo htmlspecialchars($item['username']); ?></h6>
                <span class="text-slate-400 text-sm">Package: <?php echo htmlspecialchars($item['package_name']); ?></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>


