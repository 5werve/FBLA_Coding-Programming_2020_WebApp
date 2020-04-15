<?php

  // Configuration for PHPMailer
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/PHPMailer.php';
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/Exception.php';
  require 'C:/xampp/htdocs/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/phpMailer/includes/SMTP.php';

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
  $mail->isHTML(true);

 ?>
