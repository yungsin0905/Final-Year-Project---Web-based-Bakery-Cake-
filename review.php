<?php
session_start();
require_once 'includes/config.php';

$order = [
    'ORDER_NO' => 'ORD-20260406-001',
    'PRODUCT_NAME' => 'Thai Milk Tea Basque Cheesecake'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">
<style>
:root {
    --font-color: rgb(101, 54, 31);
    --secondary-color: #fff6e6;
    --bg-color: #fff2ed;
    --primary-pink: #ffb6c1;
    --btn-color-hover: #ff99aa;
}


body {
    background-color: var(--bg-color);
    margin: 0;
    color: var(--font-color);
}

.main-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 60px 20px;
    min-height: 80vh; 
}


form {
    background: #ffffff;
    max-width: 550px;
    width: 100%;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(187, 162, 153, 0.15); 
}


h2 {
    color: var(--font-color);
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    font-size: 28px;
}


.order-review {
    margin: 25px 0;
    line-height: 1.6;
}

.label {
    font-weight: bold;
    color: var(--font-color);
}


.star-rating {
    margin: 5px 0 15px 0;
}

.star {
    font-size: 35px;
    color: #ddd; 
    cursor: pointer;
    transition: color 0.2s ease-in-out;
}

.star.active, .star:hover {
    color: #ffca08; 
}


label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: var(--font-color);
}

textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--secondary-color);
    border-radius: 12px;
    resize: vertical;
    margin-top: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

textarea:focus {
    outline: none;
    border-color: #ddd;
}


input[type="file"] {
    margin-top: 10px;
    font-size: 14px;
}


.btn-submit {
    display: block;
    width: 180px;
    margin: 30px auto 0 auto; /* 居中按钮 */
    padding: 12px;
    background-color: var(--primary-pink);
    border: none;
    border-radius: 25px;
    color: var(--font-color);
    font-weight: bold;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-submit:hover {
    background-color: var(--btn-color-hover); 
    transform: translateY(-2px);
}


form p {
    margin-bottom: 15px;
}


</style>
</head>
<body>
    <?php include_once 'header.php'; ?>
   <div class="main-container">
    <form method="post" enctype="multipart/form-data">
    <h2>Customer Review</h2>

    <p>You have products to review!</p>
     <div class="order-review">
     <p><span class="label">Order No: </span><?php echo $order['ORDER_NO'];?>-Completed</p>
        
     <p><span class="label">Product: </span><?php echo $order['PRODUCT_NAME'];?></p>
         
     <p><label>Rating:</label></p>
        <div class="star-rating">
            <span class="star" value="1">★</span>
            <span class="star" value="2">★</span>
            <span class="star" value="3">★</span>
            <span class="star" value="4">★</span>
            <span class="star" value="5">★</span>
        </div>
        <input type="hidden" name="rating" id="rating-value">
     <p><label>Comment (optional):</label>
        <textarea placeholder="Give comments for your products"rows="4" cols="50"></textarea></p>
    <p><label>Review image (optional):</label>
         <input type="file" name="photo"></p>
    </div>
    <p><input type="submit" class="btn-submit"/></p>
    </form>
    </div>
<script>

</script>

</body>
<?php include_once 'footer.php'; ?>
</html>