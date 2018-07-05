<?php
/**
* Temps View
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.0.0
*/

use Libs\Language;
?>



<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="card">
    <div class="card-header">
      <h4>House Temperatures</h4>
    </div>
    <div class="card-body">
      <?php
        // Display current temps for house
        if(isset($current_temps_data)){
          echo "<div class='row'>";
          foreach ($current_temps_data as $cur_temp) {
            echo "
              <div class='col-lg-4 col-md-4 col-sm-6' style='padding-top:10px'>
                <div class='alert alert-success' role='alert'>
									<a href='".SITE_URL."MAHTemps/$cur_temp->id'>
                  	$cur_temp->temp_title :
                  	$cur_temp->temp_data &deg; F
									</a>
                </div>
              </div>
            ";
          }
          echo "</div>";
        }

      ?>
			<?php if(isset($data['temps_today']) && isset($data['temp_sensor_name'])){ ?>
			<h4>Temperatures for <?php echo $data['temp_sensor_name'];?></h4>
			<div class="canvas-wrapper">
				<canvas class="main-chart" id="line-chart" height="200" width="600"></canvas>
			</div>
			<?php } ?>
    </div>
  </div>
</div>
<?php
if(isset($data['temps_today']) && isset($data['temp_sensor_name'])){
	// Check for missing data within hourly range for full 24
	$temps_today = array();

	for ($i = 0; $i < 24; ++$i) {
	  $new = array();

	  foreach($data['temps_today'] as $old) {
	    if ($old->hour == $i) {
				$new['hour'] = $old->hour;
	      $new['temp'] = $old->temp;
	    }
			//echo "$i - (".$old->hour.") ".$new['hour']." - ".$new['temp']." <Br>";
	  }
		if(!isset($new['hour']) && !isset($new['temp'])){
			$new['hour'] = "$i";
			$new['temp'] = "0";
		}
	  $temps_today[] = $new;
	}

	// Ready Data For Output
	$hours_display = "";
	$temp_display = "";

	foreach ($temps_today as $row) {
		$hours_display .= '"';
		$hours_display .= $row['hour'];
		$hours_display .= '",';
		$temp_display .= '"';
		$temp_display .= $row['temp'];
		$temp_display .= '",';
	}
	$hours_display1 = rtrim($hours_display,'",');
	$hours_display2 = substr($hours_display1, 1);
	$temp_display1 = rtrim($temp_display,'",');
	$temp_display2 = substr($temp_display1, 1);


	// Check for missing data within hourly range for full 24
	$temps_yesterday = array();

	for ($i = 0; $i < 24; ++$i) {
	  $new = array();

	  foreach($data['temps_yesterday'] as $old) {
	    if ($old->hour == $i) {
				$new['hour'] = $old->hour;
	      $new['temp'] = $old->temp;
	    }
			//echo "$i - (".$old->hour.") ".$new['hour']." - ".$new['temp']." <Br>";
	  }
		if(!isset($new['hour']) && !isset($new['temp'])){
			$new['hour'] = "$i";
			$new['temp'] = "0";
		}
	  $temps_yesterday[] = $new;
	}
	// Ready Data For Output
	$temp_y_display = "";

	foreach ($temps_yesterday as $row) {
		$temp_y_display .= '"';
		$temp_y_display .= $row['temp'];
		$temp_y_display .= '",';
	}
	$temp_y_display1 = rtrim($temp_y_display,'",');
	$temp_y_display2 = substr($temp_y_display1, 1);

?>
	<script type="text/javascript">
	var lineChartData = {
			labels : ["<?php echo $hours_display2; ?>"],
			datasets : [
				{
					label: "Temps for Today",
					fillColor : "rgba(48, 164, 255, 0.2)",
					strokeColor : "rgba(48, 164, 255, 1)",
					pointColor : "rgba(48, 164, 255, 1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(48, 164, 255, 1)",
					data : ["<?php echo $temp_display2; ?>"]
				},
				{
					label: "Temps for Yesterday",
					fillColor : "rgba(220,220,220,0.2)",
					strokeColor : "rgba(220,220,220,1)",
					pointColor : "rgba(220,220,220,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(220,220,220,1)",
					data : ["<?php echo $temp_y_display2; ?>"]
				}
			]

		}

		window.onload = function(){
			var chart1 = document.getElementById("line-chart").getContext("2d");
			window.myLine = new Chart(chart1).Line(lineChartData, {
				responsive: true
			});
		};
	</script>
	<script src="https://shc.myarduinohome.com/Templates/AdminPanel/Assets/js/chart.min.js" type="text/javascript"></script>
<?php } ?>
