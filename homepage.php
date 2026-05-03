<?php 
include 'include/config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
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

      body{
        font-family: 'Quicksand', sans-serif;
        background-color: var(--bg-color);
        width:100%;
      }
      
      /* hero */

      .hero-section
      {
        background-image: url(image/background/learn.png);
        height:450px;
        width:1500px;
        background-size: cover;
        background-position: center;
        margin-left:10px;
        display:flex;
        align-items:end;
      }

      .learn-btn{
        background-color: #f9d9d9;
        border:none;
        border-radius:68px;
        padding:5px 20px;
        display: inline-block;
        margin:20px 0 40px 0;
        width:250px;
        font-size:25px;
        transition:0.5s;
      }

      .learn-btn a{
        display: block;
        color: var(--font-color);
        font-weight: 600;
        padding: 8px 15px;
        text-decoration: none;
        font-family: sans-serif;
      }

      .learn-btn:hover{
        box-shadow: 0 5px 10px 0.2rem rgba(139, 139, 139, 0.76); 
      }

      /* main content */

      .main-content{
      background-color: var(--bg-color);
      }
      .cake-grid{
        display:flex;
        flex-wrap:nowrap;
        overflow-x:hidden;
        scroll-behavior: smooth;
        width:100%;
      }

      .cake-item {
        flex: 0 0 calc(33.333% - 20px);
        text-align: center;
        align-items:center
      }

      .cake-item img {
        height:350px;
        width: 350px;
        border-radius: 20px;
        aspect-ratio: 1/1;
        object-fit: cover;

      }

      .cake-name{
        color:#936752;
        font-weight:bold;
        font-family: 'Quicksand', sans-serif;
        margin:15px 0px 15px 100px;
        width:250px;
        
      }

      .cake-name a{
        color:#936752;
        text-decoration:none;
        transition:0.3s;
        
      }

      .cake-name a:hover{
        text-decoration: underline;
      }

      .stars{
        color:var(--rating-color);
        margin:0 50px;
      }
      
      .price{
        font-weight:bold;
        display:inline-block;
        margin-left:10px;
        color:var(--font-color)
      }

      .slider-wrapper {
        position: relative; 
        display: flex;
        align-items: center;
        padding: 0 50px;
      }

      .section-title{
        color:var(--font-color);
        font-family: 'Quicksand', sans-serif;
        font-weight:bold;
        margin-left:100px;
      }

      .best-selling{
        margin:50px 0;
      }

      .text-center{
        color:var(--font-color);
        margin-left:50px;
      }

      .categories{
        display:flex;
        justify-content: center;
        gap:50px;
        padding:40px 0;
        
      }

      .cat-item{
        text-align:center;
        width:120px;
      }

        .cat-item img {
        width: 125px;
        height: 125px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
      }

      .categories span{
        font-weight:bold;
        color:var(--font-color);
        
      }

      .categories a{
        text-decoration:none;
        color:var(--font-color);
        transition:0.3s;
      }

      .categories a:hover{
        text-decoration: underline;
      }

      .more-categories h2{
        text-align: center;
      }

     

    /* slider */
    .slider-wrapper {
      position:relative;
      display:flex;
      align-items: center;
      padding:0 50px;
    }

    .cake-grid{
      display:flex;
      gap:30px;
      overflow-x: auto;
      scroll-behavior:smooth;  /* smooth scrolling effect */
      scrollbar-width:none;
      padding:20px 0;
      width:100%;
    }

    /* hide crome/safari scrollbar */
    .cake-grid::-webkit-scrollbar{
      display:none;
    }

    .cake-item{
      flex: 0 0 calc(33.333% - 20px);
      min-width: 280px;
    }

    /*arrow button */
    .slide-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 255, 255, 0.8);
      border: 1px solid var(--main-color);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      cursor: pointer;
      z-index: 10;
      color: var(--font-color);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .prev-btn { left: 5px; }
    .next-btn { right: 5px; }

    .slide-arrow:hover {
      background: var(--main-color);
      color: white;
    }

  </style>
