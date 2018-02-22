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
					You can edit what each door is labled as on the website by changing the website title.
					The Alexa field is needed if you are using Alexa to control your doors.  You can also
					enable or disable doors for display on website.
					<hr>
					<form role="form" method="post" enctype="multipart/form-data">
						<?php
							if(isset($doors)){
								foreach ($doors as $door) {
									if($door->enable == "1"){
										$checked = "CHECKED";
									}else{
										$checked = "";
									}
									echo "<div class='form-group'>";
	                echo "<label for='door_id'>Garage Door Settings For : $door->door_id</label><Br>";
									echo "<div class='input-group'>";
									echo "<span class='input-group-addon' id='basic-addon1'>Door ID</span> ";
	                echo "<input id='door_title' type='text' class='form-control' name='door_title[$door->door_id]' placeholder='Door Website Title' value='$door->door_title' aria-describedby='basic-addon1'>";
									echo "</div>";
									echo "<div class='input-group'>";
									echo "<span class='input-group-addon' id='basic-addon2'>Alexa</span> ";
									echo "<input id='door_alexa_name' type='text' class='form-control' name='door_alexa_name[$door->door_id]' placeholder='Door Alexa' value='$door->door_alexa_name' aria-describedby='basic-addon2'>";
									echo "</div>";
	                echo "</div>";
									echo "<input type='checkbox' id='enable' name='enable[$door->door_id]' value='1' $checked> Enable Door Display On Website";
									echo "<hr>";
									echo "<input type='hidden' name='door_id[$door->door_id]' value='$door->door_id' />";
								}
							}
						?>
						<input type="hidden" name="token_edithouse" value="<?=$csrfToken;?>" />
						<input type="submit" name="submit" class="btn btn-primary" value="Update Doors Settings">
					</form>
        </div>
    </div>
  </div>
</div>
