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

      /* page header section */

      .page-header{
        margin-bottom:30px;
        background-color: var(--bg-color);
        width:100%;
        text-align: center;
       
      }

      .logo{
        width:150px;
        height:150px;
        margin-bottom:20px;
        border-radius:50%;
        margin-left: 20px;
      }

      .page-header h1{
        font-size:30px;
        margin:0 0 10px 0;
        font-weight:700;
        color:var(--font2-color);
        font-family: 'Pacifico', cursive;
      }

      .subtitle{
        font-size:20px;
        margin:20px 0 30px 0;
        color:var(--font2-color);
        font-family: 'Pacifico', cursive;
      }


      /* main form content */

      .custom-form-wrapper {
            display: flex;
            flex-direction:column;
            align-items: center;
            padding-bottom: 80px;
        }

        .custom-form-container {
            background: white;
            width: 90%;
            max-width: 800px;
            padding: 50px;
            border-radius: 40px; 
            box-shadow: 0 15px 40px rgba(147, 103, 82, 0.08);
            
        }

        .form-header h2 {
            color: var(--font-color);
            font-size: 26px;
            text-align: center;
            margin-bottom: 10px;
        }

        .form-header p {
            color: var(--font2-color);
            text-align: center;
            font-size: 14px;
            margin-bottom: 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; 
            gap: 25px;
            margin-bottom: 25px;
        }

        .field-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .field-group label {
            display: block;
            color: var(--font2-color);
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 15px;
            padding-left: 5px;
        }

        .field-group label span {
            color: var(--main-color);
        }

        .field-group input, 
        .field-group textarea,
        .custom-select {
            width: 100%;
            padding: 14px 20px;
            border: 2.5px solid var(--main-color);
            border-radius: 20px; 
            background-color: var(--bg-color);
            color: var(--font-color);
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        .field-group input:focus, 
        .field-group textarea:focus,
        .custom-select:focus {
            border-color: var(--font2-color);
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(240, 194, 200, 0.2);
        }

        
        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23936752' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 20px center;
        }

        /* upload picture area */
        .custom-upload-card {
            border: 2.5px dashed var(--main-color);
            border-radius: 25px;
            background: var(--bg-color);
            text-align: center;
            transition: 0.3s;
            margin-top: 5px;
        }

        .custom-upload-card:hover {
            background: var(--secondary-color);
            border-color: var(--font2-color);
        }

        .upload-inner {
            display: flex;
            flex-direction: column;
            padding: 35px;
            cursor: pointer;
            color: var(--font2-color);
            font-weight: 600;
        }

        .upload-inner i { 
            font-size: 40px; 
            margin-bottom: 12px; 
            color: var(--main-color);
        }

        /* submit button area */
        .form-footer {
            text-align: center;
            margin-top: 40px;
        }

        .submit-btn {
            background-color: var(--main-color);
            color: white;
            border: none;
            padding: 15px 60px;
            font-size: 18px;
            font-weight: 800;
            border-radius: 35px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(240, 194, 200, 0.3);
        }

        .submit-btn:hover {
            background-color: var(--font2-color);
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(147, 103, 82, 0.2);
        }

    </style>
</head>
<body>
    <?php include 'include/header.php'?>

   <div class="content-container">
        <div class="back-section">
            <a href="homepage.php" class="back-link">
                <i class="bi bi-chevron-left"></i> Back
            </a>
        </div>

        <div class="custom-form-wrapper">
            <header class="page-header">
                <img class="logo" src="image/cakeology logo.png" alt="Logo">
                <h1>Join Cakeology Today</h1>
                <p class="subtitle">Craft Your Own Cake Experience. Every detail, tailored to you</p>
            </header>

            <div class="custom-form-container">
                <div class="form-header">
                    <h2>Customised Cake Inquiry</h2>
                    <p>Fill in the details below and we will provide a quote for your dream cake!</p>
                </div>

                <form action="submit_custom_request.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="form-grid">
                        <div class="field-group">
                            <label>Recipient Name <span>*</span></label>
                            <input type="text" name="RECIPIENT_NAME" placeholder="Full Name" required>
                        </div>
                        <div class="field-group">
                            <label>Recipient Email <span>*</span></label>
                            <input type="email" name="RECIPIENT_EMAIL" placeholder="example@mail.com" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                          <label>Recipient Phone <span>*</span></label>
                          <div style="display: flex; gap: 10px;">
                              <select name="COUNTRY_CODE" class="custom-select" style="flex: 0 0 100px; padding-right: 10px;">
                                  <option value="+60">+60</option>
                              </select>
                              <input type="text" name="PHONE_NUMBER" placeholder="12-3456789" required>
                          </div>
                        </div>
                        <div class="field-group">
                            <label>Delivery Date <span>*</span></label>
                            <input type="datetime-local" name="DELIVERY_DATE" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label>Cater Count (Pax) <span>*</span></label>
                            <input type="text" name="CATER_COUNT" placeholder="e.g. 20" required>
                        </div>
                        <div class="field-group">
                            <label>Cake Size / Weight</label>
                            <input type="text" name="SIZE" placeholder="e.g. 6 inch / 1kg">
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Ideal Cake Flavour</label>
                        <input type="text" name="IDEAL_FLAVOUR" placeholder="e.g. Earl Grey / Belgian Chocolate">
                    </div>

                    <div class="field-group">
                        <label>Delivery Address <span>*</span></label>
                        <textarea name="RECIPIENT_ADDR" rows="2" placeholder="Full delivery address" required></textarea>
                    </div>

                    <div class="field-group">
                        <label>Custom Design Description <span>*</span></label>
                        <textarea name="CUSTOM_DES" rows="4" placeholder="Describe themes, colors, or wording..." required></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label>Cake Style (Select one) <span>*</span></label>
                            <select name="CAKE_STYLE" class="custom-select" required>
                                <option value="" disabled selected>Choose a style...</option>
                                <option value="1 Tier">1 Tier Cake</option>
                                <option value="2 Tier">2 Tier Cake</option>
                                <option value="3 Tier">3 Tier Cake</option>
                                <option value="Corporate">Corporate</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Your Budget (Optional)</label>
                            <input type="text" step="0.01" name="BUDGET" placeholder="RM 0.00">
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Reference Image (Design)</label>
                        <div class="custom-upload-card">
                            <input type="file" id="REF_IMAGE" name="REF_IMAGE" hidden accept="image/*">
                            <label for="REF_IMAGE" class="upload-inner">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span id="file-name">Click to upload reference image</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="submit" name="submit_request" class="submit-btn">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'include/footer.php'?>

<script>
    document.getElementById('REF_IMAGE').onchange = function () {
            const fileName = this.files[0] ? this.files[0].name : "Click to upload reference image";
            document.getElementById('file-name').innerHTML = fileName;
        };
        
</script>
</body>
</html>