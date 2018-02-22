<?php
use Libs\Form;
?>


<div class="col-lg-8 col-md-8 col-sm-8">
	<div class="panel panel-default">
        <div class="panel-heading">
            <h1>New Yard Check</h1>
        </div>
        <div class="panel-body">

			  <?php echo Form::open(array('method' => 'post')); ?>
				<div class='col-lg-12 col-md-12 col-sm-12'>
				  <div class='panel panel-default'>
					<div class='panel-heading'>
					  Start New Yard Check
					</div>
					<div class='panel-body'>
					  <input type='hidden' name='token_yard_check' value='<?php echo $data['csrfToken'] ?>'>
					  Select Yard Location From Dropdown<br>
					  <div class='input-group' style='margin-bottom: 25px'>
						  <span class='input-group-addon'>Status</span>
						  <select class='form-control' id='yard_id' name='yard_id'>
							  <option>Select Yard To Continue</option>
							  <?php
							  	foreach ($data['all_yards'] as $yard) {
							  		echo "<option value='$yard->id'>$yard->name - $yard->city, $yard->state</option>";
							  	}
							  ?>
						  </select>
					  </div>
					  <input type='hidden' name='create_yard_check' value='true' />
					  <button name='submit' type='submit' class="btn btn-sm btn-success">Start New Yard Check</button>
					</div>
				  </div>
				</div>
			  <?php echo Form::close(); ?>


        </div>
    </div>
</div>
