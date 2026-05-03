<?php include 'include/config.php';
session_start();
?>

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
        --font2-color:#936752;
        --card-bg-color:#f9d9d9;
      }

      body {
        font-family: 'Quicksand', sans-serif;
        background-color: var(--bg-color);
        margin: 0;
        padding: 0;
      }

      main{
        min-height: 300vh;
        
      }

      /* back section */
      .back-section {
        display: flex;
        align-items: center;
        margin: 30px 0 10px 0;
        padding: 0 10px;
        position:relative;
        z-index:10;
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

    /* profile section */
    .user-profile-section {
    width: 100%;
    max-width: 1200px; 
    margin: 50px auto;
    padding: 0 20px;
    }

    .profile-card {
      margin-right:50px;
      display: flex;
      align-items: center;
      gap: 30px; 
      background-color: #fff;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(101, 54, 31, 0.05); 
    }

    .profile-avatar img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--main-color);
    display: block;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }

    .profile-name {
        color: var(--font-color);
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .level-tag {
        background-color: var(--secondary-color);
        color: var(--font2-color);
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 13px;
        border: 1px solid var(--main-color);
        font-weight: 600;
    }

    .profile-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--font2-color);
        font-size: 15px;
    }

    .meta-item i {
        color: var(--main-color);
        font-size: 18px;
    }

    .meta-item a {
        color: var(--font2-color);
        text-decoration: none;
        transition: 0.3s;
    }

    .meta-item a:hover {
        color: var(--main-color);
        text-decoration: underline;
    }

    .topup-link {
        font-size: 13px;
        margin-left: 5px;
    }
    
    /* action cards */
    .personalize-cards{
      margin: 40px auto;
    }

    .action-card{
      background-color: white;
      border: 3px solid var(--card-bg-color); 
      border-radius:20px;
      padding: 20px 10px;
      text-align:center;
      box-shadow:0 5px 20px rgba(101, 54, 31, 0.05);
      transition:0.5s;
      width:250px;
      height:150px;
      margin-top:0px;
    }

    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(240, 194, 200, 0.3);
    }

    .action-card i{
      font-size:30px;
      margin-bottom: 10px;
      color:var(--font2-color);
    }

    .card-title{
      font-size:14px;
      color:var(--font2-color);
      font-weight:bold;
      height: 40px;
      display: flex;
        align-items: center;
        justify-content: center;
    }

    .arrow-btn{
      width:25px;
      height:50px;
      background-color: var(--main-color);
      color:white;
      border-radius: 50%;
      display:flex;
      align-items:center;
      justify-content: center;
      text-decoration: none;
      margin: 0 auto;
      padding: 0;
      transition:0.5s;
    }


    .arrow-btn i{
      font-size:10px;
      margin:0 ;
      padding:0;
      line-height: 1; /*used to center the icon*/
    }


    /* Profile Detail Sections */
    .detail-section-container{
      margin:100px 150px 20px 100px;
    }

    .detail-section {
        max-width: 1500px;
        margin: 20px auto 40px auto ;
        padding: 0 15px;
    }


    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 28px;
        color: var(--font2-color);
        margin: 0;
        font-weight: bold;
    }

    .edit-link {
        color: var(--font2-color);
        text-decoration: underline;
        font-size: 14px;
    }

    .content-detail {
        color: var(--font2-color);
        line-height: 1.8;
    }

    .form-control:focus {
    border-color: var(--main-color);
    box-shadow: 0 0 0 0.25rem rgba(240, 194, 200, 0.25);
}

.modal-backdrop.show {
    opacity: 0.4;
}

/* hover */
.modal-footer .btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    transition: 0.3s;
}

/*change profile pic*/
label[for="avatarUpload"]:hover {
    background-color: var(--secondary-color) !important;
    transition: 0.3s;
}
    
  </style>
