<?php

  // Initializing default values depending on the type of member added
  if($validateType === 'member') {
    $name = $email = $grade = $hours = $number = $password = $passwordReenter = $authLevel = '';
    $errors = array('name' => '', 'email' => '', 'grade' => '', 'hours' => '', 'number' => '', 'password' => '', 'passwordReenter' => '', 'authLevel' => '');
  } elseif($validateType === 'advisor') {
    $name = $email = $password = $passwordReenter = '';
  	$errors = array('name' => '', 'email' => '', 'password' => '', 'passwordReenter' => '');
  }

  // Checks if the user hit the submit button and validates the input fields
  if(isset($_POST['submit'])) {

    // Check name
    if(empty($_POST['name'])) {
      $errors['name'] = 'A name is required';
    } else {
      $name = $_POST['name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors['name'] = 'Name must be letters and spaces only';
      }
    }

    // Check email
    $_POST['email'] = trim($_POST['email']);
    if(empty($_POST['email'])) {
      $errors['email'] = 'An email is required';
    } else {
      $email = $_POST['email'];
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email must be a valid email address';
      } else { // Checking if another member was already created with the entered email
        // Using ? as a placeholder for email
        $sql = "SELECT email FROM member_data WHERE email = ?;";

        // Accessing the database
        // Initializing sql statement
        $stmt = mysqli_stmt_init($conn);
        // Parsing the sql statement for errors
        if(!mysqli_stmt_prepare($stmt, $sql)) {
          trigger_error("SQL error");
        } else {
          // Executing the statement and storing the result in $stmt
          $email = mysqli_real_escape_string($conn, $email);
          mysqli_stmt_bind_param($stmt, 's', $email);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_store_result($stmt);

          // Checking how many rows already had the email
          $resultCheck = mysqli_stmt_num_rows($stmt);
          if($resultCheck > 0) {
            $errors['email'] = 'Email is already taken';
          }
        }
      }
    }

    // Check password
    $_POST['password'] = trim($_POST['password']);
    if(empty($_POST['password'])) {
      $errors['password'] = 'An password is required';
    } else {
      $password = $_POST['password'];
      if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $errors['password'] = 'Password must be at least 8 characters long and alphanumeric';
      }
    }

    // Check re-entered password
    $_POST['passwordReenter'] = trim($_POST['passwordReenter']);
    if(empty($_POST['passwordReenter'])) {
      $errors['passwordReenter'] = 'You must re-enter your password';
    } else {
      $passwordReenter = $_POST['passwordReenter'];
      if($passwordReenter !== $password) {
        $errors['passwordReenter'] = 'The passwords you entered must match';
      }
    }

    // Only validates these fields if the member is not an advisor
    if($validateType === 'member') {
      // Check grade
      if(empty($_POST['grade'])) {
        $errors['grade'] = 'An grade is required';
      } else {
        $grade = $_POST['grade'];
        if(!($grade >= 9 && $grade <= 12)) {
          $errors['grade'] = 'Grade must be a valid high school grade';
        }
      }

      // Check hours
      if(empty($_POST['hours'])) {
        $_POST['hours'] = 0;
      } else {
        $hours = $_POST['hours'];
        if($hours < 0) {
          $errors['hours'] = 'Number of hours must be a valid amount and a whole number';
        }
      }

      // Check student id
      if(empty($_POST['number'])) {
        $errors['number'] = 'A student number is required';
      } else {
        $number = $_POST['number'];
        if(strlen($number) != 6) {
          $errors['number'] = 'ID must be a valid 6-digit number';
        }
        if(!preg_match('/^[0-9]*$/', $number)) {
          $errors['number'] = 'ID can only contain numbers';
        }
      }

      // Check authorization level
      if(empty($_POST['authLevel'])) {
        $errors['authLevel'] = 'An authorization level is required';
      } else {
        $authLevel = $_POST['authLevel'];
      }
    }

    // Sets values in database if there are no errors
    if(!array_filter($errors)) {
      // Escape sql chars to prevent injections
      $name = mysqli_real_escape_string($conn, $_POST['name']);
      $email = mysqli_real_escape_string($conn, $_POST['email']);

      // Hashing the passwork for security with bcrypt (most secure, always updated after every breach)
      $password = mysqli_real_escape_string($conn, $_POST['password']);
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      if($validateType === 'member') {
        $grade = mysqli_real_escape_string($conn, $_POST['grade']);
        $hours = mysqli_real_escape_string($conn, $_POST['hours']);
        $number = mysqli_real_escape_string($conn, $_POST['number']);
        $authLevel = mysqli_real_escape_string($conn, $_POST['authLevel']);
      } elseif($validateType === 'advisor') { // Sets default values for the advisor
        $grade = 0;
        $hours = 0;
        $number = 0;
        $authLevel = 'advisor';
      }

      // Create sql
      $sql = "INSERT INTO member_data(name, email, grade, hours, number, password, auth_level) VALUES('$name', '$email', '$grade', '$hours', '$number', '$hashedPassword', '$authLevel')";

      // Save to db and check
      if(mysqli_query($conn, $sql)){

        // Writing a log to the addMember file that a new advisor has been added
        date_default_timezone_set('America/Los_Angeles');
        $file = 'logs/addMember.txt';
        $handle = fopen($file, 'a+');
        fwrite($handle, date("h:i:sa") . " PST: " . ucfirst($authLevel) . " " . $name . " has been added." . "\n");
        fclose($handle);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header('Location: index.php');
      } else {
        echo 'query error: '. mysqli_error($conn);
      }

    }

  } // End POST check

 ?>
