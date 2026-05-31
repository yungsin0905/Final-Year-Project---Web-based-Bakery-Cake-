<?php include_once 'include/config.php';

$bakery_query = "SELECT * FROM bakery_info";
$bakery_result = $conn->query($bakery_query);
$bakery_info = $bakery_result->fetch_assoc();

//address
$address_line = trim($bakery_info['ADDRESS'] ?? '');
$city         = trim($bakery_info['CITY'] ?? '');
$state        = trim($bakery_info['STATE'] ?? '');
$postcode     = trim($bakery_info['POSTCODE'] ?? '');
$full_addr = array_filter([$address_line,  $postcode,$city, $state]);
$full_addr_str = implode(', ', $full_addr);
?>

<footer>
    <div class="footer-container">
        <div class="footer-col logo-info">
          <img src="<?php echo htmlspecialchars($bakery_info['SHOP_IMAGE']); ?>" alt="Logo" class=" footer-logo img-fluid mb-3" style="width: 150px;">
          <p><?php echo htmlspecialchars($bakery_info['BAKERY_DES']); ?></p>
        </div>
      <div class="footer-col links">
        <h4>Cake</h4>
        <h6>Cake Type</h6>
        <ul>
          <li><a href="product catalogue.php?id=1">Cheese Cake</a></li>
          <li><a href="product catalogue.php?id=2">Fruit Cake</a></li>
          <li><a href="product catalogue.php?id=3">Guilt-Free Cake</a></li>
          <li><a href="product catalogue.php?id=4">Vegan Cake</a></li>
        </ul>
        <h6>Occasion</h6>
        <ul>
          <li><a href="product catalogue.php?id=5">Corporate / Event</a></li>
          <li><a href="product catalogue.php?id=6">Birthday Cake</a></li>
        </ul>
      </div>
      <div class="footer-col links">
        <h4>Info</h4>
        <ul>
          <li><a href="about us.php">About Us</a></li>
          <li><a href="UserDashboard.php">My Profile</a></li>
          <li><a href="membership.php">Membership</a></li>
          <li><a href="Wishlist.php">Wishlist</a></li>
          <li><a href="Customise.php">Customise</a></li>
          <li><a href="voucher.php">Voucher</a></li>
        </ul>
      </div>
      <div class="footer-col contact">
        <h4>Contact Us</h4>
        <ul>
          <li><?php echo htmlspecialchars($full_addr_str); ?></li>
          <li><?php echo htmlspecialchars($bakery_info['EMAIL']); ?></li>
          <li><?php echo htmlspecialchars($bakery_info['PHONE']); ?></li>
          <li>Operating Hours: <?php 
          $formatted_days = str_replace('Mon,Tue,Wed,Thu,Fri', 'Mon - Fri', $bakery_info['OPEN_DAYS']);
          echo htmlspecialchars($formatted_days . ' ' . date('g:i A', strtotime($bakery_info['OPEN_TIME'])) . ' - ' . date('g:i A', strtotime($bakery_info['CLOSE_TIME'])));
          ?>
          </li>
        </ul>
      </div>
    </div>
  </footer>
