<?php 
session_start();
include 'include/config.php';

//retrieve current category information
$current_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
//processing the search and price filter
$min = isset($_GET['min']) ? floatval ($_GET['min']) : 0;
$max = isset($_GET['max']) ? floatval ($_GET['max']) : 2000;
$selected_allergens = isset($_GET['allergens']) ? $_GET['allergens'] : [];
//default category information
$header_title = "All Cakes";
$cat_desc = "Check out our delicous cakes.";
$cat_image = "image/category/category.jpg";
//cake filter
$cake_type = isset($_GET['cake_type']) ? $_GET['cake_type'] : '';
//sort
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'best_selling';
$order_by = "p.CREATED_AT DESC";

//process best selling and high recommended header title

if ($cake_type == 'Best Selling'){
    $header_title = "Best Selling";
    $cat_desc = "Our most popular picks loved by everyone.";
}else if ($cake_type == 'High Recommended'){
    $header_title = "High Recommended";
    $cat_desc = "Our top-rated cakes with excellent reviews."; 
}else if ($current_id > 0){
    $cat_res = $conn->query("SELECT * FROM category WHERE CATEGORY_ID = $current_id
    AND IS_DELETED = 0");
    if ($cat_res && $row = $cat_res->fetch_assoc()) {
        $header_title = $row['CATEGORY_NAME'];
        $cat_desc = $row['CATEGORY_DES'];
        $cat_image = $row['CATEGORY_IMAGE'];
    }
}else if (!empty($cake_type)) {
    $safe_term = $conn->real_escape_string($cake_type);
    $cake_res = $conn->query("SELECT * FROM category WHERE CATEGORY_NAME LIKE '%$safe_term%' AND IS_DELETED = 0 LIMIT 1");
    if ($cake_res && $row = $cake_res->fetch_assoc()) {
        $header_title = $row['CATEGORY_NAME'];
        $cat_desc = $row['CATEGORY_DES']; 
        $cat_image = $row['CATEGORY_IMAGE'];
    }
}


//sort product
$order_by = "p.CREATED_AT DESC"; 
if ($sort === 'best_selling') {
    $order_by = "p.SALES_COUNT DESC";
} elseif ($sort === 'price_desc') {
    $order_by = "MIN_PRICE DESC";
} elseif ($sort === 'price_low') {
    $order_by = "MIN_PRICE ASC";
}elseif ($cake_type === 'Best Selling') {
    $order_by = "p.SALES_COUNT DESC";
}


//category information
$cat_info_query = "";
if ($current_id > 0){
    $cat_info_query = "SELECT * FROM category WHERE CATEGORY_ID = $current_id AND IS_DELETED = 0";
} else {
    $search_term = !empty($cake_type) ? $cake_type : '';
    if (!empty($search_term)) {
        $safe_term = $conn->real_escape_string($search_term);
        $cat_info_query = "SELECT * FROM category WHERE CATEGORY_NAME LIKE '%$safe_term%' AND IS_DELETED = 0 LIMIT 1";
    }
}

if (!empty($cat_info_query)) {
    $res = $conn->query($cat_info_query);
    if ($res && $row = $res->fetch_assoc()) {
        $header_title = $row['CATEGORY_NAME'];
        $cat_desc = $row['CATEGORY_DES']; 
        $cat_image = $row['CATEGORY_IMAGE'];
    }
}

$where_clause = " WHERE p.IS_DELETED = 0 AND v.IS_DELETED = 0 AND p.PRODUCT_STATUS = 'Active'";


//category filter
if ($current_id > 0){
    $where_clause .= " AND p.CATEGORY_ID = $current_id";

    //best selling and high recommended
    }else if (!empty($cake_type)){
    
    if ($cake_type === 'Best Selling'){
    $where_clause .=" AND p.SALES_COUNT >=50";

    } else if ($cake_type === 'High Recommended'){
    $where_clause .= " AND p.AVG_RATING >= 4.5";

    }else {
        $safe_cake_type = $conn->real_escape_string($cake_type);
        $where_clause .= " AND c.CATEGORY_NAME LIKE '%$safe_cake_type%'";
    }
}

//search
if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $where_clause .= " AND (p.PRODUCT_NAME LIKE '%$safe_search%' OR c.CATEGORY_NAME LIKE '%$safe_search%')";
    
    if ($current_id == 0 && empty($cake_type)) {
        $check_cat_query = "SELECT c.CATEGORY_NAME, c.CATEGORY_DES, c.CATEGORY_IMAGE
        FROM product p
        JOIN category c ON p.CATEGORY_ID = c.CATEGORY_ID
        WHERE p.PRODUCT_NAME LIKE '%$search%'
        AND p.IS_DELETED = 0 LIMIT 1";

        $cat_res = $conn->query($check_cat_query);

        if($cat_res && $cat_res->num_rows>0){
            $row=$cat_res->fetch_assoc();
            $header_title = $row['CATEGORY_NAME'];
            $cat_desc = $row['CATEGORY_DES'];
            $cat_image = $row['CATEGORY_IMAGE'];
        }else{
             $header_title = "Search: " . htmlspecialchars($search);
        }
       
    }
}

