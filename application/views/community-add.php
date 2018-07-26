<main role="main" class="container">
<div class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Create a Community</h1>
	</div>
	<?php 
		$success = true;

		if ($creationCriteria['isLoggedIn']) {
			if ($creationCriteria['bannedFromCreation'] == false) {
				if ($creationCriteria['agedEnough'] == false) {
					echo "<div class=\"alert alert-danger\" role=\"alert\"><h4>You are not eligible for community creation!</h4><strong>Failed Criteria:</strong> Your mixer account is too young.</div>";
					$success = false;
				} 

				if ($creationCriteria['recentlyFounded'] == true) {
					echo "<div class=\"alert alert-danger\" role=\"alert\"><h4>You are not eligible for community creation!</h4><strong>Failed Criteria:</strong> You founded a community too recently.</div>";
					$success = false;
				} 


				if ($creationCriteria['pendingApproval'] == true) {
					echo "<div class=\"alert alert-danger\" role=\"alert\"><h4>You are not eligible for community creation!</h4><strong>Failed Criteria:</strong> You have a community awaiting approval.</div>";
					$success = false;
				} 
			} else {
				echo "<div class=\"alert alert-danger\" role=\"alert\"><h4>You are not eligible for community creation!</h4><strong>Failed Criteria:</strong> You are banned from making communities.</div>";
				$success = false;
			}
		} else {
			echo "<div class=\"alert alert-danger\" role=\"alert\"><h4>You are not eligible for community creation!</h4><strong>Failed Criteria:</strong> You are not logged in.</div>";
			$success = false;
		}
		

		if ($success) {
			//echo "<li>You meet all the current criteria to create a community!";
			//echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Congrats!</strong> You meet all the criteria to create a community!</div>";

			?>
		
					<p>You are clear to request the foundation of a new community! New communities must be approved by the site admins before the community is founded properly.

					<p>You may not request a new community while this request is pending.You may not request a new community until two weeks after this one is approved. If rejected, you can try again right away. Admins are allowed to bypass the two week criteria, but only if there is adequate reason to do so.</p>

					<p>Otherwise, please fill out the information below and we'll get his ball rolling!</p>

					<?php 
						$attributes = array('id' => 'requestCommunity');
						echo form_open('servlet/requestCommunity', $attributes); 
					?>

					<div class="form-row">
						<div class="form-group col-md-4">
							<?php 
								echo form_label('Community Name', 'long_name');

								$attributes = array(
									'class' => 'form-control form-control-sm',
									'placeholder' => 'Enter the community\'s name.'
								);
								echo form_input('long_name', '', $attributes); 
							?>
						</div>
						<div class="form-group col-md-4">
							<?php 
								echo form_label('URL Id', 'slug');

								$attributes = array(
									'class' => 'form-control form-control-sm',
									'placeholder' => 'The text of the url of your community'
								);
								echo form_input('slug', '', $attributes); 
							?>
						</div>

						<div class="form-group col-md-4">
							<?
							$options = array(
						        '1' => 'Stream Style',
						        '2' => 'Content',
						        '3' => 'Platform',
						        '4' => 'Regional',
						        '5' => 'Games',
						        '6' => 'Misc.',
						        '7' => 'Streamers'
							);
							
							$attributes = array(
								'class' => 'form-control form-control-sm'
							);

							echo form_label('Parent Category', 'category_id');
							echo form_dropdown('category_id', $options, 'user', $attributes);
							//echo form_submit('submit', 'Apply Role');
						?>
						</div>

					</div><!-- .form-row -->


					<div class="form-row">
						<div class="form-group col-md-6">
							<?php
								$attributes = array(
									'class' => 'form-control form-control-sm',
									'placeholder' => 'Shows up as hover text on thumbnails.'
								);
								echo form_label('Short Description', 'short_description');
								echo form_textarea('short_description', '', $attributes);
							?>
						</div>
						<div class="form-group col-md-6">
							<?php
								$attributes = array(
									'class' => 'form-control form-control-sm',
									'placeholder' => 'Shows up on community details page.'
								);

								echo form_label('Long Description', 'long_description');
								echo form_textarea('long_description', '', $attributes);
							?>
						</div>
					</div><!-- .form-row -->

					<button class="btn btn-primary requestCommunity">Submit Request</button>
					<?php
						echo form_close();
					?>
			
			<?php
		} else {
			?><p>Unfortunatly, you are not authorized to make a new community at this time for the reasons outlined above. Please rectify the above issues and then try again.</p><?php
		}
	?>
</div>
</main>