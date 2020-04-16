<?php include('templates/header.php'); ?>

  <section class="container content">
    <h4 class="center grey-text">Help & FAQs:</h4>

    <div class="container">
      <h5>Common Questions</h5>
      <ul>
        <li><a href="#login">How to login</a></li>
        <li><a href="#forgotPwd">How to reset forgotten password</a></li>
        <li><a href="#logout">How to logout</a></li>
        <li><a href="#addMember">How to add a member</a></li>
        <li><a href="#addAdvisor">How to add an advisor</a></li>
        <li><a href="#updateMember">How to update a member</a></li>
        <li><a href="#updateProfile">How to update a profile image</a></li>
        <li><a href="#updateHours">How to update the community service hours for a member</a></li>
        <li><a href="#deleteMember">How to delete a member</a></li>
        <li><a href="#memberPrivileges">Priveleges of the different authorization levels</a></li>
      </ul>
    </div>

    <!-- List of common problems and solutions -->
    <div class="container">
      <div id="login">
        <h5 class="brand-text">How to login</h5>
        <p>
          Click on the login button in the navigation bar or in the dropdown menu for smaller screens. Enter
          your credentials and hit the submit button. You should be redirected to the homepage if the login
          was a success. Your name should now be displayed in the navigation bar or within the dropdown menu.
        </p>
      </div>
      <hr>
      <div id="forgotPwd">
        <h5 class="brand-text">How to reset forgotten password</h5>
        <p>
          Click on the login button in the navigation bar or in the dropdown menu. Locate the
          "Forgot your password?" button above the submit button and click on it. Enter the email of your
          account and click the "send recovery email" button. After you see the success dialogue under the
          button, check your inbox for the recovery email (it may be in your junk/spam folder). Click on the
          recovery url in the email. Once you reach the "create new password" page, create your new password
          and click the reset button.
        </p>
      </div>
      <hr>
      <div id="logout">
        <h5 class="brand-text">How to logout</h5>
        <p>
          Click on the logout button in the navigation bar or in the dropdown menu. You should then be logged out.
          You can confirm that you are no longer logged in by checking if the word "Guest" appears in the
          navigation bar or in the dropdown menu.
        </p>
      </div>
      <hr>
      <div id="addMember">
        <h5 class="brand-text">How to add a member</h5>
        <p>
          Login to the website as an admin or an advisor. Click on the add member button in the navigation bar.
          This button might be within a dropdown menu on smaller screens. Once in the add member page, fill out
          the form with valid values and click the submit button. The member should now be added to the database.
        </p>
      </div>
      <hr>
      <div id="addAdvisor">
        <h5 class="brand-text">How to add an advisor</h5>
        <p>
          You must be logged in as an advisor in order to add another advisor. Click on the add advisor button
          in the navigation bar or within a dropdown menu on smaller screens. Fill out the form with the
          appropriate information and click the submit button. The advisor should now be added to the database.
        </p>
      </div>
      <hr>
      <div id="updateMember">
        <h5 class="brand-text">How to update a member</h5>
        <p>
          The authorization level of the person you are planning to update and the authorization level of the
          account that you are logged into might affect what members or fields you can update.
          Navigate to the homepage (click the logo in the top-left corner or the home button within a dropdown).
          Find the card belonging to the member that you would like to update. Click on the more info button of
          that card. Click on the update info button. Fill out the fields that you would like to update and hit
          the submit button.
        </p>
      </div>
      <hr>
      <div id="updateProfile">
        <h5 class="brand-text">How to update a profile image</h5>
        <p>
          The default profile image will be shown on the homepage if the profile image for that user hasn't been
          set yet. In order to update a profile image, navigate to the details page of the user you want to update.
          Do this by navigating to the homepage and clicking on the more info button of the member that you would
          like to upload a profile image for. Click on the update info button. Scroll down until you find the add a
          profile picture form. Click on the custom upload button and find the picture that you would like to upload.
          Once you select the picuture, click the upload image button.
        </p>
      </div>
      <hr>
      <div id="updateHours">
        <h5 class="brand-text">How to update the community service hours for a member</h5>
        <p>
          Navigate to the update form by following <a href="#updateMember">this step.</a> Enter the amount
          of hours that you like to add to the member's total amount in the add hours field. You can use negative
          numbers to remove from the member's total amount of hours.
        </p>
      </div>
      <hr>
      <div id="deleteMember">
        <h5 class="brand-text">How to delete a member</h5>
        <p>
          Like with updating members, you may be able to or not able to delete certain members based on
          their authorization level and your authorization level.
          Navigate to the details page of the member you would like to delete. Do this by going to the homepage and
          finding the card of the member you would like to delete. Click on the more info button. Scroll down and
          hit the delete button. Only hit in when you are sure that you would like to delete that member (there is no
          confirmation button).
        </p>
      </div>
      <hr>
      <div id="memberPrivileges">
        <h5 class="brand-text">Priveleges of the different authorization levels</h5>
        <h6 style="font-weight: bold;">Advisor</h6>
        <ul>
          <li>Can add other advisors and members</li>
          <li>Can update and delete all other members</li>
        </ul>
        <h6 style="font-weight: bold;">Admin</h6>
        <ul>
          <li>Can add other members and admins</li>
          <li>Can update and delete other members with an authorization level of member</li>
          <li>Can update all of their own information in the update form</li>
        </ul>
        <h6 style="font-weight: bold;">Member</h6>
        <ul>
          <li>Can update their own name, email, password, and profile image</li>
        </ul>
        <h6 style="font-weight: bold;">Guest</h6>
        <ul>
          <li>Can view member information</li>
        </ul>
      </div>
    </div>
  </section>

<?php include('templates/footer.php'); ?>
