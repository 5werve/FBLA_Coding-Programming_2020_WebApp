<?php

  // Add connection to database
  include('config/db_connect.php');

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

  // Configuration for PHPMailer
  require 'phpMailer/includes/PHPMailer.php';
  require 'phpMailer/includes/Exception.php';
  require 'phpMailer/includes/SMTP.php';

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;

  // Sends a report of the community hours and award category to each member
  foreach($members as $member) {
    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = 'true';
    $mail->SMTPSecure = 'tls';
    $mail->Port = '587';
    $mail->Username = 'fbla.candp.centennial@gmail.com';
    $mail->Password = 'Minhhongan';
    $mail->Subject = "Community Service Report for " . $member['name'] . " -- " . date("m/d/Y") . " " . date("h:i:sa") . " PST";
    $mail->setFrom('fbla.candp.centennial@gmail.com');
    $mail->Body = "Your number of hours is: " . $member['hours'] . ". \nYour service award category is: " . $member['awardCategory'] . ".";
    $mail->addAddress($member['email']);
    $mail->Send();
  }

  // Sends a community service report of all members to the FBLA advisor
  $mail->Subject = "Community Service Report for the Centennial FBLA chapter -- " . date("m/d/Y") . " " . date("h:i:sa") . " PST";
  $mail->setFrom('fbla.candp.centennial@gmail.com');

  $report = '';
  $totalHours = 0;
  $numCommunity = 0;
  $numService = 0;
  $numAchievement = 0;
  $numNoAward = 0;

  // Generating the report
  foreach($members as $member) {
    $totalHours += $member['hours'];
    if($member['awardCategory'] == 'CSA Community') {
      $numCommunity++;
    } else if($member['awardCategory'] == 'CSA Service') {
      $numService++;
    } else if($member['awardCategory'] == 'CSA Achievement') {
      $numAchievement++;
    } else {
      $numNoAward++;
    }
  }
  $report = "Cummulative hours for all members is: " . $totalHours . ". \nThe total number of CSA Community awards is: " . $numCommunity . ". \nThe total number of CSA Service awards is: " . $numService . ". \nThe total number of CSA Achievements awards is: " . $numAchievement . ". \nThe total number of members without awards: " . $numNoAward . ".";

  // Sending the email to the advisors
  foreach($members as $member) {
    if($member['auth_level'] === 'advisor') {
      $mail->Body = $report;
      $mail->addAddress($member['email']);
      $mail->Send();
    }
  }

  // Closing the connection to the email host
  $mail->smtpClose();

 ?>
