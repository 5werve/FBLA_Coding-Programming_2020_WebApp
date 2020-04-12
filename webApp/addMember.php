<?php

	// Add connection to database
	include('config/db_connect.php');

	// Initializing default values
	$name = $email = $grade = $hours = $number = $password = $password_reenter = $auth_level = '';
	$errors = array('name' => '', 'email' => '', 'grade' => '', 'hours' => '', 'number' => '', 'password' => '', 'password_reenter' => '', 'auth_level' => '');

	// Checks if the user hit the submit button and validates the input fields
	if(isset($_POST['submit'])) {

		// Check name
		if(empty($_POST['name'])) {
			$errors['name'] = 'A name is required';
		} else {
			$name = $_POST['name'];
			if(!preg_match('/^[a-zA-Z\s]+$/', $name)) {
				$name['name'] = 'Name must be letters and spaces only';
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
		$_POST['password_reenter'] = trim($_POST['password_reenter']);
		if(empty($_POST['password_reenter'])) {
			$errors['password_reenter'] = 'You must re-enter your password';
		} else {
			$password_reenter = $_POST['password_reenter'];
			if($password_reenter !== $password) {
				$errors['password_reenter'] = 'The passwords you entered must match';
			}
		}

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
		if(empty($_POST['auth_level'])) {
			$errors['auth_level'] = 'An authorization level is required';
		} else {
			$auth_level = $_POST['auth_level'];
		}

		// Sets values in database if there are no errors
		if(!array_filter($errors)) {
			// Escape sql chars to prevent injections
			$email = mysqli_real_escape_string($conn, $_POST['email']);

			// Hashing the passwork for security with bcrypt (most secure, always updated after every breach)
			$password = mysqli_real_escape_string($conn, $_POST['password']);
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

			$name = mysqli_real_escape_string($conn, $_POST['name']);
			$grade = mysqli_real_escape_string($conn, $_POST['grade']);
			$hours = mysqli_real_escape_string($conn, $_POST['hours']);
			$number = mysqli_real_escape_string($conn, $_POST['number']);
			$auth_level = mysqli_real_escape_string($conn, $_POST['auth_level']);

			// Create sql
			$sql = "INSERT INTO member_data(name,email,grade,hours,number,password,auth_level) VALUES('$name','$email','$grade','$hours','$number','$hashedPassword','$auth_level')";

			// Save to db and check
			if(mysqli_query($conn, $sql)){

				// Writing a log to the addMember file that a new advisor has been added
				date_default_timezone_set('America/Los_Angeles');
				$file = 'logs/addMember.txt';
				$handle = fopen($file, 'a+');
				fwrite($handle, date("h:i:sa") . " PST: " . ucfirst($auth_level) . " " . $name . " has been added." . "\n");
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

<script type="text/javascript">
	$(document).ready(function() {
		$('select').material_select();
	});
</script>

<!-- Create the add form -->
<section class="container grey-text content">
	<h4 class="center">Add a Member</h4>
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
		<label>Member Grade</label>
		<input type="number" name="grade" value="<?php echo htmlspecialchars($grade); ?>">
		<div class="red-text"><?php echo $errors['grade']; ?></div>
		<label>Member Number of Service Hours</label>
		<input type="number" name="hours" value="<?php echo htmlspecialchars($hours); ?>">
		<div class="red-text"><?php echo $errors['hours']; ?></div>
		<label>Member Student ID Number</label>
		<input type="text" name="number" maxlength="6" value="<?php echo htmlspecialchars($number); ?>">
		<div class="red-text"><?php echo $errors['number']; ?></div>
		<label>Member Authorization Level</label>
		<div class="input-field col s12">
	    <select name="auth_level">
	      <option value="" disabled selected>Choose your option</option>
	      <option value="member">Member</option>
	      <option value="admin">Admin</option>
	    </select>
  	</div>
		<div class="red-text"><?php echo $errors['auth_level']; ?></div>
		<div class="center">
			<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
		</div>
</section>

<?php include('templates/footer.php'); ?>
