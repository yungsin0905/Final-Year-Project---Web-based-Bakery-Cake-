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

//retrieve customer info
$customer_query = "SELECT c.*, t.TIER_NAME
                  FROM customer c
                  LEFT JOIN membership_tier t ON c.TIER_ID = t.TIER_ID
                  WHERE c.CUSTOMER_ID = $customer_id
                  AND c.STATUS = 'Active'";

$customer_result = mysqli_query($conn, $customer_query);
$customer = mysqli_fetch_assoc($customer_result);

//retrieve address
$address_query = "SELECT * FROM address WHERE CUSTOMER_ID = $customer_id ORDER BY IS_DEFAULT DESC";
$address_result = mysqli_query($conn,$address_query);
$address = mysqli_fetch_all($address_result, MYSQLI_ASSOC);
$coverage_query = "SELECT * FROM delivery_coverage ORDER BY STATE, CITY";
$coverage_result = mysqli_query($conn, $coverage_query);
$coverage = mysqli_fetch_all($coverage_result, MYSQLI_ASSOC);


  //update info
  $info_error = '';
  $info_success = $_SESSION['info_success'] ?? '';
  unset($_SESSION['info_success']);
  
  $pw_error = '';
  $pw_success = $_SESSION['pw_success'] ?? '';
  unset($_SESSION['pw_success']);