//allergen filter
if (!empty($selected_allergens)){
    foreach ($selected_allergens as $allergen) {
        $safe_allergen = $conn->real_escape_string($allergen);
        $where_clause .= " AND p.ALLERGEN NOT LIKE '%$safe_allergen%'";
    }
    
}

//price filter add in where_clause or having_clause
$having_clause = " HAVING MIN_PRICE BETWEEN $min AND $max ";

//retrieve min price
$total_sql = "SELECT COUNT(*) as total FROM (
        SELECT p.PRODUCT_ID, MIN(v.VARIANT_PRICE) as MIN_PRICE
        FROM product p
        LEFT JOIN product_variant v ON p.PRODUCT_ID = v.PRODUCT_ID
        LEFT JOIN category c ON p.CATEGORY_ID = c.CATEGORY_ID
        $where_clause
        GROUP BY p.PRODUCT_ID
        $having_clause
        ) as subquery";

//execute and calculate the pages
$total_result = $conn->query($total_sql);

if (!$total_result){
    die("SQL ERRROR: " . $conn->error . " | Query: " . $total_sql);
}

$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$per_page = 6;
$total_pages = ceil($total_records / $per_page);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page <1) $page = 1;
$start_from = ($page - 1) * $per_page;

$sql = "SELECT p.*, MIN(v.VARIANT_PRICE) as MIN_PRICE
        FROM product p
        LEFT JOIN product_variant v ON p.PRODUCT_ID = v.PRODUCT_ID
        LEFT JOIN category c ON p.CATEGORY_ID = c.CATEGORY_ID
        $where_clause
        GROUP BY p.PRODUCT_ID 
        $having_clause
        ORDER BY $order_by
        LIMIT $start_from, $per_page";

