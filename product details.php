<?php
include 'include/config.php';
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
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
        --font2-color:#936752;
      }

      body {
        font-family: 'Quicksand', sans-serif;
        background-color: var(--bg-color);
        margin: 0;
        padding: 0;
      }

      .page-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
      }

      /* back section */
      .back-section {
        display: flex;
        align-items: center;
        margin: 30px 0 30px -100px;

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

      /* product image */
      .product-container {
        display: grid;
        grid-template-columns: 1fr 1fr; 
        gap: 100px; 
        margin-bottom: 50px;
        margin-left:-100px;
      }

      
      .main-image img {
        width: 100%; 
        height: auto;
        border-radius: 8px;
        cursor: pointer;
        display: block;
      }

      .thumbnail-list {
        display: flex;
        gap: 15px;
        margin-top: 15px;
      }

      .thumbnail-list img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 6px;
      }

      .description {
        margin-top: 25px;
        font-size: 15px;
        line-height: 1.6;
        color: var(--font-color);
      }

      /* collapsible info */
      .accordion {
        margin-top: 20px;
        border-top: 1px solid var(--font2-color);
      }

      .accordion-item {
        border-bottom: 1px solid var(--font2-color);
        background-color: transparent;
        border:none;
        border-bottom: 1px solid var(--font2-color);
      }

      .accordion-header {
        padding: 15px 0;
        display: flex;
        justify-content: space-between;
        font-weight: bold;
        cursor: pointer;
        color: var(--font2-color);
      }

      .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        font-size: 14px;
        color: #666;
      }

      .accordion-item.active .accordion-content {
        max-height: 150px;
        padding-bottom: 15px;
      }

      /* product details */
      .product-title {
        font-size: 28px;
        color: var(--font-color);
        margin-bottom: 10px;
        font-weight: bold;
      }

      .wishlist-btn{
        background:none;
        border:none;
        padding:5px;
        cursor:pointer;
        font-size:24px;
        color: var(--font2-color);
        transition: transform 0.2s ease, color 0.2s ease;
        display: flex;
        align-items: center;
      }

      .wishlist-btn.active i::before {
        content: "\f415"; /*bootstrap icon heart-fill encoding*/
        color: var(--main-color);
      }

      .category-tag {
        display: inline-block;
        border: 1px solid var(--search-border-color);
        border-radius: 20px;
        font-size: 12px;
        background-color: var(--secondary-color);
        padding: 4px 12px;
        margin-bottom: 15px;
      }

      .category-tag a {
        text-decoration: none;
        color: var(--font2-color);
      }

      .price {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 25px;
        color: var(--font2-color);
      }

      /* selector group */
      .selector-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        color: var(--font2-color);
      }

      .quantity-input {
        display: flex;
        border: 1px solid var(--font2-color);
        border-radius: 4px;
      }

      .quantity-input button {
        background: none;
        border: none;
        padding: 5px 12px;
        cursor: pointer;
      }

      .quantity-input input {
        width: 50px;
        text-align: center;
        border: none;
        border-left: 1px solid var(--font2-color);
        border-right: 1px solid var(--font2-color);
        background: none;
        color: var(--font2-color);
      }

      select {
        width: 160px;
        padding: 6px;
        border: 1px solid var(--font2-color);
        color: var(--font2-color);
        border-radius: 4px;
      }

      #decoration{
        width: 160px;
        padding: 6px;
        border: 1px solid var(--font2-color);
        color: var(--font2-color);
        border-radius: 4px;
      }


      /* add ons */
      .add-ons {
        margin-top: 40px;
      }

      .add-ons h3 {
        font-size: 18px;
        color: var(--font2-color);
        margin-bottom: 10px;
      }

      hr {
        border: 0;
        border-top: 3px dashed var(--font-color);
        margin-bottom: 20px;
      }

      .addon-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
      }

      .addon-item img {
        width: 80px;
        height: 80px;
        border-radius: 6px;
        object-fit: cover;
      }

      .addon-info {
        flex-grow: 1;
      }

      .addon-info p {
        margin: 0;
        font-weight: bold;
        font-size: 14px;
        color:var(--font2-color);
      }

      .addon-info span{
        font-size:13px;
        color:var(--font2-color);
      }

      textarea {
        width: 100%;
        height: 80px;
        margin-top: 10px;
        padding: 10px;
        border: 1px solid var(--font2-color);
        border-radius: 4px;
        resize: none;
      }

      /* reviews section */
      .reviews-section {
        margin-top: 60px;
        margin-left:-100px;
        padding-top: 40px;
        border-top: 2px solid var(--secondary-color);
      }

      .stars {
        color: #f1c40f; 
        font-size: 18px;
        margin: 10px 0;
      }

      .review-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
      }

      .user-info {
        display: flex;
        gap: 12px;
        align-items: center;
      }

      .avatar img {
        border-radius: 50%;
        width: 45px;
        height: 45px;
      }

      /* image zoom */
      .lightbox{
        display:none;
        position:fixed;
        z-index:9999;
        left:0;
        top:0;
        width:100%;
        height:100%;
        background-color:rgba(0,0,0,0.9);
        cursor:zoom-out;
        align-items:center;
        justify-content:center;
      }

      .lightbox-content{
        max-width:90%;
        max-height:85%;
        border-radius:4px;
        box-shadow:0 0 20px rgba(0,0,0,0.5);
        animation:zoomIn 0.3s ease;
      }

      .close-btn{
        position:absolute;
        top:30px;
        right:50px;
        color:white;
        font-size:50px;
        font-weight:bold;
        cursor:pointer;
      }

      /* zoom animation */
      @keyframes zoomIn {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
      }
    </style>
