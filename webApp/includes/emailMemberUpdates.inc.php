<?php

  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/config/dbConnect.php');

  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/includes/emailQuery.inc.php');

  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/config/emailObjectInit.php');

  // Generates the daily update report
  $report = '';

  $addFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/addMember.txt';
  $deleteFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/deleteMember.txt';
  $updateFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/updateMember.txt';

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

  $reportFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/report.txt';
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
      $mail->addAttachment($reportFile);
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