$product_result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $header_title;?></title>
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
      }

      body{
        font-family: 'Quicksand', sans-serif;
        background-color: var(--bg-color);
      }

      /* back section */

      .back-section{
        display: flex;
        align-items: center;
        margin: 30px 0 20px 30px;
      }

      .back-link{
        text-decoration: none;
        color: var(--font-color);
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-right: 50px;
        transition: color 0.3s;
      }

      .back-link:hover{
        text-decoration:underline;
      }

      .title-container{
        display:flex;
        align-items:center;
        gap:20px;
        flex-grow:1;
        margin:0 0 20px 0;
      }

      .category-icon{
        width:120px;
        height:120px;
        border-radius:50%;
        object-fit:cover;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }

      .page-title{
        font-size:40px;
        font-weight:700;
        color:var(--font-color);
        font-family:'Pacifico', cursive;
      }

      .description-section{
        margin-bottom:30px;
      }

      .description-text{
        margin-bottom:15px;
        width:90%;
        color:#936752;
      }

      /* main content */
      .main-container{
        display:flex;
        gap:40px;
        align-items:flex-start;
        padding: 0 20px;
      }

      /* filter section */
      .sidebar{
        margin-top:50px;
        width:250px;
        flex-shrink: 0;
        padding:0 20px;
      }

      #refine-section {
            display: none; /* collapse it initially, only appears when click the checkbox */
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
        }

        .refine-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .filter-tag {
            background: #fff;
            border: 1px solid #ddd;
            padding: 8px 12px;
            margin-top: 8px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tag-cat { font-size: 10px; color: #888; text-transform: uppercase; display: block; }
        .tag-val { font-size: 14px; font-weight: bold; color: var(--font-color); }
        .remove-btn { cursor: pointer; color: #333; margin-left: 10px; }

      .product-content{
        flex-grow:1;
      }

      .filter-group{
        margin-bottom:25px;
        margin-left:20px;
      }

      .filter-title{
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        color:#936752;
      }

      .filter-title i {
            font-size: 12px;
            color: #936752;
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .filter-list.collapsed, 
        .price-range-inputs.collapsed {
            display: none;
        }

        .filter-title.active i {
            transform: rotate(-90deg); /* the arrow points to the right when closed  */
        }

        #clear-all:hover {
            color: var(--font-color);
        }

        .collapsed {
            display: none;
        }

        .filter-list {
            list-style: none;
            padding-left: 5px;
        }

        .filter-list a{
            color:#936752;
            text-decoration:none;
        }

         .filter-list a:hover{
            text-decoration:underline;
        }

        .filter-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 15px;
            color: #936752;
            cursor: pointer;
            transition: color 0.3s;
        }

        .filter-item input[type="checkbox"] {
            margin-right: 12px;
            accent-color: #936752; 
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .price-range-inputs {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .price-input {
            width: 80px;
            padding: 5px;
            border: 1px solid #936752;
            border-radius: 5px;
            background-color: white;
            color: #936752;
            font-size: 14px;
            text-align: center;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end; 
            margin-bottom: 30px;
            margin-right:28px;
        }

        .sort-options{
            border: 1px solid var(--font2-color);
            color: var(--font2-color);
            border-radius: 4px;
            padding:5px;
        }

        /* product section */
      .cake-grid{
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 50px;
      }

      .cake-item {
        text-align: center;
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
        margin:15px 0px 15px 60px;
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

      .section-title{
        color:var(--font-color);
        font-family: 'Quicksand', sans-serif;
        font-weight:bold;
        margin-left:100px;
      }


      /* pagination */
      .pagination{
        display:flex;
        justify-content: center;
        align-items:center;
        gap:15px;
        margin-top:40px;
      }

      .page-btn {
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%; 
            background-color: white;
            color: #936752;
            border: 1px solid #936752;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .page-btn.active {
            background-color: #936752;
            color: white;
            border-color: #936752;
        }

        .page-btn:hover:not(.active) {
            background-color: var(--font-color);
            color: white;
        }

        .page-arrow {
            color: #936752;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .page-arrow:hover {
            color: #936752;
        }

        


    </style>
</head>

<body>
    <?php include 'include/header.php';?>
    <div class="back-section">
        <a href="homepage.php" class="back-link">
            <i class ="bi bi-chevron-left"></i>Back
        </a>

    </div>

    <main class="main-container">

        <aside class="sidebar">
            <div id="refine-section">
                <div class="refine-header">
                    <h5 style="color: var(--font-color); margin: 0;">Refine By</h5>
                    <button id="clear-all" class="btn btn-sm p-0" style="color: #936752; text-decoration: underline; background: none; border: none;">Clear All</button>
                </div>
                <div id="tag-container">
                </div>
            </div>

             <div class="filter-group">
                <div class="filter-title"  onclick="toggleFilter(this)">
                    <i class="bi bi-caret-down-fill"></i>
                    Favourite
                </div>
                <ul class="filter-list">
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('Best Selling')">Best Selling</a></li>
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('High Recommended')">High Recommended</a></li>
                </ul>
            </div>
            

            <div class="filter-group">
                <div class="filter-title" onclick="toggleFilter(this)">
                    <i class="bi bi-caret-down-fill"></i>
                    Cake Type
                </div>
                <ul class="filter-list">
                    <li class="filter-item"><a href="product catalogue.php">All Cakes</a></li>
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('cheese')">Cheese Cakes</a></li>
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('vegan')">Vegan Cakes</a></li>
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('fruit')">Fruit Cakes</a></li>
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('gluten-free')">Gluten-Free Cakes</a></li>
                </ul>
            </div>

            <div class="filter-group">
                <div class="filter-title"  onclick="toggleFilter(this)">
                    <i class="bi bi-caret-down-fill"></i>
                    Occasion
                </div>
                <ul class="filter-list">
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('corporate/event')">Corporate/Event</a></li>
                    <li class="filter-item"><a href="javascript:void(0)" onclick="filterByType('birthday')">Birthday Cakes</a></li>
                </ul>
            </div>

               <div class="filter-group">
                <div class="filter-title"  onclick="toggleFilter(this)">
                    <i class="bi bi-caret-down-fill"></i>
                    Allergens
                </div>
                <ul class="filter-list">
                    <li class="filter-item">
                        <input type="checkbox" name = "allergens[]" value="gluten"
                        <?php echo (isset($_GET['allergens'])&& in_array('gluten' , $_GET['allergens'])) ? 'checked' : '';?>
                        onchange="applyFilters()">
                        No Gluten
                    </li>
                    <li class="filter-item"><input type="checkbox" name = "allergens[]" value="nuts" <?php echo (isset($_GET['allergens'])&& in_array('nuts' , $_GET['allergens'])) ? 'checked' : '';?> onchange="applyFilters()">No Nuts</li>
                    <li class="filter-item"><input type="checkbox" name = "allergens[]" value="dairy" <?php echo (isset($_GET['allergens'])&& in_array('dairy' , $_GET['allergens'])) ? 'checked' : '';?> onchange="applyFilters()">No Dairy</li>
                    <li class="filter-item"><input type="checkbox" name = "allergens[]" value="eggs" <?php echo (isset($_GET['allergens'])&& in_array('eggs' , $_GET['allergens'])) ? 'checked' : '';?> onchange="applyFilters()">No Eggs</li>
                    <li class="filter-item"><input type="checkbox" name = "allergens[]" value="caffeine" <?php echo (isset($_GET['allergens'])&& in_array('caffeine' , $_GET['allergens'])) ? 'checked' : '';?> onchange="applyFilters()">No Caffeine</li>
                </ul>
            </div>

            <div class="filter-group">
                <div class="filter-title"  onclick="toggleFilter(this)">
                    <i class="bi bi-caret-down-fill"></i>
                    Cake Price
                </div>
                <div class="price-range-inputs">
                    <input type="text" id= "min_p" class="price-input" placeholder="Min" value="<?php echo $min;?>">
                    <span class="separator">-</span>
                    <input type="text" id= "max_p" class="price-input" placeholder="Max" value="<?php echo $max;?>">
                </div>
            </div>

        </aside>

        <div class="content-wrapper" style="flex-grow: 1;">
            <div class="description-section">

                <div class="title-container">
                    <img src="<?php echo $cat_image;?>" alt="Category Icon"
                    class="category-icon">
                    <h1 class="page-title"><?php echo htmlspecialchars($header_title);?></h1>
                </div>
                <p class="description-text">
                    <?php echo htmlspecialchars($cat_desc);?>
                </p>
             </div>

         <section class="product-content">
            <div class="toolbar">
                <div class="sort-dropdown">
                    <select class="sort-options">
                        <option value="best_selling" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'best_selling') ? 'selected' : ''; ?>>Best Selling</option>
                        <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="price_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    </select>
                </div>
            </div>
        
            <div class="cake-grid">
                <?php 
                if ($product_result && $product_result->num_rows > 0){
                    while ($row = $product_result->fetch_assoc()){
                        ?>
                <div class="cake-item">
                    <img src="<?php echo str_replace(['\\', ' ', '&'], ['/', '%20', '%26'],$row['COVER_IMAGE']);?>" alt="<?php echo htmlspecialchars($row['PRODUCT_NAME']);?>">
                    <p class="cake-name"><a href="product details.php?id=<?php echo $row['PRODUCT_ID'];?>"><?php echo htmlspecialchars($row['PRODUCT_NAME']);?></a></p>
                    <div class="stars">

                        <?php
                            $rating = round($row['AVG_RATING']);
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $rating) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                            }
                            ?>
                        <span class="ms-1">(<?php echo number_format($row['AVG_RATING'], 1); ?>)</span>
                      <p class="price">RM <?php echo number_format ($row['MIN_PRICE'],2);?></p>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p style='grid-column: span 3; text-align: center; color: #936752;'>No products found matching your filters.</p>";
                }
                ?>
            </div>

            <?php if ($total_pages > 1) : ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-arrow">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                        class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
                
    </main>

    <?php include 'include/footer.php';?>
</body>
<script>
    // cake filter
    function filterByType(type){
    const url = new URL('product catalogue.php', window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1));
    const params = new URLSearchParams();
    params.set('cake_type', type);
    params.set('page',1);
    window.location.href = 'product catalogue.php?' + params.toString();
}


    //allergen filter
    function applyFilters() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    //1. get all selected allergens
    const checkedAllergens = document.querySelectorAll('input[name="allergens[]"]:checked');
    params.delete('allergens[]');// clear existing allergens
    checkedAllergens.forEach(cb => {
        params.append('allergens[]', cb.value);
    });

    //2.process price range
    const minP = document.getElementById('min_p').value || 0;
    const maxP = document.getElementById('max_p').value || 2000;
    params.set('min',minP);
    params.set('max',maxP);

    //sort
    const sortElement = document.querySelector('.sort-options');
    if (sortElement){
        params.set('sort',sortElement.value);
    }

    params.set('page',1); // reset to first page when apply new filters
    
    //3. update url
    window.location.href = window.location.pathname + '?' + params.toString();
}

    document.addEventListener('DOMContentLoaded', function() {
    const refineSection = document.getElementById('refine-section');
    const tagContainer = document.getElementById('tag-container');
    const clearAllBtn = document.getElementById('clear-all');
    const checkboxes = document.querySelectorAll('.filter-item input[type="checkbox"]');

    // 1. collapsible functions
    window.toggleFilter = function(element) {
        element.classList.toggle('active');
        const list = element.nextElementSibling;
        if (list) {
            list.classList.toggle('collapsed');
        }
    };

    // 2. hiding "refine"
    function refreshRefineArea() {
        const checkedBoxes = Array.from(checkboxes).filter(i => i.checked);
        
        const minP = document.getElementById('min_p').value;
        const maxP = document.getElementById('max_p').value;
        
        const isPriceFiltered = (minP != 0 || maxP != 2000);
        
        if (checkedBoxes.length > 0 || isPriceFiltered) {
            refineSection.style.display = 'block';
            tagContainer.innerHTML = ''; 
            checkedBoxes.forEach(box => {
                const titleElement = box.closest('.filter-list').previousElementSibling;
                const motherCategory = titleElement ? titleElement.innerText.trim() : 'Filter';
                const valLabel = box.parentElement.innerText.trim();
                const tag = document.createElement('div');
                tag.className = 'filter-tag';
                tag.innerHTML = `
                    <div class="tag-box">
                        <span class="tag-cat">${motherCategory}:</span>
                        <span class="tag-val">${valLabel}</span>
                    </div>
                    <span class="remove-btn" onclick="uncheck('${box.value}')"><i class="bi bi-x-lg"></i></span>
                `;
                tagContainer.appendChild(tag);
            });

            //price tag
            if (isPriceFiltered){
                const priceTag = document.createElement('div');
                priceTag.className = 'filter-tag';
                priceTag.innerHTML = `
                    <div class="tag-box">
                        <span class="tag-cat">Price:</span>
                        <span class="tag-val">${minP} - ${maxP}</span>
                    </div>
                    <span class="remove-btn" onclick="resetPrice()"><i class="bi bi-x-lg"></i></span>
                `;
                tagContainer.appendChild(priceTag);
            }
        } else {
            refineSection.style.display = 'none';
        }
    }

    //reset price filter
    window.resetPrice = function() {
    document.getElementById('min_p').value = 0;
    document.getElementById('max_p').value = 2000;
    applyFilters();

    };

    // 3. Checkbox changes
    checkboxes.forEach(box => {
        box.addEventListener('change', refreshRefineArea);
    });

    // 4. Global uncheck function
    window.uncheck = function(val) {
        const target = Array.from(checkboxes).find(i => i.value === val);
        if (target) {
            target.checked = false;
            refreshRefineArea();
            applyFilters(); 
        }
    };

    // 5. Clear All 
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            
            //retrieve current url parameters
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            //backup current category parameters
            const currentType = params.get('cake_type');
            const currentId = params.get('id');
            
            // clear all parameters 
            const newParams = new URLSearchParams();

            //add back category parameters
            if (currentType) newParams.set('cake_type', currentType);
            if (currentId) newParams.set('id', currentId);
            
            //back to page 1
            newParams.set('page',1);

            checkboxes.forEach(i => i.checked = false);
            //reset price input
            document.getElementById('min_p').value = 0;
            document.getElementById('max_p').value = 2000;
            
            refreshRefineArea();
            window.location.href = window.location.pathname + '?' + newParams.toString(); 
        });
    }

    //press enter execute the filter
    document.querySelectorAll('.price-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    });
    
    const sortSelect = document.querySelector('.sort-options');
    if(sortSelect){
        sortSelect.addEventListener('change',applyFilters);
    }

    // initialize the refine area based on current url parameters
    const urlParams = new URLSearchParams(window.location.search);
    const allergens = urlParams.getAll('allergens[]');
    allergens.forEach(val => {
        const cb = document.querySelector(`input[value="${val}"]`);
        if (cb) cb.checked = true;
    });

   
    refreshRefineArea();
});
</script>
</html>