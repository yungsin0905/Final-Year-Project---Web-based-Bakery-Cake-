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
        /*hover effect*/
         --transition: 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      }

      body{
        font-family: 'Quicksand', sans-serif;
        background-color: var(--bg-color);
        width:100%;
        overflow-x: hidden;
      }
      
      /* hero */
      .hero-section
      {
        background: linear-gradient(135deg, #fce8ec 0%, #fff0d6 100%);
        position: relative;
        height: 480px;
        border-radius: 0 0 50px 50px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 0 80px;
        margin-bottom: 60px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      }

      .hero-section::before {
        content: '';
        position: absolute;
        right: -60px; top: -60px;
        width: 450px; height: 450px;
        background: rgba(240,194,200,0.3);
        border-radius: 50%;
      }

      .hero-section::after {
        content: '';
        position: absolute;
        right: 120px; bottom: -100px;
        width: 300px; height: 300px;
        background: rgba(255,246,230,0.6);
        border-radius: 50%;
      }

      .container{
        position: relative; 
        z-index: 2; 
        max-width: 550px; 
        text-align: left;
        margin-left: 0;
      }

      .container h1{
        font-size: 48px;
        font-weight: 800;
        color: var(--font-color);
        line-height: 1.2;
        margin-bottom: 15px;
      }

      .container h1 span {
        color: #d4847a;
      }

      .container p {
        font-size: 18px;
        color: var(--font2-color);
        margin-bottom: 35px;
        line-height: 1.6;
        opacity: 0.9;
      }

      .learn-btn{
        display: inline-block;
        background: var(--font-color);
        padding: 2px;
        border-radius: 50px;
        transition: 0.3s all ease;
        border: none;
        box-shadow: 0 4px 15px rgba(101, 54, 31, 0.2);
      }

      .learn-btn a{
        display: block;
        color: var(--bg-color);
        font-weight: 600;
        padding: 12px 35px;
        text-decoration: none;
        font-family: 'Quicksand', sans-serif;
        font-size: 16px;
      }

      .learn-btn:hover{
        background: var(--font2-color);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(101, 54, 31, 0.3);
      }

      /* main content */
      .main-content{
        background-color: var(--bg-color);
        padding-bottom: 80px;
      }

      .section-title{
        color: var(--font-color);
        font-family: 'Quicksand', sans-serif;
        font-weight: 700;
        margin-left: 0px;
        margin-bottom: 30px;
        position: relative;
        display: inline-block;
      }

      .section-title::after {
        content: '';
        position: absolute;
        bottom: -8px; left: 0;
        width: 50px; height: 3px;
        background: var(--main-color);
        border-radius: 2px;
      }
      
      .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 100px;
        margin-bottom: 20px;
      }

      .view-all-btn {
        color: var(--font2-color);
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .view-all-btn:hover {
        color: #d4847a;
        transform: translateX(5px);
      }

      .best-selling{
        margin: 60px 0;
      }

      /* slider */
      .slider-wrapper {
        position: relative; 
        display: flex;
        align-items: center;
        padding: 0 80px;
      }

      .cake-grid{
        display: flex;
        gap: 30px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        padding: 20px 10px;
        width: 100%;
      }

      .cake-grid::-webkit-scrollbar{
        display: none;
      }

      .cake-item {
        flex: 0 0 calc(33.333% - 20px);
        min-width: 300px;
        text-align: center;
        background: #fff;
        padding: 15px;
        border-radius: 25px;
        transition: 0.4s;
        border: 1px solid rgba(240, 194, 200, 0.2);
      }

      .cake-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(147, 103, 82, 0.1);
      }

      .cake-item img {
        width: 100%;
        height: 280px;
        border-radius: 18px;
        object-fit: cover;
        margin-bottom: 20px;
      }

      .cake-name{
        color: var(--font2-color);
        font-weight: 700;
        font-family: 'Quicksand', sans-serif;
        margin: 10px auto;
        font-size: 1.1rem;
        max-width: 90%;
      }

      .cake-name a{
        color: var(--font2-color);
        text-decoration: none;
        transition: 0.3s;
      }

      .cake-name a:hover{
        color: var(--main-color);
      }

      .stars{
        color: var(--rating-color);
        margin: 10px 0;
        font-size: 0.9rem;
      }
      
      .price{
        font-weight: 800;
        display: block;
        margin-top: 8px;
        color: var(--font-color);
        font-size: 1.2rem;
      }

      /* arrow button */
      .slide-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        cursor: pointer;
        z-index: 10;
        color: var(--font-color);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: 0.3s;
      }

      .prev-btn { left: 20px; }
      .next-btn { right: 20px; }

      .slide-arrow:hover {
        background: var(--main-color);
        color: white;
        transform: translateY(-50%) scale(1.1);
      }

      /* categories */
      .more-categories {
        margin: 50px 0 60px;
      }
 
      .more-categories h2 {
        text-align: left;
      }
 
      .categories {
        display: flex;
        justify-content: center;
        gap: 36px;
        padding: 36px 60px;
        flex-wrap: wrap;
      }
 
      .cat-item {
        text-align: center;
        width: 120px;
        transition: var(--transition);
      }
 
      .cat-item:hover {
        transform: translateY(-6px);
      }
 
      .cat-item img {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 3px solid #fff;
        box-shadow: 0 6px 20px rgba(101,54,31,0.12);
        transition: var(--transition);
      }
 
      .cat-item:hover img {
        border-color: var(--main-color);
        box-shadow: 0 10px 28px rgba(101,54,31,0.18);
      }
 
      .categories span {
        font-weight: 700;
        color: var(--font-color);
        font-family: 'Quicksand', sans-serif;
        font-size: 13px;
      }
 
      .categories a {
        text-decoration: none;
        color: var(--font-color);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        align-items: center;
      }
 
      .categories a:hover span {
        color: #d4847a;
        text-decoration: underline;
      }

      .text-center {
        color: var(--font2-color);
        padding: 40px;
      }

    </style>
