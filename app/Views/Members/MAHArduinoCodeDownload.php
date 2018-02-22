<?php
/**
* MAH Code Download Page
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.0.0
*/
	use Libs\SuccessMessages;

	$filename = "ArduinoHomeAutomation_".date("Y-m-d").".ino";

	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=$filename");

	$server_address = $_SERVER['SERVER_ADDR'];
	$user_house_id = $data['MAHprofile']->house_id;
	$user_token = $data['MAHprofileHouse']->house_token;

	$codefile = highlight_file(ROOTDIR."assets/arduinocode/ahacb.ino", true);
	$codefile = str_replace('<br />', '', $codefile);
	$codefile = str_replace('char&nbsp;server[]&nbsp;=&nbsp;"***********"', 'char&nbsp;server[]&nbsp;=&nbsp;"'.$server_address.'"', $codefile);
	$codefile = str_replace('int&nbsp;house_id&nbsp;=&nbsp;"***********"', 'String&nbsp;house_id&nbsp;=&nbsp;"'.$user_house_id.'"', $codefile);
	$codefile = str_replace('String&nbsp;website_token&nbsp;=&nbsp;"***********"', 'String&nbsp;website_token&nbsp;=&nbsp;"'.$user_token.'"', $codefile);
	$codefile = str_replace('&nbsp;', ' ', $codefile);
	$codefile = str_replace('<code><span style="color: #000000">', ' ', $codefile);
	$codefile = str_replace('</span>', ' ', $codefile);
	$codefile = str_replace('</code>', ' ', $codefile);
	$codefile = html_entity_decode($codefile);
	echo $codefile;

?>
