<!-- Show All Reviews For That Package-->
<div class="mt-16 bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-semibold mb-6">Reviews</h3>

    <?php if ($reviewsResult->num_rows === 0): ?>
        <p class="italic text-gray-600">No reviews yet. Be the first to share your thoughts!</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="reviewGrid">
            <?php 
                $allReviews = [];
                while ($review = $reviewsResult->fetch_assoc()) {
                    $allReviews[] = $review;
                }
                foreach ($allReviews as $index => $review): 
                    $hiddenClass = $index >= 4 ? 'hidden extra-review' : '';
            ?>
                <div class="bg-gray-50 border rounded-lg p-4 <?php echo $hiddenClass; ?>">
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($review['username']); ?></span>
                        <span class="text-yellow-500">
                            <?php echo str_repeat('★', intval($review['rating'])) . str_repeat('☆', 5 - intval($review['rating'])); ?>
                        </span>
                    </div>
                    <p class="text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                    <small class="text-gray-400 block mt-1"><?php echo date("F j, Y", strtotime($review['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($allReviews) > 4): ?>
            <div class="text-center mt-6 space-x-4">
                <button 
                    id="seeMoreBtn" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition"
                    onclick="showMoreReviews()">
                    See more
                </button>

                <button 
                    id="seeLessBtn" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition hidden"
                    onclick="showLessReviews()">
                    See less
                </button>
            </div>

            <script>
                function showMoreReviews() {
                    document.querySelectorAll('.extra-review').forEach(el => el.classList.remove('hidden'));
                    document.getElementById('seeMoreBtn').classList.add('hidden');
                    document.getElementById('seeLessBtn').classList.remove('hidden');
                }

                function showLessReviews() {
                    document.querySelectorAll('.extra-review').forEach(el => el.classList.add('hidden'));
                    document.getElementById('seeMoreBtn').classList.remove('hidden');
                    document.getElementById('seeLessBtn').classList.add('hidden');
                }
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    let visibleCount = 4;

    function showMoreReviews() {
        const allReviews = document.querySelectorAll('.extra-review');
        const reviewsToShow = Array.from(allReviews).slice(visibleCount - 4, visibleCount);
        reviewsToShow.forEach(el => el.classList.remove('hidden'));
        visibleCount += 4;

        const seeLessBtn = document.getElementById('seeLessBtn');
        seeLessBtn.classList.remove('hidden');

        if (visibleCount >= allReviews.length + 4) {
            document.getElementById('seeMoreBtn').classList.add('hidden');
        }
    }

    function showLessReviews() {
        const allReviews = document.querySelectorAll('.extra-review');
        visibleCount -= 4;
        const reviewsToHide = Array.from(allReviews).slice(visibleCount - 4, visibleCount);
        reviewsToHide.forEach(el => el.classList.add('hidden'));

        if (visibleCount <= 4) {
            document.getElementById('seeLessBtn').classList.add('hidden');
        }

        if (visibleCount <= allReviews.length) {
            document.getElementById('seeMoreBtn').classList.remove('hidden');
        }
    }
</script>

<script>
  const seeMoreBtn = document.getElementById('seeMoreBtn');
  const seeLessBtn = document.getElementById('seeLessBtn');

  if (seeMoreBtn && seeLessBtn) {
    seeMoreBtn.addEventListener('click', () => {
      seeMoreBtn.style.display = 'none';
      seeLessBtn.style.display = 'inline-block';
    });

    seeLessBtn.addEventListener('click', () => {
      seeLessBtn.style.display = 'none';
      seeMoreBtn.style.display = 'inline-block';
    });
  }
</script>



<?php
// Submit Review
include 'config.php';

// Set default values
$success = false;
$error = '';
$rating = '';
$comment = '';
$package_id = isset($article['package_id']) ? $article['package_id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    $package_id = intval($_POST['package_id'] ?? 0);
    $user_id = 1; // Temp value, replace when login system is added

    if ($rating < 1 || $rating > 5 || empty($comment) || $package_id <= 0) {
        $error = "Please provide a valid rating and comment.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, package_id, rating, comment) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iiis", $user_id, $package_id, $rating, $comment);
            $success = $stmt->execute();
            if (!$success) {
                $error = "Failed to submit review. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "Something went wrong with the database.";
        }
    }
}
?>

<!-- Review Form Display -->
<div class="mt-10 bg-white p-6 rounded-lg shadow">
    <h4 class="text-xl font-semibold mb-4">Leave Your Review</h4>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">Thank you! Your review has been submitted.</div>
    <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($package_id); ?>">
        <div>
            <label for="rating" class="block text-gray-700 font-medium">Rating:</label>
            <select name="rating" id="rating" class="w-full border rounded p-2 mt-1" required>
                <option value="">-- Select --</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($rating == $i) ? 'selected' : ''; ?>>
                        <?php echo str_repeat('★', $i); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div>
            <label for="comment" class="block text-gray-700 font-medium">Comment:</label>
            <textarea name="comment" id="comment" rows="4" class="w-full border rounded p-2 mt-1" required><?php echo htmlspecialchars($comment); ?></textarea>
        </div>

        <button type="submit" name="submit_review" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition">
            Submit
        </button>
    </form>
</div>
