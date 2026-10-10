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
    $upload_dir = __DIR__ . '/images/listing/'; // Directory for package images
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

    // Process form submission for adding new package
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
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
        
        // Validation
        $errors = [];
        
        if (empty($name)) $errors[] = "Package name is required";
        if (empty($departure_city)) $errors[] = "Departure city is required";
        if (empty($destination_city)) $errors[] = "Destination city is required";
        if (empty($departure_date)) $errors[] = "Departure date is required";
        if (empty($return_date)) $errors[] = "Return date is required";
        if ($base_price <= 0) $errors[] = "Base price must be greater than 0";
        if ($max_capacity <= 0) $errors[] = "Max capacity must be greater than 0";
        
        // Validate dates
        if (!empty($departure_date) && !empty($return_date)) {
            $dept_date = new DateTime($departure_date);
            $ret_date = new DateTime($return_date);
            if ($ret_date <= $dept_date) {
                $errors[] = "Return date must be after departure date";
            }
        }
        
        if (empty($errors)) {
            // First, insert the package to get the package_id
            $insertQuery = "INSERT INTO packages (
                            departure_city, destination_city, name, description, 
                            transport_type, accommodation_type, hotel_name, 
                            num_nights, num_days, departure_date, return_date, 
                            base_price, includes_meals, includes_tour_guide, 
                            max_capacity, status, created_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("sssssssiissdiiis", 
                $departure_city, $destination_city, $name, $description, 
                $transport_type, $accommodation_type, $hotel_name, 
                $num_nights, $num_days, $departure_date, $return_date, 
                $base_price, $includes_meals, $includes_tour_guide, 
                $max_capacity, $status
            );
            
            if ($insertStmt->execute()) {
                $package_id = $conn->insert_id;
                $insertStmt->close();
                
                // Handle image uploads
                $img1 = $img2 = $img3 = $img4 = '';
                $upload_errors = [];
                
                // Handle file uploads for each image
                for ($i = 1; $i <= 4; $i++) {
                    $file_key = "img{$i}_file";
                    
                    // Check if a file was uploaded
                    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] !== UPLOAD_ERR_NO_FILE) {
                        $uploaded_path = handleFileUpload($_FILES[$file_key], $package_id, $i);
                        if ($uploaded_path) {
                            ${"img{$i}"} = $uploaded_path;
                        } else {
                            $upload_errors[] = "Failed to upload image {$i}";
                        }
                    }
                }
                
                // Update package with image paths
                $updateImageQuery = "UPDATE packages SET img1 = ?, img2 = ?, img3 = ?, img4 = ? WHERE package_id = ?";
                $updateImageStmt = $conn->prepare($updateImageQuery);
                $updateImageStmt->bind_param("ssssi", $img1, $img2, $img3, $img4, $package_id);
                $updateImageStmt->execute();
                $updateImageStmt->close();
                
                if (empty($upload_errors)) {
                    // Redirect with success message
                    header("Location: admin-packages.php?added=1");
                    exit;
                } else {
                    $error_message = "Package created but some images failed to upload: " . implode(', ', $upload_errors);
                }
            } else {
                $error_message = "Failed to create package. Database error: " . $insertStmt->error;
                $insertStmt->close();
            }
        } else {
            $error_message = implode(', ', $errors);
        }
    }

    // Default values for new package
    $default_values = [
        'departure_city' => '',
        'destination_city' => '',
        'name' => '',
        'description' => '',
        'transport_type' => 'bus',
        'accommodation_type' => 'hotel',
        'hotel_name' => '',
        'num_nights' => 0,
        'num_days' => 1,
        'departure_date' => '',
        'return_date' => '',
        'base_price' => '',
        'includes_meals' => 0,
        'includes_tour_guide' => 0,
        'max_capacity' => 1,
        'status' => 'active'
    ];

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
                    
                    <h5 class="text-xl font-bold">Add New Package</h5>
                </div>
                
                <!-- Error message -->
                <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
                <?php endif; ?>
                
                <!-- Package Add Form -->
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
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($default_values['name']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Status</label>
                                        <select name="status" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                            <option value="active" <?php echo $default_values['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo $default_values['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="draft" <?php echo $default_values['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        </select>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block font-medium text-sm mb-1">Description</label>
                                        <textarea name="description" rows="4" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none"><?php echo htmlspecialchars($default_values['description']); ?></textarea>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Location Information -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Location Information</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Departure City <span class="text-red-500">*</span></label>
                                        <input type="text" name="departure_city" value="<?php echo htmlspecialchars($default_values['departure_city']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Destination City <span class="text-red-500">*</span></label>
                                        <input type="text" name="destination_city" value="<?php echo htmlspecialchars($default_values['destination_city']); ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
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
                                            <option value="bus" <?php echo $default_values['transport_type'] == 'bus' ? 'selected' : ''; ?>>Bus</option>
                                            <option value="plane" <?php echo $default_values['transport_type'] == 'plane' ? 'selected' : ''; ?>>Plane</option>
                                            <option value="train" <?php echo $default_values['transport_type'] == 'train' ? 'selected' : ''; ?>>Train</option>
                                            <option value="car" <?php echo $default_values['transport_type'] == 'car' ? 'selected' : ''; ?>>Car</option>
                                            <option value="mixed" <?php echo $default_values['transport_type'] == 'mixed' ? 'selected' : ''; ?>>Mixed</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Number of Days</label>
                                        <input type="number" name="num_days" value="<?php echo $default_values['num_days']; ?>" min="1" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Number of Nights</label>
                                        <input type="number" name="num_nights" value="<?php echo $default_values['num_nights']; ?>" min="0" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Departure Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="departure_date" value="<?php echo $default_values['departure_date']; ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Return Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="return_date" value="<?php echo $default_values['return_date']; ?>" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Max Capacity <span class="text-red-500">*</span></label>
                                        <input type="number" name="max_capacity" value="<?php echo $default_values['max_capacity']; ?>" min="1" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
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
                                            <option value="hotel" <?php echo $default_values['accommodation_type'] == 'hotel' ? 'selected' : ''; ?>>Hotel</option>
                                            <option value="resort" <?php echo $default_values['accommodation_type'] == 'resort' ? 'selected' : ''; ?>>Resort</option>
                                            <option value="hostel" <?php echo $default_values['accommodation_type'] == 'hostel' ? 'selected' : ''; ?>>Hostel</option>
                                            <option value="guesthouse" <?php echo $default_values['accommodation_type'] == 'guesthouse' ? 'selected' : ''; ?>>Guesthouse</option>
                                            <option value="apartment" <?php echo $default_values['accommodation_type'] == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Hotel Name</label>
                                        <input type="text" name="hotel_name" value="<?php echo htmlspecialchars($default_values['hotel_name']); ?>" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Pricing & Inclusions -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Pricing & Inclusions</legend>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Base Price <span class="text-red-500">*</span></label>
                                        <input type="number" name="base_price" value="<?php echo $default_values['base_price']; ?>" step="0.01" min="0" required class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none">
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 mt-6">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="includes_meals" value="1" <?php echo $default_values['includes_meals'] ? 'checked' : ''; ?> class="form-checkbox h-4 w-4 text-emerald-600" style="margin-right:5px;">
                                            <span class="ml-2 text-sm">Includes Meals</span>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 mt-6">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="includes_tour_guide" value="1" <?php echo $default_values['includes_tour_guide'] ? 'checked' : ''; ?> class="form-checkbox h-4 w-4 text-emerald-600" style="margin-right:5px;">
                                            <span class="ml-2 text-sm">Includes Tour Guide</span>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <!-- Images -->
                            <fieldset class="border p-4 rounded bg-gray-50 dark:bg-slate-800">
                                <legend class="font-semibold px-2">Package Images</legend>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Upload images for your package. You can add up to 4 images.</p>
                                
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="mb-6 p-4 border rounded bg-white dark:bg-slate-700">
                                    <h4 class="font-semibold mb-3">Image <?php echo $i; ?><?php echo $i === 1 ? ' (Main Image)' : ''; ?></h4>
                                    
                                    <div>
                                        <label class="block font-medium text-sm mb-1">Upload Image <?php echo $i; ?></label>
                                        <input type="file" name="img<?php echo $i; ?>_file" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border p-2 rounded focus:border-emerald-500 focus:outline-none file:mr-4 file:py-1 file:px-4 file:rounded file:border-0 file:text-sm file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                        <p class="text-xs text-gray-500 mt-1">Max size: 5MB. Formats: JPEG, PNG, GIF, WebP</p>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </fieldset>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-between items-center">
                            <a href="admin-packages.php" class="px-6 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 transition-all font-semibold">
                                Cancel
                            </a>

                            <!-- Add Button -->
                            <button type="submit" name="add_package" class="px-8 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md shadow-md hover:shadow-lg transition-all font-semibold" style="padding-right:8px;">
                                <i class="uil uil-plus mr-2"></i>Add Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
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
                    preview.innerHTML = '<p class="text-sm text-green-600 font-medium mb-1">Image preview:</p><img class="w-32 h-24 object-cover rounded border">';
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

// Auto-set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="departure_date"]').setAttribute('min', today);
    document.querySelector('input[name="return_date"]').setAttribute('min', today);
});
</script>

<?php
$hero_content = ob_get_clean(); // Capture the hero content

// Include the base template
include $base_dir . "Base/style/base-user.php";
?>