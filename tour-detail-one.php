<?php
session_start();

$base_dir = __DIR__ . '/Base';
$static_url = '/Travosy/assets';
include 'config.php';

ob_start();
$navlink_content = ob_get_clean();
$page = 'nav2';

ob_start();
include 'config.php';

$titleSlug = isset($_GET['title']) ? $_GET['title'] : '';
if (empty($titleSlug)) {
    echo "<h2>No tour title specified.</h2>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM packages WHERE LOWER(REPLACE(name, ' ', '-')) = ?");
$stmt->bind_param("s", $titleSlug);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<h2>Package not found.</h2>";
    exit;
}
$article = $result->fetch_assoc();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;
$packageId = $article['package_id'];

// Handle login redirects
if (!$isLoggedIn && ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action']))) {
    // Store the current page URL in session for redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

// Personalized Discount (only for logged in users)
$discountPercentage = 0.0;
if ($isLoggedIn) {
    $regStmt = $conn->prepare("SELECT created_at FROM users WHERE user_id = ?");
    $regStmt->bind_param("i", $userId);
    $regStmt->execute();
    $regStmt->bind_result($created_at);
    if ($regStmt->fetch()) {
        $created_date = new DateTime($created_at);
        $now = new DateTime();
        $interval = $created_date->diff($now);

        if ($interval->y >= 1) {
            $discountPercentage = 0.10;
        } elseif ($interval->m >= 6 || $interval->y >= 0) {
            $discountPercentage = 0.05;
        }
    }
    $regStmt->close();
}

// Wishlist actions (only for logged in users)
$isInWishlist = false;
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_wishlist'])) {
        $addStmt = $conn->prepare("INSERT IGNORE INTO wishlist (user_id, package_id) VALUES (?, ?)");
        $addStmt->bind_param("ii", $userId, $packageId);
        $addStmt->execute();
    } elseif (isset($_POST['remove_from_wishlist'])) {
        $removeStmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND package_id = ?");
        $removeStmt->bind_param("ii", $userId, $packageId);
        $removeStmt->execute();
    }
}

// Check if package is in wishlist (only for logged in users)
if ($isLoggedIn) {
    $checkStmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND package_id = ?");
    $checkStmt->bind_param("ii", $userId, $packageId);
    $checkStmt->execute();
    $isInWishlist = $checkStmt->get_result()->num_rows > 0;
}

// Apply personalized discount
$basePrice = floatval($article['base_price']);
$finalBasePrice = $basePrice * (1 - $discountPercentage);

// Reviews
$reviewsStmt = $conn->prepare("SELECT r.rating, r.comment, r.created_at, u.username FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.package_id = ? ORDER BY r.created_at DESC");
$reviewsStmt->bind_param("i", $article['package_id']);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();

