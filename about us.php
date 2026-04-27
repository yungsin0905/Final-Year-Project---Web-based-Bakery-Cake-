<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">
<style>
    :root {
            --about-bg: #fffdf9; 
        }

        body {
            margin: 0;
            background-color: var(--about-bg);
            color: var(--font-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif'Segoe UI', sans-serif;
        }

        .main-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        
        h2 {
            color: var(--font-color);
            font-weight: bold;
            border-left: 6px solid var(--main-color); 
            padding-left: 15px;
            margin-top: 50px;
            margin-bottom: 30px;
            position: relative;
        }

        
        p {
            margin-bottom: 25px;
            font-size: 20px;
            color: #6d4c41; 
            line-height: 1.8;
        }

     
        .image-container {
            display: flex; 
            align-items: center;
            gap: 50px;
            margin-bottom: 60px;
        }

        .image-container img {
            width: 380px;
            height: 380px;
            object-fit: cover;
            border-radius: 30px;
            box-shadow: 0 10px 20px rgba(240, 194, 200, 0.3);
        }

        .text-content {
            flex: 1;
        }

        .text-content h2 {
            margin-top: 0;
        }

        
        .team-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin-top: 40px;
        }

        .member-card {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(187, 162, 153, 0.15);
            padding: 30px 20px;
            width: 300px;
            text-align: center;
            transition: all 0.4s ease;
            border: 1px solid rgba(240, 194, 200, 0.2);
        }

        .member-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 12px 25px rgba(240, 194, 200, 0.4);
        }

        .member-card img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid var(--main-color);
            padding: 5px;
            background: white;
        }

        .member-card h3 {
            color: var(--font-color);
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .member-card p {
            font-size: 14px;
            margin: 4px 0;
            color: #8d6e63;
        }

</style>
</head>
<body>
   <?php include_once 'header.php'; ?>
    <div class="main-container">
    <h2>About Us</h2>
      <p>At Cakeology, our journey began with a passion for creating sweet moments that bring people together. Inspired by the love for baking and celebrations, we created a platform where customers can easily explore and order cakes for their special occasions, anytime and anywhere.</p>

    <div class="image-container">
      <img src="icon/cake 1.png" alt="cake 1">
      <div class="text-content">
        <h2>Our Services</h2>
        <p>At Cakeology, we believe every celebration is unique. That’s why we offer flexible cake customization, allowing customers to choose their preferred size, flavor, design, and personal touches.</p>
        <p>Our platform also provides convenient features such as delivery scheduling, add-on services, promotional offers, and multiple secure payment methods to ensure a smooth ordering experience. In addition, customers can easily view detailed product information, including ingredients and allergen details, to make informed and safe choices based on their dietary needs. </p>
      </div>
    </div>

    <div class="image-container">
      <div class="text-content">
        <h2>Mission</h2>
        <p>Our mission is to bring happiness to every celebration by delivering a smooth, enjoyable, and personalized cake ordering experience. We strive to make every moment sweeter while ensuring convenience and reliability for our customers.</p>
      </div>
      <img src="icon/cake 2.jpg" alt="cake 2">
    </div>

    <div class="image-container">
      <img src="icon/cake 3.png" alt="cake 3">
      <div class="text-content">
        <h2>Vision</h2>
        <p>Our vision is to become a trusted and leading online cake ordering platform, known for quality, creativity, and excellent customer experience. We aim to connect people through meaningful celebrations and make Cakeology a part of every special moment.</p>
      </div>
    </div>

    <h2>Our Team</h2>
    <div class="team-container">
      <div class="member-card">
        <img src="icon/member 1.jpg" alt="Member 1">
        <h3>Wong Yung Sin</h3>
        <p>Student ID: 243DT2463D</p>
        <p>Email: WONG.YUNG.SIN@student.mmu.edu.my</p>
    </div>

    <div class="member-card">
        <img src="icon/member 2.jpg" alt="Member 2">
        <h3>Chung Shin Ru</h3>
        <p>Student ID: 243DT2463D</p>
        <p>Email: CHUNG.SHIN.RU@student.mmu.edu.my</p>
    </div>

    <div class="member-card">
        <img src="icon/member 3.jpg" alt="Member 3">
        <h3>Heng Yu Xuan</h3>
        <p>Student ID: 243DT245CM</p>
        <p>Email: HENG.YU.XUAN@student.mmu.edu.my</p>
    </div>
   </div>
</div>

</body>
<?php include_once 'footer.php'; ?>
</html>