</head>
<body>
  <header>
    <?php include 'include/header.php';?>
  </header>

 <div class="container-fluid px-4 mt-3">
    <div class="back-section">
        <a href="homepage.php" class="back-link">
          <i class="bi bi-chevron-left"></i>Back
        </a>
      </div>
  </div>


  <main>
    <!-- background area -->


    <!-- user information -->
    <div class="user-profile-section">
      <div class="profile-card">
        <div class="profile-avatar">
          <img src="image/user image/user_default.jpg" alt="User Image">
        </div>

        <div class="profile-details">
            <div class="profile-header">
              <h3 class="profile-name">Wong Yung Sin</h3>
              <span class="level-tag">Bronze Member</span>
            </div>
            
            <div class="profile-meta">
                <div class="meta-item">
                    <i class="bi bi-gem"></i>
                    <a href="membership.php">Membership Benefits</a>
                </div>
                <div class="meta-item">
                    <i class="bi bi-wallet2"></i>
                    <span>Balance: <strong>RM 0.00</strong></span>
                    <a href="#" class="topup-link">(Top-up)</a>
                </div>
            </div>
        </div>
      </div>
    </div>

    <!-- card section -->
    <div class="container personalize-cards">
        <div class="row row-cols-2 row-cols-md-4 g-3"> <div class="col">
                <div class="card action-card">
                    <i class="bi bi-person-circle"></i>
                    <h4 class="card-title">My Custom Request</h4>
                    <a href="CustomiseRequest.php" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
            <div class="col">
                <div class="card action-card">
                    <i class="bi bi-heart-fill"></i>
                    <h4 class="card-title">Wishlist</h4>
                    <a href="Wishlist.php" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
            <div class="col">
                <div class="card action-card">
                    <i class="bi bi-cart-fill"></i>
                    <h4 class="card-title">Shopping Cart</h4>
                    <a href="#" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
            <div class="col">
                <div class="card action-card">
                    <i class="bi bi-check-circle-fill"></i>
                    <h4 class="card-title">Order History</h4>
                    <a href="#" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- profile and address section -->
    <div class="detail-section-container">
      <div class="detail-section">
        <div class="section-header">
            <h1 class="section-title">Profile</h1>
            <a href="javascript:void(0)" class="edit-link" data-bs-toggle="modal" data-bs-target="#editProfileModal" class="edit-link">Edit Profile</a>
        </div>
        <div class="content-detail">
            <p>Join Date: xx-xx-xx</p>
        </div>
      </div>

      <div class="detail-section">
        <div class="section-header">
            <h1 class="section-title">Address</h1>
            <a href="javascript:void(0)" class="edit-link" data-bs-toggle="modal" data-bs-target="#editAddressModal" 
            class="edit-link">Edit Address</a>
        </div>
        <div class="content-detail">
            <p>Default Address:</p>
            <p>xxx, Jalan xxx, Taman xxx</p>
        </div>
      </div>
    </div>
    
  </main>

  <!-- edit profile pop up modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 20px; border: none; background-color: var(--bg-color);">
      
      <div class="modal-header" style="border-bottom: 1px solid #ddd;">
        <h5 class="modal-title username" id="editProfileModalLabel">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <form id="profileForm">

        <!-- change profile pic -->
          <div class="mb-4 text-center">
            <div class="position-relative d-inline-block">
                <img id="previewAvatar" src="image/user image/user_default.jpg" 
                    alt="Avatar Preview" 
                    style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--main-color);">
                
                <label for="avatarUpload" class="position-absolute bottom-0 end-0 bg-white shadow-sm d-flex align-items-center justify-content-center" 
                      style="width: 32px; height: 32px; border-radius: 50%; cursor: pointer; border: 1px solid #ddd;">
                    <i class="bi bi-camera-fill" style="color: var(--font2-color); font-size: 16px;"></i>
                </label>
                
                <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
            </div>
            <p class="mt-2" style="font-size: 12px; color: #888;">Click the camera icon to change photo</p>
          </div>

          <!-- form change -->
          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Username</label>
            <input type="text" class="form-control" v placeholder="Change your username"
            style="border-radius: 10px; border: 1px solid var(--main-color);">
          </div>
          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Email</label>
            <input type="email" class="form-control" placeholder="user@example.com"
             style="border-radius: 10px; border: 1px solid var(--main-color);">
          </div>
          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Phone</label>
            <input type="text" class="form-control"  placeholder="xxx-xxxx-xxxxx"
            style="border-radius: 10px; border: 1px solid var(--main-color);">
          </div>
        </form>

        <form id="profileForm">
          <!-- password  -->
           <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Change Password</label>
            <input type="password" class="form-control"  placeholder="Enter your new password"
            style="border-radius: 10px; border: 1px solid var(--main-color);">
          </div>

          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Confirm Password</label>
            <input type="password" class="form-control"  placeholder="Confirm your new password"
            style="border-radius: 10px; border: 1px solid var(--main-color);">
          </div>
        </form>
      </div>

      <div class="modal-footer" style="border-top: none;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Cancel</button>
        <button type="button" class="btn" style="background-color: var(--main-color); color: white; border-radius: 20px; padding: 6px 25px;">Save Changes</button>
      </div>

    </div>
  </div>
