<?php

  // Setting the default timezone
  date_default_timezone_set('America/Los_Angeles');

  // Write query for all members:
  $sql = "SELECT id, name, grade, hours, email, auth_level,
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
