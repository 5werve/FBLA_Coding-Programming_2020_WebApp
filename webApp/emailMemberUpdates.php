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

  // Initializing PHPMailer object
  $mail = new PHPMailer();

  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = 'true';
  $mail->SMTPSecure = 'tls';
  $mail->Port = '587';
  $mail->Username = 'fbla.candp.centennial@gmail.com';
  $mail->Password = 'Minhhongan';

  // Generates the daily update report
  $report = '';

  $addFile = 'C:/xampp/htdocs/FBLA_App/StudentDB/webApp/logs/addMember.txt';
  $deleteFile = 'C:/xampp/htdocs/FBLA_App/StudentDB/webApp/logs/deleteMember.txt';
  $updateFile = 'C:/xampp/htdocs/FBLA_App/StudentDB/webApp/logs/updateMember.txt';

  $addHandle = fopen($addFile, 'a+');
  $deleteHandle = fopen($deleteFile, 'a+');
  $updateHandle = fopen($updateFile, 'a+');

  if(filesize($addFile) > 0) {
    $addLog = fread($addHandle, filesize($addFile));
  } else {
    $addLog = "No members were added.";
  }

  if(filesize($deleteFile) > 0) {
    $deleteLog = fread($deleteHandle, filesize($deleteFile));
  } else {
    $deleteLog = "No members were deleted.";
  }

  if(filesize($updateFile) > 0) {
    $updateLog = fread($updateHandle, filesize($updateFile));
  } else {
    $updateLog = "No members were updated";
  }

  $reportFile = 'C:/xampp/htdocs/FBLA_App/StudentDB/webApp/logs/report.txt';
  $reportHandle = fopen($reportFile, 'a+');

  $report = "Members added:\n\n" . $addLog . "\n\nMembers deleted:\n\n" . $deleteLog . "\n\nMembers updated:\n\n" . $updateLog;
  fwrite($reportHandle, $report);

  // Sends a report of the community hours and award category to each member
  foreach($members as $member) {
    if($member['auth_level'] === 'advisor') {
      $mail->Subject = "Database update logs for " . " -- " . date("m/d/Y") . " " . date("h:i:sa") . " PST";
      $mail->setFrom('fbla.candp.centennial@gmail.com');
      $mail->Body = "See the attachment below for the daily database interaction logs.";
      $mail->addAddress($member['email']);
      $mail->addAttachment('C:/xampp/htdocs/FBLA_App/StudentDB/webApp/logs/report.txt');
      $mail->Send();
    }
  }

  // Clears the reports for the next day
  ftruncate($addHandle, 0);
  ftruncate($deleteHandle, 0);
  ftruncate($updateHandle, 0);
  ftruncate($reportHandle, 0);

  fclose($addHandle);
  fclose($deleteHandle);
  fclose($updateHandle);
  fclose($reportHandle);

  // Closing the connection to the email host
  $mail->smtpClose();

 ?>
