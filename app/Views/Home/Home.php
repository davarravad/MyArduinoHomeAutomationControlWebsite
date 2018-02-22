<?php
/**
* Home View
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.0.0
*/

use Libs\Language;
?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="panel panel-default">
        <div class="panel-heading">
            <h4>Welcome to Your Arduino Smart Home Control Console!</h4>
        </div>
        <div class="panel-body">



				<?php
					// Display all Relays in order by id
					if(isset($temps_data)){
						echo "<div class='row'>";
						foreach ($temps_data as $temp) {
							echo "
								<div class='col-lg-4 col-md-4 col-sm-6' style='padding-top:10px'>
									<div class='alert alert-success' role='alert'>
										$temp->temp_title :
										$temp->temp_data &deg; F
									</div>
								</div>
							";
						}
						echo "</div><hr>";
					}
				?>

				<?php
					// Display all Garage Doors in order by id
					if(isset($garage_doors_data)){
						echo "<div class='row'>";
						foreach ($garage_doors_data as $garage) {
							if($garage->door_status == "OPEN"){
								$door_status = $garage->door_title." is Open";
								$door_link = DIR."GarageControl/Update/".$garage->door_id."/PUSH_BUTTON";
								$door_btn = "success";
								$door_oc = "Close Door";
							}else{
								$door_status = $garage->door_title." is Closed";
								$door_link = DIR."GarageControl/Update/".$garage->door_id."/PUSH_BUTTON";
								$door_btn = "danger";
								$door_oc = "Open Door";
							}
							echo "
								<div class='col-lg-12 col-md-12 col-sm-12' style='padding-top:10px'>
									<div class='alert alert-warning' role='alert'>
										<div class='row'>
											<div class='col-lg-6 col-md-6 col-sm-6'>
												$door_status
											</div>
											<div class='col-lg-6 col-md-6 col-sm-6'>
												<a href='$door_link' class='btn btn-xs btn-$door_btn pull-right'>
													$door_oc
												</a>
											</div>
										</div>
									</div>
								</div>
							";
						}
						echo "</div><hr>";
					}
				?>

			<div class='row'>
				<div class='col-lg-4 col-md-4 col-sm-6' style='padding-top:10px'>
					<div class='alert alert-info' role='alert'>
							<?=$all_lights_title?>
							<a href='<?=$all_lights_link?>' class='btn btn-xs btn-<?=$all_lights_btn?> pull-right'>
								<?=$all_lights_status?>
							</a>
					</div>
				</div>
				<?php
					// Display all Relays in order by id
					if(isset($light_relays_data)){
						foreach ($light_relays_data as $relay) {
							$relay_title = $relay->relay_title;
							$relay_server_name = $relay->relay_server_name;
							// Check to see if relay is on or off and set links and color
							if($relay->relay_action == "LIGHT_ON"){
								$light_title = $relay_title." is On";
								$light_status = "Turn OFF";
								$light_link = DIR."RelayControl/Update/$relay_server_name/LIGHT_OFF";
								$light_btn = "success";
							}else{
								$light_title = $relay_title." is Off";
								$light_status = "Turn ON";
								$light_link = DIR."RelayControl/Update/$relay_server_name/LIGHT_ON";
								$light_btn = "danger";
							}
							echo "
								<div class='col-lg-4 col-md-4 col-sm-6' style='padding-top:10px'>
									<div class='alert alert-info' role='alert'>
										<div class='row'>
											<div class='col-lg-6 col-md-6 col-sm-6'>
												$light_title
											</div>
											<div class='col-lg-6 col-md-6 col-sm-6'>
												<a href='$light_link' class='btn btn-xs btn-$light_btn pull-right'>
													$light_status
												</a>
											</div>
										</div>
									</div>
								</div>
							";
						}
					}
				?>
			</div>
        </div>
    </div>
</div>