// Booking (only for logged in users)
if ($isLoggedIn && isset($_POST['book_now'])) {
    $nr_adults = isset($_POST['nr_adults']) ? intval($_POST['nr_adults']) : 1;
    $nr_children = isset($_POST['nr_children']) ? intval($_POST['nr_children']) : 0;

    if ($nr_adults < 1) {
        echo "<script>alert('You must book at least one adult.');</script>";
    } else {
        $childDiscount = 0.5;
        $totalPrice = ($nr_adults * $finalBasePrice) + ($nr_children * $finalBasePrice * $childDiscount);

        $insertBooking = $conn->prepare("INSERT INTO bookings (user_id, package_id, personalised_price, nr_adults, nr_children) VALUES (?, ?, ?, ?, ?)");
        $insertBooking->bind_param("iisii", $userId, $packageId, $totalPrice, $nr_adults, $nr_children);

        if ($insertBooking->execute()) {
            echo "<script>alert('Booking successful!');</script>";
        } else {
            echo "<script>alert('Booking failed: " . $insertBooking->error . "');</script>";
        }

        $insertBooking->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($article['name']); ?> | Tour Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">

<!-- Hero -->
<section class="relative table w-full py-36 lg:py-44 bg-top bg-no-repeat bg-cover" style="background-image: url('<?php echo $static_url . $article['img1']; ?>');">
    <div class="absolute inset-0 bg-black opacity-70"></div>
    <div class="container mx-auto relative px-6">
        <div class="text-center text-white mt-10">
            <h1 class="text-4xl font-bold"><?php echo htmlspecialchars($article['name']); ?></h1>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-16 md:py-24">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-12 gap-12">

            <!-- Left -->
            <div class="md:col-span-8">
                <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-8">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <?php if (!empty($article['img' . $i])): ?>
                            <img src="<?php echo $static_url . $article['img' . $i]; ?>" class="w-full h-48 object-cover rounded-xl shadow" alt="Image <?php echo $i; ?>">
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <h2 style="margin-top:15px;" class="text-2xl font-semibold mb-4">About This Package</h2>
                <p class="text-gray-700 whitespace-pre-line leading-relaxed"><?php echo htmlspecialchars($article['description']); ?></p>
            </div>

            <!-- Right Sidebar -->
            <aside class="md:col-span-4 bg-white p-6 rounded-2xl shadow-lg border border-gray-200 space-y-4">
                <!-- Header with title and heart icons -->
                <div class="flex justify-between items-start">
                    <h3 class="text-xl font-bold text-green-700 border-b pb-2">PACKAGE INFORMATION</h3>
                    <!-- Heart icons -->
                    <div class="text-xl cursor-pointer">
                        <?php if ($isLoggedIn): ?>
                            <form method="POST" class="text-center mt-4">
                            <?php if ($isInWishlist): ?>
                                <button type="submit" name="remove_from_wishlist"
                                    class="inline-flex items-center justify-center w-10 h-10 text-red-600 hover:text-red-800 transition">
                                    <i class="fas fa-heart text-xl pointer-events-none"></i>
                                </button>
                            <?php else: ?>
                                <button type="submit" name="add_to_wishlist"
                                    class="inline-flex items-center justify-center w-10 h-10 text-red-600 hover:text-red-800 transition">
                                    <i class="far fa-heart text-xl pointer-events-none"></i>
                                </button>
                            <?php endif; ?>
                            </form>
                        <?php else: ?>
                            <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                               class="inline-flex items-center justify-center w-10 h-10 text-gray-400 hover:text-red-600 transition">
                                <i class="far fa-heart text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <ul class="space-y-2 text-gray-800 text-sm">
                    <li class="font-medium"><strong>Departure:</strong> <?php echo htmlspecialchars($article['departure_city']); ?></li>
                    <li class="font-medium"><strong>Destination:</strong> <?php echo htmlspecialchars($article['destination_city']); ?></li>
                    <li class="font-medium"><strong>Departure Date:</strong> <?php echo htmlspecialchars($article['departure_date']); ?></li>
                    <li class="font-medium"><strong>Return Date:</strong> <?php echo htmlspecialchars($article['return_date']); ?></li>
                    <li class="font-medium"><strong>Duration:</strong> <?php echo htmlspecialchars($article['num_days']) . ' Days / ' . htmlspecialchars($article['num_nights']) . ' Nights'; ?></li>
                    <li class="font-medium"><strong>Transport:</strong> <?php echo htmlspecialchars($article['transport_type']); ?></li>
                    <li class="font-medium"><strong>Accommodation:</strong> <?php echo htmlspecialchars($article['accommodation_type']); ?></li>
                    <li class="font-medium"><strong>Hotel:</strong> <?php echo htmlspecialchars($article['hotel_name']); ?></li>
                    <li class="font-medium"><strong>Max Capacity:</strong> <?php echo htmlspecialchars($article['max_capacity']); ?></li>
                    <li class="font-medium"><strong>Meals:</strong> <?php echo $article['includes_meals'] ? 'Yes' : 'No'; ?></li>
                    <li class="font-medium"><strong>Guide:</strong> <?php echo $article['includes_tour_guide'] ? 'Yes' : 'No'; ?></li>
                    <li class="font-medium"><strong>Base Price (1 adult):</strong>$<?php echo number_format($finalBasePrice, 2); ?></span></li>
                </ul>

                <h3 style="margin-top:15px;" class="text-xl font-bold text-green-700 border-b pb-2">Booking</h3>

                <!-- Price Calculation -->
                <div class="mt-4 space-y-4">
                    <?php if ($isLoggedIn): ?>
                        <form id="priceCalcForm" class="space-y-3" method="POST" action="tour-booking.php">
                        <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($_GET['title']); ?>">
                        <input type="hidden" name="package_id" value="<?php echo $packageId; ?>">
                        <input type="hidden" name="calculated_price" id="calculated_price" value="<?php echo $finalBasePrice; ?>">
                            <div>
                                <label for="nr_adults" class="block text-sm font-medium text-gray-700">Number of Adults:</label>
                                <input type="number" name="nr_adults" id="nr_adults" min="1" value="1" required class="w-full border p-2 rounded mt-1" />
                            </div>

                            <div>
                                <label for="nr_children" class="block text-sm font-medium text-gray-700">Number of Children:</label>
                                <input type="number" name="nr_children" id="nr_children" min="0" value="0" class="w-full border p-2 rounded mt-1" />
                            </div>

                            <button style="margin-top:15px; cursor: pointer;" type="button" onclick="calculatePersonalisedPrice()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition">Calculate Price</button>

                        <!-- Price result -->
                        <div id="priceResult" class="text-center mt-4 text-lg text-green-700 font-semibold">
                            Total Price: $<span id="priceValue"><?php echo number_format($finalBasePrice, 2); ?></span>
                        </div>

                        <!-- Book Now Button -->
                        <button 
                            type="button" 
                            onclick="handleBooking()" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition w-full" 
                            style="margin-top:15px; cursor: pointer;">
                            Book Now
                        </button>                    
                        </form>
                    <?php else: ?>
                        <!-- Login Required Message -->
                        <div class="text-center p-4 bg-gray-100 rounded-lg">
                            <p class="text-gray-600 mb-4">Please login to book this package</p>
                            <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                               class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow transition inline-block">
                                Login to Book
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <?php 
        if(isset($_SESSION['user_id'])){
            include "show_submit_reviews.php";
        } 
        ?>
    </div>
</section>

<script>
    function handleBooking() {
        calculatePersonalisedPrice();
        document.getElementById('priceCalcForm').submit();
    }
    function calculatePersonalisedPrice() {
        const nrAdults = parseInt(document.getElementById("nr_adults").value) || 0;
        const nrChildren = parseInt(document.getElementById("nr_children").value) || 0;

        if (nrAdults < 1) {
            alert("There must be at least one adult.");
            return;
        }

        const basePrice = <?php echo json_encode($finalBasePrice); ?>;
        const childDiscount = 0.5;
        const totalPrice = (nrAdults * basePrice) + (nrChildren * basePrice * childDiscount);

        document.getElementById("priceValue").textContent = totalPrice.toFixed(2);
        document.getElementById("calculated_price").value = totalPrice.toFixed(2);
    }
</script>

</body>
</html>
<?php 
    $hero_content = ob_get_clean(); 
    include "$base_dir/style/base.php"; 
?>