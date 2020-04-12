<?php

  // Unsetting and destrying sesson variables
  session_start();
  session_unset();
  session_destroy();

  header('Location: ../index.php');

 ?>
