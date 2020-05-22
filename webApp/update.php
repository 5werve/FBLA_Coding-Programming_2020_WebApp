<?php

	include('config/dbConnect.php');

	// Same as add form:
	$name = $email = $grade = $hours = $number = $password = $passwordReenter = $authLevel = '';
	$errors = array('name' => '', 'email' => '', 'grade' => '', 'hours' => '', 'number' => '', 'password' => '', 'passwordReenter' => '', 'authLevel' => '');
	$updated = array('name' => false, 'email' => false, 'grade' => false, 'hours' => false, 'number' => false, 'password' => false, 'authLevel' => false);

  // Check GET request id param
	if(isset($_GET['id'])) {
		// Escape sql chars
		$id = mysqli_real_escape_string($conn, $_GET['id']);

		// Make sql
		$sql = "SELECT * FROM member_data WHERE id = $id";

		// Get the query result
		$result = mysqli_query($conn, $sql);

		// Fetch result in array format
		$member = mysqli_fetch_assoc($result);
	}

	// Validating the form data if the user hit the submit button
  if(isset($_POST['submit'])) {
    if(empty($_POST['name'])) {
      $name = $member['name'];
    } else {
      $name = $_POST['name'];
      if(!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors['name'] = 'Name must be letters and spaces only';
      }
			$updated['name'] = true;
    }

		$_POST['email'] = trim($_POST['email']);
		if(empty($_POST['email'])) {
			$email = $member['email'];
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
			$updated['email'] = true;
		}

		$_POST['password'] = trim($_POST['password']);
		if(empty($_POST['password'])) {
			$password = '';
		} else {
			$password = $_POST['password'];
			if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
				$errors['password'] = 'Password must be at least 8 characters long and alphanumeric';
			}

			$_POST['passwordReenter'] = trim($_POST['passwordReenter']);
			if(empty($_POST['passwordReenter'])) {
				if(isset($password)) {
					$errors['passwordReenter'] = 'You must re-enter your new password';
				}
			} else {
				$passwordReenter = $_POST['passwordReenter'];
				if($passwordReenter !== $password) {
					$errors['passwordReenter'] = 'The passwords you entered must match';
				}
			}
			$updated['password'] = true;
		}

		if(empty($_POST['grade'])) {
			$grade = $member['grade'];
		} else {
			$grade = $_POST['grade'];
			if(!($grade >= 9 && $grade <= 12)) {
				$errors['grade'] = 'Grade must be a valid high school grade';
			}
			$updated['grade'] = true;
		}

		if(empty($_POST['hours'])) {
			$hours = 0;
		} else {
			$hours = $_POST['hours'];
			$updated['hours'] = true;
		}

		if(empty($_POST['number'])) {
			$number = $member['number'];
		} else {
			$number = $_POST['number'];
			if(strlen($number) != 6) {
				$errors['number'] = 'ID must be a valid 6-digit number';
			}
			if(!preg_match('/^[0-9]*$/', $number)) {
				$errors['number'] = 'ID can only contain numbers';
			}
			$updated['number'] = true;
		}

		if(empty($_POST['authLevel'])) {
			$authLevel = $member['auth_level'];
		} else {
			$authLevel = $_POST['authLevel'];
			$updated['authLevel'] = true;
		}

		if(!array_filter($errors)) {
			$email = mysqli_real_escape_string($conn, $email);
			if(isset($password)) {
				$password = mysqli_real_escape_string($conn, $password);
				$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
			}
			$name = mysqli_real_escape_string($conn, $name);
			$grade = mysqli_real_escape_string($conn, $grade);
			$hours = mysqli_real_escape_string($conn, $hours);
			$number = mysqli_real_escape_string($conn, $number);
			$authLevel = mysqli_real_escape_string($conn, $authLevel);

			// Updates the user data if there are no errors
			if(isset($password)) {
				// Add the number of hours inputted to total hours
				$sql = "UPDATE member_data SET name='$name', email='$email', grade='$grade', hours=hours+'$hours', number='$number', auth_level='$authLevel', password='$hashedPassword' WHERE id='$id';";
			} else {
				$sql = "UPDATE member_data SET name='$name', email='$email', grade='$grade', hours=hours+'$hours', number='$number', auth_level='$authLevel' WHERE id='$id';";
			}

			if(mysqli_query($conn, $sql)) {
				// Writing a log to the updateMember file that member info has been updated
				$updatedFields = '';
				foreach($updated as $key => $value) {
					if($updated[$key] === true) {
						$newVal = $_POST[$key];
						$updatedFields = $updatedFields . $key . "($newVal) ";
					}
				}

				date_default_timezone_set('America/Los_Angeles');
				$file = 'logs/updateMember.txt';
				$handle = fopen($file, 'a+');
				fwrite($handle, date("h:i:sa") . " PST: " . "The values -- $updatedFields" . "-- have been updated for " . ucfirst($authLevel) . " " . $name . ".\n");
				fclose($handle);

        mysqli_free_result($result);
				if(isset($stmt)) {
					mysqli_stmt_close($stmt);
				}
				mysqli_close($conn);
				header('Location: index.php');
			} else {
				echo 'update error: '. mysqli_error($conn);
			}
		} else {
			foreach($updated as $key => $value) {
				$updated[$key] = false;
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

<!-- Update form -->
<section class="container grey-text content">
	<?php if(isset($member)): ?>
		<h4 class="center">Edit Data for <?php echo $member['name']; ?></h4>
		<form class="white" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $member['id'] ?>" method="POST" autocomplete="off">

			<!-- Shows different fields that the user is allowed to update based on their authorization level and the authorization level of the
				person they are trying to update -->

			<?php include('includes/updateChoices.inc.php'); ?>

			<?php if($sessionAuthLevel === 'advisor'): ?>

				<?php if($member['auth_level'] !== 'advisor'): ?>

					<?php
						EchoUpdateField::echoName($name, $errors['name']);
						EchoUpdateField::echoEmail($errors['email']);
						EchoUpdateField::echoPassword($errors['password'], $errors['passwordReenter']);
						EchoUpdateField::echoGrade($errors['grade']);
						EchoUpdateField::echoHours($errors['hours']);
						EchoUpdateField::echoIdNumber($errors['number']);
						EchoUpdateField::echoAuthLevel($errors['authLevel']);
					?>

				<?php endif; ?>

				<?php if($member['auth_level'] === 'advisor'): ?>
					<?php
						EchoUpdateField::echoName($name, $errors['name']);
						EchoUpdateField::echoEmail($errors['email']);
						EchoUpdateField::echoPassword($errors['password'], $errors['passwordReenter']);
					 ?>
				<?php endif; ?>

			<?php elseif($sessionAuthLevel === 'admin'): ?>
				<?php if($member['auth_level'] === 'member' || $member['auth_level'] === 'admin'): ?>
					<?php
						EchoUpdateField::echoName($name, $errors['name']);
						EchoUpdateField::echoEmail($errors['email']);
						EchoUpdateField::echoPassword($errors['password'], $errors['passwordReenter']);
						EchoUpdateField::echoGrade($errors['grade']);
						EchoUpdateField::echoHours($errors['hours']);
						EchoUpdateField::echoIdNumber($errors['number']);
						EchoUpdateField::echoAuthLevel($errors['authLevel']);
					?>
				<?php elseif($member['id'] == $userSessionId): ?>
					<?php
						EchoUpdateField::echoName($name, $errors['name']);
						EchoUpdateField::echoEmail($errors['email']);
						EchoUpdateField::echoPassword($errors['password'], $errors['passwordReenter']);
						EchoUpdateField::echoGrade($errors['grade']);
						EchoUpdateField::echoHours($errors['hours']);
						EchoUpdateField::echoIdNumber($errors['number']);
					?>
				<?php endif; ?>

			<?php elseif($sessionAuthLevel === 'member'): ?>
				<?php if($member['id'] == $userSessionId): ?>
					<?php
						EchoUpdateField::echoName($name, $errors['name']);
						EchoUpdateField::echoEmail($errors['email']);
						EchoUpdateField::echoPassword($errors['password'], $errors['passwordReenter']);
					?>
				<?php endif; ?>

			<?php endif; ?>

			<div class="center">
				<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
			</div>
		</form>

		</form>
			<!-- Adding the profile picture -->
			<h4 class="center">Add a Profile Picture</h4>
			<form class="white" action="upload.php" method="POST" enctype="multipart/form-data">
				<label>
					<div class="center">
						<b>Select image to upload:</b>
						<br />
						<br />
						<label class="btn-small field z-depth-0" for="fileToUpload"><i class="material-icons right">attach_file</i>Custom Upload</label>
						<input type="file" name="fileToUpload" id="fileToUpload">
						<br />
						<br />
						<input type="submit" value="Upload Image" name="submit" class="btn brand z-depth-0">
						<input type="hidden" name="member_id" value=<?php echo $member['id']; ?>/>
					</div>
				</label>
			</form>
	<!-- Displays an error if no user exists with the given id -->
	<?php else: ?>
		<h5 class="center">No such member exists</h5>
	<?php endif; ?>
</section>

<?php include('templates/footer.php'); ?>
