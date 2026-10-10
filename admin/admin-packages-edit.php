<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        { header("Location: ../login.php"); exit; }
    }

    if($_SESSION['role'] != "admin"){
        { header("Location: ../index.php"); exit; }
    }

    $base_dir = __DIR__ . '/../'; 
    $static_url = '/Travosy/assets'; // Ensure this is the correct path
    $upload_dir = $base_dir . '../assets/images/listing/'; // Directory for package images
    include '../db.php';

    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Include the common navlink content
    ob_start();
    $navlink_content = ob_get_clean();
    $page = 'nav3';
    $user_id = $_SESSION['user_id']; // Assuming this is the logged-in user ID

    // Check if package_id is provided
    if (!isset($_GET['package_id']) || empty($_GET['package_id'])) {
        header("Location: admin-packages.php");
        exit;
    }
    $package_id = (int)$_GET['package_id'];

    // Get package information
    $packageQuery = "SELECT * FROM packages WHERE package_id = ?";
    $packageStmt = $conn->prepare($packageQuery);
    $packageStmt->bind_param("i", $package_id);
    $packageStmt->execute();
    $packageResult = $packageStmt->get_result();

    if ($packageResult->num_rows === 0) {
        header("Location: admin-packages.php");
        exit;
    }

    if (isset($_POST['delete_package'])) {
        $package_id = $_POST['package_id'];
        // Validate and sanitize $package_id

        // Delete package from DB
        $stmt = $conn->prepare("DELETE FROM packages WHERE package_id = ?");
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $stmt->close();

        // Optionally, delete related images from disk here

        // Redirect after deletion
        header("Location: admin-packages.php");
        exit;
    }

    $package = $packageResult->fetch_assoc();
    $packageStmt->close();

    // Function to handle file upload
    function handleFileUpload($file, $package_id, $i) {
        // Set up folder paths - Fixed to upload to Travosy/assets/images/listing/
        $upload_folder = __DIR__ . '/assets/images/listing/';
        
        // Ensure folder exists
        if (!is_dir($upload_folder)) {
            mkdir($upload_folder, 0755, true);
        }

        // Generate unique filename
        $filename = "package_{$package_id}_img{$i}_" . time() . ".png";

        // Full file system path
        $target_path = $upload_folder . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Return relative path to store in database (only images/listing/filename)
            return '/images/listing/' . $filename;
        } else {
            return false;
        }
    }

    // Process form submission for updating package
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_package'])) {
        // Get form data
        $departure_city = htmlspecialchars(trim($_POST['departure_city']));
        $destination_city = htmlspecialchars(trim($_POST['destination_city']));
        $name = htmlspecialchars(trim($_POST['name']));
        $description = htmlspecialchars(trim($_POST['description']));
        $transport_type = htmlspecialchars(trim($_POST['transport_type']));
        $accommodation_type = htmlspecialchars(trim($_POST['accommodation_type']));
        $hotel_name = htmlspecialchars(trim($_POST['hotel_name']));
        $num_nights = (int)$_POST['num_nights'];
        $num_days = (int)$_POST['num_days'];
        $departure_date = $_POST['departure_date'];
        $return_date = $_POST['return_date'];
        $base_price = (float)$_POST['base_price'];
        $includes_meals = isset($_POST['includes_meals']) ? 1 : 0;
        $includes_tour_guide = isset($_POST['includes_tour_guide']) ? 1 : 0;
        $max_capacity = (int)$_POST['max_capacity'];
        $status = htmlspecialchars(trim($_POST['status']));
        
        // Handle image uploads or keep existing URLs
        $img1 = $package['img1']; // Keep existing by default
        $img2 = $package['img2'];
        $img3 = $package['img3'];
        $img4 = $package['img4'];
        
        $upload_errors = [];
        
        // Handle file uploads for each image
        for ($i = 1; $i <= 4; $i++) {
            $file_key = "img{$i}_file";
            $url_key = "img{$i}_url";
            
            // Check if a file was uploaded
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploaded_path = handleFileUpload($_FILES[$file_key], $package_id, $i);
                if ($uploaded_path) {
                    // Delete old file if it exists and is a local upload
                    $old_image = $package["img{$i}"];
                    if ($old_image && strpos($old_image, 'images/listing/') !== false) {
                        // Convert the stored database path to actual file path for deletion
                        $old_file_path = __DIR__ . '/assets/' . $old_image;
                        
                        if (file_exists($old_file_path)) {
                            unlink($old_file_path);
                        }
                    }
                    ${"img{$i}"} = $uploaded_path;
                } else {
                    $upload_errors[] = "Failed to upload image {$i}";
                }
            }
            // Otherwise keep existing image
        }
        
        if (empty($upload_errors)) {
            // Update package in database - Fixed the prepared statement
            $updateQuery = "UPDATE packages SET 
                            departure_city = ?, 
                            destination_city = ?, 
                            name = ?, 
                            description = ?, 
                            transport_type = ?, 
                            accommodation_type = ?, 
                            hotel_name = ?, 
                            num_nights = ?, 
                            num_days = ?, 
                            departure_date = ?, 
                            return_date = ?, 
                            base_price = ?, 
                            includes_meals = ?, 
                            includes_tour_guide = ?, 
                            max_capacity = ?, 
                            img1 = ?, 
                            img2 = ?, 
                            img3 = ?, 
                            img4 = ?, 
                            status = ? 
                            WHERE package_id = ?";
            
            $updateStmt = $conn->prepare($updateQuery);
            
            // Fixed the bind_param call - correct parameter count and types
            $updateStmt->bind_param("sssssssiissdiiisssssi", 
                $departure_city, $destination_city, $name, $description, 
                $transport_type, $accommodation_type, $hotel_name, 
                $num_nights, $num_days, $departure_date, $return_date, 
                $base_price, $includes_meals, $includes_tour_guide, 
                $max_capacity, $img1, $img2, $img3, $img4, $status, $package_id 
            );
            
            if ($updateStmt->execute()) {
                $updateStmt->close();
                // Refresh package data
                $packageStmt = $conn->prepare($packageQuery);
                $packageStmt->bind_param("i", $package_id);
                $packageStmt->execute();
                $packageResult = $packageStmt->get_result();
                $package = $packageResult->fetch_assoc();
                $packageStmt->close();
                
                // Redirect with success message
                header("Location: admin-packages-edit.php?package_id=$package_id&updated=1");
                exit;
            } else {
                $error_message = "Failed to update package. Database error: " . $updateStmt->error;
                $updateStmt->close();
            }
        } else {
            $error_message = implode(', ', $upload_errors);
        }
    }

    // Create URL-friendly title slug for the package
    $titleSlug = strtolower(str_replace(' ', '-', $package['name']));

    // Format dates for display
    $departureDate = date('M d, Y', strtotime($package['departure_date']));
    $returnDate = date('M d, Y', strtotime($package['return_date']));

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

            <?php include 'admin-sidebar.php'; ?>
            
            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <div class="mb-6 flex justify-between items-center">
                    <a href="admin-packages.php" class="inline-flex items-center text-red-600 hover:text-red-700 underline">
                        &lt; Back to Packages
                    </a>
                    
                    <h5 class="text-xl font-bold">Edit Package</h5>
                </div>
                
                <!-- Success message -->
                <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Package has been updated successfully.</span>
                </div>
                <?php endif; ?>
                
                <!-- Error message -->
                <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
                <?php endif; ?>
                
                <!-- Package Edit Form -->
                <div class="bg-white dark:bg-slate-900 rounded-md shadow dark:shadow-gray-800 overflow-hidden">
                    <form method="POST" action="" enctype="multipart/form-data" class="p-6">
                        <h6 class="text-lg font-semibold mb-6">Package Information</h6>
                        
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <fieldset class="border p-4 rounded bg-blue-50 dark:bg-blue-900/20 border-blue-200">
                                <legend class="font-semibold px-2 text-blue-700">Basic Information</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Package Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($package['name']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Status</label>
                                        <select name="status" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                            <option value="active" <?php echo $package['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo $package['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="draft" <?php echo $package['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        </select>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block font-medium text-sm mb-1">Description</label>
                                        <textarea name="description" rows="4" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none"><?php echo htmlspecialchars($package['description']); ?></textarea>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Location Information -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Location Information</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Departure City <span class="text-red-500">*</span></label>
                                        <input type="text" name="departure_city" value="<?php echo htmlspecialchars($package['departure_city']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Destination City <span class="text-red-500">*</span></label>
                                        <input type="text" name="destination_city" value="<?php echo htmlspecialchars($package['destination_city']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Travel Details -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Travel Details</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Transport Type</label>
                                        <select name="transport_type" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                            <option value="bus" <?php echo $package['transport_type'] == 'bus' ? 'selected' : ''; ?>>Bus</option>
                                            <option value="plane" <?php echo $package['transport_type'] == 'plane' ? 'selected' : ''; ?>>Plane</option>
                                            <option value="train" <?php echo $package['transport_type'] == 'train' ? 'selected' : ''; ?>>Train</option>
                                            <option value="car" <?php echo $package['transport_type'] == 'car' ? 'selected' : ''; ?>>Car</option>
                                            <option value="mixed" <?php echo $package['transport_type'] == 'mixed' ? 'selected' : ''; ?>>Mixed</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Number of Days</label>
                                        <input type="number" name="num_days" value="<?php echo $package['num_days']; ?>" min="1" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Number of Nights</label>
                                        <input type="number" name="num_nights" value="<?php echo $package['num_nights']; ?>" min="0" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Departure Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="departure_date" value="<?php echo $package['departure_date']; ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Return Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="return_date" value="<?php echo $package['return_date']; ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Max Capacity</label>
                                        <input type="number" name="max_capacity" value="<?php echo $package['max_capacity']; ?>" min="1" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Accommodation -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Accommodation</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Accommodation Type</label>
                                        <select name="accommodation_type" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                            <option value="hotel" <?php echo $package['accommodation_type'] == 'hotel' ? 'selected' : ''; ?>>Hotel</option>
                                            <option value="resort" <?php echo $package['accommodation_type'] == 'resort' ? 'selected' : ''; ?>>Resort</option>
                                            <option value="hostel" <?php echo $package['accommodation_type'] == 'hostel' ? 'selected' : ''; ?>>Hostel</option>
                                            <option value="guesthouse" <?php echo $package['accommodation_type'] == 'guesthouse' ? 'selected' : ''; ?>>Guesthouse</option>
                                            <option value="apartment" <?php echo $package['accommodation_type'] == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Hotel Name</label>
                                        <input type="text" name="hotel_name" value="<?php echo htmlspecialchars($package['hotel_name']); ?>" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Pricing & Inclusions -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Pricing & Inclusions</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Base Price <span class="text-red-500">*</span></label>
                                        <input type="number" name="base_price" value="<?php echo $package['base_price']; ?>" step="0.01" min="0" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 mt-6">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="includes_meals" value="1" <?php echo $package['includes_meals'] ? 'checked' : ''; ?> class="form-checkbox h-4 w-4 text-emerald-600" style="margin-right:5px;">
                                            <span class="ml-2 text-sm">Includes Meals</span>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 mt-6">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="includes_tour_guide" value="1" <?php echo $package['includes_tour_guide'] ? 'checked' : ''; ?> class="form-checkbox h-4 w-4 text-emerald-600" style="margin-right:5px;">
                                            <span class="ml-2 text-sm">Includes Tour Guide</span>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Images -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Package Images</legend>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">You can upload new images to replace existing ones. Leave empty to keep current images.</p>
                                
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="mb-6 p-4 border rounded bg-white dark:bg-slate-700">
                                    <h4 class="font-semibold mb-3">Image <?php echo $i; ?></h4>
                                    
                                    <?php if (!empty($package["img{$i}"])): ?>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Current Image:</p>
                                        <img src="<?php echo "../assets/" . htmlspecialchars($package["img{$i}"]); ?>" alt="Current Image <?php echo $i; ?>" class="w-32 h-24 object-cover rounded border mt-1">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Upload New Image <?php echo $i; ?></label>
                                        <input type="file" name="img<?php echo $i; ?>_file" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none file:mr-4 file:py-1 file:px-4 file:rounded file:border-0 file:text-sm file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                        <p class="text-xs text-gray-500 mt-1">Max size: 5MB. Formats: JPEG, PNG, GIF, WebP</p>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </fieldset>
                            
                            <!-- Package Info Display -->
                            <fieldset class="border p-4 rounded bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200">
                                <legend class="font-semibold px-2 text-yellow-700">Package Information</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-slate-400">Package ID:</span>
                                        <p class="font-medium"><?php echo $package['package_id']; ?></p>
                                    </div>
                                    <div>
                                        <span class="text-slate-400">Created At:</span>
                                        <p class="font-medium"><?php echo date('M d, Y H:i', strtotime($package['created_at'])); ?></p>
                                    </div>
                                    <div>
                                        <span class="text-slate-400">Current Status:</span>
                                        <p class="font-medium capitalize"><?php echo $package['status']; ?></p>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-between items-center">
                            <!-- Delete Package Button -->
                            <button type="button" onclick="showDeleteModal('<?php echo htmlspecialchars($package['name']); ?>')" class="px-6 py-2 text-red-700 bg-red-100 border border-red-300 rounded-md hover:bg-red-200 transition-all font-semibold">
                                Delete Package
                            </button>

                            <!-- Update Button -->
                            <button type="submit" name="update_package" class="px-8 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md shadow-md hover:shadow-lg transition-all font-semibold">
                                <i class="uil uil-check mr-2"></i>Update Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Package Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 shadow-2xl p-6 m-4 w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="modalContent" style="border-radius: 24px; border: 3px solid #000000;">
        <!-- Header with Icon -->
        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="uil uil-exclamation-triangle text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Delete Package?</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">
                You're about to permanently delete<br>
                <span id="packageName" class="font-bold text-gray-900 dark:text-white text-base"></span>
            </p>
        </div>
        
        <!-- Warning Notice -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-l-4 border-amber-400 rounded-lg p-3 mb-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-amber-500 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                    <i class="uil uil-info-circle text-white text-xs"></i>
                </div>
                <div class="text-sm text-center flex-1">
                    <p class="text-amber-800 dark:text-amber-200 font-semibold mb-1">⚠️ Permanent Action</p>
                    <p class="text-amber-700 dark:text-amber-300">
                        This action is <strong>permanent</strong> and cannot be undone. All package data and associated bookings will be lost.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col gap-3">
            <button onclick="confirmDelete()" class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-200 font-semibold shadow-lg hover:shadow-xl border-0" style="background-color: #dc2626 !important; color: white !important;">
                <i class="uil uil-trash mr-2"></i>Delete Package
            </button>
            <button onclick="hideDeleteModal()" class="w-full px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all duration-200 font-medium">
                <i class="uil uil-arrow-left mr-2"></i>Keep Package
            </button>
        </div>
        
        <!-- Close button -->
        <button onclick="hideDeleteModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <i class="uil uil-times text-xl"></i>
        </button>
    </div>
</div>

<script>

    function showDeleteModal(packageName) {
    document.getElementById('packageName').textContent = packageName;
    const modal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animate modal appearance
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('modalContent');
    
    // Animate modal disappearance
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
}

function confirmDelete() {
    // Create a form to submit the deletion
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'admin-packages-edit.php';
    
    const packageIdInput = document.createElement('input');
    packageIdInput.type = 'hidden';
    packageIdInput.name = 'package_id';
    packageIdInput.value = '<?= htmlspecialchars($package_id) ?>';
    
    const deletePackageInput = document.createElement('input');
    deletePackageInput.type = 'hidden';
    deletePackageInput.name = 'delete_package';
    deletePackageInput.value = '1';
    
    form.appendChild(packageIdInput);
    form.appendChild(deletePackageInput);
    document.body.appendChild(form);
    form.submit();
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
        hideDeleteModal();
    }
});

// Auto-calculate return date based on departure date and number of days
document.querySelector('input[name="departure_date"]').addEventListener('change', function() {
    const departureDate = new Date(this.value);
    const numDays = parseInt(document.querySelector('input[name="num_days"]').value) || 1;
    
    if (departureDate && numDays > 0) {
        const returnDate = new Date(departureDate);
        returnDate.setDate(returnDate.getDate() + numDays - 1);
        
        const returnDateStr = returnDate.toISOString().split('T')[0];
        document.querySelector('input[name="return_date"]').value = returnDateStr;
    }
});

document.querySelector('input[name="num_days"]').addEventListener('change', function() {
    const departureInput = document.querySelector('input[name="departure_date"]');
    if (departureInput.value) {
        departureInput.dispatchEvent(new Event('change'));
    }
});

// Preview uploaded images
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                this.value = '';
                return;
            }
            
            // Check file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                // Find or create preview image
                let preview = input.parentNode.parentNode.querySelector('.new-image-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'new-image-preview mt-2';
                    preview.innerHTML = '<p class="text-sm text-green-600 font-medium mb-1">New image preview:</p><img class="w-32 h-24 object-cover rounded border">';
                    input.parentNode.appendChild(preview);
                }
                preview.querySelector('img').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    // Validate dates
    const departureDate = new Date(document.querySelector('input[name="departure_date"]').value);
    const returnDate = new Date(document.querySelector('input[name="return_date"]').value);
    
    if (returnDate <= departureDate) {
        alert('Return date must be after departure date.');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields and ensure dates are valid.');
    }
});

// Debug form submission
document.querySelector('form').addEventListener('submit', function(e) {
    console.log('Form is being submitted');
    
    // Log form data for debugging
    const formData = new FormData(this);
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
});
</script>

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include $base_dir . "Base/style/base-user.php";
?>