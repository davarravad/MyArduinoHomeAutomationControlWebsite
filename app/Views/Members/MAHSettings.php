<?php
/**
* MAH Settings Page
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
			<h1><?php echo  $data['title']?></h1>
		</div>
		<div class="panel-body">
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
                    <label for="house_token">House Token: </label><span class="label label-danger pull-right"><?=Language::show('required', 'Members'); ?></span>
                    <input id="house_token" type="text" class="form-control" name="house_token" placeholder="House Token" value="<?php echo $data['MAHprofileHouse']->house_token; ?>">
                </div>
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
