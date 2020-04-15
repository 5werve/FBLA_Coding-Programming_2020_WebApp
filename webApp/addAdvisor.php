<?php

	include('config/dbConnect.php');

	$validateType = 'advisor';

	// Validates the form as an advisor
	include('includes/addMemberValidator.inc.php');

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
		<input type="password" name="passwordReenter" placeholder="Re-enter Password">
		<div class="red-text"><?php echo $errors['passwordReenter']; ?></div>
		<div class="center">
			<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
		</div>
</section>

<?php include('templates/footer.php'); ?>
