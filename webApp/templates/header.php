<?php

  // Creating a sesson for the user when they visit the website
  session_start();

  // Creating session varibales to personalize user experience
  $loginStatus = $_SESSION['loginStatus'] ?? false;
  $userSessionId = $_SESSION['id'] ?? '-1';
  $sessionAuthLevel = $_SESSION['authLevel'] ?? 'member';
  $userSessionName = $_SESSION['name'] ?? 'Guest';

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Centennial FBLA Chapter</title>
    <!-- Compiled and minified CSS -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.3/js/materialize.min.js"></script>

    <!-- Extra styles -->
    <style><?php require 'styles.css'; ?></style>
  </head>
  <body id="body" class="grey lighten-4">
    <nav id="nav" class="white z-depth-2">
      <!-- Creates the navbar -->
      <div class="wrapper">
        <!-- Creates the logo image which acts as a home button -->
        <div>
          <a href="index.php" class="left logo-link1">
          <img src="images/fbla.webp" alt="Centennial FBLA Logo" id="logo-img">
          </a>
        </div>

        <!-- Creates logo text which acts as the home button when the window gets smaller -->
        <div>
          <a href="index.php" class="left logo-link2">
          <h4 id="logo-text">CHS FBLA</h4>
          </a>
        </div>

        <!-- Creates the regular dropdown menu when the window is large enough but too small for the regular navbar buttons -->
        <div>
          <ul id="dropdown" class="dropdown-content">
            <li><a class="brand-text" href="index.php">Home</a></li>
            <li><a class="brand-text" href="help.php">Help</a></li>
            <?php if($sessionAuthLevel === 'admin' || $sessionAuthLevel === 'advisor'): ?>
              <li><a class="brand-text" href="addMember.php">Add Member</a></li>
            <?php endif; ?>
            <?php if($sessionAuthLevel === 'advisor'): ?>
              <li><a class="brand-text" href="addAdvisor.php">Add an Advisor</a></li>
            <?php endif; ?>
            <li id="login-status-drop" class="grey-text"><?php echo $userSessionName; ?></li>
            <?php if(!$loginStatus): ?>
              <li><a class="brand-text" href="login.php">Login</a></li>
            <?php elseif($loginStatus): ?>
              <li class="divider"></li>
              <li><a class="brand-text" href="includes/logout.inc.php">Logout</a></li>
            <?php endif; ?>
          </ul>

          <a id="adrop" class="btn-large brand dropdown-button z-depth-0 adaptive-drop" href="#" data-activates="dropdown">Menu<i class="material-icons right">arrow_drop_down</i></a>
        </div>

        <!-- Combines the logo text and the dropdown into one button when the window is too small -->
        <div>
          <ul id="dropdown" class="dropdown-content">
            <li><a class="brand-text" href="index.php">Home</a></li>
            <li><a class="brand-text" href="help.php">Help</a></li>
            <?php if($sessionAuthLevel === 'admin' || $sessionAuthLevel === 'advisor'): ?>
              <li><a class="brand-text" href="addMember.php">Add Member</a></li>
            <?php endif; ?>
            <?php if($sessionAuthLevel === 'advisor'): ?>
              <li><a class="brand-text" href="addAdvisor.php">Add an Advisor</a></li>
            <?php endif; ?>
            <li id="login-status-drop" class="grey-text"><?php echo $userSessionName; ?></li>
            <?php if(!$loginStatus): ?>
              <li><a class="brand-text" href="login.php">Login</a></li>
            <?php elseif($loginStatus): ?>
              <li class="divider"></li>
              <li><a class="brand-text" href="includes/logout.inc.php">Logout</a></li>
            <?php endif; ?>
          </ul>

          <a id="compact-drop" class="btn-large brand dropdown-button z-depth-0 logo-link3" href="#" data-activates="dropdown">CHS FBLA<i class="material-icons right">arrow_drop_down</i></a>
        </div>
      </div>

      <!-- Create the navbar buttons when the window is sufficiently large -->
      <ul id="nav-mobile" class="right hide-small-and-down vertical-align bar">
        <li><a href="help.php" class="btn-large brand z-depth-0">Help</a></li>
        <?php if($sessionAuthLevel === 'admin' || $sessionAuthLevel === 'advisor'): ?>
          <li><a href="addMember.php" class="btn-large brand z-depth-0">Add a Member</a></li>
        <?php endif; ?>
        <?php if($sessionAuthLevel === 'advisor'): ?>
          <li><a href="addAdvisor.php" class="btn-large brand z-depth-0">Add an Advisor</a></li>
        <?php endif; ?>
        <li id="login-status-nav" class="grey-text"><?php echo $userSessionName; ?></li>
        <?php if(!$loginStatus): ?>
          <li><a href="login.php" class="btn-large brand z-depth-0">Login</a></li
        <?php elseif($loginStatus): ?>
          <li><a href="includes/logout.inc.php" class="btn-large brand z-depth-0">Logout</a></li>
        <?php endif; ?>
      </ul>
    </nav>