//post handler
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $customer_id = $_SESSION['CUSTOMER_ID'];

  if (isset($_POST['form_type']) && $_POST['form_type'] === 'update_info'){
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $country_code = mysqli_real_escape_string($conn, trim($_POST['country-code']));
    $phone_raw = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $phone = $country_code . $phone_raw;

    //verify the field cannot be empty
    if(empty($username) || empty($email) || empty($phone)){
        $info_error = "All fields are required!";

    } else {

      //checking whether is email, username or phone have already existed
      $checkQuery = "SELECT * FROM customer 
                       WHERE (EMAIL = '$email' OR PHONE = '$phone' OR CUSTOMER_NAME = '$username') 
                       AND CUSTOMER_ID != $customer_id";  // 排除自己
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $existingUser = mysqli_fetch_assoc($checkResult);

            if ($existingUser['CUSTOMER_NAME'] === $username) {
                $info_error = "Username is already taken!";
            } else if ($existingUser['EMAIL'] === $email) {
                $info_error = "Email is already registered by another account!";
            } else if ($existingUser['PHONE'] === $phone) {
                $info_error = "Phone number is already linked to another account!";
            }
      }
      
      if (empty($info_error)) {
        //handle upload avatar
        $imageField = '';
        if (!empty($_FILES['avatar']['name'])){
            $fileType = $_FILES['avatar']['type'];
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if(!in_array($fileType, $allowed)){
                $info_error = "Invalid image format. Please upload JPG, PNG, GIF or WEBP.";
            } else {
                $uploadDir = 'image/user image/';
                $fileName = time() . '_' . basename($_FILES['avatar']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)){
                    $escaped = mysqli_real_escape_string($conn, $targetPath);
                    $imageField = ", PROFILE_IMAGE = '$escaped'";
                }
            }
        }


        // 
        if (empty($info_error)){
            $query = "UPDATE customer 
                      SET CUSTOMER_NAME = '$username', EMAIL = '$email', PHONE = '$phone' $imageField 
                      WHERE CUSTOMER_ID = $customer_id";

            if (mysqli_query($conn, $query)){
                 $_SESSION['info_success'] = "Profile updated successfully!";
                header("Location: UserDashboard.php");
                exit();
            } else {
                $info_error = "Update failed, please try again.";
            }
        }
      }
    }
  }
  

  //change password
  if(isset($_POST['form_type']) && $_POST['form_type'] === 'update_password'){
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    //retrieve current password
    $pw_result = mysqli_query($conn, "SELECT PASSWORD FROM customer WHERE CUSTOMER_ID = $customer_id");
    $pw_row = mysqli_fetch_assoc($pw_result);

    if(!password_verify($current_pass, $pw_row['PASSWORD'])){
      $pw_error = "Wrong current password, please try again.";
    
    }else if ($new_pass !== $confirm_pass){
      $pw_error = "Passwords do not match!";
      
    }else if (password_verify($new_pass, $pw_row['PASSWORD'])){
      $pw_error = "New password cannot be the same as your current password.";

    }else if (strlen($new_pass) <8 || 
    !preg_match('/[A-Z]/', $new_pass) || 
    !preg_match('/[a-z]/', $new_pass) || 
    !preg_match('/[0-9]/', $new_pass) || 
    !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_pass)) {
    $pw_error = "Password does not meet the requirements.";
    }else {
      $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
      if(mysqli_query($conn, "UPDATE customer SET PASSWORD = '$hashed' WHERE CUSTOMER_ID = $customer_id")){
         $_SESSION['pw_success'] = "Password updated successfully!";
        header("Location: UserDashboard.php");
    exit();
      } else {
          $pw_error = "Failed to update password, please try again.";
      }
    }
  }

  //edit address
  if(isset($_POST['address_line'])){
    $addr_id = intval($_POST['address_id'] ?? 0);
    $fname = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $country_code_a = mysqli_real_escape_string($conn, trim($_POST['country-code']));
    $phone_raw_a = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $phone_a = $country_code_a . $phone_raw_a;
    $company = mysqli_real_escape_string($conn, trim($_POST['company'] ?? ''));
    $addr_line = mysqli_real_escape_string($conn, trim($_POST['address_line']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $postcode = mysqli_real_escape_string($conn, trim($_POST['postcode']));
    $state = mysqli_real_escape_string($conn, trim($_POST['state']));
    $country = mysqli_real_escape_string($conn, trim($_POST['country']));
    $is_def = isset($_POST['is_default']) ? 1 : 0;

    if($is_def){
      mysqli_query($conn, "UPDATE address SET IS_DEFAULT = 0 WHERE CUSTOMER_ID = $customer_id");
    }

    if ($addr_id > 0){
      $query = "UPDATE address SET
      FIRST_NAME = '$fname',
      LAST_NAME = '$lname',
      PHONE = '$phone_a',
      COMPANY = '$company',
      ADDRESS_LINE = '$addr_line',
      CITY = '$city',
      POSTCODE = '$postcode',
      STATE = '$state',
      IS_DEFAULT = $is_def
      WHERE ADDRESS_ID = $addr_id AND CUSTOMER_ID = $customer_id";
    }else {
      //add new address
      $query = "INSERT INTO address
      (CUSTOMER_ID, FIRST_NAME, LAST_NAME, PHONE, COMPANY, ADDRESS_LINE, CITY, POSTCODE, STATE, IS_DEFAULT)
      VALUES
      ($customer_id, '$fname', '$lname', '$phone_a', '$company', '$addr_line', '$city', '$postcode', '$state', $is_def)";

    }

    if(mysqli_query($conn, $query)){
       echo "<script>alert('Address Updated Successful!'); window.location.href='UserDashboard.php';</script>";
    }else{
      echo "<script>alert('Address update failure'); window.history.back();</script>";
    }
    exit();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
        --font2-color:#936752;
        --card-bg-color:#f9d9d9;
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

    /*profile section */
    .user-profile-section {
      width: 100%;
      max-width: 1200px;
      margin: 40px auto 20px;
      padding: 0 20px;
    }

    .profile-card {
      position: relative;
      background: #fff;
      border-radius: 24px;
      box-shadow: 0 4px 30px rgba(101, 54, 31, 0.07);
      overflow: hidden;
      padding: 0;
    }

    /* Decorative banner strip at top */
    .profile-card::before {
      content: '';
      display: block;
      width: 100%;
      height: 90px;
    }

    .profile-card-body {
      display: flex;
      align-items: flex-end;
      gap: 28px;
      padding: 0 36px 28px;
      margin-top: -50px;
      flex-wrap: wrap;
    }

    .profile-avatar {
      flex-shrink: 0;
    }

    .profile-avatar img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      box-shadow: 0 4px 16px rgba(101, 54, 31, 0.15);
      display: block;
    }

    .profile-info {
      flex: 1;
      padding-bottom: 4px;
    }

    .profile-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 10px;
      flex-wrap: wrap;
    }

    .profile-name {
      color: var(--font2-color);
      font-size: 26px;
      font-weight: 700;
      margin: 0;
    }

    .level-tag {
      background-color: var(--secondary-color);
      color: var(--font2-color);
      padding: 4px 14px;
      border-radius: 50px;
      font-size: 12px;
      border: 1px solid var(--main-color);
      font-weight: 700;
      letter-spacing: 0.4px;
    }

    .profile-meta {
      display: flex;
      gap: 24px;
      flex-wrap: wrap;
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--font2-color);
      font-size: 14px;
    }

    .meta-item i {
      color: var(--main-color);
      font-size: 17px;
    }

    .meta-item a {
      color: var(--font2-color);
      text-decoration: none;
      transition: 0.3s;
    }

    .meta-item a:hover {
      color: var(--main-color);
      text-decoration: underline;
    }

    .topup-link {
      font-size: 13px;
      margin-left: 4px;
    }

    /* action cards */
    .personalize-cards{
      margin: 40px auto;
    }

    .action-card{
      background-color: white;
      border:none;
      border-radius:20px;
      padding: 20px 10px;
      text-align:center;
      box-shadow:0 5px 20px rgba(101, 54, 31, 0.05);
      transition:0.5s;
      width: 100%;
      height: auto;
      min-height: 160px;
      margin: 0;
    }

    .user-profile-section, 
    .personalize-cards, 
    .sections {
      max-width: 1200px ;
      margin-left: auto ;
      margin-right: auto ;
      padding-left: 20px ;
      padding-right: 20px ;
    }

    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(240, 194, 200, 0.3);
    }

    .action-card i{
      font-size:30px;
      margin-bottom: 10px;
      color:var(--font2-color);
    }

    .card-title{
      font-size:14px;
      color:var(--font2-color);
      font-weight:bold;
      height: 40px;
      display: flex;
        align-items: center;
        justify-content: center;
    }

    .arrow-btn{
      width:25px;
      height:25px;
      background-color: var(--main-color);
      color:white;
      border-radius: 50%;
      display:flex;
      align-items:center;
      justify-content: center;
      text-decoration: none;
      margin: 0 auto;
      padding: 0;
      transition:0.5s;
    }

    .arrow-btn i{
      font-size:10px;
      margin:0 ;
      padding:0;
      line-height: 1; /*used to center the icon*/
    }

    /* Profile + Address sections grid */
    .sections {
      display: grid;
      grid-template-columns: 1fr 1.4fr;
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto 0px;
      padding: 0 20px;
    }

    .detail-section {
      background: #fff;
      border-radius: 20px;
      padding: 28px 30px;
      box-shadow: 0 3px 20px rgba(101, 54, 31, 0.06);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 22px;
      padding-bottom: 14px;
      border-bottom: 2px solid #fceee9;
    }

    .section-title {
      font-size: 17px;
      font-weight: 700;
      color: var(--font2-color);
      letter-spacing: 0.3px;
      margin: 0;
    }

    /* Profile fields */
    .profile-field {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      padding: 12px 0;
      border-bottom: 1px solid #faf2ef;
    }

    .profile-field:last-child {
      border-bottom: none;
    }

    .profile-field-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: var(--secondary-color);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .profile-field-icon i {
      color: var(--font2-color);
      font-size: 16px;
    }

    .profile-field-body {
      flex: 1;
    }

    .profile-field label {
      font-size: 10px;
      color: #c5a898;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      margin-bottom: 2px;
      display: block;
      font-weight: 600;
    }

    .profile-field p {
      font-size: 14px;
      color: var(--font2-color);
      margin: 0;
      font-weight: 600;
    }

    /* Address cards  */
    .address-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .address-item {
      border: 1.5px solid #f0e4df;
      border-radius: 14px;
      padding: 14px 16px;
      position: relative;
      transition: .25s;
      background: #fffcfb;
    }

    .address-item.is-default {
      border-color: var(--main-color);
      background: #fff9f9;
      box-shadow: 0 2px 12px rgba(240, 194, 200, 0.25);
    }

    .address-item-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 8px;
    }

    .address-name {
      font-size: 13px;
      font-weight: 700;
      color: var(--font2-color);
    }

    .default-badge {
      background: var(--main-color);
      color: var(--font2-color);
      font-size: 10px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 50px;
      white-space: nowrap;
      flex-shrink: 0;
    }

    .address-text {
      font-size: 12px;
      color: var(--font2-color);
      margin-top: 3px;
      line-height: 1.5;
    }

    .address-actions {
      display: flex;
      gap: 6px;
      margin-top: 10px;
      flex-wrap: wrap;
    }

    .addr-btn {
      font-size: 11px;
      padding: 4px 12px;
      border-radius: 50px;
      border: 1px solid;
      cursor: pointer;
      transition: .2s;
      background: none;
      font-weight: 600;
    }

    .addr-btn-set {
      border-color: var(--font2-color);
      color: var(--font2-color);
    }

    .addr-btn-set:hover {
      background: var(--secondary-color);
    }

    .addr-btn-edit {
      border-color: #c5afb1;
      color: var(--font2-color);
    }

    .addr-btn-edit:hover {
      background: #faf0f0;
    }

    .addr-btn-del {
      border-color: #e8b4b4;
      color: #c96b6b;
    }

    .addr-btn-del:hover {
      background: #fff0f0;
    }

    .add-address-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 10px;
      border-radius: 12px;
      border: 1.5px dashed #e4c8c8;
      background: none;
      color: var(--font2-color);
      font-size: 13px;
      cursor: pointer;
      transition: .25s;
      margin-top: 10px;
      font-weight: 600;
    }

    .add-address-btn:hover {
      background: #fff5f5;
      border-color: var(--main-color);
    }

    .edit-link {
      color: var(--font2-color);
      text-decoration: none;
      font-size: 12px;
      cursor: pointer;
      background: var(--secondary-color);
      border: 1px solid var(--main-color);
      border-radius: 20px;
      padding: 4px 14px;
      font-weight: 600;
      transition: 0.2s;
    }

    .edit-link:hover {
      background: var(--main-color);
      color: #fff;
    }

    /* Modals — untouched styles */
    .form-control:focus {
      border-color: var(--main-color);
      box-shadow: 0 0 0 0.25rem rgba(240, 194, 200, 0.25);
    }

    .modal-backdrop.show {
      opacity: 0.4;
    }

    .modal-footer .btn:hover {
      opacity: 0.9;
      transform: translateY(-1px);
      transition: 0.3s;
    }

    label[for="avatarUpload"]:hover {
      background-color: var(--secondary-color) 
      transition: 0.3s;
    }

    .check-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 14px;
      font-size: 13px;
    }

    .check-row input[type=checkbox] {
      width: 15px;
      height: 15px;
      accent-color: var(--font2-color);
    }

    /*profile information modal*/
    .modal-content{
      border-radius: 20px; 
      border: none; 
      background-color: var(--bg-color);
    }

    .modal-header{
      border-bottom: 1px solid #ddd;
    }

    .avatar-image{
      width: 100px; 
      height: 100px; 
      border-radius: 50%; 
      object-fit: cover; 
      border: 3px solid var(--main-color);
    }

    .camera-icon{
      width: 32px; 
      height: 32px; 
      border-radius: 50%; 
      cursor: pointer; 
      border: 1px solid #ddd;
    }

    .camera-fill{
      color: var(--font2-color); 
      font-size: 16px;
    }

    .avatar-hint{
      font-size: 12px; 
      color: #888;
    }

    .modal-form
    {
      border-radius: 10px; 
      border: 1px solid var(--main-color);
      width:100%;
      padding: 10px 15px;
      color:var(--font2-color);
      font-size:13px;
      box-sizing:border-box;
    }

    .form-select
    {
      border-radius: 10px; 
      border: 1px solid var(--main-color);
      font-size:13px;
      padding: 10px 15px;
    }

    .form-label{
      color: var(--font2-color);
    }

    .phone-group{
        display:flex;
        justify-content:space-between;
        margin-bottom:10px;
      }

      .phone-input{
        width:87%;
        padding: 10px 15px;
        border-radius: 10px; 
        border: 1px solid var(--main-color);
        color:var(--font2-color);
        font-size:13px;
        box-sizing:border-box;
      }

      .phone-group select{
        width:60px;
        height:41px;
        padding: 10px 15px;
        border-radius: 10px; 
        border: 1px solid var(--main-color);
        color:var(--font2-color);
        font-size:13px;
        box-sizing:border-box;
      }

    .btn-cancel{
      border-radius: 20px;
      border:none;
      background-color:rgb(123, 123, 123);
      padding: 6px 25px;
      transition:0.5s;
      color:white;
    }

    .btn-cancel:hover{
      background-color:rgb(102, 102, 102);
    }

    .btn-save{
      background-color: var(--main-color); 
      color: white; 
      border-radius: 20px; 
      padding: 6px 25px;
      border:none;
      transition:0.5s;
      margin-top:10px;
    }

    .btn-save:hover{
      background-color:rgb(220, 169, 176);
    }

    hr{
      margin-top:30px;
      border: 1px solid rgb(158, 120, 91);
    }

    /* change password */

    .password-rules {
      list-style: none;
      padding: 0;
      margin: 0;
      font-size: 12px;
      max-height: 0;
      overflow: hidden;
      opacity: 0;
      transition: all 0.4s ease;
    }

    .password-rules li {
      color: #bbb;
      padding: 2px 0;
    }

    .password-rules li::before {
      content: '✗  ';
      color: #e57373;
    }

    .password-rules li.passed::before {
      content: '✓  ';
      color: #66bb6a;
    }

    .password-rules li.passed {
      color: #66bb6a;
    }

     input[name="new_password"]:focus + .password-rules,
        input[name="new_password"]:focus ~ .password-rules {
            display: block;
      }


    .modal-form:focus + .password-rules {
        max-height: 200px;
        opacity: 1;
        margin-top: 10px;
        margin-bottom: 10px;
    }

      .modal-form:focus-within .password-rules {
          max-height: 200px;    
          opacity: 1;            
          margin-top: 10px;     
          margin-bottom: 10px;
      }

      .char-counter {
            text-align: right;
            font-size: 12px;
            color: var(--font2-color);
            margin-top: 6px;
            padding-right: 5px;
        }

        .char-counter.over {
            color: #e74c3c;
            font-weight: 700;
        }


  </style>
