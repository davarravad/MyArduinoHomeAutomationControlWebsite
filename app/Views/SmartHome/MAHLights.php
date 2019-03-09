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
					You can edit what each light is labled as on the website by changing the website title.
					The Alexa field is needed if you are using Alexa to control your lights.  You can also
					enable or disable lights for display on website.
					<hr>
					<form role="form" method="post" enctype="multipart/form-data">
						<?php
							if(isset($lights)){
								foreach ($lights as $light) {
									if($light->enable == "1"){
										$checked = "CHECKED";
									}else{
										$checked = "";
									}
									echo "<div class='form-group'>";
	                echo "<label for='relay_server_name'>Light Settings For : $light->relay_server_name</label><Br>";
									echo "<div class='input-group mb-3'>";
									echo "<div class='input-group-prepend'><span class='input-group-text' id='basic-addon1'>Website Title</span></div> ";
	                echo "<input id='relay_title' type='text' class='form-control' name='relay_title[$light->relay_server_name]' placeholder='Light Website Title' value='$light->relay_title' aria-describedby='basic-addon1'>";
									echo "</div>";
									echo "<div class='input-group mb-3'>";
									echo "<div class='input-group-prepend'><span class='input-group-text' id='basic-addon2'>Alexa</span></div> ";
									echo "<input id='relay_alexa_name' type='text' class='form-control' name='relay_alexa_name[$light->relay_server_name]' placeholder='Light Alexa' value='$light->relay_alexa_name' aria-describedby='basic-addon2'>";
									echo "</div>";
	                echo "</div>";
									echo "<input type='checkbox' id='enable' name='enable[$light->relay_server_name]' value='1' $checked> Enable Light Display On Website";
									echo "<hr>";
									echo "<input type='hidden' name='relay_server_name[$light->relay_server_name]' value='$light->relay_server_name' />";
								}
							}
						?>
						<input type="hidden" name="token_edithouse" value="<?=$csrfToken;?>" />
						<input type="submit" name="submit" class="btn btn-primary" value="Update Lights Settings">
					</form>
        </div>
    </div>
  </div>
</div>
