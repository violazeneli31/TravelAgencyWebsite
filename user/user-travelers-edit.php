<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        { header("Location: ../login.php"); exit; }
    }

    if($_SESSION['role'] != "user"){
        { header("Location: ../index.php"); exit; }
    }
    $base_dir = __DIR__ . '/../Base';
    $static_url = '/Travosy/assets'; // Ensure this is the correct path
    include '../db.php';
    // Include the common navlink content
    ob_start();
    $navlink_content = ob_get_clean();
    $page = 'nav3';
    $user_id = $_SESSION['user_id']; // Assuming this is the logged-in user ID

    // Check if booking_id is provided
    if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
        header("Location: user-bookings.php");
        exit;
    }
    $booking_id = (int)$_GET['booking_id'];

    // Verify the booking belongs to the current user
    $verifyQuery = "SELECT b.*, p.* FROM bookings b 
                JOIN packages p ON b.package_id = p.package_id 
                WHERE b.booking_id = ? AND b.user_id = ?";
    $verifyStmt = $conn->prepare($verifyQuery);
    $verifyStmt->bind_param("ii", $booking_id, $user_id);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();

    if ($verifyResult->num_rows === 0) {
        header("Location: user-bookings.php");
        exit;
    }

    $booking = $verifyResult->fetch_assoc();
    $verifyStmt->close();

    // Check if this is an upcoming booking (can be cancelled)
    $currentDate = date('Y-m-d');
    $canCancel = strtotime($booking['departure_date']) > time();

    // Process booking cancellation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
        $cancel_booking_id = (int)$_POST['booking_id'];
        
        // Verify the booking belongs to the current user and is upcoming
        $verifyQuery = "SELECT b.*, p.departure_date FROM bookings b 
                    JOIN packages p ON b.package_id = p.package_id 
                    WHERE b.booking_id = ? AND b.user_id = ?";
        $verifyStmt = $conn->prepare($verifyQuery);
        $verifyStmt->bind_param("ii", $cancel_booking_id, $user_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
    
        if ($verifyResult->num_rows > 0) {
            $bookingToCancel = $verifyResult->fetch_assoc();
            
            // Check if the booking can still be cancelled (departure date is in the future)
            if (strtotime($bookingToCancel['departure_date']) > time()) {
                // Begin transaction to ensure data integrity
                $conn->begin_transaction();
                
                try {
                    // First, delete related travellers
                    $deleteTravellersQuery = "DELETE FROM other_travellers WHERE booking_id = ?";
                    $deleteTravellersStmt = $conn->prepare($deleteTravellersQuery);
                    $deleteTravellersStmt->bind_param("i", $cancel_booking_id);
                    $deleteTravellersStmt->execute();
                    $deleteTravellersStmt->close();
                    
                    // Then delete the booking itself
                    $deleteBookingQuery = "DELETE FROM bookings WHERE booking_id = ? AND user_id = ?";
                    $deleteBookingStmt = $conn->prepare($deleteBookingQuery);
                    $deleteBookingStmt->bind_param("ii", $cancel_booking_id, $user_id);
                    $deleteBookingStmt->execute();
                    $deleteBookingStmt->close();
                    
                    // Commit the transaction
                    $conn->commit();
                    
                    // Redirect with success message
                    header("Location: user-bookings.php?cancelled=success");
                    exit;
                    
                } catch (Exception $e) {
                    // Rollback the transaction on error
                    $conn->rollback();
                    header("Location: user-bookings.php?error=cancellation_failed");
                    exit;
                }
            }
        }
        $verifyStmt->close();
    }

    // Process form submission for updating travelers
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_travelers'])) {
        // Get traveler details from the form
        $traveler_ids = $_POST['traveler_id'] ?? [];
        $names = $_POST['traveler_name'] ?? [];
        $surnames = $_POST['traveler_surname'] ?? [];
        $birthdays = $_POST['traveler_birthday'] ?? [];
        $nationalities = $_POST['traveler_nationality'] ?? [];
        $passport_numbers = $_POST['traveler_passport_number'] ?? [];
        $genders = $_POST['traveler_gender'] ?? [];
    
    // Update each traveler's information
    $updateStmt = $conn->prepare("UPDATE other_travellers SET 
                                  name = ?, 
                                  surname = ?, 
                                  birthday = ?, 
                                  nationality = ?, 
                                  passport_number = ?, 
                                  gender = ? 
                                  WHERE traveller_id = ? AND booking_id = ?");
    
    for ($i = 0; $i < count($traveler_ids); $i++) {
        if (empty($traveler_ids[$i])) continue;
        
        $name = htmlspecialchars(trim($names[$i]));
        $surname = htmlspecialchars(trim($surnames[$i]));
        $birthday = htmlspecialchars(trim($birthdays[$i]));
        $nationality = htmlspecialchars(trim($nationalities[$i]));
        $passport_number = htmlspecialchars(trim($passport_numbers[$i]));
        $gender = htmlspecialchars(trim($genders[$i]));
        $traveler_id = (int)$traveler_ids[$i];
        
        $updateStmt->bind_param(
            "ssssssii",
            $name,
            $surname,
            $birthday,
            $nationality,
            $passport_number,
            $gender,
            $traveler_id,
            $booking_id
        );
        
        $updateStmt->execute();
    }
    
    $updateStmt->close();
    
    // Redirect to show success message
    header("Location: user-travelers-edit.php?booking_id=$booking_id&updated=1");
    exit;
}

