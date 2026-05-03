<?php include 'include/config.php';
session_start();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher</title>
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
        margin: 30px 0 30px -50px;

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

      .voucher-section{
        margin-left:60px;
      }

      .voucher-title {
        color:var(--font2-color);
        font-weight:bold;
      }

      .group-label {
        color: var(--font2-color);
        font-size:15px;
        font-weight:bold;
        margin:20px 0 20px;
        
      }

      .voucher-card {
        display: flex;
        filter: drop-shadow(2px 2px 5px rgba(0,0,0,0.05));
        margin-bottom:20px;
        height:140px;
        overflow:visible;
        
      }

      .voucher-group{
        margin-bottom:100px;
      }

      .voucher-left, .voucher-right {
        background-color: var(--main-color);
        position: relative;
    }

      .voucher-left {
        width:150px;
        min-width:150px;
        margin-right:0;
        display:flex;
        justify-content: center;
        align-items: center;
        /*four corners position for masking*/
        -webkit-mask-position: 0 0;

        /* make the four corners concave at the same time */
        background: 
        radial-gradient(circle at 0 0, transparent 15px, var(--main-color) 0) top left,
        radial-gradient(circle at 100% 0, transparent 15px, var(--main-color) 0) top right,
        radial-gradient(circle at 0 100%, transparent 15px, var(--main-color) 0) bottom left,
        radial-gradient(circle at 100% 100%, transparent 15px, var(--main-color) 0) bottom right;
        background-size: 51% 51%;
        background-repeat: no-repeat;
      }

      .voucher-right{
        width:50%;
        display:flex;
        flex-direction: column;
        justify-content: center;
        padding:10px 30px 10px 20px;

         /* make the four corners concave at the same time */
        background: 
        radial-gradient(circle at 0 0, transparent 15px, var(--main-color) 0) top left,
        radial-gradient(circle at 100% 0, transparent 15px, var(--main-color) 0) top right,
        radial-gradient(circle at 0 100%, transparent 15px, var(--main-color) 0) bottom left,
        radial-gradient(circle at 100% 100%, transparent 15px, var(--main-color) 0) bottom right;
        background-size: 51% 51%;
        background-repeat: no-repeat;
      }

      .voucher-left::after {
        content:"";
        position:absolute;
        right:0;
        top:10%;
        height:80%;
        border-right: 2px dashed #ffff;
      }

      .logo-circle{
        border-radius:50%;
        width:100px;
        height:100px;
        padding:5px;
        display:flex;
        justify-content: center;
      }

      .logo-circle img{
        height:100px;
        object-fit:contain;
      }

      .promo-title {
        color:#fff;
        font-size:24px;
        font-weight:bold;
        margin-bottom:5px;
      }

      .min-spend {
        color:white;
        font-size:13px;
        margin-bottom:15px;
      }

      .badge {
        background: var(--secondary-color);
        color: var(--search-border-color);
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 11px;
        margin-left: 10px;
        border: 1px solid var(--search-border-color);
      }

      .voucher-footer{
        display:flex;
        gap:20px;
        font-size:14px;
        color:var(--font2-color);
        font-weight:300;
      }

      .exp-date {
        color:#ff4d4d;
      }

      .not-available .voucher-card{
        opacity:0.6;
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
  </div>

  <section class="voucher-section">
    <h2 class="voucher-title">Vouchers</h2>
      
      <div class="voucher-group">
        <h3 class="group-label">Haven't Used</h3>

        <div class="voucher-card">
          <div class="voucher-left">
            <div class="logo-circle">
              <img src="image/cakeology logo.png" alt="Cakeology logo">
            </div>
          </div>

          <div class="voucher-right">
            <div class="voucher-info">
              <h4 class="promo-title">15% OFF + FREE SHIPPING</h4>
              <p class="min-spend">MIN SPEND RM 120 <span class="badge">ONLY FOR SILVER</span></p>
              <div class="voucher-footer">
                <span class="code">VOUCHER CODE: SILVER2026</span>
                <span class="exp-date">EXP: XX-XX-XX</span>
              </div>
            </div>
          </div>
        </div>
      </div>



      <div class="voucher-group not-available">
        <h3 class="group-label">Not Available</h3>

        <div class="voucher-card">
          <div class="voucher-left">
            <div class="logo-circle">
              <img src="image/cakeology logo.png" alt="Cakeology logo">
            </div>
          </div>
          <div class="voucher-right">
            <div class="voucher-info">
              <h4 class="promo-title">20% OFF + FREE SHIPPING</h4>
              <p class="min-spend">MIN SPEND RM 120 <span class="badge">ONLY FOR GOLD</span></p>
              <div class="voucher-footer">
                <span class="code">VOUCHER CODE : GOLD2026</span>
                <span class="exp-date">EXP: XX-XX-XX</span>
              </div>
            </div>
          </div>
        </div>
      </div>
  </section>

  <?php include 'include/footer.php'?>
</body>
</html>