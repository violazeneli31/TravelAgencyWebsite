<?php
$base_dir = __DIR__ . '/Base';
$static_url = '/Travosy/assets';
include 'config.php';
session_start();

ob_start();
$navlink_content = ob_get_clean();
$page = 'nav2';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_travelers'])) {
    $user_id = $_SESSION['user_id'] ?? null;
    $titleSlug = $_POST['tour_id'];

    // Prepare query matching slugified name
    $stmt = $conn->prepare("SELECT * FROM packages WHERE LOWER(REPLACE(name, ' ', '-')) = ?");
    $stmt->bind_param("s", $titleSlug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<h2>Package not found.</h2>";
        exit;
    }

    $row = $result->fetch_assoc();
    $package_id = $row['package_id'];
    $date_booked = date('Y-m-d H:i:s'); // current date and time
    $personalised_price = floatval($_POST['calculated_price'] ?? 0); // Use calculated_price from form
    $nr_adults = (int) ($_POST['nr_adults'] ?? 0);
    $nr_children = (int) ($_POST['nr_children'] ?? 0);

    // Insert new booking with all required fields
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, package_id, date_booked, personalised_price, nr_adults, nr_children) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdii", $user_id, $package_id, $date_booked, $personalised_price, $nr_adults, $nr_children);

    if (!$stmt->execute()) {
        echo "<p class='text-red-600'>Booking failed: " . $stmt->error . "</p>";
        exit;
    }
    $booking_id = $conn->insert_id;

    // Check if traveler 1 is user (checkbox present and checked)
    $traveler1_is_user = isset($_POST['traveler1_is_user']) && $_POST['traveler1_is_user'] == 'on';

    // Traveler fields arrays
    $names = $_POST['traveler_name'];
    $surnames = $_POST['traveler_surname'];
    $birthdays = $_POST['traveler_birthday'];
    $nationalities = $_POST['traveler_nationality'];
    $passport_numbers = $_POST['traveler_passport_number'];
    $traveller_types = $_POST['traveller_type'];
    $genders = $_POST['traveler_gender'];

    $stmt = $conn->prepare("INSERT INTO other_travellers (booking_id, name, surname, birthday, nationality, passport_number, traveller_type, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($names); $i++) {
        // Skip traveler 1 if checkbox checked
        if ($i === 0 && $traveler1_is_user) {
            continue;
        }

        $name = htmlspecialchars(trim($names[$i]));
        $surname = htmlspecialchars(trim($surnames[$i]));
        $birthday = htmlspecialchars(trim($birthdays[$i]));
        $nationality = htmlspecialchars(trim($nationalities[$i]));
        $passport_number = htmlspecialchars(trim($passport_numbers[$i]));
        $traveller_type = htmlspecialchars(trim($traveller_types[$i]));
        $gender = htmlspecialchars(trim($genders[$i]));

        $stmt->bind_param(
            "isssssss",
            $booking_id,
            $name,
            $surname,
            $birthday,
            $nationality,
            $passport_number,
            $traveller_type,
            $gender
        );

        if (!$stmt->execute()) {
            echo "<p class='text-red-600'>Failed to save traveler #" . ($i + 1) . ": " . $stmt->error . "</p>";
            exit;
        }
    }

    header("Location: thank-you.php");
    exit;
}

