<?php
include 'include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

  //retrieve product information
  $query = "SELECT p.*, c.CATEGORY_NAME ,
  (SELECT AVG(RATING) FROM review WHERE PRODUCT_ID = p.PRODUCT_ID AND REVIEW_STATUS = 'Unhide') as CALCULATED_RATING,
  (SELECT COUNT(*) FROM review WHERE PRODUCT_ID = p.PRODUCT_ID AND REVIEW_STATUS = 'Unhide') as TOTAL_REVIEWS
  FROM product p
  LEFT JOIN category c ON p.CATEGORY_ID = c.CATEGORY_ID
  WHERE p.product_id = $product_id
  AND p.IS_DELETED = 0 LIMIT 1";
              

  $result = mysqli_query($conn, $query);
  $product = mysqli_fetch_assoc($result);

  if(!$product){
    header("Location: homepage.php");
    exit();
  }

  $display_rating = $product['CALCULATED_RATING'] ? round($product['CALCULATED_RATING'], 1) : 0;
  $total_reviews = $product['TOTAL_REVIEWS'];

  //retrieve add on
  $add_query = "SELECT * FROM add_on WHERE IS_DELETED = 0";
  $add_result = mysqli_query($conn, $add_query);
  $all_addons = mysqli_fetch_all($add_result, MYSQLI_ASSOC);

  //retrieve product image
  $img_query = "SELECT IMAGE_PATH FROM product_images WHERE PRODUCT_ID = $product_id AND IS_DELETED = 0";
  $img_result = mysqli_query($conn, $img_query);
  $images = mysqli_fetch_all($img_result, MYSQLI_ASSOC);

  //retrieve product variant
  $variant_query = "SELECT * FROM product_variant WHERE PRODUCT_ID = $product_id AND VARIANT_STATUS = 'Active' AND IS_DELETED = 0
  ORDER BY VARIANT_SIZE ASC";
  $variant_result = mysqli_query($conn, $variant_query);
  $variants = mysqli_fetch_all($variant_result, MYSQLI_ASSOC);
                  
  //setup price initialization to variant price
  $initial_price = count($variants) > 0 ? (float)$variants[0]['VARIANT_PRICE'] : 0.0;

  //sort review
  $sort = isset($_GET['sort']) ? $_GET['sort'] : 'high_ratings';
  $order_by = "r.CREATED_AT DESC"; 
  if ($sort === 'new_ratings') {
    $order_by = "r.CREATED_AT DESC";
  } else if ($sort === 'high_ratings') {
    $order_by = "r.RATING DESC";
  } 


  //retrieve review
  $review_query = "SELECT r.*, u.CUSTOMER_NAME, w.REPLY_TEXT,w.CREATED_AT AS REPLY_DATE
  FROM review r
  JOIN customer u ON r.CUSTOMER_ID = u.CUSTOMER_ID
  LEFT JOIN review_reply w ON w.REVIEW_ID = r.REVIEW_ID  AND (w.IS_DELETED = 0 OR w.IS_DELETED IS NULL)
  WHERE r.PRODUCT_ID = $product_id
  AND r.REVIEW_STATUS  = 'Unhide'
  ORDER BY $order_by";

  $reviews_result = mysqli_query($conn, $review_query);
  $reviews = mysqli_fetch_all($reviews_result, MYSQLI_ASSOC);
  $review_count = mysqli_num_rows($reviews_result);

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
        border-top: 2px solid var(--font2-color);
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

      #cardMessageInput:required:invalid {
      border: 1px solid #ff4d4d ;
      }

      /* checkout summary */
      .checkout-summary {
      border-radius: 8px;
      }

      .subtotal-section span{
        font-size:15px;
      }

      .button-area button{
        border-radius: 25px;
        padding:13px;
        font-size:13px;
        border:none;
        width: 100%; 
        cursor: pointer;
        transition: 0.5s;
      }

      .btn-add{

        background-color:var(--main-color);
      }

      .btn-pay{
        background-color:var(--search-border-color);
      }

      .btn-add:hover,.btn-pay:hover{
        opacity:0.5;
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

    <form id="productForm" action="add_to_cart.php" method="POST">
      <input type="hidden" name="product_id" value="<?php echo intval($product_id); ?>">
      <div class="product-container">
        
        <!-- product image -->
        <div class="product-gallery">
          <div class="main-image">
            <img id="mainImg" src="<?php echo htmlspecialchars($product['COVER_IMAGE']); ?>" alt="Main Product">
          </div>
          <div class="thumbnail-list">
            <?php foreach ($images as $img):?>
            <img src="<?php echo htmlspecialchars($img['IMAGE_PATH']); ?>" onclick="document.getElementById('mainImg').src=this.src" alt="Thumb">
            <?php endforeach;?>
          </div>
          
          <p class="description">
            <?php echo htmlspecialchars($product['PRODUCT_DES']);?>
          </p>

          <!-- product info -->
          <div class="accordion">
            <div class="accordion-item">
              <div class="accordion-header">Ingredients <span>+</span></div>
              <div class="accordion-content">
                <p><?php echo htmlspecialchars($product['INGREDIENTS']);?></p>
              </div>
            </div>
            <div class="accordion-item">
              <div class="accordion-header">Allergens <span>+</span></div>
              <div class="accordion-content">
                <p><?php echo htmlspecialchars($product['ALLERGEN']);?></p>
              </div>
            </div>
          </div>
        </div>

          <div class="product-details">
            <div class="header-section">
              <div class="title-wrapper d-flex align-items-center justify-content-between">
                <h1 class="product-title"><?php echo htmlspecialchars($product['PRODUCT_NAME']);?></h1>
                <a class="wishlist-btn" data-product-id="<?php echo $product_id;?>">
                <i class="bi bi-heart"></i></a>
              </div>
              <span class="category-tag"><a href="product catalogue.php?id=<?php echo $product['CATEGORY_ID'];?>"><?php echo htmlspecialchars($product['CATEGORY_NAME']);?></a></span>
              <div class="price">RM <span id="displayPrice"><?php echo number_format($initial_price, 2); ?></span></div>
            </div>
            
            
            <div class="selectors">
              <div class="selector-group">

                <!-- quantity -->
                <label>Quantity:</label>
                <div class="quantity-input">
                  <button type="button" onclick="changeQty(this, -1)">-</button>
                  <input type="text" name="quantity" value="1" max="<?php echo $product['STOCK_QTY'];?>" readonly>
                  <button type="button" onclick="changeQty(this, 1)">+</button>
                </div>
              </div>

              <!-- select size -->
              <div class="selector-group">
                <label>Select Size</label>
                <select id="variantSelect" name="variant_id" onchange="updatePrice()">
                  <?php foreach ($variants as $v): ?>
                    <option value="<?php echo $v['VARIANT_ID'];?>" data-price="<?php echo $v['VARIANT_PRICE'];?>">
                      <?php echo htmlspecialchars($v['VARIANT_SIZE']);?> inch
                    </option>
                  <?php endforeach;?>
                </select>
              </div>

              <!-- cake writing -->
              <?php if($product['ALLOW_WRITING'] == 1):?>
                <div class="selector-group" style="display: block;">
                  <label>Cake Writing</label>
                  <textarea name = "cake_writing" placeholder="Write your message here..."></textarea>
                </div>
              <?php endif;?>

            <!-- add on -->
            <div class="add-ons">
              <h3>Add On</h3>
              <hr>
              <?php if (count($all_addons) > 0): ?>
                <?php foreach ($all_addons as $addon):?>
                  <div class="addon-item">
                    <input type="checkbox" name ="selected_addons[]" 
                    value="<?php echo $addon['ADD_ON_ID'];?>" 
                    data-price="<?php echo $addon['ADD_ON_PRICE'];?>" 
                    onclick="calculationTotal(); "> 
                    
                    <?php if(!empty($addon['ADD_ON_IMAGE'])):?>
                    
                      <img src="<?php echo htmlspecialchars($addon['ADD_ON_IMAGE']);?>" alt="<?php echo htmlspecialchars($addon['ADD_ON_NAME']);?>">
                    <?php else: ?>
                      <div style="width:80px; height:80px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:10px; color:#999;">No Image</div>
                    <?php endif;?>

                    <div class="addon-info">
                      <p><?php echo htmlspecialchars($addon['ADD_ON_NAME']);?></p>
                      <span>RM <?php echo number_format($addon['ADD_ON_PRICE'],2);?></span>
                    </div>

                    <div class="quantity-input">
                      <button type="button" onclick="changeQty(this, -1); calculationTotal()">-</button>
                      <input type="text" name="addon_qty[<?php echo $addon['ADD_ON_ID'];?>]"value="1" readonly>
                      <button type="button" onclick="changeQty(this, 1); calculationTotal()">+</button>
                    </div>
                  </div>

                  <?php if($addon['ADD_ON_ID'] == 3):?>
                    <div id="cardMessageArea" style="margin-left: 30px; margin-top: 10px;">
                      <textarea id="cardMessageInput" name="card_message" placeholder="Write your greeting message here..." style="width: 100%; height: 60px; border: 1px solid var(--font2-color); border-radius: 4px; padding: 10px;"></textarea>
                    </div>
                  <?php endif;?>
                <?php endforeach;?>
              <?php endif;?>

            </div>

             <!-- checkout summary section -->
       <div class="checkout-summary mt-4 p-3" >
          <div class="subtotal-section d-flex justify-content-between mb-2">
              <span>Product Price (x<span id="summaryQty">1</span>)</span>
              <span>RM <span id="summaryProductPrice"><?php echo number_format($initial_price, 2); ?></span></span>
          </div>
          <div class="subtotal-section d-flex justify-content-between mb-2">
              <span>Options Price (Add-ons)</span>
              <span>RM <span id="summaryOptionPrice">0.00</span></span>
          </div>
          <hr>
          <div class="total-section d-flex justify-content-between align-items-center mb-3">
              <strong style="font-size: 1.2rem;">Total</strong>
              <strong style="font-size: 1.2rem; color: var(--font-color);">RM <span id="summaryTotal"><?php echo number_format($initial_price, 2); ?></span></strong>
          </div>
          
          <div class="row g-2">
            <div class="button-area col-6">
              <button type="button" id="addToCartBtn" name="add_to_cart" class=" btn-add">ADD TO CART</button>
            </div>
            <div class="button-area col-6">
              <button type="button" id="buyNowBtn" name="buy_now" class=" btn-pay">CHECK OUT</button>
            </div>
            </div>
          </div>
        </div>
      </div>
      </div>  
    </form>

    <!-- reviews -->
    <div class="reviews-section">
      <h3>Customer Reviews</h3>
        <div class="review-header d-flex align-items-center gap-3">
          <div class="stars">
            <?php for ($i=1; $i<=5; $i++) {
              echo ($i <= round($display_rating)) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
            }
            ?>
          </div>
          <span class="review-count text-muted">Based on <?php echo $review_count;?> reviews</span>
          
          <select onchange="location.href='?id=<?php echo $product_id; ?>&sort='+this.value" style="margin-left: auto; width: auto;">
            <option value="high_ratings" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'high_ratings') ? 'selected' : ''; ?>>Highest Ratings</option>
            <option value = "new_ratings" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'new_ratings') ? 'selected' : ''; ?>>Latest Ratings</option>
          </select>
        </div>

        <?php if ($review_count > 0) : ?>
          <?php foreach($reviews as $r): ?>
            <div class="review-card">
              <div class="user-info">
                <div class="avatar">
                  <img src="image/user image/user_default.jpg" alt="User">
                </div>
                <div>
                  <p class="username mb-0" style="font-weight: bold;"><?php echo htmlspecialchars($r['CUSTOMER_NAME']);?></p>
                  <small class="text-muted"><?php echo date('d-m-Y', strtotime($r['CREATED_AT']));?></small>
                </div>
              </div>
              <div class="stars small">
                <?php for ($i=1; $i<=5; $i++){
                  echo ($i <= $r['RATING']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                }
                ?>
              </div>

              <p class="reviews-content"><?php echo htmlspecialchars($r['COMMENTS']);?></p>
              <?php if ($r['REVIEW_IMAGE']):?>
                <img src="<?php echo htmlspecialchars ($r['REVIEW_IMAGE']);?>" alt="review" style ="width: 80px; border-radius: 4px; cursor:pointer;" onclick="openLightbox(this.src)">
              <?php endif;?>

              <?php if(!empty($r['REPLY_TEXT'])) :?>
                <div class="admin-reply mt-3 p-3" style="background-color: #f8f9fa; border-left: 4px solid var(--main-color); border-radius: 4px;">
                  <p class="mb-1" style="font-weight: bold; color: var(--font-color);">
                    <i class="bi bi-reply-fill"></i> Admin Response:
                  </p>
                  <p class="mb-0" style="font-size: 14px; color: #555;">
                    <?php echo htmlspecialchars($r['REPLY_TEXT']); ?>
                  </p>
                  <small class="text-muted" style="font-size: 11px;">
                    Replied on: <?php echo date('d-m-Y', strtotime($r['REPLY_DATE'])); ?>
                  </small>
                </div>
              <?php endif; ?> 
            </div> 
          <?php endforeach;?>
        <?php else:?>
          <p class="text-muted">No reviews yet for this product.</p>
        <?php endif;?>
    </div>

    <!-- lightbox -->
    <div id="imageLightbox" class="lightbox" onclick="closeLightbox()">
      <span class="close-btn"> &times;</span>
      <img class="lightbox-content" id="expandedImg">
    </div>
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
    });
  });

  //zooom image

  function openLightbox(src){
    if(!src) return;
    const lightbox = document.getElementById('imageLightbox');
    const expandedImg = document.getElementById('expandedImg');

    lightbox.style.display = 'flex';
    expandedImg.src = src;
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox(){
    const lightbox = document.getElementById('imageLightbox');
    lightbox.style.display = 'none';
    document.body.style.overflow = 'auto';
  }

    document.addEventListener('DOMContentLoaded',function(){
    const images = document.querySelectorAll('.main-image img, .thumbnail-list img, .review-card img');

    images.forEach(img => {
      img.style.cursor = 'zoom-in';
      img.onclick = function(){
        openLightbox(this.src);
      };
    });

    calculationTotal();
  });

  //add to wishlist process
  document.querySelector('.wishlist-btn').addEventListener('click',function(e) {
    e.preventDefault();

    const btn = this;
    const productId = btn.getAttribute('data-product-id');
    const icon = btn.querySelector('i');

    //sent the AJAX request
    fetch('add_to_wishlist.php', {
      method: 'POST',
      headers: {
      'X-Requested-With': 'XMLHttpRequest' ,
      'Content-Type': 'application/x-www-form-urlencoded' 
      },
      body:'product_id=' + productId
    })

    .then(response => response.json())
    .then(data => {
      if(data.status === 'success') {
        icon.classList.toggle('bi-heart');
        icon.classList.toggle('bi-heart-fill');
        btn.classList.toggle('active');
        alert(data.message);
      }else if (data.status == 'error') {
        alert(data.message);
        if(data.message === 'Please login first') {
          window.location.href = 'login.php';
        }
      }
    })
    .catch(error => console.error('Error:' , error));
  });

  //add to cart process
  document.getElementById('addToCartBtn').addEventListener('click', function() {
    //check whether if selected the variant
    const variantId = document.getElementById('variantSelect').value;
    if (!variantId) {
      alert("Please select a size first!");
      return;
    }

    const form = document.getElementById('productForm');
    const formData = new FormData(form);

    fetch('add_to_cart.php', {
      method: 'POST',
      body: formData
    })

    .then(response => {
        return response.json();
    })

    .then(data => {
      if (data.status === 'success') {
          if (data.redirect){
            window.location.href = data.redirect;
          }else {
            alert("✨ " + data.message);
          }
        
      }else {
        //haven't login and redirect user to login
        alert(data.message);
            if (data.message === 'Please login first') {
                window.location.href = 'login.php';
            }
        }
  })
  .catch(error => {
          console.error('Error:', error);
          alert("Ops! error details:" + error.message);
      });
  });

  //check out process
  document.getElementById('buyNowBtn').addEventListener('click', function() {
    const variantId = document.getElementById('variantSelect').value;
    if (!variantId){
      alert('Please select a size first!');
      return;
    }
  
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    formData.append('buy_now', '1');

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })

    .then(response => response.json()) 
    .then(data => {
      if (data.status === 'success') {
        if (data.redirect) {
          window.location.href = data.redirect;
        } else {
          window.location.href = 'payment.php'; 
        }
      } else {
        alert(data.message);
        if (data.message === 'Please login first') {
          window.location.href = 'login.php';
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert("Ops! error details: " + error.message);
    });
  });



  //update price
  function updatePrice(){
    calculationTotal();
  }

  //total calculation
  function calculationTotal(){
    //get the basic unit price
    const variantSelect = document.getElementById('variantSelect');
    if (!variantSelect) return;

    //get the variant price
    const selectOption = variantSelect.options[variantSelect.selectedIndex];
    const unitPrice = parseFloat(selectOption.getAttribute('data-price')) || 0;

    //get the quantity
    const qtyInput = document.querySelector('input[name="quantity"]');
    const qty = parseInt(qtyInput.value) || 1;

    //calculate the addon total price
    let totalAddonPrice = 0;

    document.querySelectorAll('input[name="selected_addons[]"]:checked').forEach(checkbox => {
      
      //find the corresponding quantity of addon
      const addonId = checkbox.value;
      const addonQtyInput = document.querySelector(`input[name="addon_qty[${addonId.trim()}]"]`);
      const addonQty = (addonQtyInput && addonQtyInput.value) ? parseInt(addonQtyInput.value) : 1;
      
      const addonPrice = parseFloat(checkbox.getAttribute('data-price')) || 0;
      totalAddonPrice += (addonPrice * addonQty);
    });

    //update ui display
    const productSubtotal = unitPrice * qty;
    const finalTotal = productSubtotal + totalAddonPrice;

    //update checkout summary
    const summaryQty = document.getElementById('summaryQty');
    const summaryProductPrice = document.getElementById('summaryProductPrice');
    const summaryOptionPrice = document.getElementById('summaryOptionPrice');
    const summaryTotal = document.getElementById('summaryTotal');
    const displayPrice = document.getElementById('displayPrice');

    if (summaryQty) summaryQty.innerText = qty;
    if (summaryProductPrice) summaryProductPrice.innerText = productSubtotal.toFixed(2);
    if (summaryOptionPrice) summaryOptionPrice.innerText = totalAddonPrice.toFixed(2);
    if (summaryTotal) summaryTotal.innerText = finalTotal.toFixed(2);
    if (displayPrice) displayPrice.innerText = finalTotal.toFixed(2);

  }

  //qty button
  function changeQty(btn, delta) {
    //delta = Quantity change value
    const input = btn.parentElement.querySelector('input');
    //find the maximum stock and convert to integer, default maximum 999 stocks
    const maxStock = parseInt(input.getAttribute('max')) || 999; 
    let value = parseInt(input.value) || 1;
    
    value += delta;

    //current quantity cannot maximum than stock and minimum than 1
    input.value = Math.min(Math.max(1, value), maxStock);

    calculationTotal();
  }

  document.addEventListener('DOMContentLoaded', function() {
    calculationTotal();
  });

  //if card text selected is required to fill up the textbox
  function toggleCardRequired(checkbox){
    const messageArea = document.getElementById('cardMessageArea');
    const messageInput = document.getElementById('cardMessageInput');
    if (checkbox.checked) {
       //required to filled
        messageArea.style.display = 'block';
        messageInput.setAttribute('required', 'required');
    }else {
      messageArea.style.display = 'none';
      messageInput.removeAttribute('required');
      messageInput.value = '';
    }
  }

</script>
</body>


</html>