</head>
<body>
<?php include 'include/header.php';?>

    <!-- banner -->
    <section class="hero-section">
      <div class="container">
        <h1>Every slice is a<br><span>sweet moment</span></h1>
        <p>Fresh-baked cakes made for every occasion — birthdays, anniversaries, or just because.</p>
        <button class="learn-btn"><a href="about us.php">Learn More</a></button>
      </div>
    </section>

    <!-- main-content -->
    <section class="main-content">
      <!-- best selling -->
      <div class="best-selling">
        <div class="section-header">
          <h2 class="section-title">Best Selling</h2>
          <a href="product catalogue.php?sort=best_selling" class="view-all-btn">
                View All <i class="bi bi-arrow-right"></i>
          </a>
        </div>
        
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
                LIMIT 10"; 

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                  while ($row=$result->fetch_assoc()){
                    ?>
              <div class="cake-item">
                  <img src="<?php echo $row['COVER_IMAGE'];?>" alt="<?php echo htmlspecialchars($row['PRODUCT_NAME']);?>">
                  <p class="cake-name">
                    <a href="product details.php?id=<?php echo $row['PRODUCT_ID']; ?>">
                      <?php echo htmlspecialchars($row['PRODUCT_NAME']);?>
                    </a>
                  </p>
                  <div class="stars">
                    <?php
                      $rating = round($row['AVG_RATING']);
                      for ($i=1; $i<=5; $i++) {
                          echo ($i <= $rating) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                      }
                    ?>
                    <span class="ms-1">(<?php echo number_format($row['AVG_RATING'], 1); ?>)</span>
                    <p class="price">RM <?php echo number_format($row['MIN_PRICE'], 2);?></p>
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
        <div class="section-header">
          <h2 class="section-title">Highly Recommended</h2>
           
          <!-- 这边没弄好 -->
          <a href="product catalogue.php?sort=highly_rated" class="view-all-btn">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
           
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
                            <a href="product details.php?id=<?php echo $row['PRODUCT_ID']; ?>">
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
              } else {
                echo "<p class='text-center'>Currently no highly rated products.</p>";
              }
              ?>
            </div>
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
          <div class="section-header">
            <h2 class="section-title">More Categories</h2>
          </div>
          <div class="categories">
            <?php 
            // Retrieve all active categories
            $cat_sql = "SELECT * FROM category WHERE CATEGORY_STATUS = 'Active' AND IS_DELETED = 0";
            $cat_result = $conn->query($cat_sql);
    
            if ($cat_result && $cat_result->num_rows > 0) {
              while ($cat_row = $cat_result->fetch_assoc()) { ?>
                <div class="cat-item">
                  <a href="product catalogue.php?id=<?php echo htmlspecialchars($cat_row['CATEGORY_ID']); ?>">
                    <img src="<?php echo $cat_row['CATEGORY_IMAGE']; ?>" 
                        alt="<?php echo htmlspecialchars($cat_row['CATEGORY_NAME']); ?>">
                    <span><?php echo htmlspecialchars($cat_row['CATEGORY_NAME']); ?></span>
                  </a>
                </div>
              <?php }
            } ?>
          </div>
        </div>
    </section>

    <?php include 'include/footer.php';?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  //scrollbar function
  function moveSlider(direction, sliderId) {
    //"sliderId" represents the id of the slider we want to control,

    // it allows we to have multiple sliders on the same page and control them independently.
    const slider = document.getElementById(sliderId);
     //scrolling distance = width of the visible area of the container
    if (slider) {
        const scrollAmount = slider.offsetWidth * 0.8; // scroll 80% of width
        slider.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
}
</script>
</body>
</html>