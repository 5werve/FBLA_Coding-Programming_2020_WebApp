<?php

  session_start();

  $login_status = $_SESSION['login_status'] ?? false;
  $user_session_id = $_SESSION['id'] ?? '-1';
  $session_auth_level = $_SESSION['auth_level'] ?? 'member';
  $user_session_name = $_SESSION['name'] ?? 'Guest';

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
      <!-- Create home button -->
      <div class="wrapper">
        <div>
          <a href="index.php" class="left logo-link1">
          <img src="images/fbla.webp" alt="Centennial FBLA Logo" id="logo-img">
          </a>
        </div>

        <div>
          <a href="index.php" class="left logo-link2">
          <h4 id="logo-text">CHS FBLA</h4>
          </a>
        </div>

        <div>
          <ul id="dropdown" class="dropdown-content">
            <li><a class="brand-text" href="index.php">Home</a></li>
            <li><a class="brand-text" href="help.php">Help</a></li>
            <?php if($session_auth_level === 'admin' || $session_auth_level === 'advisor'): ?>
              <li><a class="brand-text" href="addMember.php">Add Member</a></li>
            <?php endif; ?>
            <?php if($session_auth_level === 'advisor'): ?>
              <li><a class="brand-text" href="addAdvisor.php">Add an Advisor</a></li>
            <?php endif; ?>
            <li id="login-status-drop" class="grey-text"><?php echo $user_session_name; ?></li>
            <?php if(!$login_status): ?>
              <li><a class="brand-text" href="login.php">Login</a></li>
            <?php elseif($login_status): ?>
              <li class="divider"></li>
              <li><a class="brand-text" href="includes/logout.inc.php">Logout</a></li>
            <?php endif; ?>
          </ul>

          <a id="adrop" class="btn-large brand dropdown-button z-depth-0 adaptive-drop" href="#" data-activates="dropdown">Menu<i class="material-icons right">arrow_drop_down</i></a>
        </div>

        <div>
          <ul id="dropdown" class="dropdown-content">
            <li><a class="brand-text" href="index.php">Home</a></li>
            <li><a class="brand-text" href="help.php">Help</a></li>
            <?php if($session_auth_level === 'admin' || $session_auth_level === 'advisor'): ?>
              <li><a class="brand-text" href="addMember.php">Add Member</a></li>
            <?php endif; ?>
            <?php if($session_auth_level === 'advisor'): ?>
              <li><a class="brand-text" href="addAdvisor.php">Add an Advisor</a></li>
            <?php endif; ?>
            <li id="login-status-drop" class="grey-text"><?php echo $user_session_name; ?></li>
            <?php if(!$login_status): ?>
              <li><a class="brand-text" href="login.php">Login</a></li>
            <?php elseif($login_status): ?>
              <li class="divider"></li>
              <li><a class="brand-text" href="includes/logout.inc.php">Logout</a></li>
            <?php endif; ?>
          </ul>

          <a id="compact-drop" class="btn-large brand dropdown-button z-depth-0 logo-link3" href="#" data-activates="dropdown">CHS FBLA<i class="material-icons right">arrow_drop_down</i></a>
        </div>
      </div>

      <!-- Create the navbar buttons -->
      <ul id="nav-mobile" class="right hide-small-and-down vertical-align bar">
        <li><a href="help.php" class="btn-large brand z-depth-0">Help</a></li>
        <?php if($session_auth_level === 'admin' || $session_auth_level === 'advisor'): ?>
          <li><a href="addMember.php" class="btn-large brand z-depth-0">Add a Member</a></li>
        <?php endif; ?>
        <?php if($session_auth_level === 'advisor'): ?>
          <li><a href="addAdvisor.php" class="btn-large brand z-depth-0">Add an Advisor</a></li>
        <?php endif; ?>
        <li id="login-status-nav" class="grey-text"><?php echo $user_session_name; ?></li>
        <?php if(!$login_status): ?>
          <li><a href="login.php" class="btn-large brand z-depth-0">Login</a></li
        <?php elseif($login_status): ?>
          <li><a href="includes/logout.inc.php" class="btn-large brand z-depth-0">Logout</a></li>
        <?php endif; ?>
      </ul>
    </nav>
