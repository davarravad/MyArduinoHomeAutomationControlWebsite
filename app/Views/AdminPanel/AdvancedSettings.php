<?php
/**
* Admin Panel Advanced Settings View
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
*/
use Libs\Form,
    Libs\ErrorMessages,
    Libs\SuccessMessages,
    Libs\Language;

?>
<div class='col-lg-12 col-md-12 col-sm-12'>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
    	<div class='card mb-3'>
    		<div class='card-header h4'>
    			<h3 class='jumbotron-heading'>Site Users Settings</h3>
    		</div>
    		<div class='card-body'>
    			<p><?php echo $data['welcomeMessage'] ?></p>

    			<?php echo Form::open(array('method' => 'post')); ?>

    			<!-- Site Invite Code -->
    			<div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
    				  <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Site Invitation Code</span>
            </div>
    				<?php echo Form::input(array('type' => 'text', 'name' => 'site_user_invite_code', 'class' => 'form-control', 'value' => $site_user_invite_code, 'placeholder' => 'Site Invitation Code', 'maxlength' => '255')); ?>
    			</div>
          <div style='margin-bottom: 25px'>
            <i>Default: blank</i> - Requries new users to use correct Invitation Code to Register for site.  Site does not require if left blank.
          </div>

          <!-- Max Login Attempts -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Login Attempts</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_attempts', 'class' => 'form-control', 'value' => $max_attempts, 'placeholder' => 'Max Login Attempts', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 5</i> - Sets total number of attempts before user is locked for a set time.
          </div>

          <!-- Failed Login Attempts Block Time -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Block Failed Login User Duration in Minutes</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'security_duration', 'class' => 'form-control', 'value' => $security_duration, 'placeholder' => 'Block Failed Login User Duration in Minutes', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 5</i> - Sets amount of Minutes user is blocked from being able to login.
          </div>

          <!-- Basic User Session Duration -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Basic User Session Duration in Days</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'session_duration', 'class' => 'form-control', 'value' => $session_duration, 'placeholder' => 'How Many Days a User Stays Logged In', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 1</i> - Sets amount of Days users stay logged in to a basic session.
          </div>

          <!-- Remember Me User Session Duration -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Remember Me Session Duration in Months</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'session_duration_rm', 'class' => 'form-control', 'value' => $session_duration_rm, 'placeholder' => 'How Many Months a User Stays Logged In', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 1</i> - Sets amount of Months users stay logged in when they check Remember Me.
          </div>

          <!-- Min Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Min Username Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'min_username_length', 'class' => 'form-control', 'value' => $min_username_length, 'placeholder' => 'Min Username Length', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 5</i> - Minimum character length for Usernames.
          </div>

          <!-- Max Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Username Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_username_length', 'class' => 'form-control', 'value' => $max_username_length, 'placeholder' => 'Max Username Length', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 30</i> - Maximum character length for Usernames.
          </div>

          <!-- Min Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Min Password Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'min_password_length', 'class' => 'form-control', 'value' => $min_password_length, 'placeholder' => 'Min Password Length', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 5</i> - Minimum character length for Passwords.
          </div>

          <!-- Max Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Password Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_password_length', 'class' => 'form-control', 'value' => $max_password_length, 'placeholder' => 'Max Password Length', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 30</i> - Maximum character length for Passwords.
          </div>

          <!-- Min Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Min Email Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'min_email_length', 'class' => 'form-control', 'value' => $min_email_length, 'placeholder' => 'Min Email Address Length', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 5</i> - Minimum character length for Email Addresses.
          </div>

          <!-- Max Username Length -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Max Email Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'max_email_length', 'class' => 'form-control', 'value' => $max_email_length, 'placeholder' => 'Max Email Address Length', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 100</i> - Maximum character length for Email Addresses.
          </div>

          <!-- New User Activation Token -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Activation Token Length</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'random_key_length', 'class' => 'form-control', 'value' => $random_key_length, 'placeholder' => 'Activation Token Length', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 15</i> - Character length for tokens that are generated for new users when required to activate via email.
          </div>
        </div>
      </div>
    </div>
    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          <h3 class='jumbotron-heading'>Site Time Zone Settings</h3>
        </div>
        <div class='card-body'>

          <!-- Site Default Time Zone -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Default Time Zone</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'default_timezone', 'class' => 'form-control', 'value' => $default_timezone, 'placeholder' => 'Default Time Zone', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: America/Chicago</i> - Default Site Time Zone.
          </div>

        </div>
      </div>
    </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          <h3 class='jumbotron-heading'>Paginator Limits</h3>
        </div>
        <div class='card-body'>

          <?php
          /** Check to see if Friends Plugin is installed, if it is show link **/
          if(file_exists(ROOTDIR.'app/Plugins/Friends/Controllers/Friends.php')){
          ?>
          <!-- Members Paginator Limit -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Members Paginator</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'users_pageinator_limit', 'class' => 'form-control', 'value' => $users_pageinator_limit, 'placeholder' => 'Members Paginator Limit', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 20</i> - How many Members to list per page on Members Pages.
          </div>
          <?php } ?>

          <!-- Members Paginator Limit -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Friends Paginator</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'friends_pageinator_limit', 'class' => 'form-control', 'value' => $friends_pageinator_limit, 'placeholder' => 'Friends Paginator Limit', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 20</i> - How many Friends to list per page on Friends Pages.
          </div>

        </div>
      </div>
    </div>

    <?php
    /** Check to see if Private Message Plugin is installed, if it is show link **/
    if(file_exists(ROOTDIR.'app/Plugins/Messages/Controllers/Messages.php')){
    ?>

    <div class='col-lg-12 col-md-12 col-sm-12'>
      <div class='card mb-3'>
        <div class='card-header h4'>
          <h3 class='jumbotron-heading'>Messages Plugin Settings</h3>
        </div>
        <div class='card-body'>

          <!-- Members Paginator Limit -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Messages Quota</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'message_quota_limit', 'class' => 'form-control', 'value' => $message_quota_limit, 'placeholder' => 'Messages Quota', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 50</i> - Messages Quota Limits how many messages each user can have in their Inbox.
          </div>

          <!-- Members Paginator Limit -->
          <div class='input-group mb-3' style='margin-bottom: 25px'>
            <div class="input-group-prepend">
              <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Messages Paginator Limit</span>
            </div>
            <?php echo Form::input(array('type' => 'text', 'name' => 'message_pageinator_limit', 'class' => 'form-control', 'value' => $message_pageinator_limit, 'placeholder' => 'Messages Paginator Limit', 'maxlength' => '255')); ?>
          </div>
          <div style='margin-bottom: 25px'>
            <i>Default: 10</i> - How many Messages to list per page on Messages Pages.
          </div>

        </div>
      </div>
    </div>

  <?php } ?>

  <div class='col-lg-12 col-md-12 col-sm-12'>
    <div class='card mb-3'>
      <div class='card-header h4'>
        <h3 class='jumbotron-heading'>Sweets Settings</h3>
      </div>
      <div class='card-body'>

        <!-- Members Paginator Limit -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Sweets Title Display</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'sweet_title_display', 'class' => 'form-control', 'value' => $sweet_title_display, 'placeholder' => 'Sweets Title', 'maxlength' => '255')); ?>
        </div>
        <div style='margin-bottom: 25px'>
          <i>Default: Sweets</i> - Text shown on sweets count displays. EX: Likes/+1s/Hearts
        </div>

        <!-- Members Paginator Limit -->
        <div class='input-group mb-3' style='margin-bottom: 25px'>
          <div class="input-group-prepend">
            <span class='input-group-text'><i class='fa fa-fw  fa-globe'></i> Sweets Button Display</span>
          </div>
          <?php echo Form::input(array('type' => 'text', 'name' => 'sweet_button_display', 'class' => 'form-control', 'value' => $sweet_button_display, 'placeholder' => 'Sweets Button', 'maxlength' => '255')); ?>
        </div>
        <div style='margin-bottom: 25px'>
          <i>Default: Sweet</i> - Text shown on Button for sweets. EX: Like/+1/Heart
        </div>

      </div>
    </div>
  </div>

    <div class='col-lg-12 col-md-12 col-sm-12'>
        <button class="btn btn-md btn-success" name="submit" type="submit">
            Update Site Advanced Settings
        </button>
        <!-- CSRF Token and What is Being Updated -->
        <input type="hidden" name="token_settings" value="<?php echo $data['csrfToken']; ?>" />
        <input type="hidden" name="update_advanced_settings" value="true" />
        <?php echo Form::close(); ?><Br><br>
    </div>
  </div>
</div>
