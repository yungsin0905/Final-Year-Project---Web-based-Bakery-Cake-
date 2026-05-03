<?php include 'include/config.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
        margin: 30px 0 30px 50px;

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

      /* card section */

      .request-card {
        background: white;
        border-radius: 25px;
        padding: 25px;
        margin: 0 30px 30px 30px;
        box-shadow: 0 10px 30px rgba(147, 103, 82, 0.05);
        border: 1px solid var(--secondary-color);
    }

    .page-title{
       color: var(--font-color); 
       font-weight: 700;
        text-align:center;
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        border-bottom: 1.5px dashed var(--main-color);
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-badge.quoted { background: #e1f5fe; color: #0288d1; }
    .status-badge.pending { background: #fff3e0; color: #ef6c00; }

    /* content section */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .info-item label {
        color: var(--font2-color);
        font-weight: 700;
        margin-right: 8px;
    }

    .price-section {
        grid-column: span 2;
        background: var(--secondary-color);
        padding: 10px;
        border-radius: 10px;
        display: flex;
        align-items: center;
    }

    .price {
        font-size: 20px;
        color: var(--font-color);
        font-weight: 800;
    }

    /* required section */
    .required-image{
        height:200px;
        border-radius:20px;
    }

    .required-details{
        font-size:14px;
    }

    /* recipient section */

    .recipient-details{
        margin-top:10px;
    }

    .recipient-details{
        margin-right: 8px;
        font-size:14px
    }
    
    .request-footer {
        margin-top: 20px;
        text-align: right;
    }

    .btn-proceed {
        background: var(--main-color);
        color: white;
        padding: 10px 25px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 700;
        transition: 0.5s;
    }

    .btn-reject {
        background: transparent;
        color: #e57373;
        border: 1.5px solid #e57373;
        padding: 8px 20px;
        border-radius: 20px;
        margin-right: 10px;
        font-weight: 600;
        transition:0.5s;
    }

    .btn-reject:hover{
        background-color: #e57373;
        color:white;
    }

    .btn-proceed:hover{
        background-color: rgb(211, 127, 139);
        color:white;
    }

    summary {
        cursor: pointer;
        color: var(--font2-color);
        font-size: 14px;
        font-weight: 600;
    }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>
  <div class="back-section">
    <a href="homepage.php" class="back-link">
      <i class="bi bi-chevron-left"></i>Back
    </a>
  </div>

  <div class="content-container">
    <h2 class="mb-4 page-title">My Custom Inquiries</h2>

    <div class="request-card">
        <div class="request-header">
            <span class="date"><i class="bi bi-calendar3"></i> Submitted on: 20/10/2023</span>
            <span class="status-badge quoted">Quoted</span>
        </div>

        <div class="request-body">
            <div class="info-grid">
                 <div class="info-item">
                    <label>Delivery Date:</label>
                    <span>24/4/2026</span>
                </div>
                <div class="info-item">
                    <label>Style:</label>
                    <span>2 Tier Cake</span>
                </div>
                 <div class="info-item">
                    <label>Cake Size:</label>
                    <span>8 inch</span>
                </div>
                <div class="info-item">
                    <label>Flavor:</label>
                    <span>Belgian Chocolate</span>
                </div>
                <div class="info-item">
                    <label>Pax:</label>
                    <span>20 Pax</span>
                </div>
                 <div class="info-item">
                    <label>Budget:</label>
                    <span>RM 350.80</span>
                </div>

                <div class="info-item price-section">
                    <label>Admin Quote:</label>
                    <span class="price">RM 250.00</span>
                </div>
            </div>
            

            <!-- details section -->
            <details class="mt-3">
                <summary>View My Requirements</summary>
                <div class="required-details">
                    <label class="mt-2 text-muted required-details">Description:</label>
                    <span class="text-muted required-details"> Theme is pastel pink with gold flakes. Name "Happy Birthday" on top...</span>
                </div>

                <label class="required-details text-muted">Reference Image:</label>
                <img class="required-image" src="image/product/cheesecake/matcha basque cake/matcha basque1.jpg">
            </details>

            <details class="mt-3">
                <summary>Recipient Details</summary>
                <div class="recipient-grid">
                    <div class="recipient-details">
                        <label class="text-muted">Name:</label>
                        <span  class="text-muted">anna</span>
                    </div>

                    <div class="recipient-details">
                        <label  class="text-muted">Email:</label>
                        <span  class="text-muted">anna@gmail.com</span>
                    </div>

                    <div class="recipient-details">
                        <label class="text-muted">Phone:</label>
                        <span  class="text-muted">01x-xxxxxxx</span>
                    </div>

                    <div class="recipient-details">
                        <label  class="text-muted">Email:</label>
                        <span  class="text-muted">anna@gmail.com</span>
                    </div>

                    <div class="recipient-details">
                        <label  class="text-muted">Delivery Address:</label>
                        <span  class="text-muted">xx,Jalan xx, Taman xx</span>
                    </div>
                </div>
                
            </details>
        </div>

        <!-- card footer -->
        <div class="request-footer">
            <div class="action-buttons">
                <button class="btn-reject">Reject</button>
                <a href="checkout.php?id=123" class="btn-proceed">Proceed to Checkout</a>
            </div>
        </div>
    </div>
    </div>
    <?php include 'include/footer.php'; ?>
</body>
</html>