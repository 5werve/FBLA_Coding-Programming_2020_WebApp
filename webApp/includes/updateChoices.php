<?php

  class EchoUpdateField {

    public static function echoName($name, $nameError) {
      echo '
        <label>Member Name</label>
        <input type="text" name="name" value="' . $name . '">
        <div class="red-text">' . $nameError . '</div>
      ';
    }

    public static function echoEmail($emailError) {
      echo '
        <label>Member Email</label>
        <input type="text" name="email">
        <div class="red-text">' . $emailError . '</div>
      ';
    }

    public static function echoPassword($pwdError, $pwdReError) {
      echo '
        <label>Member Password</label>
        <input type="password" name="password" placeholder="New Password">
        <div class="red-text">' . $pwdError . '</div>
        <input type="password" name="passwordReenter" placeholder="Re-enter New Password">
        <div class="red-text">' . $pwdReError . '</div>
      ';
    }

    public static function echoGrade($gradeError) {
      echo '
        <label>Member Grade</label>
        <input type="number" name="grade" min="9" max="12">
        <div class="red-text">' . $gradeError . '</div>
      ';
    }

    public static function echoHours($hoursError) {
      echo '
        <label style="font-size: 15px;"><b>Add Hours</b></label>
        <input type="number" name="hours">
        <div class="red-text">' . $hoursError . '</div>
      ';
    }

    public static function echoIdNumber($numberError) {
      echo '
        <label>Member Student ID Number</label>
        <input type="text" name="number" maxlength="6">
        <div class="red-text">' . $numberError . '</div>
      ';
    }

    public static function echoAuthLevel($authLevelError) {
      echo '
        <label>Member Authorization Level</label>
        <div class="input-field col s12">
          <select name="authLevel">
            <option value="" disabled selected>Choose your option</option>
            <option value="member">Member</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="red-text">' . $authLevelError . '</div>
      ';
    }
  }

 ?>
