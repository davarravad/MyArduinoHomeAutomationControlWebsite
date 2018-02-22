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
					Use the following code with your AHACB board to communicate with the web server.
					You may copy and paste the code or use the download link below.  Your user data
					is already updated in the code.  Save and upload to your Arduino microcontroller.
					<hr>
					<a href="<?=DIR?>MAHArduinoCodeDownload" target="_blank" class="btn btn-success">Download Arduino File</a><hr>
					<div class='codeblock'>
						<div class='php' width='' align='left'>
							<pre class='prettyprint'>
								<b><i><font size=1>Your Arduino Code</font></i></b>
								<pre><code>
									<?php
										$server_address = $_SERVER['SERVER_ADDR'];
										$user_house_id = $data['MAHprofile']->house_id;
										$user_token = $data['MAHprofileHouse']->house_token;

										$codefile = highlight_file(ROOTDIR."assets/arduinocode/ahacb.ino", true);
										$codefile = str_replace('<br />', '', $codefile);
										$codefile = str_replace('char&nbsp;server[]&nbsp;=&nbsp;"***********"', 'char&nbsp;server[]&nbsp;=&nbsp;"'.$server_address.'"', $codefile);
										$codefile = str_replace('int&nbsp;house_id&nbsp;=&nbsp;"***********"', 'String&nbsp;house_id&nbsp;=&nbsp;"'.$user_house_id.'"', $codefile);
										$codefile = str_replace('String&nbsp;website_token&nbsp;=&nbsp;"***********"', 'String&nbsp;website_token&nbsp;=&nbsp;"'.$user_token.'"', $codefile);
										$codefile = str_replace('&nbsp;', ' ', $codefile);
										echo $codefile;
									?>
								</code></pre>
							</pre>
						</div>
					</div>
        </div>
    </div>
  </div>
</div>
