<?php

	include('config/db_connect.php');

	$name = $email = $password = $password_reenter = '';
	$errors = array('name' => '', 'email' => '', 'password' => '', 'password_reenter' => '');

	if(isset($_POST['submit'])) {

		if(empty($_POST['name'])) {
			$errors['name'] = 'A name is required';
		} else {
			$name = $_POST['name'];
			if(!preg_match('/^[a-zA-Z\s]+$/', $name)) {
				$name['name'] = 'Name must be letters and spaces only';
			}
		}

		$_POST['email'] = trim($_POST['email']);
		if(empty($_POST['email'])) {
			$errors['email'] = 'An email is required';
		} else {
			$email = $_POST['email'];
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors['email'] = 'Email must be a valid email address';
			} else {
				$sql = "SELECT email FROM member_data WHERE email = ?;";

				$stmt = mysqli_stmt_init($conn);

				if(!mysqli_stmt_prepare($stmt, $sql)) {
					trigger_error("SQL error");
				} else {
					$email = mysqli_real_escape_string($conn, $email);
					mysqli_stmt_bind_param($stmt, 's', $email);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);

					$resultCheck = mysqli_stmt_num_rows($stmt);
					if($resultCheck > 0) {
						$errors['email'] = 'Email is already taken';
					}
				}
			}
		}

		$_POST['password'] = trim($_POST['password']);
		if(empty($_POST['password'])) {
			$errors['password'] = 'An password is required';
		} else {
			$password = $_POST['password'];
			if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
				$errors['password'] = 'Password must be at least 8 characters long and alphanumeric';
			}
		}

		$_POST['password_reenter'] = trim($_POST['password_reenter']);
		if(empty($_POST['password_reenter'])) {
			$errors['password_reenter'] = 'You must re-enter your password';
		} else {
			$password_reenter = $_POST['password_reenter'];
			if($password_reenter !== $password) {
				$errors['password_reenter'] = 'The passwords you entered must match';
			}
		}

	  $grade = 0;
    $hours = 0;
    $number = 0;
    $auth_level = 'advisor';

		if(!array_filter($errors)) {
			$email = mysqli_real_escape_string($conn, $_POST['email']);

			$password = mysqli_real_escape_string($conn, $_POST['password']);
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

			$name = mysqli_real_escape_string($conn, $_POST['name']);

			$sql = "INSERT INTO member_data(name,email,grade,hours,number,password,auth_level) VALUES('$name','$email','$grade','$hours','$number','$hashedPassword','$auth_level')";

			if(mysqli_query($conn, $sql)){

				// Writing a log to the addMember file that a new advisor has been added
				date_default_timezone_set('America/Los_Angeles');
				$file = 'logs/addMember.txt';
				$handle = fopen($file, 'a+');
				fwrite($handle, date("h:i:sa") . " PST: Advisor " . $name . " has been added." . "\n");
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

<?php include('templates/header.php'); ?>

<!-- Create the add form -->
<section class="container grey-text content">
	<h4 class="center">Add an Advisor</h4>
	<form class="white" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="off">
		<label>Member Name</label>
		<input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
		<div class="red-text"><?php echo $errors['name']; ?></div>
		<label>Member Email</label>
		<input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
		<div class="red-text"><?php echo $errors['email']; ?></div>
		<label>Member Password</label>
		<input type="password" name="password" placeholder="Password">
		<div class="red-text"><?php echo $errors['password']; ?></div>
		<input type="password" name="password_reenter" placeholder="Re-enter Password">
		<div class="red-text"><?php echo $errors['password_reenter']; ?></div>
		<div class="center">
			<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
		</div>
</section>

<?php include('templates/footer.php'); ?>
