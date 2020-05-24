<?php

  // Unsetting and destroying session variables
  session_start();
  session_unset();
  session_destroy();

  // Redirecting the user
  header('Location: ../index.php');

 ?>
