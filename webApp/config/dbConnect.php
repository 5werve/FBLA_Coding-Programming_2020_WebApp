<?php

  $serverName = 'localhost';
  $dbUsername = 'matthew';
  $dbPassword = 'Minhhongan';
  $dbName = 'fbla_members';

  // Connect to database:
  $conn = mysqli_connect($serverName, $dbUsername, $dbPassword, $dbName);

  // Checking connection:
  if(!$conn) {
    die('Connection error: ' . mysqli_connect_error());
  }

 ?>
