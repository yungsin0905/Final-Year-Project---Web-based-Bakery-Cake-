<?php
ob_start(); 
include 'include/config.php';
session_start();

file_put_contents('debug.log', print_r($_POST, true));

// 1. check login
if(!isset($_SESSION['CUSTOMER_ID'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit();
}

$customer_id = $_SESSION['CUSTOMER_ID'];

// 2. retrieve product id
if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $variant_id = intval($_POST['variant_id']);
    $qty = intval($_POST['quantity'] ?? 1);
    
    // convert in to string
    $cake_writing = !empty($_POST['cake_writing']) ? mysqli_real_escape_string($conn, $_POST['cake_writing']) : "";
    $card_text = !empty($_POST['card_message']) ? mysqli_real_escape_string($conn, $_POST['card_message']) : "";

    // handle null value
    $cake_val = $cake_writing ? "'$cake_writing'" : "NULL";
    $card_val = $card_text ? "'$card_text'" : "NULL";

    // C. process addon
    $selected_addons = $_POST['selected_addons'] ?? [];
    $addon_qtys = $_POST['addon_qty'] ?? [];
    
    if (isset($_POST['buy_now'])) {
    // buy now: store in session, skip cart insert
        $_SESSION['buy_now'] = [
            'product_id'      => $product_id,
            'variant_id'      => $variant_id,
            'quantity'        => $qty,
            'cake_writing'    => $cake_writing,
            'card_text'       => $card_text,
            'selected_addons' => $selected_addons,
            'addon_qtys'      => $addon_qtys,
        ];

        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'redirect' => 'payment.php']);
        exit();
    }

    // A. create a new data
    $cart_res = mysqli_query($conn, "SELECT CART_ID FROM cart WHERE CUSTOMER_ID = $customer_id LIMIT 1");
    if (mysqli_num_rows($cart_res) > 0) {
        $cart_id = mysqli_fetch_assoc($cart_res)['CART_ID'];
    } else {
        mysqli_query($conn, "INSERT INTO cart (CUSTOMER_ID, CREATED_AT) VALUES ($customer_id, NOW())");
        $cart_id = mysqli_insert_id($conn);
    }

    // B. insert into cart item
    $insert_item_sql = "INSERT INTO cart_item (CART_ID, VARIANT_ID, PRODUCT_ID, QUANTITY, CAKE_WRITING,CREATED_AT) 
                        VALUES ($cart_id, $variant_id, $product_id, $qty, $cake_val, NOW())";


    if (mysqli_query($conn, $insert_item_sql)) {
        $cart_item_id = mysqli_insert_id($conn);

        foreach ($selected_addons as $addon_id) {
            $addon_id = intval($addon_id);
            $aqty = isset($addon_qtys[$addon_id]) ? intval($addon_qtys[$addon_id]) : 1;
            $addon_sql = "INSERT INTO cart_item_addon (CART_ITEM_ID, ADD_ON_ID, QUANTITY,CARD_TEXT,CREATED_AT) VALUES ($cart_item_id, $addon_id, $aqty, $card_val, NOW())";
            mysqli_query($conn, $addon_sql); 
        }

        // D. response success
        ob_end_clean();
        header('Content-Type: application/json');
        if (isset($_POST['buy_now'])) {
            echo json_encode(['status' => 'success', 'redirect' => 'payment.php']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Added to cart successfully!']);
        }
        exit();

    } else {
        // SQL execute failure
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . mysqli_error($conn)]);
        exit();
    }
} else {
    // 3. if haven't receive product id
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request: Missing product ID']);
    exit();
}
