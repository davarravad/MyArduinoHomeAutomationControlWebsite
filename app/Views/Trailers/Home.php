<?php
use Libs\Form;
?>


<div class="col-lg-8 col-md-8 col-sm-8">
	<div class="panel panel-default">
        <div class="panel-heading">
            <h1>Yard Checks Home</h1>
        </div>
        <div class="panel-body">
			<?php echo Form::open(array('method' => 'post', 'action' => SITE_URL.'YardChecks')); ?>
			Select Yard Location From Dropdown<br>
			<div class='input-group' style='margin-bottom: 25px'>
				<span class='input-group-addon'>Yard</span>
				<select class='form-control' id='yard_id' name='yard_id'>
					<option>Select Yard To Continue</option>
					<?php
					  foreach ($data['all_yards'] as $yard) {
						  echo "<option value='$yard->id'";
						  if($yard->id == $yard_info[0]->id){ echo "SELECTED"; }
						  echo ">$yard->name - $yard->city, $yard->state</option>";
					  }
					?>
				</select>
				<span class="input-group-btn">
					<button name='submit' type='submit' class="btn btn-success">Get Yard Checks</button>
				</span>
			</div>
			<?php echo Form::close(); ?>
			<hr>
            <p>Select from one of the following Trailer Counts to view data.</p>

			<?php

				if(isset($recent_yard_checks)){

					echo "<table class='table table-striped'><tr>";
					echo "<th>Location</th><th>Date</th><th>Total</th><th></th>";
					echo "</tr>";
					foreach($recent_yard_checks as $row) {
						echo "<tr><td>$row->location_name</td><td>".date("l F jS Y", strtotime($row->timestamp))."</td>";
						echo "<td>$row->trailertotal</td>";
						    echo "<td align='right'>";
							echo "<a href='".DIR."YardCheck/$row->id' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-pencil'></span></a>";
							echo "</td>";
						echo "</tr>";
					}
					echo "</table>";

				}


			?>

        </div>
		<?php
			// Check to see if there is more than one page
			if($data['pageLinks'] > "1"){
				echo "<div class='panel-footer' style='text-align: center'>";
				echo $data['pageLinks'];
				echo "</div>";
			}
		?>
    </div>
</div>
