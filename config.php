<?php
//error report for debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Cakeology"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} 

// ── Auto-assign tier vouchers ─────────────────────────────────────
function assignTierVouchers($conn, $customer_id, $new_tier_id) {
    
    // delete vouchers from old tier if not used
    mysqli_query($conn, "
        DELETE cv FROM customer_voucher cv
        INNER JOIN voucher v ON cv.VOUCHER_ID = v.VOUCHER_ID
        WHERE cv.CUSTOMER_ID = $customer_id
          AND v.TIER_ID != $new_tier_id
          AND cv.USED_COUNT = 0
    ");

    // sent new tier vouchers
    $vouchers = mysqli_query($conn, "
        SELECT VOUCHER_ID FROM voucher
        WHERE TIER_ID = $new_tier_id
          AND VOUCHER_STATUS = 'Active'
          AND IS_DELETED = 0
    ");

    while ($v = mysqli_fetch_assoc($vouchers)) {
        $voucher_id = $v['VOUCHER_ID'];

        $exists = mysqli_query($conn, "
            SELECT CUSTOMER_VOUCHER_ID FROM customer_voucher
            WHERE CUSTOMER_ID = $customer_id AND VOUCHER_ID = $voucher_id
        ");

        if (mysqli_num_rows($exists) === 0) {
            mysqli_query($conn, "
                INSERT INTO customer_voucher (CUSTOMER_ID, VOUCHER_ID, USED_COUNT, CLAIMED_AT, EXPIRY_DATE)
                VALUES ($customer_id, $voucher_id, 0, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH))
            ");
        }
    }
}

?>
