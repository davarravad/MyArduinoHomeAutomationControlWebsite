<?php
/**
* Install Script Step 5
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
*/

/** Install Success **/

/** Last thing we need to do is Copy Config.example.php to Config.php **/
if (file_exists(ROOTDIR.'app/Example-Config.php') && is_writable(ROOTDIR.'app')) {
	if(copy(ROOTDIR.'app/Example-Config.php', ROOTDIR.'app/Config.php')){
		$copy_file = true;
	}else{
		$copy_file = false;
	}
}else{
	$copy_file = false;
}

if(!$copy_file){
	echo "<div class='alert alert-danger'>There was an error creating Config.php.  You must manually rename Config.example.php to Config.php in the /app/ folder.</div>";
}else{
?>

<div class='card border-info mb-3'>
	<div class='card-header h4'>
		<h3>UAP 4 Installation Step 4</h3>
	</div>
	<div class='card-body'>
		UserApplePie 4 Has Successfully Installed on your Server.  <br>
		Make sure to go sign up for your site, as the first user to sign up is admin by default. <br>
		<br>
		Thank You for choosing UserApplePie to run your website.  Make sure to visit
		<a href='http://www.userapplepie.com/' target='_blank'>www.userapplepie.com</a>
		for updates, plugins, and much more!<br>
		<hr>
		You may change site settings in the future by editing /app/Config.php file.<br><br>
		Also if you like you can delete the Install Folder that is located in /app/.
		<hr>
		<a href='/' class='btn btn-primary btn-lg'>Click Here To Enjoy Your New Install</a>

	</div>
</div>
<?php } ?>
