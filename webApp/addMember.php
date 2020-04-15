<?php

	include('config/dbConnect.php');

	$validateType = 'member';

	// Validates the form as a club member (non-advisor)
	include('includes/addMemberValidator.inc.php');

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
		<input type="password" name="passwordReenter" placeholder="Re-enter Password">
		<div class="red-text"><?php echo $errors['passwordReenter']; ?></div>
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
	    <select name="authLevel">
	      <option value="" disabled selected>Choose your option</option>
	      <option value="member">Member</option>
	      <option value="admin">Admin</option>
	    </select>
  	</div>
		<div class="red-text"><?php echo $errors['authLevel']; ?></div>
		<div class="center">
			<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
		</div>
</section>

<?php include('templates/footer.php'); ?>
