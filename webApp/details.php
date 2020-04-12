<?php

	// Add connection to database
	include('config/db_connect.php');

	// Check GET request id param
	if(isset($_GET['id'])){

		// Escape sql chars
		$id = mysqli_real_escape_string($conn, $_GET['id']);

		// Make sql
		$sql = "SELECT *,
			CASE
				WHEN hours >= 50 AND hours <= 199 THEN 'CSA Community'
				WHEN hours >= 200 AND hours <= 499 THEN 'CSA Service'
				WHEN hours >= 500 THEN 'CSA Achievement'
				ELSE 'No service award'
			END as 'awardCategory'
			FROM member_data
			WHERE id = $id;";

		// Get the query result
		$result = mysqli_query($conn, $sql);

		// Fetch result in array format
		$member = mysqli_fetch_assoc($result);

		// Clearing space
		mysqli_free_result($result);
		mysqli_close($conn);

	}

	// Checks if the user hit the delete button
	if(isset($_POST['delete'])){

		// Get the id to delete
		$idToDelete = mysqli_real_escape_string($conn, $_POST['idToDelete']);

		// Getting the deleted member info to log
		$sql = "SELECT auth_level, name FROM member_data WHERE id = $idToDelete;";
		$result = mysqli_query($conn, $sql);
		$member = mysqli_fetch_assoc($result);
		$auth_level = $member['auth_level'];
		$name = $member['name'];

		// Create sql
		$sql = "DELETE FROM member_data WHERE id = $idToDelete;";

		// Redirects user to the homepage if the query is successful
		if(mysqli_query($conn, $sql)){

			// Logging the deleted user to the deleteMember file
			date_default_timezone_set('America/Los_Angeles');
			$file = 'logs/deleteMember.txt';
			$handle = fopen($file, 'a+');
			fwrite($handle, date("h:i:sa") . " PST: " . ucfirst($auth_level) . " " . $name . " has been deleted." . "\n");
			fclose($handle);

			mysqli_free_result($result);
			mysqli_close($conn);

			header('Location: index.php');
		} else {
			echo 'query error: '. mysqli_error($conn);
		}
	}

?>

<?php include('templates/header.php'); ?>

<section class="container grey-text content">
	<h4 class="center grey-text">Additional Member Info:</h4>

	<!-- Displaying member info -->
	<div class="container center">
		<?php if(isset($member)): ?>
			<div class="container white" id="data">
				<?php if($member['auth_level'] !== 'advisor'): ?>
					<h5 style="font-weight: bold;"><?php echo $member['name']; ?></h5>
					<p><b>Grade:</b> <?php echo $member['grade']; ?></p>
					<p><b>Email:</b> <?php echo $member['email']; ?></p>
					<?php if($member['id'] == $user_session_id || $session_auth_level === 'admin' || $session_auth_level === 'advisor'): ?>
						<p><b>Student ID Number:</b> <?php echo $member['number']; ?></p>
					<?php endif; ?>
					<p><b>Total Hours:</b> <?php echo $member['hours']; ?></p>
					<h5>Award Category:</h5>
					<p><?php echo $member['awardCategory']; ?></p>
					<p><b>Authorization Level:</b> <?php echo $member['auth_level']; ?></p>
				<?php else: ?>
					<h5 style="font-weight: bold;"><?php echo $member['name']; ?></h5>
					<p><b>Email:</b> <?php echo $member['email']; ?></p>
					<p><b>Authorization Level:</b> <?php echo $member['auth_level']; ?></p>
				<?php endif; ?>
			</div>

			<!-- DELETE FORM -->
			<?php if($member['auth_level'] === 'member'): ?>
				<?php if($session_auth_level === 'admin' || $session_auth_level === 'advisor'): ?>
					<form action="details.php" method="POST">
						<input type="hidden" name="idToDelete" value="<?php echo $member['id']; ?>">
						<input type="submit" name="delete" value="Delete" class="btn brand z-depth-0">
					</form>
			  <?php endif; ?>
		  <?php endif; ?>

			<?php if($member['auth_level'] === 'admin' || $member['auth_level'] === 'advisor'): ?>
				<?php if($session_auth_level === 'advisor'): ?>
					<form action="details.php" method="POST">
						<input type="hidden" name="idToDelete" value="<?php echo $member['id']; ?>">
						<input type="submit" name="delete" value="Delete" class="btn brand z-depth-0">
					</form>
			  <?php endif; ?>
		  <?php endif; ?>

			<!-- EDIT FORM -->
			<?php if($member['auth_level'] === 'member'): ?>
				<?php if($member['id'] == $user_session_id || $session_auth_level === 'admin' || $session_auth_level === 'advisor'): ?>
					<a class="btn brand z-depth-0" href="update.php?id=<?php echo $member['id'] ?>">Update Info</a>
				<?php endif; ?>
			<?php endif; ?>

			<?php if($member['auth_level'] === 'admin' || $member['auth_level'] === 'advisor'): ?>
				<?php if($member['id'] == $user_session_id || $session_auth_level === 'advisor'): ?>
					<a class="btn brand z-depth-0" href="update.php?id=<?php echo $member['id'] ?>">Update Info</a>
				<?php endif; ?>
			<?php endif; ?>

		<!-- Displays message if the member specified by the id doesn't exist -->
		<?php else: ?>
			<h5>No such member exists</h5>
		<?php endif ?>
	</div>
</section>

<?php include('templates/footer.php'); ?>
