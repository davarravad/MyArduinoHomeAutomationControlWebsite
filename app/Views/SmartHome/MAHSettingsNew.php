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
	<div class="card border-primary mb-3">
		<div class="card-header h4">
			<h1><?php echo  $data['title']?></h1>
		</div>
		<div class="card-body">
        <div class="col-xs-12">
            <h4>Home Automation Settings</h4>
            <hr>
							Welcome to the AHACB Smart Home Controller Website.  Please use
							the following form to create a new smart home profile.
						<hr>
						<form role="form" method="post" enctype="multipart/form-data">
								How many Arduino Home Automation Control Boards are you using
								in your house?
								<div class='input-group mb-3'>
									<div class='input-group-prepend'><span class='input-group-text'>Control Boards</span></div>
									<select class='form-control' id='boards' name='boards'>
										<option value='1'>1 Board</option>
										<option value='2'>2 Boards Stacked</option>
										<option value='3'>3 Boards Stacked</option>
										<option value='4'>4 Boards Stacked</option>
									</select>
								</div>
								How many Temperature Sensors do you plan to use in your house?
								<div class='input-group mb-3'>
									<div class='input-group-prepend'><span class='input-group-text'>Temperature Sensors</span></div>
									<select class='form-control' id='temp_sensors' name='temp_sensors'>
										<option value='1'>1 Temp Sensor</option>
										<option value='2'>2 Temp Sensors</option>
										<option value='3'>3 Temp Sensors</option>
									</select>
								</div>
								How many Garage Doors do you plan to control in your house?
								<div class='input-group mb-3'>
									<div class='input-group-prepend'><span class='input-group-text'>Garage Doors</span></div>
									<select class='form-control' id='garage_doors' name='garage_doors'>
										<option value='1'>1 Garage Door</option>
										<option value='2'>2 Garage Doors</option>
									</select>
								</div>
								<input type="hidden" name="new_house" value="true" />
								<input type="hidden" name="token_edithouse" value="<?=$csrfToken;?>" />
								<input type="submit" name="submit" class="btn btn-sm btn-success" value="Create New SHC Profile">
						</form>
        </div>
    </div>
  </div>
</div>