</head>

<body>
  <?php include 'include/header.php'; ?>

  <div class="page-wrapper">
    
    <div class="back-section">
      <a href="homepage.php" class="back-link">
        <i class="bi bi-chevron-left"></i>Back
      </a>
    </div>

    <div class="product-container">
      
    <!-- product image -->
      <div class="product-gallery">
        <div class="main-image">
          <img src="image/product/cheesecake/​Thai Milk Tea & Salted Cheese Basque Cheesecake/thai cheesecake1.jpg" alt="Main Product">
        </div>
        <div class="thumbnail-list">
          <img src="image/product/cheesecake/​Thai Milk Tea & Salted Cheese Basque Cheesecake/thai cheesecake2.jpg" alt="Thumb">
          <img src="image/product/cheesecake/​Thai Milk Tea & Salted Cheese Basque Cheesecake/thai cheesecake3.jpg" alt="Thumb">
        </div>
        
        <p class="description">
          Indulge in our delectable cheese cakes, crafted with the finest ingredients and a touch of love. 
          Each bite offers a creamy, unforgettable experience.
        </p>

        <!-- product info -->
        <div class="accordion">
          <div class="accordion-item">
            <div class="accordion-header">Ingredients <span>+</span></div>
            <div class="accordion-content">
              <p>Whipped cream, Thai black tea, cream cheese, cocoa powder.</p>
            </div>
          </div>
          <div class="accordion-item">
            <div class="accordion-header">Allergens <span>+</span></div>
            <div class="accordion-content">
              <p>Dairy, eggs, caffeine.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="product-details">
        <div class="header-section">
          <div class="title-wrapper d-flex align-items-center justify-content-between">
            <h1 class="product-title">Thai Milk Tea & Salted Cheese Basque Cheesecake</h1>
            <button class="wishlist-btn">
            <i class="bi bi-heart"></i> </button>
          </div>
            <span class="category-tag"><a href="#">Cheese Cakes</a></span>
            <div class="price">RM 89.00</div>
        </div>

        <div class="selectors">
          <div class="selector-group">
            <label>Quantity:</label>
            <div class="quantity-input">
              <button>-</button>
              <input type="text" value="1">
              <button>+</button>
            </div>
          </div>

          <div class="selector-group">
            <label>Select Size</label>
            <select>
              <option>Select Size</option>
              <option value="6">6 inch</option>
              <option value ="8">8 inch</option>
            </select>
          </div>

          <div class="selector-group" style="display: block;">

              <label>Cake Writing</label>
            
            <textarea placeholder="Write your message here..."></textarea>
          </div>

          <div class="selector-group">
             <label>Decoration</label>
            <input id="decoration" type="text" placeholder="Enter 2 characters">
          </div>

        </div>

        <!-- add on -->
        <div class="add-ons">
          <h3>Add On</h3>
          <hr>
          <div class="addon-item">
            <input type="checkbox">
            <img src="image/product/add on/birthday candles.jpg" alt="candles">
            <div class="addon-info">
              <p>Birthday Candles</p>
              <span>RM 3.03</span>
            </div>
            <div class="quantity-input">
              <button>-</button>
              <input type="text" value="1">
              <button>+</button>
            </div>
          </div>

          <div class="addon-item">
            <input type="checkbox">
            <img src="image/product/add on/birthday balloons.jpg" alt="balloons">
            <div class="addon-info">
              <p>Birthday Balloons</p>
              <span>RM 3.03</span>
            </div>
            <div class="quantity-input">
              <button>-</button>
              <input type="text" value="1">
              <button>+</button>
            </div>
          </div>

          <div class="addon-item" style="display: block;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <input type="checkbox">
                <div class="addon-info">
                  <p>Message on cards</p>
                  <span>RM 1.50</span>
                </div>
            </div>
            <textarea placeholder="Write your message here..."></textarea>
          </div>
        </div>
      </div>
    </div>

      <!-- reviews -->
    <div class="reviews-section">
      <h3>Customer Reviews</h3>
      <div class="review-header d-flex align-items-center gap-3">
        <div class="stars">
          <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
        </div>
        <span class="review-count text-muted">Based on 45 reviews</span>
        <select style="margin-left: auto; width: auto;">
            <option>Highest Ratings</option>
            <option>Latest Ratings</option>
        </select>
      </div>

      <div class="review-card">
        <div class="user-info">
          <div class="avatar">
            <img src="image/user image/user_default.jpg" alt="User">
          </div>
          <div>
            <p class="username mb-0" style="font-weight: bold;">Jenny</p>
            <small class="text-muted">3/10/2025</small>
          </div>
        </div>
        <div class="stars small">
          <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
        </div>
        <p class="reviews-content">Very Nice Taste! Highly recommended.</p>
        <img src="image/product/cheesecake/​Thai Milk Tea & Salted Cheese Basque Cheesecake/thai cheesecake1.jpg" alt="review" style="width: 80px; border-radius: 4px;">
      </div>
    </div>
  </div>

  <div id="imageLightbox" class="lightbox" onclick="closeLightbox()">
    <span class="close-btn"> &times;</span>
    <img class="lightbox-content" id="expandedImg">
  </div>

  <?php include 'include/footer.php'?>

  <script>
    // info collapse
  document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', () => {
      const item = header.parentElement;

      //convert to current clicking status
      item.classList.toggle('active');

      //changing symbol "+" to "-"
      const icon = header.querySelector('span');
      if (item.classList.contains('active')){
        icon.textContent = '-';
      } else{
        icon.textContent = '+';
      }
    })
  })

  //zooom image

  document.addEventListener('DOMContentLoaded',function(){
    const images = document.querySelectorAll('.main-image img, .thumbnail-list img, .review-card img');

    images.forEach(img => {
      img.style.cursor = 'zoom-in';
      img.onclick = function(){
        openLightbox(this.src);
      };
    });
  });

  function openLightbox(src){
    const lightbox = document.getElementById('imageLightbox');
    const expandedImg = document.getElementById('expandedImg');

    lightbox.style.display = 'flex';
    expandedImg.src = src;
    expandedImg.src = src;
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox(){
    const lightbox = document.getElementById('imageLightbox');
    lightbox.style.display = 'none';
    document.body.style.overflow = 'auto';
  }

  //change wishlist button status
  document.querySelector('.wishlist-btn').addEventListener('click',function(){
    const icon = this.querySelector('i');

    //switch class name: solid to hollow
    icon.classList.toggle('bi-heart');
    icon.classList.toggle('bi-heart-fill');

    //change color
    this.classList.toggle('active');

    if(icon.classList.contains('bi-heart-fill')){
      console.log("Added to wishlist");
    }else{
      console.log("Remove from wishlist");
    }
  });
</script>
</body>


</html>