</div>

    <!-- address edit pop up modal -->
     <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content" style="border-radius: 20px; border: none; background-color: var(--bg-color);">
      
      <div class="modal-header" style="border-bottom: 1px solid #ddd;">
        <h5 class="modal-title username" id="editAddressModalLabel">Edit Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <form id="addressForm">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" style="color: var(--font2-color);">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" placeholder="First Name" required style="border-radius: 10px; border: 1px solid var(--main-color);">
            </div>
            <div class="col-md-6">
              <label class="form-label" style="color: var(--font2-color);">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" placeholder="Last Name" required style="border-radius: 10px; border: 1px solid var(--main-color);">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Phone Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="xxx-xxxx-xxxxx" required style="border-radius: 10px; border: 1px solid var(--main-color);">
          </div>

          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Company (Optional)</label>
            <input type="text" class="form-control" placeholder="Company name" style="border-radius: 10px; border: 1px solid var(--main-color);">
          </div>

          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">Address Line <span class="text-danger">*</span></label>
            <textarea class="form-control" placeholder="Street name, Unit, Building" required style="border-radius: 10px; border: 1px solid var(--main-color);"></textarea>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" style="color: var(--font2-color);">City <span class="text-danger">*</span></label>
              <input type="text" class="form-control" placeholder="City" required style="border-radius: 10px; border: 1px solid var(--main-color);">
            </div>
            <div class="col-md-6">
              <label class="form-label" style="color: var(--font2-color);">Postcode <span class="text-danger">*</span></label>
              <input type="text" class="form-control" placeholder="Postcode" required style="border-radius: 10px; border: 1px solid var(--main-color);">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" style="color: var(--font2-color);">State <span class="text-danger">*</span></label>
            <select class="form-select" required style="border-radius: 10px; border: 1px solid var(--main-color);">
              <option selected disabled value="">Choose your state...</option>
              <option>Johor</option>
              <option>Kedah</option>
              <option>Kelantan</option>
              <option>Malacca</option>
              <option>Negeri Sembilan</option>
              <option>Pahang</option>
              <option>Penang</option>
              <option>Perak</option>
              <option>Perlis</option>
              <option>Sabah</option>
              <option>Sarawak</option>
              <option>Selangor</option>
              <option>Terengganu</option>
              <option>Kuala Lumpur</option>
            </select>
          </div>
        </form>
      </div>

      <div class="modal-footer" style="border-top: none;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 20px;">Cancel</button>
        <button type="submit" form="addressForm" class="btn" style="background-color: var(--main-color); color: white; border-radius: 20px; padding: 6px 25px;">Save Address</button>
      </div>

    </div>
  </div>
</div>

  <!-- footer -->
  <footer>
     <?php include 'include/footer.php'?>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 头像即时预览逻辑
document.getElementById('avatarUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // 检查文件是否为图片
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            // 将预览图的 src 改为选中的图片
            document.getElementById('previewAvatar').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});
    </script>
</body>
</html>