// Get all travelers for this booking from other_travellers table
$travelersQuery = "SELECT * FROM other_travellers WHERE booking_id = ? ORDER BY traveller_id ASC";
$travelersStmt = $conn->prepare($travelersQuery);
$travelersStmt->bind_param("i", $booking_id);
$travelersStmt->execute();
$travelersResult = $travelersStmt->get_result();
$travelers = [];
while ($traveler = $travelersResult->fetch_assoc()) {
    $travelers[] = $traveler;
}
$travelersStmt->close();

// Count adults in other_travellers table
$adultTravelersCount = 0;
foreach ($travelers as $traveler) {
    if ($traveler['traveller_type'] === 'adult') {
        $adultTravelersCount++;
    }
}

// Check if user should be shown as traveler 1
$userIsTraveler1 = ($booking['nr_adults'] == $adultTravelersCount + 1);

// Get user info 
$sql = "SELECT first_name, last_name, username, birthday, phone, created_at, email, nationality, passport_number, gender FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Create URL-friendly title slug for the package
$titleSlug = strtolower(str_replace(' ', '-', $booking['name']));

// Format dates for display
$departureDate = date('M d, Y', strtotime($booking['departure_date']));
$returnDate = date('M d, Y', strtotime($booking['return_date']));

// Calculate total travelers
$totalTravelers = $booking['nr_adults'] + $booking['nr_children'];

// Optionally define the Hero block content
ob_start();
?>