</head>
<body>
<?php include 'include/header.php';?>

  
     <!-- banner -->
   <section class="hero-section">
      <div class="container">
        <button class="learn-btn"><a href="#">Learn More</a></button>
      </div>
    </section>

    <!-- main-content -->
    <section class="main-content">
      <div class="best-selling">
        <h2 class="section-title">Best Selling</h2>
          <div class="slider-wrapper">
            <div class="cake-grid" id="productSlider1">

                <?php 
                //retrieve sql
                $sql = "SELECT p.*, MIN(v.VARIANT_PRICE) as MIN_PRICE FROM product p
                LEFT JOIN product_variant v ON p.PRODUCT_ID = v.PRODUCT_ID
                WHERE p.PRODUCT_STATUS = 'Active' 
                AND p.IS_DELETED = 0
                AND p.SALES_COUNT >= 50
                GROUP BY p.PRODUCT_ID 
                ORDER BY p.SALES_COUNT DESC
                LIMIT 10"; // at least 10 best selling

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                  while ($row=$result->fetch_assoc()){
                    ?>
              <div class="cake-item">
                  <img src="<?php echo $row['COVER_IMAGE'];?>" alt="echo htmlspecialchars ($row ['PRODUCT_NAME']);?>">
                  
                  <div class="cake-item-info">
                  
                  </div>
                  <p class="cake-name">
                    <a href="product details.php? id=<?php $row ['PRODUCT_ID']?>">
                      <?php echo htmlspecialchars($row['PRODUCT_NAME']);?>
                    </a>
                  </p>
                  <div class="stars">
                    <?php
                      $rating = round ($row['AVG_RATING']);
                      for ($i=1; $i<=5; $i++) {
                          echo ($i <= $rating) ? '<i class="bi bi-star-fill"></i></i>' : '<i class="bi bi-star"></i>';
                        }
                    ?>
                      <span class="ms-1">(<?php echo number_format($row['AVG_RATING'], 1); ?>)</span>
                      <p class="price">RM <?php echo number_format($row['MIN_PRICE'],2);?></p>
                  </div>
              </div>
              <?php
              }
                } else {
                  echo "<p class='text-center'>No best-selling products at the moment</p>";
                }
              ?>
            </div>

          
            <button class="slide-arrow prev-btn" onclick="moveSlider(-1, 'productSlider1')">
               <i class="bi bi-chevron-left"></i>
            </button>
            <button class="slide-arrow next-btn" onclick="moveSlider(1, 'productSlider1')">
                <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>

       <!-- high recommended -->
      <div class="best-selling">
        <h2 class="section-title">Highly Recommended</h2>
    
        <div class="slider-wrapper">
          <div class="cake-grid" id="productSlider2">
              <?php

              $sql_rec = "SELECT p.*, MIN(v.VARIANT_PRICE) as MIN_PRICE
                        FROM product p
                        LEFT JOIN product_variant v on p.PRODUCT_ID = v.PRODUCT_ID
                        WHERE p.PRODUCT_STATUS = 'Active'
                        AND p.IS_DELETED = 0
                        AND p.AVG_RATING >= 4.5 
                        GROUP BY p.PRODUCT_ID 
                        ORDER BY p.AVG_RATING DESC, p.SALES_COUNT DESC 
                        LIMIT 10";

              $result_rec = $conn->query($sql_rec);

              if ($result_rec && $result_rec->num_rows > 0) {
                while ($row = $result_rec->fetch_assoc()) {
                    ?>
                    <div class="cake-item">
                        <img src="<?php echo $row['COVER_IMAGE']; ?>" alt="<?php echo htmlspecialchars($row['PRODUCT_NAME']); ?>">
                        
                        <p class="cake-name">
                            <a href="product_details.php?id=<?php echo $row['PRODUCT_ID']; ?>">
                                <?php echo htmlspecialchars($row['PRODUCT_NAME']); ?>
                            </a>
                        </p>
                        
                        <div class="stars">
                            <?php
                            $rating = round($row['AVG_RATING']);
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $rating) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                            }
                            ?>
                            <span class="ms-1">(<?php echo number_format($row['AVG_RATING'], 1); ?>)</span>
                            <p class="price">RM <?php echo number_format($row['MIN_PRICE'], 2); ?></p>
                        </div>
                    </div>
                    <?php
                  }
                }else{
                  echo "<p class='text-center'>Currently no highly rated products.</p>";
                }

              ?>
              
            <button class="slide-arrow prev-btn" onclick="moveSlider(-1, 'productSlider2')">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button class="slide-arrow next-btn" onclick="moveSlider(1, 'productSlider2')">
                <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>

      
        <!-- more categories -->
      <div class="more-categories">
        <h2 class="section-title">More Categories</h2>
        <div class="categories">
          <?php 
          $cat_sql = "SELECT * FROM category WHERE CATEGORY_STATUS = 'Active' 
          AND IS_DELETED = 0";
          $cat_result = $conn->query($cat_sql);

          if ($cat_result && $cat_result->num_rows > 0) {
            while ($cat_row = $cat_result->fetch_assoc()){
              ?>
              <div class="cat-item">
                <a href ="product catalogue.php?id=<?php echo htmlspecialchars($cat_row['CATEGORY_ID']);?>">
                  <img src="<?php echo $cat_row['CATEGORY_IMAGE'];?>" 
                  alt="<?php echo htmlspecialchars($cat_row['CATEGORY_NAME']);?>">
                  <span><?php echo htmlspecialchars ($cat_row['CATEGORY_NAME']);?></span>
                </a>
              </div>
              <?php

            }
          }

          ?>
        </div>
      </div>
  </section>

  <?php include 'include/footer.php';?>

  
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

  //scrobar function
  function moveSlider(direction, sliderId) {
    //"sliderId" represents the id of the slider we want to control, 
    // it allows we to have multiple sliders on the same page and control them independently.
    const slider = document.getElementById(sliderId);
    if (slider) {
        //scrolling distance = width of the visible area of the container
        const scrollAmount = slider.offsetWidth; 
        
        slider.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
}
</script>
</html>