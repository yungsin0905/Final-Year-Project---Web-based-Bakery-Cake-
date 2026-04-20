 <?php
  if (session_status() === PHP_SESSION_NONE){
      session_start();
  }
 ?>

 <!-- search section -->
    <header class="top-bar">
        <div class="logo"><img src="image/cakeology logo.png" alt="Logo" class="img-fluid"></div>

        <form action="product catalogue.php" method="GET" class="search-bar">
            <input type="text" name="search" class="form-control search-input" 
            placeholder="What are you looking for..."
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <button type="submit" class="search-btn"><i class="bi bi-search search-icon">Search</i></button>
        </form>
        
        <div class="btn-group">
            <a class="save-btn" href="Wishlist.php"><i class="bi bi-heart-fill"></i></a>
            <a class="cart-btn" href="#"><i class="bi bi-cart-fill"></i></a>
            <a class="profile-btn" href="#"><i class="bi bi-person-circle"></i></a>
            <a href="sign-up.php" class="sign-up">Sign Up/Login</a>
            <i class ="bi bi-list menu-icon" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"></i>
        </div>

         <!-- menu collapsible bar -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
              <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobileMenuLabel" style="color: var(--main-color); font-weight: bold;">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                <ul class="list-group list-group-flush">
                  
                <li class="list-group-item border-0"><a href="homepage.php" class="text-decoration-none"><i class="bi bi-house me-2"></i> Homepage</a></li>
                <li class="list-group-item border-0"><a href="UserDashboard.php" class="text-decoration-none"><i class="bi bi-person-circle me-2"></i>User Profile</a></li>
                <li class="list-group-item border-0"><a href="Wishlist.php" class="text-decoration-none"><i class="bi bi-heart-fill me-2"></i> Wishlist</a></li>

                  <li class="list-group-item border-0"><a href="cart.php" class="text-decoration-none"><i class="bi bi-cart-fill me-2"></i> Shopping Cart</a></li>

                  <li class="list-group-item border-0"><a href="About Us.php" class="text-decoration-none"><i class="bi bi-info-circle me-2"></i> About Us</a></li>
                  <!-- //if logged in will change the icon to "logged out"
                  logged out will changed it to "sign up" -->
                  <?php if(isset($_SESSION['CUSTOMER_ID'])):?>
                      <li class="list-group-item border-0"><a href="javascript:void(0);" onclick="confirmLogout()" class="text-decoration-none text-danger"><i class="bi bi-box-arrow-in-left me-2"></i> Logout</a></li>
                    <?php else:?>
                      <li class="list-group-item border-0"><a href="sign-up.php" class="text-decoration-none"><i class="bi bi-box-arrow-in-right me-2"></i> Sign Up/Sign In</a></li>
                    <?php endif;?>
                </ul>
              </div>
          </div>
    </header>

    <!-- navigation section -->
    <nav class="nav-main">
      <a href="#">About Us</a>

      <!-- dropdown menu -->
      <div class="dropdown-container">
        <a href="#" class="nav-link-item">All Cakes</a>
        
        <div class="mega-menu">
            <div class="menu-column">
                <h4>Favourites</h4>
                <ul>
                    <li><a href="product catalogue.php">Best Selling</a></li>
                    <li><a href="product catalogue.php">High Recommended</a></li>
                </ul>
            </div>
            <div class="menu-column">
                <h4>Occasions</h4>
                <ul>
                    <li><a href="product catalogue.php">Corporate/ Event</a></li>
                    <li><a href="product catalogue.php">Birthday Cakes</a></li>
                </ul>
            </div>
            <div class="menu-column">
                <h4>Cake Type</h4>
                <ul>
                    <li><a href="product catalogue.php?id=all">All Cakes</a></li>
                    <li><a href="product catalogue.php?id=2">Cheese Cakes</a></li>
                    <li><a href="product catalogue.php?id=3">Fruit Cakes</a></li>
                    <li><a href="product catalogue.php?id=4">Gluten-Free Cakes</a></li>
                    <li><a href="product catalogue.php?id=5">Vegan Cakes</a></li>
                </ul>
            </div>
        </div>
      </div>

      <a href="#">Voucher</a>
      <a href="#">Customise</a>
      <a href="#">Membership</a>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      //<!-- menu collapsible bar -->
    window.onload = function() {
      var myOffcanvas = document.getElementById('mobileMenu');
      if(myOffcanvas) {
          console.log("Offcanvas element found!");
      } else {
          console.error("Offcanvas element NOT found! Check your ID.");
      }
    };

    //logout
  function confirmLogout(){
  if(confirm("Are you sure you want to logout?"))
  window.location.href = 'Login.php?logout=true';
}    

     // link copy
  function copyPageLink() {
    // get current URL
    const currentUrl = window.location.href;

    // Using clipboard API copy it
    navigator.clipboard.writeText(currentUrl).then(function() {
       
        //display notifier
        const toast = document.getElementById('copy-toast');
        toast.style.display = 'block';
        
        setTimeout(() => {
            toast.style.display = 'none';
        }, 2000);
        
        //copy failed
    }).catch(function(err) {
        console.error('Link copy failed: ', err);
        alert("Copying failed, please copy the link manually.");
    });
}
    </script>
