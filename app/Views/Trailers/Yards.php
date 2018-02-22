<div class="col-lg-8 col-md-8 col-sm-8">
	<div class="panel panel-default">
        <div class="panel-heading">
            <h1>Yards List</h1>
        </div>
        <div class="panel-body">

            <p>Select from one of the following Trailer Counts to view data.</p>

			<?php

				if(isset($yards)){

					echo "<table class='table table-striped'><tr>";
					echo "<th>Name</th><th>City</th><th>State</th><th></th>";
					echo "</tr>";
					foreach($yards as $row) {
						echo "<tr><td>$row->name</td><td>$row->city</td>";
						echo "<td>$row->state</td>";
						    echo "<td align='right'>";
							echo "<a href='".DIR."YardEdit/$row->id' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-pencil'></span></a>";
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
