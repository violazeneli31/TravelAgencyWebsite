<?php 
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    $is_logged_in = isset($_SESSION['username']) && isset($_SESSION['role']);
    $user_name = $_SESSION['username'] ?? '';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
?>
<!-- TAGLINE START-->
<div class="tagline bg-slate-900">
   <div class="container relative">                
       <div class="grid grid-cols-1">
           <div class="flex items-center justify-between">
               <ul class="list-none">
                   <li class="inline-flex items-center">
                       <i data-feather="clock" class="text-red-500 size-4"></i>
                       <span class="ms-2 text-slate-300">Mon-Sat: 9am to 6pm</span>
                   </li>
                   <li class="inline-flex items-center ms-2">
                       <i data-feather="map-pin" class="text-red-500 size-4"></i>
                       <span class="ms-2 text-slate-300">Tirana, Albania</span>
                   </li>
               </ul>

               <ul class="list-none">
                   <li class="inline-flex items-center">
                       <i data-feather="mail" class="text-red-500 size-4"></i>
                       <a href="mailto:contact@example.com" class="ms-2 text-slate-300 hover:text-slate-200">user@gmail.com</a>
                   </li>
                   <li class="inline-flex items-center ms-2">
                       <ul class="list-none">
                           <li class="inline-flex mb-0"><a href="#!" class="text-slate-300 hover:text-red-500"><i data-feather="facebook" class="size-4 align-middle" title="facebook"></i></a></li>
                           <li class="inline-flex ms-2 mb-0"><a href="#!" class="text-slate-300 hover:text-red-500"><i data-feather="instagram" class="size-4 align-middle" title="instagram"></i></a></li>
                           <li class="inline-flex ms-2 mb-0"><a href="#!" class="text-slate-300 hover:text-red-500"><i data-feather="twitter" class="size-4 align-middle" title="twitter"></i></a></li>
                           <li class="inline-flex ms-2 mb-0"><a href="tel:+152534-468-854" class="text-slate-300 hover:text-red-500"><i data-feather="phone" class="size-4 align-middle" title="phone"></i></a></li>
                       </ul><!--end icon-->
                   </li>
               </ul>
           </div>
       </div>
   </div><!--end container-->
</div><!--end tagline-->
<!-- TAGLINE END-->
<!-- Start Navbar -->
<nav id="topnav" class="defaultscroll is-sticky tagline-height">
   <div class="container relative">
       <!-- Logo container-->
       <a class="logo" href="/Travosy/index.php">
           <span class="inline-block dark:hidden">
               <img src="<?php echo $static_url; ?>/images/logo-dark.png" class="h-7 l-dark" alt="">
               <img src="<?php echo $static_url; ?>/images/logo-light.png" class="h-7 l-light" alt="">
           </span>
           <img src="<?php echo $static_url; ?>/images/logo-white.png" class="hidden dark:inline-block" alt="">
       </a>
       <!-- End Logo container-->

       <!-- Start Mobile Toggle -->
       <div class="menu-extras">
           <div class="menu-item">
               <a class="navbar-toggle" id="isToggle" onclick="toggleMenu()">
                   <div class="lines">
                       <span></span>
                       <span></span>
                       <span></span>
                   </div>
               </a>
           </div>
       </div>
       <!-- End Mobile Toggle -->

       <div id="navigation">
           <!-- Navigation Menu-->
           <ul class="navigation-menu nav-right !justify-end nav-light">
               
               <li><a href="/Travosy/index.php" class="sub-menu-item">Home</a></li>

               <li><a href="/Travosy/list.php" class="sub-menu-item">Packages</a></li>
       
               <li><a href="/Travosy/aboutus.php" class="sub-menu-item">About Us</a></li>

               <li><a href="/Travosy/contact.php" class="sub-menu-item">Contact Us</a></li>

               <?php if ($is_logged_in && $role=="user"): ?>
                   <!-- User is logged in - show user dropdown -->
                   <li class="has-submenu parent-menu-item relative">
                       <a href="/Travosy/user/user-profile.php" class="sub-menu-item flex items-center" style="margin-top:3px;">
                           <i data-feather="user" class="size-4 me-2"></i>
                           <span class="hidden sm:inline"><?php echo htmlspecialchars($user_name); ?></span>
                       </a>
                   </li>
                <?php elseif ($is_logged_in && $role=="admin"): ?>
                <!-- User is not logged in - show login link -->
                    <li class="has-submenu parent-menu-item relative">
                       <a href="/Travosy/admin/admin-dashboard.php" class="sub-menu-item flex items-center">
                           <i data-feather="user" class="size-4 me-2"></i>
                           <span class="hidden sm:inline"><?php echo htmlspecialchars($user_name); ?></span>
                       </a>
                   </li>
               <?php else: ?>
                   <!-- User is not logged in - show login link -->
                   <li><a href="/Travosy/login.php" class="sub-menu-item">Login</a></li>
               <?php endif; ?>
           </ul><!--end navigation menu-->
       </div><!--end navigation-->
   </div><!--end container-->
</nav><!--end header-->
<!-- End Navbar -->

<!-- Add CSS for dropdown functionality -->
<style>
.has-submenu:hover .submenu {
    display: block;
}

.submenu {
    display: none;
}

.has-submenu {
    position: relative;
}

/* Mobile responsiveness for user menu */
@media (max-width: 640px) {
    .submenu {
        position: static;
        box-shadow: none;
        background: transparent;
        display: block;
        margin-left: 1rem;
    }
    
    .has-submenu:hover .submenu {
        display: block;
    }
}
</style>

<!-- Add JavaScript for better dropdown control -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown menu clicks
    const userMenuItem = document.querySelector('.has-submenu');
    const submenu = document.querySelector('.submenu');
    
    if (userMenuItem && submenu) {
        userMenuItem.addEventListener('click', function(e) {
            e.preventDefault();
            submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuItem.contains(e.target)) {
                submenu.style.display = 'none';
            }
        });
    }
});
</script>