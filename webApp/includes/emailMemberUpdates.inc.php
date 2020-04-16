<?php

  // Connect to database
  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/config/dbConnect.php');
  // Query database for member data
  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/includes/emailQuery.inc.php');
  // Initializing PHPMailer object
  include('C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/config/emailObjectInit.php');

  // Generates the daily update report
  $report = '';

  // Setting file paths to generate report later
  $addFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/addMember.txt';
  $deleteFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/deleteMember.txt';
  $updateFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/updateMember.txt';

  // Opening the log files with write permissions
  $addHandle = fopen($addFile, 'a+');
  $deleteHandle = fopen($deleteFile, 'a+');
  $updateHandle = fopen($updateFile, 'a+');

  // Generates a report from the add log file
  if(filesize($addFile) > 0) {
    $addLog = fread($addHandle, filesize($addFile));
  } else {
    $addLog = "No members were added.";
  }

  // Generates a report from the delete log file
  if(filesize($deleteFile) > 0) {
    $deleteLog = fread($deleteHandle, filesize($deleteFile));
  } else {
    $deleteLog = "No members were deleted.";
  }

  // Generates a report from the update log file
  if(filesize($updateFile) > 0) {
    $updateLog = fread($updateHandle, filesize($updateFile));
  } else {
    $updateLog = "No members were updated";
  }

  // Accessing and opening the report file with write permissions
  $reportFile = 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/logs/report.txt';
  $reportHandle = fopen($reportFile, 'a+');

  // Adds the reports from teh various log files to the report file
  $report = "Members added:\n\n" . $addLog . "\n\nMembers deleted:\n\n" . $deleteLog . "\n\nMembers updated:\n\n" . $updateLog;
  fwrite($reportHandle, $report);

  // Sends the database update log file to the advisors
  foreach($members as $member) {
    if($member['auth_level'] === 'advisor') {
      $mail->Subject = "Database update logs for " . " -- " . date("m/d/Y") . " " . date("h:i:sa") . " PST";
      $mail->setFrom('fbla.candp.centennial@gmail.com');

      $body = "
        <body style='background-color: #F5F5F5'>
          <h4 style='color: #ad2d2d; font-family: 'Verdana, Geneva, sans-serif''>See the attachment linked for daily database interaction logs.</h4>
        </body>
        ";

      $mail->Body = $body;
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

  // Closing the handles for the files
  fclose($addHandle);
  fclose($deleteHandle);
  fclose($updateHandle);
  fclose($reportHandle);

  // Closing the connection to the email host
  $mail->smtpClose();

 ?>