<!-- Start Hero -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover" style="background-image: url('<?php echo $static_url . '/images/bg/cta.jpg'; ?>');"></div>
    </div>

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">
            <div class="lg:w-1/4 md:w-1/3 md:px-3">
                <div class="sidebar">
                    <?php include 'user-sidebar.php'; ?>
                </div>
            </div>
            
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <div class="mb-6 flex justify-between items-center">
                    <a href="user-bookings.php" class="inline-flex items-center text-red-600 hover:text-red-700 underline">
                        &lt; Back
                    </a>
                    
                    <h5 class="text-xl font-bold">Manage Travelers</h5>
                </div>
                
                <!-- Booking Info -->
                <div class="bg-white dark:bg-slate-900 rounded-md shadow dark:shadow-gray-800 p-4 mb-6">
                    <h6 class="text-lg font-semibold mb-2">Booking Details</h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-slate-400">Package:</span>
                            <p class="font-medium"><?php echo htmlspecialchars($booking['name']); ?></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Departure:</span>
                            <p class="font-medium"><?php echo $departureDate; ?></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Travelers:</span>
                            <p class="font-medium"><?php echo $booking['nr_adults']; ?> Adults, <?php echo $booking['nr_children']; ?> Children</p>
                        </div>
                    </div>
                    
                    <!-- Cancel Booking Button -->
                    <?php if ($canCancel): ?>
                    <div class="mt-4 flex justify-start">
                        <button onclick="showCancelModal(<?php echo $booking_id; ?>, '<?php echo htmlspecialchars($booking['name']); ?>')" class="px-4 py-2 text-sm font-medium bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 text-white rounded-md shadow-md hover:shadow-lg transition-all">
                            <i class="uil uil-times mr-1"></i> Cancel Booking
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Success message -->
                <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Traveler information has been updated successfully.</span>
                </div>
                <?php endif; ?>
                
                <!-- Traveler Information Form -->
                <div class="bg-white dark:bg-slate-900 rounded-md shadow dark:shadow-gray-800 overflow-hidden">
                    <form method="POST" action="" class="p-6">
                        <h6 class="text-lg font-semibold mb-4">Traveler Information</h6>
                        
                        <div class="space-y-8">
                            <?php if ($userIsTraveler1): ?>
                                <!-- Primary Traveler (User) -->
                                <fieldset class="border p-4 rounded bg-blue-50 dark:bg-blue-900/20 border-blue-200">
                                    <legend class="font-semibold px-2 text-blue-700">Traveler 1 (Adult - You)</legend>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block font-medium text-sm mb-1">First Name</label>
                                            <input type="text" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" class="w-full border p-2 rounded bg-gray-200" readonly>
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Surname</label>
                                            <input type="text" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" class="w-full border p-2 rounded bg-gray-200" readonly>
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Birthday</label>
                                            <input type="date" value="<?php echo htmlspecialchars($user['birthday'] ?? ''); ?>" class="w-full border p-2 rounded bg-gray-200" readonly>
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Nationality</label>
                                            <input type="text" value="<?php echo htmlspecialchars($user['nationality'] ?? ''); ?>" class="w-full border p-2 rounded bg-gray-200" readonly>
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Passport Number</label>
                                            <input type="text" value="<?php echo htmlspecialchars($user['passport_number'] ?? ''); ?>" class="w-full border p-2 rounded bg-gray-200" readonly>
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Gender</label>
                                            <input type="text" value="<?php echo htmlspecialchars(ucfirst($user['gender'] ?? '')); ?>" class="w-full border p-2 rounded bg-gray-200" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 text-sm text-slate-500">
                                        <p>To update your personal information, please visit your <a href="user-setting.php" class="text-emerald-600 hover:text-emerald-700 underline">profile settings</a>.</p>
                                    </div>
                                </fieldset>
                            <?php endif; ?>
                            
                            <!-- Other Travelers -->
                            <?php if (count($travelers) > 0): ?>
                                <?php foreach ($travelers as $index => $traveler): 
                                    $travelerNumber = $userIsTraveler1 ? $index + 2 : $index + 1;
                                    $isChild = $traveler['traveller_type'] === 'child';
                                ?>
                                <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                    <legend class="font-semibold px-2">Traveler <?php echo $travelerNumber; ?> (<?php echo $isChild ? 'Child' : 'Adult'; ?>)</legend>
                                    
                                    <input type="hidden" name="traveler_id[]" value="<?php echo $traveler['traveller_id']; ?>">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block font-medium text-sm mb-1">First Name</label>
                                            <input type="text" name="traveler_name[]" value="<?php echo htmlspecialchars($traveler['name']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Surname</label>
                                            <input type="text" name="traveler_surname[]" value="<?php echo htmlspecialchars($traveler['surname']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Birthday</label>
                                            <input type="date" name="traveler_birthday[]" value="<?php echo htmlspecialchars($traveler['birthday']); ?>" required max="<?php echo date('Y-m-d'); ?>" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Nationality</label>
                                            <input type="text" name="traveler_nationality[]" value="<?php echo htmlspecialchars($traveler['nationality']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Passport Number</label>
                                            <input type="text" name="traveler_passport_number[]" value="<?php echo htmlspecialchars($traveler['passport_number']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                        </div>
                                        
                                        <div>
                                            <label class="block font-medium text-sm mb-1">Gender</label>
                                            <select name="traveler_gender[]" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                                <option value="" disabled>Select Gender</option>
                                                <option value="male" <?php echo $traveler['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                                                <option value="female" <?php echo $traveler['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                                                <option value="other" <?php echo $traveler['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if (!$userIsTraveler1 && count($travelers) == 0): ?>
                                <div class="text-center py-6 bg-gray-50 dark:bg-slate-800 rounded">
                                    <p class="text-slate-600 dark:text-slate-400">No travelers found for this booking.</p>
                                    <p class="text-sm text-slate-500 mt-2">This might indicate that traveler information hasn't been added to the database yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (count($travelers) > 0): ?>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" name="update_travelers" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md shadow transition">Save Changes</button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cancel Booking Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 shadow-2xl p-6 m-4 w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="modalContent" style="border-radius: 24px; border: 3px solid #000000;">
        <!-- Header with Icon -->
        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="uil uil-exclamation-triangle text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Cancel Booking?</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">
                You're about to cancel your booking for<br>
                <span id="tourName" class="font-bold text-gray-900 dark:text-white text-base"></span>
            </p>
        </div>
        
        <!-- Warning Notice -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-l-4 border-amber-400 rounded-lg p-3 mb-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-amber-500 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                    <i class="uil uil-info-circle text-white text-xs"></i>
                </div>
                <div class="text-sm text-center flex-1">
                    <p class="text-amber-800 dark:text-amber-200 font-semibold mb-1">⚠️ No Refunds Policy</p>
                    <p class="text-amber-700 dark:text-amber-300">
                        This action is <strong>permanent</strong> and cannot be undone. No refunds will be issued for cancelled bookings.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col gap-3">
            <button onclick="confirmCancel()" class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-200 font-semibold shadow-lg hover:shadow-xl border-0" style="background-color: #dc2626 !important; color: white !important;">
                <i class="uil uil-times mr-2"></i>Cancel Booking
            </button>
            <button onclick="hideCancelModal()" class="w-full px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all duration-200 font-medium">
                <i class="uil uil-arrow-left mr-2"></i>Keep My Booking
            </button>
        </div>
        
        <!-- Close button -->
        <button onclick="hideCancelModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <i class="uil uil-times text-xl"></i>
        </button>
    </div>
</div>

<script>
let bookingToCancel = null;

function showCancelModal(bookingId, tourName) {
    bookingToCancel = bookingId;
    document.getElementById('tourName').textContent = tourName;
    const modal = document.getElementById('cancelModal');
    const modalContent = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animate modal appearance
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function hideCancelModal() {
    const modal = document.getElementById('cancelModal');
    const modalContent = document.getElementById('modalContent');
    
    // Animate modal disappearance
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        bookingToCancel = null;
    }, 200);
}

function confirmCancel() {
    if (bookingToCancel) {
        // Create a form to submit the cancellation to the same page
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const bookingIdInput = document.createElement('input');
        bookingIdInput.type = 'hidden';
        bookingIdInput.name = 'booking_id';
        bookingIdInput.value = bookingToCancel;
        
        const cancelBookingInput = document.createElement('input');
        cancelBookingInput.type = 'hidden';
        cancelBookingInput.name = 'cancel_booking';
        cancelBookingInput.value = '1';
        
        form.appendChild(bookingIdInput);
        form.appendChild(cancelBookingInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideCancelModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('cancelModal').classList.contains('hidden')) {
        hideCancelModal();
    }
});
</script>

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include "$base_dir/style/base-user.php";
?>