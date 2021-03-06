<?php

  // Connect to database
  include('config/dbConnect.php');

  // Setting default values for the email and password along with their erros
  $email = $password = '';
  $errors = array('email' => '', 'password' => '');

  if(isset($_POST['login-submit'])) {

    // Setting the email and password to the form values
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Checks if the email is registered to a user in the database
    if(empty($email)) {
      $errors['email'] = 'An email is required';
    } else {
      $sql = "SELECT * FROM member_data WHERE email = ?;";

      $stmt = mysqli_stmt_init($conn);

      if(!mysqli_stmt_prepare($stmt, $sql)) {
        trigger_error("SQL error");
      } else {
        $email = mysqli_real_escape_string($conn, $email);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Checks if result is assigned a value and assigns it to $row (assoc array)
        if($row = mysqli_fetch_assoc($result)) {
          if(empty($password)) {
            $errors['password'] = 'A password is required';
          } else {
            // Verifies the password that the user entered against the database (hashes the entered password and compares it with the password in the database)
            $pwdCheck = password_verify($password, $row['password']);

            // If password is correct, redirect user to the homepage
            if($pwdCheck) {
              session_start();
              $_SESSION['loginStatus'] = true;
              $_SESSION['id'] = $row['id'];
              $_SESSION['authLevel'] = $row['auth_level'];
              $_SESSION['name'] = $row['name'];

              header('Location: index.php');
            } else {
              $errors['password'] = 'The password you entered is incorrect';
            }
          }
        } else {
          $errors['email'] = 'No such member exists with this email';
        }
      }
    }
  }

 ?>

<?php include('templates/header.php'); ?>

<!-- Login form -->
<section class="container grey-text content">
  <h4 class="center grey-text">Login</h4>
  <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
    <label>Your Email</label>
    <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <div class="red-text"><?php echo $errors['email']; ?></div>
    <label>Your Password</label>
    <input type="password" name="password">
    <div class="red-text"><?php echo $errors['password']; ?></div>
    <a href="resetPassword.php">Forgot your password?</a>
    <div class="center">
      <input type="submit" name="login-submit" value="Submit" class="btn brand z-depth-0">
    </div>
  </form>
</section>

<?php include('templates/footer.php'); ?>
