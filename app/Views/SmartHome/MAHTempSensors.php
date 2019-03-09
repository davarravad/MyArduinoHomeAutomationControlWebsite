<?php
/**
* MAH Settings Page
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
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
					You can edit what each temp sensor is labled as on the website by changing the website title.
					The Alexa field is needed if you are using Alexa to read your temp sensors.  You can also
					enable or disable temp sensors for display on website.
					<hr>
					<form role="form" method="post" enctype="multipart/form-data">
						<?php
							if(isset($temp_sensors)){
								foreach ($temp_sensors as $temp_sensor) {
									if($temp_sensor->enable == "1"){
										$checked = "CHECKED";
									}else{
										$checked = "";
									}
									echo "<div class='form-group'>";
	                echo "<label for='temp_server_name'>Temp Settings For : $temp_sensor->temp_server_name</label><Br>";
									echo "<div class='input-group mb-3'>";
									echo "<div class='input-group-prepend'><span class='input-group-text' id='basic-addon1'>Website Title</span></div> ";
	                echo "<input id='temp_title' type='text' class='form-control' name='temp_title[$temp_sensor->temp_server_name]' placeholder='Temperature Website Title' value='$temp_sensor->temp_title' aria-describedby='basic-addon1'>";
									echo "</div>";
									echo "<div class='input-group mb-3'>";
									echo "<div class='input-group-prepend'><span class='input-group-text' id='basic-addon2'>Alexa</span></div> ";
									echo "<input id='temp_alexa_name' type='text' class='form-control' name='temp_alexa_name[$temp_sensor->temp_server_name]' placeholder='Temperature Alexa' value='$temp_sensor->temp_alexa_name' aria-describedby='basic-addon2'>";
									echo "</div>";
	                echo "</div>";
									echo "<input type='checkbox' id='enable' name='enable[$temp_sensor->temp_server_name]' value='1' $checked> Enable Temp Display On Website";
									echo "<hr>";
									echo "<input type='hidden' name='temp_server_name[$temp_sensor->temp_server_name]' value='$temp_sensor->temp_server_name' />";
								}
							}
						?>
						<input type="hidden" name="token_edithouse" value="<?=$csrfToken;?>" />
						<input type="submit" name="submit" class="btn btn-primary" value="Update Temperature Settings">
					</form>
        </div>
    </div>
  </div>
</div>
