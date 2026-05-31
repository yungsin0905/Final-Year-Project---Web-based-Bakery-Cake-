<?php include 'include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//error report for debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

//verify user login
if (!isset($_SESSION['CUSTOMER_ID'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['CUSTOMER_ID'];

$customer_query = "SELECT c.*, t.TIER_NAME, t.MIN_SPENT
FROM customer c
LEFT JOIN membership_tier t ON c.TIER_ID = t.TIER_ID
WHERE c.CUSTOMER_ID = $customer_id
AND c.STATUS = 'Active'";

$customer_result = mysqli_query($conn, $customer_query);
$customer = mysqli_fetch_assoc($customer_result);

//retrieve all membership tier
$tier_query = "SELECT * FROM membership_tier ORDER BY MIN_SPENT ASC";
$tier_result = mysqli_query($conn, $tier_query);
$tiers = mysqli_fetch_all($tier_result, MYSQLI_ASSOC);

$current_tier_name = $customer['TIER_NAME'];
$total_spent = (float)($customer['TOTAL_SPENT']??0);

//automatically update tier if total spent exceeds current tier
$correct_tier = $tiers[0];
foreach ($tiers as $tier) {
    if ($total_spent >= $tier['MIN_SPENT']) {
        $correct_tier = $tier;
    }
}

if ($correct_tier['TIER_NAME'] !== $customer['TIER_NAME']) {
    //update customer's tier in database
    mysqli_query($conn, "UPDATE customer SET TIER_ID = {$correct_tier['TIER_ID']} WHERE CUSTOMER_ID = $customer_id");
    $customer['TIER_ID'] = $correct_tier['TIER_ID'];
    $customer['TIER_NAME'] = $correct_tier['TIER_NAME'];
    $customer['MIN_SPENT'] = $correct_tier['MIN_SPENT'];
}
//update current tier info for display
$current_tier_name = $correct_tier['TIER_NAME'];
$current_tier_min_spent = $correct_tier['MIN_SPENT'];


//find another tier and progress
$progress_text = "All benefits unlocked";
foreach ($tiers as $index => $tier) {
    if ($tier ['TIER_NAME'] === $current_tier_name) {
        if (isset($tiers[$index + 1])) {
           $next = $tiers[$index +1];
           $spend_needed = max(0, $next['MIN_SPENT'] - $total_spent);
           $progress_text = "Spend more RM" . number_format($spend_needed,2) . " / RM" . number_format($next['MIN_SPENT'], 2) . " to upgrade to " . $next['TIER_NAME'];
        }
        break;
    }
}

$higher_tiers = array_filter($tiers, function($tier) use ($current_tier_min_spent){
  return $tier['MIN_SPENT'] > $current_tier_min_spent;
});

function getTierClass($name){
  $map = [
    'Bronze' => 'bronze-tier',
    'Silver' => 'silver-tier',
    'Gold' => 'gold-tier'
  ];
  return $map[$name] ?? 'bronze-tier';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css?v=3.0">
    <link rel="stylesheet" href="css/footer.css?v=5.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
      :root {
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
        margin: 30px 0 30px 20px; /* 修正了负边距，防止超出屏幕 */
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

      .main-content {
        max-width:1100px;
        margin:0 auto;
        padding:20px;
      }

      .page-title {
        font-weight:bold;
        color:var(--font2-color);
        margin-bottom:20px;
      }

      .member-tier-section {
        width: 100%;
        height: 220px; 
        border-radius: 20px;
        margin-bottom: 25px;
        padding: 30px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-size: cover;
        background-position: center;
        gap:30px;
      }

      .bronze-tier {
        background-image: url('image/membership/bronze.png');
        color: white;
      }
      .silver-tier {
        background-image: url('image/membership/silver.png');
        color: var(--font2-color); 
      }
      .gold-tier {
        background-image: url('image/membership/gold.png');
        color: #2c3c7c; 
      }


      /* member tier */
      .member-tier {
        font-size:20px;
        margin:0;
        display:block;
      }

      .tier-name {
        font-size:45px;
        font-weight:bold;
        margin:0;
        line-height: 1;
      }

      .progress-text {
        font-size:20px;
        margin:0;
        opacity:0.8;
      }

      /* rules */
      .member-benefits-section {
        margin-top:60px;
        margin-bottom:60px;
      }

      .title-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
      }

      .join-title {
        font-weight: bold;
        color: var(--font2-color);
        margin: 0;
      }

      .voucher-btn {
        border: 2px solid var(--search-border-color);
        background-color:var(--secondary-color);
        border-radius:20px;
        padding:10px 15px;
        text-decoration: none;
        color: var(--search-border-color);
        font-size: 15px;
        font-weight: bold;
      }

      .rules-content ol {
        padding-left:18px;
        color:var(--font2-color);
        font-size:14px;
        line-height:1.8;
      }

      /* upgrade */
      .upgrade-section{
        margin-top:100px;
        border-top: 1px solid var(--search-border-color);
      }

      .upgrade-title {
        font-weight: bold;
        color: var(--font2-color);
        margin: 100px 0 20px 0;
      }

      .progress-bar {
        display: none;
      }

      /* opacity upgrade section */
      .upgrade-section .member-tier-section {
          /* make the card color gray */
          filter: grayscale(100%); 

          position: relative;
          z-index: 1;
      }

      /* gray card text */
      .upgrade-section .member-tier-section p,
      .upgrade-section .member-tier-section h2 {
          color: #999; 
          font-weight: normal; 
          transition: color 0.3s;
      }
    </style>
</head>

<body>
  <?php include 'include/header.php';?>

  <div class="container-fluid">
    <div class="back-section">
      <a href="homepage.php" class="back-link">
        <i class="bi bi-chevron-left"></i>Back
      </a>
    </div>
  </div>

  <div class="main-content">
    <div class="member-title">
      <h1 class="page-title">Membership</h1>
    </div>
    
    <div class="member-tier-section <?php echo getTierClass($current_tier_name);?>">
      <p class="member-tier">Your Member tier <i class="bi bi-chevron-right"></i></p>
        <h2 class="tier-name"><?php echo htmlspecialchars($current_tier_name); ?></h2>
         <p class="progress-text"><?php echo htmlspecialchars($progress_text); ?></p>
    </div> <div class="member-benefits-section">
      <div class="title-section">
        <h2 class="join-title">Join Cakeology Membership</h2>
        <a href="voucher.php" class="voucher-btn">View your vouchers</a>
      </div>
      
      <div class="rules-content">
        <ol>
          <li>Sign up as a member to start enjoying the loyalty program</li>
          <li>Unlock higher tiers as you spend more</li>
          <li>Bronze: Granted upon registration. Members can use public vouchers.</li>
          <li>Silver: Unlocked after accumulating RM200 in total spending. Members receive exclusive promotional codes with a 15% discount and free shipping.</li>
          <li>Gold: Unlocked after accumulating RM500 in total spending. Members receive exclusive promotional codes with a 25% discount and free shipping.</li>
        </ol>
      </div>

      <?php 
      $higher_tiers = array_filter($tiers, function($tier) use ($customer){
        return $tier['MIN_SPENT']>$customer['MIN_SPENT'];
      });
      ?>

      <?php if (!empty($higher_tiers)): ?>
      <div class="upgrade-section">
        <h3 class="upgrade-title">Upgrade Your Membership</h3>
        
        <?php foreach($higher_tiers as $tier): ?>
          <?php
          $upgrade_text = "All benefits unlocked";
          foreach ($tiers as $i => $t) {
            if ($t['TIER_ID'] === $tier['TIER_ID'] && isset($tiers[$i + 1])){
              $upgrade_text = "Spend RM" . number_format($tiers[$i + 1]['MIN_SPENT'], 2) . " to unlock next tier";
              break;
            }
          }
          ?>

        <div class="member-tier-section <?php echo getTierClass($tier['TIER_NAME']);?>">
          <p class="member-tier">Your Member tier <i class="bi bi-chevron-right"></i></p>
            <h2 class="tier-name"><?php echo htmlspecialchars($tier['TIER_NAME']); ?></h2>
            <p class="progress-text">Unlock at RM<?php echo number_format($tier['MIN_SPENT'], 2); ?> total spending</p>
        </div>
        <?php endforeach;?>
      </div> 
      <?php endif;?>
    </div> 
  </div> 

  <?php include 'include/footer.php';?>
</body>
</html>