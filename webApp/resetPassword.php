<?php

  // Connect to database
  include('config/dbConnect.php');
  // Initialize PHPMailer object
  include('config/emailObjectInit.php');

  $errorEmail = '';

  // Send the recovery email after validating that the email is registered in the database
  if(isset($_POST['reset-password-submit'])) {

    // Setting the email to the form values
    $userEmail = $_POST['email'];
    $userEmail = mysqli_real_escape_string($conn, $userEmail);

    if(empty($userEmail)) {
      $errorEmail = 'Email field cannot be left empty';
    } else {
      // Queries the database and checks if the entered email is registered in the database
      $sql = 'SELECT email FROM member_data WHERE email = ?;';
      $stmt = mysqli_stmt_init($conn);

      if(!mysqli_stmt_prepare($stmt, $sql)) {
        trigger_error('SQL error');
      } else {
        mysqli_stmt_bind_param($stmt, 's', $userEmail);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $resultCheck = mysqli_stmt_num_rows($stmt);
        
        if($resultCheck < 1) {
          $errorEmail = 'No such member exists with this email';
        } else {
          // 2 tokens (one to authenticate user, the other to cross reference against the user in the database)
          $selector = bin2hex(random_bytes(8));
          $token = random_bytes(32);

          // Creating link to website for password recovery with the tokens
          $url = 'http://localhost/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/createNewPassword.php?selector=' . $selector . '&validator=' . bin2hex($token);

          // Setting the expiration of the tokens to 30 minutes
          $expires = date('U') + 900;

          // Deleting previously set tokens for a user
          $sql = "DELETE FROM password_reset WHERE password_reset_email = ?;";
          // Initiating connection to db
          $stmt = mysqli_stmt_init($conn);
          // Preparing statement
          if(!mysqli_stmt_prepare($stmt, $sql)) {
            trigger_error("SQL error");
          } else {
            // Subbing the ? for actual data
            mysqli_stmt_bind_param($stmt, 's', $userEmail);
            mysqli_stmt_execute($stmt);
          }

          $sql = "INSERT INTO password_reset (password_reset_email, password_reset_selector, password_reset_token, password_reset_expires) VALUES (?, ?, ?, ?);";
          // Initiating connection to db
          $stmt = mysqli_stmt_init($conn);
          // Preparing statement
          if(!mysqli_stmt_prepare($stmt, $sql)) {
            trigger_error("SQL error");
          } else {
            // Subbing the ? for actual data
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, 'ssss', $userEmail, $selector, $hashedToken, $expires);
            mysqli_stmt_execute($stmt);
          }

          mysqli_stmt_close($stmt);
          mysqli_close($conn);

          // Sending the user the recovery email
          $to = $userEmail;
          $subject = 'Reset your password for CHS FBLA member database app';
          $body = "
            <body style='background-color: #F5F5F5'>
              <h1 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>CHS FBLA Password Reset:</h1>
              <p style='color: black; font-size: 14px; font-family: 'Verdana, Geneva, sans-serif''>
                We received a password reset request. The link to reset your password is below.
                If you did not make this request, you can ignore this email.
              </p>
              <b>
                YOU HAVE 30 MINUTES TO RESET THE EMAIL USING THIS LINK
              </b>
              <hr />
              <p style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>
                Here is your password reset link: <br /><br />
                <a style='color: #2d5fad; font-family: 'Verdana, Geneva, sans-serif'' href='$url'>$url</a>
              </p>
            </body>
          ";

          $mail->Subject = $subject;
          $mail->setFrom('fbla.candp.centennial@gmail.com');
          $mail->Body = $body;
          $mail->addAddress($to);
          $mail->Send();

          // Closing the connection to the email host
          $mail->smtpClose();

          header('Location: resetPassword.php?reset=success');
        }
      }
    }
  }

 ?>

<?php include('templates/header.php'); ?>

<section class="container grey-text content">
  <h4 class="center grey-text">Reset your password</h4>
  <p class="center">An email will be sent to your inbox with instructions on how to reset your password</p>
  <?php
    if(isset($_GET['newpwd'])) {
      if($_GET['newpwd'] === 'passwordupdated') {
        echo '<h5 class="center brand-text">Password has been successfully updated</h5>';
      }
    }
   ?>
  <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
    <label>Your Email</label>
    <input type="text" name="email" placeholder="Enter address for your recovery email">
    <div class="red-text"><?php echo $errorEmail; ?></div>
    <div class="center">
      <input type="submit" name="reset-password-submit" value="Send recovery email" class="btn brand z-depth-0">
    </div>
  </form>

  <?php
    if(isset($_GET['reset'])) {
      if($_GET['reset'] === 'success') {
        echo '<h5 class="center brand-text">The recovery email has been sent. Check your email</h4>';
      }
    }
   ?>
</section>

<?php include('templates/footer.php'); ?>
