<?php
use Libs\Form;
?>


<div class="col-lg-8 col-md-8 col-sm-8">
	<div class="panel panel-default">
        <div class="panel-heading">
			<?php
			if(isset($edit_yard_data)){
				echo "<h1>Edit Yard</h1>";
			}else{
				echo "<h1>Add New Yard</h1>";
			}
			?>

        </div>
        <div class="panel-body">

			  <?php echo Form::open(array('method' => 'post')); ?>

					  <input type='hidden' name='token_yard_edit' value='<?php echo $data['csrfToken'] ?>'>
					  <input type='hidden' name='yard_edit' value='true' />

						<div class='input-group' style='margin-bottom: 25px'>
							<span class='input-group-addon'>Customer Name</span>
							<?php echo Form::input(array('type' => 'text', 'name' => 'name', 'class' => 'form-control', 'value' => $edit_yard_data_name, 'placeholder' => 'Customer Name', 'maxlength' => '150')); ?>
						</div>

						<div class='input-group' style='margin-bottom: 25px'>
							<span class='input-group-addon'>Address</span>
							<?php echo Form::input(array('type' => 'text', 'name' => 'address', 'class' => 'form-control', 'value' => $edit_yard_data_address, 'placeholder' => 'Customer Address', 'maxlength' => '150')); ?>
						</div>

						<div class='input-group' style='margin-bottom: 25px'>
							<span class='input-group-addon'>City</span>
							<?php echo Form::input(array('type' => 'text', 'name' => 'city', 'class' => 'form-control', 'value' => $edit_yard_data_city, 'placeholder' => 'Customer City', 'maxlength' => '150')); ?>
						</div>

						<div class='input-group' style='margin-bottom: 25px'>
							<span class='input-group-addon'>State</span>
							<?php echo Form::input(array('type' => 'text', 'name' => 'state', 'class' => 'form-control', 'value' => $edit_yard_data_state, 'placeholder' => 'Customer State', 'maxlength' => '150')); ?>
						</div>

						<div class='input-group' style='margin-bottom: 25px'>
							<span class='input-group-addon'>ZIP</span>
							<?php echo Form::input(array('type' => 'text', 'name' => 'zip', 'class' => 'form-control', 'value' => $edit_yard_data_zip, 'placeholder' => 'Customer Zip', 'maxlength' => '150')); ?>
						</div>



					<?php
						if(isset($edit_yard_data)){
							echo "<div class='row'>";
							echo "<div class='col-lg-6 col-md-6 col-sm-6'>";
							echo "<input type='hidden' name='new_update' value='update' />";
							echo "<button name='submit' type='submit' class='btn btn-sm btn-success'>Update Yard</button>";
							echo Form::close();
							echo "</div>";

							echo "<div class='col-lg-6 col-md-6 col-sm-6' style='padding-top:12px'>";
							echo Form::open(array('method' => 'post'));
							echo "<input type='hidden' name='token_yard_edit' value='".$data['csrfToken']."'>";
							echo "<input type='hidden' name='yard_edit' value='true' />";
							echo "<input type='hidden' name='new_update' value='delete_yard' />";
							echo "<input type='hidden' name='yard_id' value='".$yard_id."' />";
							echo "<input type='hidden' name='name' value='".$edit_yard_data_name."' />";
							echo "<button name='submit' type='submit' class='btn btn-xs btn-danger'>Delete Yard</button>";
							echo Form::close();
							echo "</div></div>";

						}else{

							echo "<input type='hidden' name='new_update' value='new' />";
							echo "<button name='submit' type='submit' class='btn btn-sm btn-success'>Add New Yard</button>";
							echo Form::close();

						}
					?>

			  <?php echo Form::close(); ?>



        </div>
    </div>
</div>
