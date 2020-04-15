<?php

  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/config/dbConnect.php');

  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/includes/emailQuery.inc.php');

  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/PHPMailer.php';
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/Exception.php';
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/SMTP.php';

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

  // Generating the report
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
