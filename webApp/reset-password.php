<?php

  // Add connection to database
  include('config/db_connect.php');

  // Configuration for PHPMailer
  require 'phpMailer/includes/PHPMailer.php';
  require 'phpMailer/includes/Exception.php';
  require 'phpMailer/includes/SMTP.php';

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;

  // Initializing PHPMailer object
  $mail = new PHPMailer();

  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = 'true';
  $mail->SMTPSecure = 'tls';
  $mail->Port = '587';
  $mail->Username = 'fbla.candp.centennial@gmail.com';
  $mail->Password = 'Minhhongan';
  $mail->isHTML(true);

  $errorEmail = '';

  if(isset($_POST['reset-password-submit'])) {

    $userEmail = $_POST['email'];
    $userEmail = mysqli_real_escape_string($conn, $userEmail);

    if(empty($userEmail)) {
      $errorEmail = 'Email field cannot be left empty';
    } else {
      $sql = 'SELECT email FROM member_data WHERE email = ?;';
      $stmt = mysqli_stmt_init($conn);

      if(!mysqli_stmt_prepare($stmt, $sql)) {
        trigger_error('SQL error');
      } else {
        mysqli_stmt_bind_param($stmt, 's', $userEmail);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $resultCheck = mysqli_stmt_num_rows($stmt);
        echo $resultCheck;
        if($resultCheck < 1) {
          $errorEmail = 'No such member exists with this email';
        } else {
          // 2 tokens (one to authenticate user, the other to cross reference against the user in the database)
          $selector = bin2hex(random_bytes(8));
          $token = random_bytes(32);

          // Creating link to website for password recovery with the tokens
          $url = 'http://localhost/FBLA_App/StudentDB/webApp/create-new-password.php?selector=' . $selector . '&validator=' . bin2hex($token);

          // Setting the expiration of the tokens to 30 minutes
          $expires = date('U') + 900;

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

          $to = $userEmail;
          $subject = 'Reset your passoword for CHS FBLA member database app';
          $body = "
            <p>
              We received a password reset request. The link to reset your password is below.
              If you did not make this request, you can ignore this email.
            </p>
            <p>
              Here is your password reset link: <br />
              <a href='$url'>$url</a>
            </p>
          ";

          $mail->Subject = $subject;
          $mail->setFrom('fbla.candp.centennial@gmail.com');
          $mail->Body = $body;
          $mail->addAddress($to);
          $mail->Send();

          // Closing the connection to the email host
          $mail->smtpClose();

          header('Location: reset-password.php?reset=success');
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
