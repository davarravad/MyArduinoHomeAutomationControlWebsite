<?php
use Libs\Form;
?>


<div class="col-lg-8 col-md-8 col-sm-8">
	<div class="panel panel-default">
        <div class="panel-heading">
            <h1>Yard Check - <?php echo $yard_info[0]->name; ?></h1>
        </div>
        <div class="panel-body">

			  <?php echo Form::open(array('method' => 'post')); ?>
				<div class='col-lg-12 col-md-12 col-sm-12'>
				  <div class='panel panel-default'>
					<div class='panel-heading'>
					  Add Trailer to Yard Check
					</div>
					<div class='panel-body'>
					  <input type='hidden' name='token_yard_check' value='<?php echo $data['csrfToken'] ?>'>
					  <input type='hidden' name='yard_check' value='true' />

						<div class='input-group' style='margin-bottom: 25px'>
							<span class='input-group-addon'>Trailer #</span>
							<?php echo Form::input(array('type' => 'text', 'name' => 'trailer', 'class' => 'form-control', 'value' => $edit_count_data[0]->trailer, 'placeholder' => 'Trailer Number', 'maxlength' => '150')); ?>
						</div>

						<div class='input-group' style='margin-bottom: 25px'>
							<span class='input-group-addon'>Status</span>
							<select class='form-control' id='status' name='status'>
								<option value='Empty' <?php if($edit_count_data[0]->status == "Empty"){echo "SELECTED";}?> >Empty</option>
								<option value='<?php echo $data['yard_info'][0]->name; ?> Load' <?php if($edit_count_data[0]->status == $data['yard_info'][0]->name." Load"){echo "SELECTED";}?> ><?php echo $data['yard_info'][0]->name; ?> Load</option>
								<option value='Milan Load' <?php if($edit_count_data[0]->status == "Milan Load"){echo "SELECTED";}?> >Milan Load</option>
							</select>
						</div>

						<div class='input-group' style='margin-bottom: 0px'>
							<span class='input-group-addon'>Notes</span>
							<?php echo Form::input(array('type' => 'text', 'name' => 'notes', 'class' => 'form-control', 'value' => $edit_count_data[0]->notes, 'placeholder' => 'Trailer Notes', 'maxlength' => '255')); ?>
						</div>

					</div>
					<div class='panel-footer'>

					<?php
						if(isset($edit_count_data)){
							echo "<div class='row'>";
							echo "<div class='col-lg-6 col-md-6 col-sm-6'>";
							echo "<input type='hidden' name='new_update' value='update' />";
							echo "<button name='submit' type='submit' class='btn btn-sm btn-success'>Update Trailer</button>";
							echo Form::close();
							echo "</div>";

							echo "<div class='col-lg-6 col-md-6 col-sm-6' style='padding-top:12px'>";
							echo Form::open(array('method' => 'post'));
							echo "<input type='hidden' name='token_yard_check' value='".$data['csrfToken']."'>";
	  					  	echo "<input type='hidden' name='yard_check' value='true' />";
							echo "<input type='hidden' name='new_update' value='delete' />";
							echo "<input type='hidden' name='trailer_id' value='".$edit_count_data[0]->id."' />";
							echo "<button name='submit' type='submit' class='btn btn-xs btn-danger'>Delete Trailer</button>";
							echo Form::close();
							echo "</div></div>";

						}else{

							echo "<input type='hidden' name='new_update' value='new' />";
							echo "<button name='submit' type='submit' class='btn btn-sm btn-success'>Add Trailer</button>";
							echo Form::close();

						}
					?>

					</div>
				  </div>
				</div>


				<div class='col-lg-12 col-md-12 col-sm-12'>
					<div class='panel panel-default'>
						<div class='panel-heading'>
							Yard Check Totals
						</div>
						<div class='panel-body'>
							<div class='col-lg-4 col-md-4 col-sm-4'>
								<center>
									<b>Empty</b><Br>
									<?=$count_empty?>
								</center>
							</div>
							<div class='col-lg-4 col-md-4 col-sm-4'>
								<center>
									<b><?php echo $data['yard_info'][0]->name; ?></b><Br>
									<?=$count_pca?>
								</center>
							</div>
							<div class='col-lg-4 col-md-4 col-sm-4'>
								<center>
									<b>Mialn</b><Br>
									<?=$count_carrier?>
								</center>
							</div>
						</div>
					</div>
				</div>




			<?php

				if(isset($current_count)){

					echo "<table class='table table-striped'><tr>";
					echo "<th>Trailer #</th><th>Status</th><th>Notes</th><th></th>";
					echo "</tr>";
					foreach($current_count as $row) {
						echo "<tr><td>$row->trailer</td><td>$row->status</td><td>$row->notes</td>";
						    echo "<td align='right'>";
							echo "<a href='".DIR."YardCheck/$row->count_id/$row->id' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-pencil'></span></a>";
							echo "</td>";
						echo "</tr>";
					}
					echo "</table>";

				}


			?>
			<hr>
			<?php echo Form::open(array('method' => 'post')); ?>
			<input type='hidden' name='token_yard_check' value='<?php echo $data['csrfToken'] ?>'>
			<input type='hidden' name='yard_check_email' value='true' />
			<!-- Email Yard Check -->
			<div class='input-group' style='margin-bottom: 25px'>
				<span class='input-group-addon'><i class='glyphicon glyphicon-envelope'></i></span>
				<?php echo Form::input(array('type' => 'text', 'name' => 'yc_email', 'class' => 'form-control', 'placeholder' => 'TO Email Address', 'maxlength' => '100')); ?>
				<span class="input-group-btn">
					<button name='submit' type='submit' class='btn btn-default'>Email Yard Check</button>
				</span>
			</div>
			<?php echo Form::close(); ?>
			<hr>
			<a href='<?=SITE_URL?>EmailYardCheck/<?=$count_id?>' target='_blank' class='btn btn-xs btn-primary'>View In Email Format</a>
			<hr>
			<?php

				echo Form::open(array('method' => 'post'));
				echo "<input type='hidden' name='token_yard_check' value='".$data['csrfToken']."'>";
				echo "<input type='hidden' name='yard_check' value='true' />";
				echo "<input type='hidden' name='new_update' value='delete_yard_check' />";
				echo "<input type='hidden' name='yc_id' value='".$count_id."' />";
				echo "<button name='submit' type='submit' class='btn btn-xs btn-danger'>Delete Yard Check</button>";
				echo Form::close();

			?>
        </div>
    </div>
</div>
