<?php include 'include/config.php';
session_start();

//error report for debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

//verify user login
if (!isset($_SESSION['CUSTOMER_ID'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['CUSTOMER_ID'];


//get customer's tier info
$customer_query = "SELECT c.TIER_ID, c.TOTAL_SPENT, mt.TIER_NAME
                   FROM customer c
                   LEFT JOIN membership_tier mt ON c.TIER_ID = mt.TIER_ID
                   WHERE c.CUSTOMER_ID = '$customer_id'
                   AND c.STATUS = 'Active'";

$customer_result = mysqli_query($conn, $customer_query);
$customer = mysqli_fetch_assoc($customer_result);

$customer_tier_id = $customer['TIER_ID'];
$customer_total_spent = $customer['TOTAL_SPENT'];
assignTierVouchers($conn, $customer_id, $customer_tier_id);


//get all active, non-deleted vouchers
$voucher_result = mysqli_query($conn, "
    SELECT 
        v.*,
        mt.TIER_NAME,
        cv.CUSTOMER_VOUCHER_ID,
        cv.USED_COUNT  AS CUSTOMER_USED_COUNT,
        cv.CLAIMED_AT,
        cv.EXPIRY_DATE AS CUSTOMER_EXPIRY_DATE,
        cv.LAST_USED_AT
    FROM voucher v
    LEFT JOIN membership_tier mt ON v.TIER_ID = mt.TIER_ID
    LEFT JOIN customer_voucher cv 
           ON v.VOUCHER_ID = cv.VOUCHER_ID AND cv.CUSTOMER_ID = $customer_id
    WHERE v.IS_DELETED = 0 
      AND v.VOUCHER_STATUS = 'Active'
    ORDER BY v.EXPIRY_DATE ASC
");

$available_vouchers = [];
$unavailable_vouchers = [];
$today = new DateTime();

function formatDate($dateStr) {
    if (empty($dateStr) || $dateStr === '0000-00-00 00:00:00' || $dateStr === '0000-00-00') {
        return 'N/A';
    }
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateStr)
         ?: DateTime::createFromFormat('Y-m-d', $dateStr);
    return $date ? $date->format('d M, Y') : 'N/A';
}

while ($row = mysqli_fetch_assoc($voucher_result)) {

    $customer_expiry = $row['CUSTOMER_EXPIRY_DATE'];
    
    if (empty($customer_expiry) || $customer_expiry === '0000-00-00') {
        continue;
    }

    $expiry        = new DateTime($customer_expiry);
    $is_expired    = $expiry < $today;
    $customer_used = $row['CUSTOMER_USED_COUNT'] ?? 0;
    $per_user_ok   = $row['PER_USER_LIMIT'] == -1 || $customer_used < $row['PER_USER_LIMIT'];
    $max_usage_ok  = $row['MAX_USAGE'] == -1 || $row['USED_COUNT'] < $row['MAX_USAGE'];

    if (!$is_expired && $per_user_ok && $max_usage_ok) {
        $available_vouchers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css?v=3.0">
    <link rel="stylesheet" href="css/footer.css?v=5.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
      :root
      {
        --main-color:rgb(240, 194, 200);
        --font-color:rgb(101, 54, 31);
        --secondary-color:#fff6e6;
        --rating-color:#c5afb1;
        --search-border-color:rgb(187, 162, 153);
        --bg-color:#fffdf9;
        --font2-color:rgb(147, 103, 82);
      }

      body {
        font-family: 'Quicksand', sans-serif;
        background-color: var(--bg-color);
        margin: 0;
        padding: 0;
      }

      /* back section */
      .back-section {
        display: flex;
        align-items: center;
        margin: 30px 0 10px 0;
        padding: 0 10px;
        position:relative;
        z-index:10;
      }

      .back-link {
        text-decoration: none;
        color: var(--font2-color);
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s;
      }

      .back-link:hover {
        text-decoration: underline;
      }

      .voucher-section{
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 15px;
      }

      .voucher-title {
        color: var(--font2-color);
        font-weight: bold;
        font-size: 32px;
        margin-bottom: 25px;
      }

      .group-label {
        color: var(--font2-color);
        font-size: 16px;
        font-weight: bold;
        margin: 30px 0 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      /* Base spacing for voucher groups */
      .voucher-group{
        margin-bottom: 80px; /* Increased padding below groups */
      }

      .voucher-container {
        position: relative;
        width: 100%;
        height: 380px;
        background-color: #fbcada; 
        border-radius: 40px; 
        display: flex;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(251, 202, 218, 0.3); 
        margin-bottom: 25px; /* Added margin between individual cards */
      }

      /* background design */
      .voucher-container::before {
        content: '';
        position: absolute;
        right: 320px;
        top: -20px;
        width: 150px;
        height: 150px;
        background: repeating-linear-gradient(
            -45deg,
            rgba(255, 255, 255, 0.15),
            rgba(255, 255, 255, 0.15) 10px,
            transparent 10px,
            transparent 20px
        );
        border-radius: 50%;
      }

      /* left side */
      .voucher-left {
        flex: 1;
        padding: 45px 0 45px 50px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 2;
      }

      .brand-title {
        color: #ffffff;
        font-size: 24px;
        font-weight: 300;
        letter-spacing: 1px;
        line-height: 1;
        margin-bottom: 5px;
      }

      .main-voucher-title {
        color: #ffffff;
        font-size: 55px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        line-height: 1;
        margin-top:15px;
      }

      .voucher-meta-row {
        display: flex;
        align-items: center;
        gap: 20px;
      }

      .tier-badge {
        background-color: var(--font2-color);
        color: #ffffff;
        font-size: 14px;
        font-weight: bold;
        padding: 10px 25px;
        border-radius: 20px;
      }

      .voucher-type {
        background-color: #ffffff;
        color:var(--font2-color); 
        font-size: 15px;
        font-weight: bold;
        padding: 10px 25px;
        border-radius: 50px;
        width: fit-content;
        margin: 15px 0;
      }

      .voucher-valid-date {
        color: var(--font2-color);
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 15px;
      }

      .website-link {
        color: #835229;
        font-size: 14px;
        text-decoration: none;
        opacity: 0.8;
        font-weight: bold;
      }

      /* right side voucher */
      .voucher-right {
        position: relative;
        width: 360px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .image-circle-wrapper {
        width: 340px;
        height: 340px;
        border-radius: 50%;
        border: 15px solid #fbcada; 
        overflow: hidden;
        position: absolute;
        right: -20px;
        top: -10px;
      }

      .cake-img {
        width: 100%;
        height: 100%;
        object-fit: cover; 
      }

      .discount-badge {
        position: absolute;
        left: -20px;
        top: 30%;
        width: 110px;
        height: 110px;
        background-color: var(--font2-color);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #ffffff;
        z-index: 3;
        box-shadow: 0 5px 15px rgba(131, 82, 41, 0.3);
        transition: transform 0.3s ease;
      }

      .voucher-container:hover .discount-badge {
        transform: scale(1.1) rotate(-5deg);
      }

      .discount-num {
        font-size: 26px;
        font-weight: 900;
        line-height: 1;
      }

      .discount-text {
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 1px;
      }

      .dots-deco {
        position: absolute;
        bottom: 25px;
        left: -30px;
        display: flex;
        gap: 8px;
      }

      .dot {
        width: 12px;
        height: 12px;
        border: 2px solid #835229;
        border-radius: 50%;
      }
      
      .dot.solid {
        background-color: #835229;
      }

      .ready-badge {
        background-color: #a8d5a2;
        color: #2d5a27;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }


    </style>
</head>
<body>
  <?php include 'include/header.php'?>

  <div class="container">
    <div class="back-section">
        <a href="homepage.php" class="back-link">
          <i class="bi bi-chevron-left"></i>Back
        </a>
      </div>
  </div>

  <section class="voucher-section">
    <h2 class="voucher-title">Vouchers</h2>
    

    <!-- available voucher -->
      <div class="voucher-group">
        <h3 class="group-label">Available</h3>
        
        <?php if (empty($available_vouchers)):?>
          <p style="color:var(--font2-color); opacity:0.6;">No vouchers available for you right now.</p>
        <?php else:?>
          <?php foreach ($available_vouchers as $v):
            $isClaimed = !is_null($v['CUSTOMER_VOUCHER_ID']);
          ?>

          <div class="voucher-container">
          <div class="voucher-left">
              <div>
                  <div class="brand-title">Cakeology</div>
                  <div class="main-voucher-title">Discount Voucher</div>
              </div>
              
              <div class="voucher-meta-row">
                <div class="voucher-type">
                    <?= $v['TIER_NAME'] ? "Only for " . htmlspecialchars($v['TIER_NAME']) : 'All Tiers' ?>
                </div>
                <span class="tier-badge">Min Spent RM <?= number_format($v['MIN_SPEND'], 0)?></span>
              </div>
              
              <div>
                  <p class="voucher-valid-date">Valid Until <?= formatDate($v['CUSTOMER_EXPIRY_DATE']) ?>
                  </p>
                  <span class="ready-badge">
                    <i class="bi bi-check-circle-fill"></i> Ready to Use
                  </span>
              </div>
          </div>

          <div class="voucher-right">
              <div class="discount-badge">
                  <span class="discount-num"><?= $v['DISCOUNT_RATE']?>%</span>
                  <span class="discount-text">OFF</span>
              </div>
              
              <div class="image-circle-wrapper">
                  <img src="image/category/birthday cake.jpeg" alt="Strawberry Cake" class="cake-img">
              </div>

              <div class="dots-deco">
                  <div class="dot"></div>
                  <div class="dot"></div>
                  <div class="dot"></div>
                  <div class="dot"></div>
              </div>
          </div>
        </div>
        <?php endforeach;?>
        <?php endif;?>
      </div>
  </section>

  <?php include 'include/footer.php'?>
</body>
</html>