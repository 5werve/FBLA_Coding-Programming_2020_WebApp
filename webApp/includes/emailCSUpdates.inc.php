<?php

  // Connect to database
  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/config/dbConnect.php');

  // Querying database for member data
  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/includes/emailQuery.inc.php');

  // Importing packages for PHPMailer
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/PHPMailer.php';
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/Exception.php';
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/SMTP.php';

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;

  // Sends a report of the community hours and award category to each member
  foreach($members as $member) {

    // Initializing mailer object
    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = 'true';
    $mail->SMTPSecure = 'tls';
    $mail->Port = '587';
    $mail->Username = 'fbla.candp.centennial@gmail.com';
    $mail->Password = 'Minhhongan';
    $mail->isHTML(true);

    $mail->Subject = "Community Service Report for " . $member['name'] . " -- " . date("m/d/Y") . " " . date("h:i:sa") . " PST";
    $mail->setFrom('fbla.candp.centennial@gmail.com');

    // Creating email with HTML formatting
    $body = "
      <body style='background-color: #F5F5F5'>
        <h1 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>CHS FBLA Chapter Member CS Report</h1>

        <h5 style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>Your total number of hours is:</h5>
        <h3 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>" . $member['hours'] . "</h3>
        <br />

        <hr />

        <h6 style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>Your service award category is:</h6>
        <h3 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>" . $member['awardCategory'] . "</h3>
      </body>
      ";

    $mail->Body = $body;
    // Only sends to members that are not advisors
    if($member['auth_level'] !== 'advisor') {
      $mail->addAddress($member['email']);
    }
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

  // Generating the report for the number of awards in each category
  foreach($members as $member) {
    if($member['auth_level'] !== 'advisor') {
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
  }

  // Styling the report with HTML
  $report = "
    <body style='background-color: #F5F5F5'>
      <h1 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>CHS FBLA Chapter Member CS Report</h1>

      <h5 style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>Cummulative hours for all members:</h5>
      <h3 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>$totalHours</h3>
      <br />

      <hr />

      <h6 style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>Total number of CSA Community awards:</h6>
      <h3 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>$numCommunity</h3>

      <h6 style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>Total number of CSA Service awards:</h6>
      <h3 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>$numService</h3>

      <h6 style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>Total number of CSA Achievement awards:</h6>
      <h3 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>$numAchievement</h3>

      <h6 style='color: #2d5fad; font-size: 18px; font-family: 'Verdana, Geneva, sans-serif''>Total number with no awards:</h6>
      <h3 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>$numNoAward</h3>
    </body>
    ";

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
