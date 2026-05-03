<?php include 'include/config.php';
session_start();

if(isset($_SESSION['CUSTOMER_ID'])){
    header("Location: homepage.php");
    exit();
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'include/vendor/Exception.php';
require 'include/vendor/PHPMailer.php';
require 'include/vendor/SMTP.php';



$registrationMessage = "";
$messageType = "error";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    //verify whether user is existing
    $checkUser = "SELECT CUSTOMER_ID FROM customer WHERE EMAIL = '$email' AND CUSTOMER_NAME = '$full_name' LIMIT 1";
    $result = mysqli_query($conn,$checkUser);

    if(mysqli_num_rows($result)>0){
        $user = mysqli_fetch_assoc($result);
        $customer_id = $user['CUSTOMER_ID'];

        //generate unique token
        $token = bin2hex(random_bytes(32));

        //setup date (add 10m in current time)
        $expires_at = date("Y-m-d H:i:s", strtotime('+10 minutes') );
        $created_at = date("Y-m-d H:i:s");

        mysqli_query($conn, "DELETE FROM pass_reset WHERE CUSTOMER_ID = '$customer_id'");

        $insertSql = "INSERT INTO pass_reset (CUSTOMER_ID, RESET_EMAIL, TOKEN, EXPIRES_AT, CREATED_AT)
        VALUES ('$customer_id','$email','$token','$expires_at','$created_at')";

        if(mysqli_query($conn, $insertSql)){
            $resetLink = "http://yungsinwong.infinityfree.me/password reset.php?token=" . $token;

            //email sent
            $mail = new PHPMailer(true);

            try {
                // 1. server configuration
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.gmail.com';                     
                $mail->SMTPAuth   = true;                                   
                $mail->Username   = 'wongyungsin04@gmail.com'; // sender's email
                $mail->Password   = 'jfim afvt zusc vqwg'; // google application password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
                $mail->Port       = 587;                                    

                // 2. setting sent and receive
                $mail->setFrom('wongyungsin04@gmail.com', 'Cakeology (No-Reply)');
                $mail->addAddress($email, $full_name); 

                // 3. email content
                $mail->isHTML(true);                                  
                $mail->Subject = 'Password Reset Request - Cakeology';
                $mail->Body    = "Hi $full_name,<br><br>
                                  You requested to reset your password. Please click the link below:<br>
                                  <a href='$resetLink'>$resetLink</a><br><br>
                                  This link will expire in 10 minutes.";
                $mail->AltBody = "Hi $full_name, Please use this link to reset your password: $resetLink";

                $mail->send();
                $registrationMessage = "Reset link has been sent to your email. Please check it.";
                $messageType = "success";
            } catch (Exception $e) {
                // if email cannot be sent, display the error message
                $registrationMessage = "Email could not be sent. Error: {$mail->ErrorInfo}";
                $messageType = "error";
            }

        }


    }else {
        $registrationMessage = "No user found with this name and email.";
    }

}   

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password Page</title>
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

      .page-wrapper{
        width:100%;
        max-width:700px;
        padding:40px 20px;
        position:relative;
        text-align:center;
        margin:0 auto;
      }

        /* back section */
      .back-section {
        display: flex;
        align-items: center;
        margin: 30px 0 30px 20px;

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

       main{
        
      }

      .page-header{
        margin-bottom:30px;
        background-color: var(--bg-color);
        margin-left:-50px;
       
      }

      .logo{
        width:100px;
        height:100px;
        margin-bottom:20px;
        border-radius:50%;
        margin-left: 20px;
      }

      .page-header h1{
        font-size:28px;
        margin:0 0 10px 0;
        font-weight:700;
        color:var(--font2-color);
        font-family: 'Pacifico', cursive;
      }

      /* form */


      .signup-container{
        background-color:#fff9ef;
        border-radius: 30px;
        border:2px solid var(--search-border-color);
        padding:40px 25px;
        box-shadow: 0 4px 15px rgba(209, 184, 165, 0.2);
        width:600px;
      }

      .signup-container h2{
        color:var(--font2-color);
        font-weight:bold;
      }

      .login-prompt{
        color:var(--font2-color);
        margin: 20px 0 25px 0;
      }

      .login-prompt a, .success-message a{
        margin-left:10px;
        color:#38b6ff;
      }

      /* --- Message hint display--- */
      .error-message, .success-message {
          padding: 12px 15px;
          border-radius: 6px;
          margin-bottom: 25px;
          font-size: 14px;
          display: flex;
          align-items: center;
      }

      .error-message {
          background-color: #fff2f2;
          color: #d93025;
          border: 1px solid #ffcfcf;
      }

      .success-message {
          background-color: #f1f8e9;
          color: #388e3c;
          border: 1px solid #c8e6c9;
      }

      .form-group{
        text-align:left;
        margin-bottom:15px;
        color:var(--font2-color);
        font-weight:bold;
      }

      .form-label{
        display:block;
        font-size:15px;
        font-weight:700;
        margin-bottom:6px;
        margin-left:5px;
      }

      .form-input{
        width:100%;
        padding: 10px 15px;
        border:1px solid var(--search-border-color);
        border-radius:10px;
        background-color: transparent;
        color:var(--font2-color);
        font-size:13px;
        box-sizing:border-box;
      }

      .form-input::placeholder {
        color:#bba299;
      }

      .hints{
        padding:0 20px;
        margin: 8px 0 0 5px;
        color:#bba299;
        font-size:11px;
        font-weight:100;
        display:none;
       
    }

     input[name="password"]:focus + .hints,
        input[name="password"]:focus ~ .hints {
            display: block;
      }

      .btn-reset{
        width:70%;
        padding: 12px;
        background-color: var(--main-color);
        color: var(--font2-color); 
        border: none;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 25px;
        margin-bottom: 25px;
        transition: background-color 0.5s;
      }

      .btn-reset:hover {
      background-color: #f8dbdf; 
      }

      .register-link{
        color:var(--font2-color);
        font:12px;
      }

      .back-signin{
        margin-top:20px;
      }

      .register-link a{
        color:#38b6ff;
      }

      /* password reset */

      .error-message, .success-message {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 14px;
        display: flex;
        flex-direction: column; 
        align-items: center; 
        line-height: 1.6;    
        text-align: center;
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
    
    <div class="page-wrapper">

      <div class="main-content">

        <main class="signup-container">
          <h2>Reset Password</h2>
          <p class="login-prompt">Enter your username and email to receive reset password email.</p>

          <!-- Message display -->
              <?php if (!empty($registrationMessage)): ?>
                  <div class="error-message">
                      <?php echo $registrationMessage; ?>
                  </div>
              <?php endif; ?>

            <form method="post" action="" class="register-form">
                 
                <div class="form-group">
                  <label class="form-label">Username : </label>
                  <input type="text" name="full_name" class="form-control form-input" placeholder="Enter your Full Name" required>
                </div>
          
                <div class="form-group">
                  <label class="form-label">Email :</label>
                  <input type="email" name="email" class="form-control form-input" placeholder="Enter your Email" required>
                </div>
              <p>
                <button type="submit" class="btn-reset">Sent Link</button>
              </p>

              <div class="register-link">
                    Remembered your password? <a href="login.php">Back to Login</a>
              </div> 
              <div class="register-link back-signin">
                    Do not have an account? <a href="sign-up.php">Sign Up</a>
              </div> 
            </form>
          
        </main>

      </div>
      
    </div>

    
    <?php include 'include/footer.php'?>
    <script>
    //get the error message box//
    const errorBox = document.querySelector('.error-message');
    
    // hide error message on input//
    document.querySelector('.register-form').addEventListener('input', function() {
        if (errorBox) {
            // hide error-message when user starts typing//
            errorBox.style.display = 'none';
        }
    });
  </script>
  </body>
</html>              
              
              
              
