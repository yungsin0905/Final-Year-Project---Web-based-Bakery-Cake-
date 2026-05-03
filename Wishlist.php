<?php
include 'include/config.php';
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">

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
        margin: 30px 0 30px 10px;

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

      .container{
       padding-left:30px;
       padding-right:30px;
       min-height:300vh;
      }

      .wishlist-section{
        padding:15px;
        margin-bottom:40px;
        border-bottom: 2px solid rgba(147,103,82,0.3);
        width:100%;
      }

      .wishlist-header{
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        margin-bottom:20px;
      }

      .wishlist-header h2{
        margin:0;
        font-size:24px;
        color:#936752;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom:20px;
        border-bottom:20px;
        padding-bottom:10px;
        font-weight: bold;
      }

      .btn-text{
        text-decoration:underline;
        color:var(--font2-color);
        margin-left: 15px;
        font-size:14px;
      }

      .btn-text:hover{
        color:var(--font-color);
      }

      .wishlist-card{
        display:flex;
        align-items:center;
        gap:20px;
        position:relative;
        width:100%;
      }

      .header-actions{
        display:flex;
        gap:20px;
        
      }

      .wishlist-info{
        flex-grow:1;
      }

      .product-thumb{
        width:120px;
        height:120px;
        object-fit: cover;
        border-radius:4px;
      }

      .product-name{
        margin:0 0 8px 0;
        font-size:18px;
        color:var(--font2-color);
        font-weight:bold
      }

      .price{
        color:var(--font2-color);
        font-size:16px;
        margin:5px 0;
      }

      .stars{
        color:var(--rating-color);
      }

      .card-checkbox{
        margin-left:auto;
        width:20px;
        height:20px;
        accent-color:var(--font2-color);
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


    <section class="wishlist-section">
      <div class="wishlist-header">
        <h2>Wishlist</h2>
        <div class="header-actions">
        <a href="#" class="btn-text">Add to Cart</a>
        <a href="#" class="btn-text">Delete</a>
      </div>
      </div>
      

      <div class="wishlist-card">
        <img class="product-thumb"src="image/product/cheesecake/​Thai Milk Tea & Salted Cheese Basque Cheesecake/thai cheesecake1.jpg">
        <div class="wishlist-info">
          <h3 class="product-name"​>Thai Milk Tea & Salted Cheese Basque Cheesecake</h3>
          <p class="price">RM 89.00</p>
          <div class="stars">
            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
          </div>
        </div>
        
        <input type="checkbox" class="card-checkbox">
      </div>
    </section>

    </div>
 
  <?php include 'include/footer.php'?>
</body>
</html>