// Step 2: Show traveler info form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_id = $_POST['tour_id'];
    $package_id = $_POST['package_id'] ?? '';
    $adults = max(1, (int) $_POST['nr_adults']);
    $children = max(0, (int) $_POST['nr_children']);
    $calculated_price = floatval($_POST['calculated_price'] ?? 0);
    $total = $adults + $children;
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Book Tour - Traveler Info</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
    <body class="bg-gray-50 font-sans">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow mt-16">
        <h2 class="text-xl font-bold mb-4">Traveler Information</h2>
        
        <!-- Show booking summary -->
        <div class="bg-gray-100 p-4 rounded mb-6">
            <h3 class="font-semibold mb-2">Booking Summary</h3>
            <p><strong>Adults:</strong> <?php echo $adults; ?></p>
            <p><strong>Children:</strong> <?php echo $children; ?></p>
            <p><strong>Total Price:</strong> $<?php echo number_format($calculated_price, 2); ?></p>
        </div>
        
        <form method="POST" action="tour-booking.php" class="space-y-6">
            <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour_id); ?>">
            <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($package_id); ?>">
            <input type="hidden" name="nr_adults" value="<?php echo $adults; ?>">
            <input type="hidden" name="nr_children" value="<?php echo $children; ?>">
            <input type="hidden" name="calculated_price" value="<?php echo $calculated_price; ?>">

            <?php for ($i = 1; $i <= $total; $i++):
                $traveller_type = ($i <= $adults) ? 'adult' : 'child'; // Match database enum values
            ?>
                <fieldset class="border p-4 rounded bg-gray-50" style="margin-bottom:15px;">
                    <legend class="font-semibold mb-3">Traveler <?php echo $i; ?> (<?php echo ucfirst($traveller_type); ?>)</legend>

                    <?php if ($i === 1): ?>
                        <label class="inline-flex items-center mb-3 cursor-pointer">
                            <input type="checkbox" name="traveler1_is_user" id="traveler1_is_user" class="mr-2" />
                            <span>Traveler 1 is User</span>
                        </label>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1">First Name</label>
                            <input type="text" name="traveler_name[]" id="traveler_name_<?php echo $i; ?>" required class="w-full border p-2 rounded" />
                        </div>

                        <div>
                            <label class="block font-medium mb-1">Surname</label>
                            <input type="text" name="traveler_surname[]" id="traveler_surname_<?php echo $i; ?>" required class="w-full border p-2 rounded" />
                        </div>

                        <div>
                            <label class="block font-medium mb-1">Birthday</label>
                            <input type="date" name="traveler_birthday[]" id="traveler_birthday_<?php echo $i; ?>" required max="<?php echo date('Y-m-d'); ?>" class="w-full border p-2 rounded" />
                        </div>

                        <div>
                            <label class="block font-medium mb-1">Nationality</label>
                            <input type="text" name="traveler_nationality[]" id="traveler_nationality_<?php echo $i; ?>" required class="w-full border p-2 rounded" />
                        </div>

                        <div>
                            <label class="block font-medium mb-1">Passport Number</label>
                            <input type="text" name="traveler_passport_number[]" id="traveler_passport_number_<?php echo $i; ?>" required class="w-full border p-2 rounded" />
                        </div>

                        <div>
                            <label class="block font-medium mb-1">Traveler Type</label>
                            <input type="text" name="traveller_type[]" value="<?php echo $traveller_type; ?>" readonly class="w-full border p-2 rounded bg-gray-200" />
                        </div>

                        <div>
                            <label class="block font-medium mb-1">Gender</label>
                            <select name="traveler_gender[]" id="traveler_gender_<?php echo $i; ?>" required class="w-full border p-2 rounded">
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
            <?php endfor; ?>
            <button
            type="submit"
            name="submit_travelers"
            style="
                width: 100%;
                background-color: #16a34a; /* Tailwind green-600 */
                color: white;
                font-weight: 600;
                padding: 12px 0;
                border-radius: 0.5rem;
                box-shadow: 0 4px 6px rgba(22, 163, 74, 0.4);
                border: none;
                cursor: pointer;
                transition: background-color 0.3s ease, box-shadow 0.3s ease;
            ">
            Confirm Booking
            </button>
        </form>
    </div>

    </body>
    <script>
        document.getElementById('traveler1_is_user').addEventListener('change', function() {
            if (this.checked) {
                // Fetch user data from server
                fetch('get-user-info.php')
                    .then(response => response.json())
                    .then(data => {
                        if(data.error) {
                            alert('Error: ' + data.error);
                            this.checked = false;
                            return;
                        }

                        document.getElementById('traveler_name_1').value = data.first_name || '';
                        document.getElementById('traveler_surname_1').value = data.last_name || '';
                        document.getElementById('traveler_birthday_1').value = data.birthday || '';
                        document.getElementById('traveler_nationality_1').value = data.nationality || '';
                        document.getElementById('traveler_passport_number_1').value = data.passport_number || '';
                        const gender = data.gender || '';
                        // Convert to lowercase to match database enum values
                        document.getElementById('traveler_gender_1').value = gender.toLowerCase();
                        })
                    .catch(err => {
                        alert('Failed to load user data.');
                        this.checked = false;
                    });
            } else {
                // Clear fields when unchecked
                document.getElementById('traveler_name_1').value = '';
                document.getElementById('traveler_surname_1').value = '';
                document.getElementById('traveler_birthday_1').value = '';
                document.getElementById('traveler_nationality_1').value = '';
                document.getElementById('traveler_passport_number_1').value = '';
                document.getElementById('traveler_gender_1').value = '';
            }
        });
        </script>
    </html>

    <?php
    $hero_content = ob_get_clean();
    include "$base_dir/style/base.php";
    exit;
}