<?php

  // Initializing variables
  $targetDir = "uploads/";
  $uploadOk = 1;
  $error = '';
  $uploadResult = '';

  // Setting the target directory
  if(isset($_POST["member_id"])) {

    $targetDir = "uploads/" . strval($_POST['member_id']);

    // Creates directory if not already present
    if(!is_dir("uploads/" . strval($_POST['member_id']))) {
      mkdir("uploads/" . strval($_POST['member_id']));
    } else { // Deletes all photos in the directory if one was already created
      $files = glob($targetDir . "/*"); // get all file names
      foreach($files as $file){ // iterate files
        if(is_file($file)) {
          unlink($file); // delete file
        }
      }
    }
  } else {
      $error = "Member ID not found";
      $uploadOk = 0;
  }

  // Setting the file name and type
  $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
  $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

  // Check if image file is a actual image or fake image
  if(isset($_POST["submit"])) {
    if($_FILES["fileToUpload"]["tmp_name"]) {
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    } else {
      $check = false;
    }

    if($check !== false) {
      $uploadOk = 1;
    } else {
      $error = "File is not an image";
      $uploadOk = 0;
    }
  }
  // Check if file already exists
  if (file_exists($targetFile)) {
    $error = "File already exists";
    $uploadOk = 0;
  }
  // Check file size
  if ($_FILES["fileToUpload"]["size"] > 500000) {
    $error = "Your file is too large";
    $uploadOk = 0;
  }
  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
  && $imageFileType != "gif" ) {
    $error = "Only JPG, JPEG, PNG & GIF files are allowed";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    $uploadResult = "Your file was not uploaded";
    rmdir($targetDir);
  // If everything is ok, try to upload file
  } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
        $uploadResult = "The file ". basename($_FILES["fileToUpload"]["name"]). " has been uploaded";
      } else {
        $uploadResult = "There was an error uploading your file";
      }
  }

?>

<?php include('templates/header.php'); ?>

<section class="content">
  <!-- Outputs an error if there is one -->
  <?php if($error): ?>

    <div class="center red-text container">
      <h4><?php echo $error; ?></h4>
    </div>

  <?php else: ?>

    <div class="center grey-text container">
      <h3><?php echo $uploadResult; ?></h3>
    </div>

  <?php endif; ?>

</section>

<?php include('templates/footer.php'); ?>
