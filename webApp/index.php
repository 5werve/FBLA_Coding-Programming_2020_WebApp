<?php

  include('config/dbConnect.php');

  // Write query for all members:
  $sql = "SELECT id, name, grade, hours, auth_level, email,
    CASE
      WHEN hours >= 50 AND hours <= 199 THEN 'CSA Community'
      WHEN hours >= 200 AND hours <= 499 THEN 'CSA Service'
      WHEN hours >= 500 THEN 'CSA Achievement'
      ELSE 'No service award'
    END as 'awardCategory'
    FROM member_data ORDER BY name;";

  // Make query and get result:
  $result = mysqli_query($conn, $sql);

  // Fetch the resulting rows as an array:
  $members = mysqli_fetch_all($result, MYSQLI_ASSOC);

  // Free result from memory:
  mysqli_free_result($result);

  // Close connection:
  mysqli_close($conn);
 ?>

<?php include('templates/header.php'); ?>

<section class="container content">
  <h4 class="center grey-text">Members:</h4>

  <div class="container">
    <div class="row">

      <!-- Diplay a card for each member including the profile image, name, hours, and award category -->
      <?php foreach($members as $member): ?>
        <div id="stack" class="col s6 md3">
          <div class="card z-depth-0">
            <div id="card-info" class="card-content center">
              <h6><?php echo htmlspecialchars($member['name']); ?></h6>
              <img src="
                <?php $dir = 'uploads/' . $member['id'] . '/' ?>
                <?php if(is_dir($dir)): ?>
                  <?php $files = scandir($dir); ?>
                  <?php $targetFile = 'uploads/' . $member['id'] . '/' . $files[2]; ?>
                  <?php if(file_exists($targetFile)): ?>
                    <?php echo $targetFile; ?>
                  <?php endif; ?>
                <?php else: // Sets the profile as a default image if it is not set yet ?>
                  <?php $targetFile = 'images/default_profile.jpg'; ?>
                  <?php echo $targetFile; ?>
                <?php endif; ?>"
                width=100px height = 150px style="object-fit:cover;;"
              />
              <?php if($member['auth_level'] !== 'advisor'): ?>
                <ul>
                  <li><?php echo 'Grade: ' . htmlspecialchars($member['grade']); ?></li>
                  <li><?php echo 'Total Hours: ' . htmlspecialchars($member['hours']); ?></li>
                  <li><?php echo 'Award Category: ' . htmlspecialchars($member['awardCategory']); ?></li>
                </ul>
              <?php else: ?>
                <ul>
                  <li>Club Advisor</li>
                  <li><?php echo 'Email: ' . htmlspecialchars($member['email']); ?></li>
                </ul>
              <?php endif; ?>
             </div>
             <div class="card-action right-align">
               <a class="brand-text" href="details.php?id=<?php echo $member['id']; ?>">more info</a>
             </div>
          </div>
        </div>

      <?php endforeach; ?>

    </div>
  </div>
</section>

<?php include('templates/footer.php'); ?>
