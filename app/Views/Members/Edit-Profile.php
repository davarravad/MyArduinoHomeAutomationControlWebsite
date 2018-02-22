<?php
/**
* Account Edit Profile View
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.0.0
*/

use Libs\Language, Libs\Form;
?>

<div class="col-lg-8 col-md-8 col-sm-8">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h1><?=$title;?></h1>
		</div>
		<div class="panel-body">
        <div class="col-xs-12">
            <h4><?=Language::show('edit_profile', 'Members'); ?> <strong><?php echo $data['profile']->username; ?></strong></h4>
            <hr>

            <form role="form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="first_name"><?=Language::show('members_profile_firstname', 'Members'); ?>: </label><span class="label label-danger pull-right"><?=Language::show('required', 'Members'); ?></span>
                    <input id="first_name" type="text" class="form-control" name="first_name" placeholder="<?=Language::show('members_profile_firstname', 'Members'); ?>" value="<?php echo $data['profile']->first_name; ?>">
                </div>
								<div class="form-group">
										<label for="last_name"><?=Language::show('members_profile_lastname', 'Members'); ?>: </label><span class="label label-danger pull-right"><?=Language::show('required', 'Members'); ?></span>
										<input id="last_name" type="text" class="form-control" name="last_name" placeholder="<?=Language::show('members_profile_lastname', 'Members'); ?>" value="<?php echo $data['profile']->last_name; ?>">
								</div>
                <div class='form-group'>
                    <label for="gender"><?=Language::show('members_profile_gender', 'Members'); ?>: </label><span class="label label-danger pull-right"><?=Language::show('required', 'Members'); ?></span>
                    <select class='form-control' id='gender' name='gender'>
                        <option value='male' <?php if($data['profile']->gender == "Male") echo "selected";?> >Male</option>
                        <option value='female' <?php if($data['profile']->gender == "Female") echo "selected";?> >Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="website"><?=Language::show('members_profile_website', 'Members'); ?>: </label>
                    <input id="website" type="website" class="form-control" name="website" placeholder="<?=Language::show('members_profile_website', 'Members'); ?>" value="<?php echo $data['profile']->website; ?>">
                </div>
                <?php if($data['profile']->userImage != ""){ ?>
	                <input id="oldImg" name="oldImg" type="hidden" value="<?php echo $data['profile']->userImage; ?>"">
	                <div class="form-group">
	                    <label for="email"><?=Language::show('members_profile_cur_photo', 'Members'); ?>: </label>
	                    <img alt="User Pic" src="<?php echo SITE_URL.IMG_DIR_PROFILE.$data['profile']->userImage; ?>" class="img-rounded img-responsive">
	                </div>
                <?php } ?>
                <div class="form-group">
                    <label class="control-label"><?=Language::show('members_profile_new_photo', 'Members'); ?></label>
                    <input type="file" class="form-control" accept="image/jpeg, image/gif, image/x-png" id="profilePic" name="profilePic">
                </div>
                <div class="form-group">
                    <label for="aboutMe"><?=Language::show('edit_profile_aboutme', 'Members'); ?>: </label>
                    <textarea id="aboutMe"  class="form-control" name="aboutMe" placeholder="<?=Language::show('edit_profile_aboutme', 'Members'); ?>" rows="5"><?php echo str_replace('<br />' , '', $data['profile']->aboutme); ?></textarea>
                </div>
								<?php
									/* Check to see if Private Message Module is installed, if it is show link */
									if(file_exists(ROOTDIR.'app/Plugins/Forum/Controllers/Forum.php')){
								?>
									<div class="form-group">
	                    <label for="signature"><?=Language::show('edit_profile_forum_sign', 'Members'); ?>: </label>
	                    <textarea id="signature"  class="form-control" name="signature" placeholder="<?=Language::show('edit_profile_forum_sign', 'Members'); ?>" rows="5"><?php echo str_replace('<br />' , '', $data['profile']->signature); ?></textarea>
	                </div>
								<?php } ?>
                <input type="hidden" name="token_editprofile" value="<?=$csrfToken;?>" />
                <input type="submit" name="submit" class="btn btn-primary" value="<?=Language::show('edit_profile_button', 'Members'); ?>">
            </form>
        </div>
    </div>
  </div>
</div>