</head>
<body>
  <header>
    <?php include 'include/header.php';?>
  </header>

 <div class="container-fluid px-4 mt-3">
    <div class="back-section">
        <a href="homepage.php" class="back-link">
          <i class="bi bi-chevron-left"></i>Back
        </a>
      </div>
  </div>


  <main>
    <!-- background area -->

    <!-- user information -->
    <div class="user-profile-section">
      <div class="profile-card">
        <div class="profile-card-body">
          <div class="profile-avatar">
            <img src="<?php echo !empty ($customer['PROFILE_IMAGE']) ? htmlspecialchars($customer['PROFILE_IMAGE']) : 'image/user image/user_default.jpg'; ?>" alt="<?php echo htmlspecialchars($customer['CUSTOMER_NAME']);?>">
          </div>

          <div class="profile-info">
            <div class="profile-header">
              <h3 class="profile-name"><?php echo htmlspecialchars ($customer['CUSTOMER_NAME']);?></h3>
              <span class="level-tag"><?php echo htmlspecialchars($customer['TIER_NAME']); ?></span>
            </div>

            <div class="profile-meta">
              <div class="meta-item">
                <i class="bi bi-gem"></i>
                <a href="membership.php">Membership Benefits</a>
              </div>
              <div class="meta-item">
                <i class="bi bi-wallet2"></i>
                <span>Balance: <strong>RM <?php echo number_format((float)($customer['WALLET_BALANCE'] ?? 0), 2);?></strong></span>
                <a href="topup.php" class="topup-link">(Top-up)</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- card section -->
    <div class="personalize-cards">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4"> 
          <div class="col">
                <div class="card action-card">
                    <i class="bi bi-person-circle"></i>
                    <h4 class="card-title">My Custom Request</h4>
                    <a href="CustomiseRequest.php" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
            <div class="col">
                <div class="card action-card">
                    <i class="bi bi-heart-fill"></i>
                    <h4 class="card-title">Wishlist</h4>
                    <a href="Wishlist.php" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
            <div class="col">
                <div class="card action-card">
                    <i class="bi bi-cart-fill"></i>
                    <h4 class="card-title">Shopping Cart</h4>
                    <a href="shopping_cart.php" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
            <div class="col">
                <div class="card action-card">
                    <i class="bi bi-check-circle-fill"></i>
                    <h4 class="card-title">Order History</h4>
                    <a href="order_history.php" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
      </div>

      <!-- profile and address section -->
      <div class="sections">
        <div class="detail-section">
          <div class="section-header">
            <h2 class="section-title">Profile</h2>
            <button class="edit-link" data-bs-toggle = "modal" data-bs-target="#editProfileModal">Edit Profile</button>
          </div>

          <div class="profile-field">
            <div class="profile-field-icon"><i class="bi bi-person"></i></div>
            <div class="profile-field-body">
              <label>Username</label>
              <p><?php echo htmlspecialchars($customer['CUSTOMER_NAME']);?></p>
            </div>
          </div>

          <div class="profile-field">
            <div class="profile-field-icon"><i class="bi bi-envelope"></i></div>
            <div class="profile-field-body">
              <label>Email</label>
              <p><?php echo htmlspecialchars($customer['EMAIL']);?></p>
            </div>
          </div>

          <div class="profile-field">
            <div class="profile-field-icon"><i class="bi bi-telephone"></i></div>
            <div class="profile-field-body">
              <label>Phone</label>
              <p><?php echo htmlspecialchars($customer['PHONE']);?></p>
            </div>
          </div>

          <div class="profile-field">
            <div class="profile-field-icon"><i class="bi bi-calendar3"></i></div>
            <div class="profile-field-body">
              <label>Join Date</label>
              <p><?php echo date('d-m-Y', strtotime($customer['CREATED_AT']));?></p>
            </div>
          </div>
        </div>

        <!-- address section -->
        <div class="detail-section address-section-full">
          <div class="section-header">
            <h2 class="section-title">Saved Addresses</h2>
            <button class="edit-link" onclick="openAddAddress()">+ Add New</button>
          </div>

          <div class="address-list" id="addressList">

            <?php if(count($address) > 0):?>
              <?php foreach($address as $addr):?>
                <div class="address-item <?php echo $addr['IS_DEFAULT'] ? 'is-default' : ''; ?>"
                data-id="<?php echo $addr['ADDRESS_ID']; ?>">
                  <div class="address-item-top">
                    <div>
                      <div class="address-name"><?php echo htmlspecialchars($addr['FIRST_NAME'] . ' ' . $addr['LAST_NAME']);?></div>
                      <div class="address-text"><?php echo htmlspecialchars($addr['PHONE']);?></div>
                      <div class="address-text"><?php echo htmlspecialchars($addr['ADDRESS_LINE'] . ', ' . $addr['POSTCODE'] . ', ' . $addr['CITY'] . ', ' . $addr['STATE']);?></div>
                    </div>

                    <?php if($addr['IS_DEFAULT']):?>
                      <span class="default-badge">Default</span>
                    <?php endif;?>
                  </div>

                  <div class="address-actions">
                    <?php if (!$addr['IS_DEFAULT']):?>
                      <button class="addr-btn addr-btn-set" onclick="setDefault(<?php echo $addr['ADDRESS_ID']; ?>)">Set as Default</button>
                    <?php endif;?>
                    <button class="addr-btn addr-btn-edit" onclick="openEditAddress(
                      <?php echo $addr['ADDRESS_ID']; ?>,
                      '<?php echo addslashes($addr['FIRST_NAME']); ?>',
                      '<?php echo addslashes($addr['LAST_NAME']); ?>',
                      '<?php echo addslashes(preg_replace('/^\+60/', '', $addr['PHONE'])); ?>',
                      '<?php echo addslashes($addr['ADDRESS_LINE']); ?>',
                      '<?php echo addslashes($addr['CITY']); ?>',
                      '<?php echo addslashes($addr['STATE']); ?>',
                      '<?php echo addslashes($addr['COMPANY'] ?? ''); ?>',
                      <?php echo $addr['IS_DEFAULT']; ?>)">
                      <i class="ti ti-pencil" aria-hidden="true"></i> Edit
                    </button>

                    <button class="addr-btn addr-btn-del" onclick="deleteAddress(<?php echo $addr['ADDRESS_ID']; ?>, <?php echo $addr['IS_DEFAULT'];?>)"><i class="ti ti-trash" aria-hidden="true"></i> Delete</button>
                  </div>
                </div>
              <?php endforeach;?>
            <?php else:?>
              <p style="color: var(--font2-color); font-size: 13px;">No saved addresses yet.</p>
            <?php endif;?>
          </div>
          <button class="add-address-btn" onclick="openAddAddress()">
            <i class="ti ti-plus" aria-hidden="true"></i> Add New Address
          </button>
        </div>
      </div>
    </main>

  <!-- edit profile pop up modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title username" id="editProfileModalLabel">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <form action="UserDashboard.php" id="profileForm" method="POST" enctype="multipart/form-data" class="user-information">
            <input type="hidden" name="form_type" value="update_info">
            
            <?php if(!empty($info_error)): ?>
              <div class="alert alert-danger" style="border-radius:10px; font-size:13px;">
                <?php echo $info_error; ?>
              </div>
            <?php endif; ?>

            <?php if(!empty($info_success)): ?>
              <div class="alert alert-success" style="border-radius:10px; font-size:13px;">
                <?php echo $info_success; ?>
              </div>
            <?php endif; ?>

            <!-- change profile pic -->
            <div class="mb-4 text-center">
              <div class="position-relative d-inline-block">
                  <img class="avatar-image" id="previewAvatar" src="<?php echo !empty ($customer['PROFILE_IMAGE']) ? htmlspecialchars($customer['PROFILE_IMAGE']) : 'image/user image/user_default.jpg'; ?>" alt="<?php echo htmlspecialchars($customer['CUSTOMER_NAME']);?>" alt="Avatar Preview">
                  
                  <label for="avatarUpload" class="camera-icon position-absolute bottom-0 end-0 bg-white shadow-sm d-flex align-items-center justify-content-center">
                      <i class="bi bi-camera-fill"></i>
                  </label>
                  
                  <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
              </div>
              <p class="avatar-hint mt-2">Click the camera icon to change photo</p>
            </div>

            <!-- form change -->
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control modal-form" 
              value="<?php echo htmlspecialchars($customer['CUSTOMER_NAME']);?>" 
              placeholder="Change your username">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control modal-form" 
              value="<?php echo htmlspecialchars($customer['EMAIL']);?>"
              placeholder="user@example.com">
            </div>

            <div class="mb-3">
              <label class="form-label" for="phone">Phone number </label>
              <div class="phone-group">
                <select id="country-code" name="country-code" required>
                  <option value="+60">+60</option>
                </select>
            
                <input type="tel" id="phone" name="phone" class="form-control phone-input"
                  maxlength="11" placeholder="123456789" 
                  pattern = "\d{9,11}"
                  title = "please enter 9 to 11 digit numbers"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                  value="<?php echo htmlspecialchars(preg_replace('/^\+60/', '', $customer['PHONE'])); ?>"
                  required>
              </div>
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn-save">Update Info</button>
              </div>
            </div>
          </form>

          <hr>

          <!-- password  -->
          <form action="UserDashboard.php" id="passwordForm" method="POST">
            <input type="hidden" name="form_type" value="update_password">
             
            <?php if(!empty($pw_error)): ?>
                <div class="alert alert-danger" style="border-radius:10px; font-size:13px;">
                    <?php echo $pw_error; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($pw_success)): ?>
                <div class="alert alert-success" style="border-radius:10px; font-size:13px;">
                    <?php echo $pw_success; ?>
                </div>
            <?php endif; ?>
            
            <div class="mb-3">
              <label class="form-label">Current Password</label>
              <input type="password" name="current_password" class="form-control modal-form"
                    placeholder="Enter current password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">New Password</label>
              <input type="password" name="new_password" class="form-control modal-form"  placeholder="Enter your new password">
              <ul class="password-rules mt-2" id="pwRules">
                <li id="rule-length"> At least 8 characters</li>
                <li id="rule-upper"> At least one uppercase (A-Z)</li>
                <li id="rule-lower"> At least one lowercase (a-z)</li>
                <li id="rule-number"> At least one number (0-9)</li>
                <li id="rule-symbol"> At least one symbol (!@#$...)</li>
              </ul>
            </div>

            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="confirm_password" class="form-control modal-form" placeholder="Confirm your new password">
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn-save" onclick="return validatePassword()">Change Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

    <!-- address edit pop up modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg"> 
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title username" id="editAddressModalLabel">Add New Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <form action = "UserDashboard.php" id="addressForm" method="POST">
              <input type="hidden" id="addressId" name="address_id" value="">

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" class="form-control modal-form" placeholder="First Name" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text"  name="last_name"  class="form-control modal-form" placeholder="Last Name" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                <div class="phone-group">
                <select id="country-code" name="country-code" required>
                  <option value="+60">+60</option>
                </select>
            
                <input type="tel" id="phone" name="phone" class="form-control phone-input"
                  maxlength="11" placeholder="123456789" 
                  pattern = "\d{9,11}"
                  title = "please enter 9 to 11 digit numbers"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
              </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Company (Optional)</label>
                <input type="text" name="company" class="form-control modal-form" placeholder="Company name">
              </div>

              <div class="mb-3">
                <label class="form-label">Address Line <span class="text-danger">*</span></label>
                <textarea name="address_line" id = "addressLine" class="form-control modal-form" maxlength="50" placeholder="Street name, Unit, Building" required></textarea>
                <div class="char-counter"><span id="addr-count">0</span> / 50</div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">City <span class="text-danger">*</span></label>
                  <select name="city" id="citySelect" class="form-select" required onchange="updatePostcode(this)">
                    <option selected disabled value="">Choose your city...</option>
                    <?php foreach($coverage as $area):?>
                      <option value="<?php echo htmlspecialchars($area['CITY']); ?>"
                      data-postcode="<?php echo htmlspecialchars($area['POSTCODE']); ?>"
                      data-state="<?php echo htmlspecialchars($area['STATE']); ?>">
                      <?php echo htmlspecialchars($area['CITY']); ?>
                      </option>
                    <?php endforeach;?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Postcode <span class="text-danger">*</span></label>
                  <input type="text" name="postcode" id="postcodeInput" class="form-control modal-form" placeholder="Auto-filled" readonly>
                </div>
              </div>

              <div class="mb-3">
                <div class="col-md-6">
                  <label class="form-label">State <span class="text-danger">*</span></label>
                  <input type="text" name="state" id="stateInput" class="form-control modal-form" placeholder="Auto-filled" readonly>
                </div>
              </div>
              <div class="check-row">
                <input type="checkbox" id="setDefaultCheck" name="is_default">
                <label for="setDefaultCheck">Set as default address</label>
              </div>
            </form>
          </div>
          <div class="modal-footer" style="border-top: none;">
            <button type="button" class="btn-cancel btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="addressForm" class="btn-save">Save Address</button>
          </div>
        </div>
      </div>
    </div>

  <!-- footer -->
  <footer>
     <?php include 'include/footer.php'?>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // avatar preview
    document.getElementById('avatarUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // check whether the file is image
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('previewAvatar').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    //password validation
    function validatePassword(){
      const current = document.querySelector('[name="current_password"]').value;
      const newPass = document.querySelector('[name="new_password"]').value;
      const confirm  = document.querySelector('[name="confirm_password"]').value;

      if (!current){
        alert('Please enter your current password.');
        return false;
      }

      return true;
    }


    //checking the password format
    document.querySelector('[name="new_password"]').addEventListener('input',function(){
      const val=this.value;

      toggleRule('rule-length', val.length >= 8);
      toggleRule('rule-upper',  /[A-Z]/.test(val));
      toggleRule('rule-lower',  /[a-z]/.test(val));
      toggleRule('rule-number', /[0-9]/.test(val));
      toggleRule('rule-symbol', /[!@#$%^&*(),.?":{}|<>]/.test(val));
    });

    function toggleRule(id, passed){
      const el = document.getElementById(id);
      if(passed) {
        el.classList.add('passed');
      }else{
        el.classList.remove('passed');
      }
    }


    // address modal
    function openAddAddress() {
      document.getElementById('editAddressModalLabel').textContent = 'Add New Address';
      document.getElementById('addressForm').reset();
      document.getElementById('addressId').value = '';
      const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
      modal.show();
    }

    function openEditAddress(addressId, firstName, lastName, phone, addressLine, city, state, company, isDefault) {
      document.getElementById('editAddressModalLabel').textContent = 'Edit Address';
      document.getElementById('addressId').value = addressId;
      document.querySelector('[name="first_name"]').value = firstName;
      document.querySelector('[name="last_name"]').value  = lastName;
      document.querySelector('[name="phone"]').value      = phone;
      document.querySelector('[name="address_line"]').value = addressLine;
      document.querySelector('[name="company"]').value    = company || '';
      document.getElementById('setDefaultCheck').checked  = isDefault == 1;

      const citySelect = document.getElementById('citySelect');
      citySelect.value = city;
      updatePostcode(citySelect);

      const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
      modal.show();
    }

    function closeAddressModal() {
      document.getElementById('editAddressModal').classList.add('hidden');
    }

    //set address default
    function setDefault(addressId) {
      if (!confirm('Set this as your default address?')) return;

      //sent the data to backend
      fetch('update_default_address.php',{
        method: 'POST',
        headers:{
          'Content-Type' : 'application/x-www-form-urlencoded',
        },
        body: 'address_id=' + addressId
      })

      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const list = document.getElementById('addressList');

          //remove all default status
          list.querySelectorAll('.address-item').forEach(item => {
            item.classList.remove('is-default');
            const badge = item.querySelector('.default-badge');
            if (badge) badge.remove();
          });

          //find the corresponding card

          const card = document.querySelector(`.address-item[data-id="${addressId}"]`);
          if (card) {
            card.classList.add('is-default');
            const topRow = card.querySelector('.address-item-top');
            const badge = document.createElement('span');
            badge.className = 'default-badge';
            badge.textContent = 'Default';
            topRow.appendChild(badge);


            const setBtn = card.querySelector('.addr-btn-set');
            if (setBtn) setBtn.remove();
          }
            location.reload();
          }else{
            alert('Error: ' + data.message);
          }
        })

        .catch(error => {
          console.error('Error: ', error);
          alert('An error occured while updating the address.');
        });
      }
      

      //delete address
    function deleteAddress(addressId, isDefault) {
      if (isDefault) {
        alert('Cannot delete the default address. Please set another address as default first.');
        return;
      }
      if (!confirm('Delete this address?')) {
        return;
      }

      //Submit delete request
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'delete_address.php';

      const idInput = document.createElement('input');
      idInput.type = 'hidden';
      idInput.name = 'address_id';
      idInput.value = addressId;
      form.appendChild(idInput);
      document.body.appendChild(form);
      form.submit();
    }

    //auto-filled postcode and state
    function updatePostcode(select) {
      const selected = select.options[select.selectedIndex];
      document.getElementById('postcodeInput').value = selected.dataset.postcode || '';
      document.getElementById('stateInput').value    = selected.dataset.state    || '';
    }

        //word counter
      function initCounter(textareaId, countId, max) {
        const textarea = document.getElementById(textareaId);
        const counter  = document.getElementById(countId);
        const wrapper  = counter.parentElement;

        counter.textContent = textarea.value.length;

        textarea.addEventListener('input', function () {
            const len = this.value.length;
            counter.textContent = len;
            wrapper.classList.toggle('over', len >= max);
        });
      }

    initCounter('addressLine', 'addr-count', 50);

    </script>

    <?php if(!empty($pw_error) || !empty($pw_success) || !empty($info_error) || !empty($info_success)): ?>
      <script>
          window.addEventListener('DOMContentLoaded', function() {
              new bootstrap.Modal(document.getElementById('editProfileModal')).show();
          });
      </script>
    <?php endif; ?>

    </body>
</html>