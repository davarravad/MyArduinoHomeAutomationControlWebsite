<?php
/**
* MAH Settings Page
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
*/

use Libs\Language, Libs\Form;

if($data['MAHprofileHouse']->email_enable_doors == "1"){
	$eed_checked = "CHECKED";
}else{
	$eed_checked = "";
}
?>

<div class="col-lg-8 col-md-8 col-sm-8">
	<div class="card border-primary mb-3">
		<div class="card-header h4">
			<h1><?php echo  $data['title']?></h1>
		</div>
		<div class="card-body">
        <div class="col-xs-12">
            <h4>Home Automation Settings</h4>
            <hr>
							The following settings are needed for your AHACB to communicate with the
							web server.  The token is used for extra security.  You can create your own
							token or use the Generate New House Token.  Make sure to update your AHACB
							Code if you change any of the settings on this page.
						<hr>
						<strong>House ID</strong> : <?php echo  $data['MAHprofile']->house_id ?>
						<hr>
            <form role="form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <span class="label label-danger float-right"><?=Language::show('required', 'Members'); ?></span>
										<div class='input-group mb-3'>
											<div class='input-group-prepend'>
												<span class='input-group-text'>House Token: </span>
											</div>
                    	<input id="house_token" type="text" class="form-control" name="house_token" placeholder="House Token" value="<?php echo $data['MAHprofileHouse']->house_token; ?>">
										</div>
                </div>
								<div class="form-inline">
									<input type='checkbox' id='email_enable_doors' name='email_enable_doors' value='1' <?php echo $eed_checked; ?>> Enable Email Notification When a Door is Open for more than
									<input id="email_doors_minutes" type="text" class="form-control" name="email_doors_minutes" placeholder="0" maxlength="3" value="<?php echo $data['MAHprofileHouse']->email_doors_minutes; ?>" style="width: 50px; padding: 2px; margin: 4px;">
								 		 minutes.
								</div>
								<br>
                <input type="hidden" name="token_edithouse" value="<?=$csrfToken;?>" />
                <input type="submit" name="submit" class="btn btn-primary" value="Update Settings">
            </form>
						<hr>
						<form role="form" method="post" enctype="multipart/form-data">
								<input type="hidden" name="gen_new_house_token" value="true" />
								<input type="hidden" name="token_edithouse" value="<?=$csrfToken;?>" />
								<input type="submit" name="submit" class="btn btn-sm btn-danger" value="Generate New House Token">
						</form>
        </div>
    </div>
  </div>
</div>
