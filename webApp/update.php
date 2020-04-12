<?php

	// Add connection to database
	include('config/db_connect.php');

	// Same as add form:
	$name = $email = $grade = $hours = $number = $password = $password_reenter = $auth_level = '';
	$errors = array('name' => '', 'email' => '', 'grade' => '', 'hours' => '', 'number' => '', 'password' => '', 'password_reenter' => '', 'auth_level' => '');
	$updated = array('name' => false, 'email' => false, 'grade' => false, 'hours' => false, 'number' => false, 'password' => false, 'auth_level' => false);

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

			$_POST['password_reenter'] = trim($_POST['password_reenter']);
			if(empty($_POST['password_reenter'])) {
				if(isset($password)) {
					$errors['password_reenter'] = 'You must re-enter your new password';
				}
			} else {
				$password_reenter = $_POST['password_reenter'];
				if($password_reenter !== $password) {
					$errors['password_reenter'] = 'The passwords you entered must match';
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

		if(empty($_POST['auth_level'])) {
			$auth_level = $member['auth_level'];
		} else {
			$auth_level = $_POST['auth_level'];
			$updated['auth_level'] = true;
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
			$auth_level = mysqli_real_escape_string($conn, $auth_level);

			if(isset($password)) {
				// Add the number of hours inputted to total hours
				$sql = "UPDATE member_data SET name='$name', email='$email', grade='$grade', hours=hours+'$hours', number='$number', auth_level='$auth_level', password='$hashedPassword' WHERE id='$id';";
			} else {
				$sql = "UPDATE member_data SET name='$name', email='$email', grade='$grade', hours=hours+'$hours', number='$number', auth_level='$auth_level' WHERE id='$id';";
			}

			if(mysqli_query($conn, $sql)) {
				// Writing a log to the updateMember file that member info has been updated
				$updated_fields = '';
				foreach($updated as $key => $value) {
					if($updated[$key] === true) {
						$newVal = $_POST[$key];
						$updated_fields = $updated_fields . $key . "($newVal) ";
					}
				}

				date_default_timezone_set('America/Los_Angeles');
				$file = 'logs/updateMember.txt';
				$handle = fopen($file, 'a+');
				fwrite($handle, date("h:i:sa") . " PST: " . "The values -- $updated_fields" . "-- have been updated for " . ucfirst($auth_level) . " " . $name . ".\n");
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

<section class="container grey-text content">
	<?php if(isset($member)): ?>
		<h4 class="center">Edit Data for <?php echo $member['name']; ?></h4>
		<form class="white" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $member['id'] ?>" method="POST" autocomplete="off">
			<?php if($session_auth_level === 'advisor'): ?>

				<?php if($member['auth_level'] !== 'advisor'): ?>
					<label>Member Name</label>
					<input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
					<div class="red-text"><?php echo $errors['name']; ?></div>
					<label>Member Email</label>
					<input type="text" name="email">
					<div class="red-text"><?php echo $errors['email']; ?></div>
					<label>Member Password</label>
					<input type="password" name="password" placeholder="New Password">
					<div class="red-text"><?php echo $errors['password']; ?></div>
					<input type="password" name="password_reenter" placeholder="Re-enter New Password">
					<div class="red-text"><?php echo $errors['password_reenter']; ?></div>
					<label>Member Grade</label>
					<input type="number" name="grade" min="9" max="12">
					<div class="red-text"><?php echo $errors['grade']; ?></div>
					<label style="font-size: 15px;"><b>Add Hours</b></label>
					<input type="number" name="hours">
					<div class="red-text"><?php echo $errors['hours']; ?></div>
					<label>Member Student ID Number</label>
					<input type="text" name="number" maxlength="6">
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
				<?php endif; ?>

				<?php if($member['auth_level'] === 'advisor'): ?>
					<label>Member Name</label>
					<input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
					<div class="red-text"><?php echo $errors['name']; ?></div>
					<label>Member Email</label>
					<input type="text" name="email">
					<div class="red-text"><?php echo $errors['email']; ?></div>
					<label>Member Password</label>
					<input type="password" name="password" placeholder="New Password">
					<div class="red-text"><?php echo $errors['password']; ?></div>
					<input type="password" name="password_reenter" placeholder="Re-enter New Password">
					<div class="red-text"><?php echo $errors['password_reenter']; ?></div>
				<?php endif; ?>

			<?php elseif($session_auth_level === 'admin'): ?>
				<?php if($member['auth_level'] === 'member'): ?>
					<label>Member Name</label>
					<input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
					<div class="red-text"><?php echo $errors['name']; ?></div>
					<label>Member Email</label>
					<input type="text" name="email">
					<div class="red-text"><?php echo $errors['email']; ?></div>
					<label>Member Password</label>
					<input type="password" name="password" placeholder="New Password">
					<div class="red-text"><?php echo $errors['password']; ?></div>
					<input type="password" name="password_reenter" placeholder="Re-enter New Password">
					<div class="red-text"><?php echo $errors['password_reenter']; ?></div>
					<label>Member Grade</label>
					<input type="number" name="grade" min="9" max="12">
					<div class="red-text"><?php echo $errors['grade']; ?></div>
					<label style="font-size: 15px;"><b>Add Hours</b></label>
					<input type="number" name="hours">
					<div class="red-text"><?php echo $errors['hours']; ?></div>
					<label>Member Student ID Number</label>
					<input type="text" name="number" maxlength="6">
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
				<?php elseif($member['id'] == $user_session_id): ?>
					<label>Member Name</label>
					<input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
					<div class="red-text"><?php echo $errors['name']; ?></div>
					<label>Member Email</label>
					<input type="text" name="email">
					<div class="red-text"><?php echo $errors['email']; ?></div>
					<label>Member Password</label>
					<input type="password" name="password" placeholder="New Password">
					<div class="red-text"><?php echo $errors['password']; ?></div>
					<input type="password" name="password_reenter" placeholder="Re-enter New Password">
					<div class="red-text"><?php echo $errors['password_reenter']; ?></div>
					<label>Member Grade</label>
					<input type="number" name="grade" min="9" max="12">
					<div class="red-text"><?php echo $errors['grade']; ?></div>
					<label style="font-size: 15px;"><b>Add Hours</b></label>
					<input type="number" name="hours">
					<div class="red-text"><?php echo $errors['hours']; ?></div>
					<label>Member Student ID Number</label>
					<input type="text" name="number" maxlength="6">
					<div class="red-text"><?php echo $errors['number']; ?></div>
				<?php endif; ?>

			<?php elseif($session_auth_level === 'member'): ?>
				<?php if($member['id'] == $user_session_id): ?>
					<label>Member Name</label>
					<input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
					<div class="red-text"><?php echo $errors['name']; ?></div>
					<label>Member Email</label>
					<input type="text" name="email">
					<div class="red-text"><?php echo $errors['email']; ?></div>
					<label>Member Password</label>
					<input type="password" name="password" placeholder="New Password">
					<div class="red-text"><?php echo $errors['password']; ?></div>
					<input type="password" name="password_reenter" placeholder="Re-enter New Password">
					<div class="red-text"><?php echo $errors['password_reenter']; ?></div>
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
	<?php else: ?>
		<h5 class="center">No such member exists</h5>
	<?php endif; ?>
</section>

<?php include('templates/footer.php'); ?>
