<?php

  include('config/dbConnect.php');

  $password = $passwordReenter = $selector = $validator = '';
  $errors = array('password' => '', 'passwordReenter' => '', 'selector' => '', 'validator' => '');

  if(isset($_POST['submit'])) {

    $selector = $_POST['selector'];
    $validator = $_POST['validator'];

    $_POST['password'] = trim($_POST['password']);
		if(empty($_POST['password'])) {
			$errors['password'] = 'An password is required';
		} else {
			$password = $_POST['password'];
			if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
				$errors['password'] = 'Password must be at least 8 characters long and alphanumeric';
			}
		}

		$_POST['passwordReenter'] = trim($_POST['passwordReenter']);
		if(empty($_POST['passwordReenter'])) {
			$errors['passwordReenter'] = 'You must re-enter your password';
		} else {
			$passwordReenter = $_POST['passwordReenter'];
			if($passwordReenter !== $password) {
				$errors['passwordReenter'] = 'The passwords you entered must match';
			}
		}

    $currentDate = date('U');

    $sql = "SELECT * FROM password_reset WHERE password_reset_selector = ? AND password_reset_expires >= ?;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)) {
      trigger_error("SQL error");
    } else {
      mysqli_stmt_bind_param($stmt, 'ss', $selector, $currentDate);
      mysqli_stmt_execute($stmt);

      $result = mysqli_stmt_get_result($stmt);
      if(!$row = mysqli_fetch_assoc($result)) {
        echo 'selector error';
        $errors['selector'] = 'Unable to validate reset request. Please resubmit your reset request';
      } else {

        $tokenBin = hex2bin($validator);
        $tokenCheck = password_verify($tokenBin, $row['password_reset_token']);

        if(!$tokenCheck) {
          echo 'validator error';
          $errors['validator'] = 'Unable to validate reset request. Please resubmit your reset request';
        }

      }
    }

    if(!array_filter($errors)) {
      $tokenEmail = $row['password_reset_email'];

      $sql = "SELECT * FROM member_data WHERE email = ?;";

      $stmt = mysqli_stmt_init($conn);
      if(!mysqli_stmt_prepare($stmt, $sql)) {
        trigger_error("SQL error");
      } else {
        mysqli_stmt_bind_param($stmt, 's', $tokenEmail);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if(!$row = mysqli_fetch_assoc($result)) {
          trigger_error('There was an error');
        } else {
          $sql = "UPDATE member_data SET password = ? WHERE email = ?;";

          $stmt = mysqli_stmt_init($conn);
          if(!mysqli_stmt_prepare($stmt, $sql)) {
            trigger_error("SQL error");
          } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, 'ss', $hashedPassword, $tokenEmail);
            mysqli_stmt_execute($stmt);

            $sql = "DELETE FROM password_reset WHERE password_reset_email = ?;";
            // Initiating connection to db
            $stmt = mysqli_stmt_init($conn);
            // Preparing statement
            if(!mysqli_stmt_prepare($stmt, $sql)) {
              trigger_error("SQL error");
            } else {
              // Subbing the ? for actual data
              mysqli_stmt_bind_param($stmt, 's', $tokenEmail);
              mysqli_stmt_execute($stmt);

              // Writing a log to the updateMember file that a user's password has been updated
      				date_default_timezone_set('America/Los_Angeles');
      				$file = 'logs/updateMember.txt';
      				$handle = fopen($file, 'a+');
      				fwrite($handle, date("h:i:sa") . " PST: " . $tokenEmail . " has updated their passoword." . "\n");
      				fclose($handle);

              header('Location: resetPassword.php?newpwd=passwordupdated');
            }
          }

        }
      }
    } else {
      $redirectError = 'http://localhost/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/createNewPassword.php?selector=' . $selector . '&validator=' . $validator . '&pwderr=' . $errors['password'] . '&pwdreerr=' . $errors['passwordReenter'] . '&selerr=' . $errors['selector'] . '&valerr=' . $errors['validator'];
      header("Location: $redirectError");
    }
  }

 ?>

<?php include('templates/header.php'); ?>

<section class="container grey-text content">
  <h4 class="center grey-text">Create new password</h4>

  <?php
    // Checking the tokens for correct values
    $selector = $_GET['selector'] ?? '';
    $validator = $_GET['validator'] ?? '';
    // Checking if the tokens are empty
    if(empty($selector) || empty($validator)) {
      echo '<h5 class="center red-text">Could not validate your request. Please reclick the link in your recovery email</h5>';
    } else {
      if(ctype_xdigit($selector) && ctype_xdigit($validator)) {
        if(isset($_GET['pwderr'])) {
          $passwordError = htmlspecialchars($_GET['pwderr']);
        }
        if(isset($_GET['pwdreerr'])) {
          $passwordReenterError = htmlspecialchars($_GET['pwdreerr']);
        }
        if(isset($_GET['selerr'])) {
          $selectorError = htmlspecialchars($_GET['selerr']);
        }
        if(isset($_GET['valerr'])) {
          $validatorError = htmlspecialchars($_GET['valerr']);
        }
        ?>

        <form class="white" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <input type="hidden" name="selector" value="<?php echo $selector; ?>">
          <div class="red-text"><?php echo $selectorError ?? ''; ?></div>
          <input type="hidden" name="validator" value="<?php echo $validator; ?>">
          <div class="red-text"><?php echo $validatorError ?? ''; ?></div>
          <label>New Password</label>
          <input type="password" name="password" placeholder="New Password">
          <div class="red-text"><?php echo $passwordError ?? ''; ?></div>
      		<input type="password" name="passwordReenter" placeholder="Re-enter New Password">
      		<div class="red-text"><?php echo $passwordReenterError ?? ''; ?></div>
      		<div class="center">
      			<input type="submit" name="submit" value="Reset password" class="btn brand z-depth-0">
      		</div>
        </form>

        <?php
      }
    }
   ?>

</section>

<?php include('templates/footer.php'); ?>
