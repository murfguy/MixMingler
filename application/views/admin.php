<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>MixMingler Admin Panel</h1>
	</div>
	<p class="devNote">Admin features are planned to added/implemented alongside Community features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>
	<div id="glance" class="container">

	<div class="row">
		<nav class="nav flex-column col-2">
		  <a class="viewToggle btn btn-secondary" category="glance">At a Glance</a>
		  <a class="viewToggle btn btn-secondary " category="users">User Management</a>
		  <a class="viewToggle btn btn-secondary " category="communities">Communities</a>
		</nav>

		<div id="glance" class="mainView activeView container col">
			<h2>At a Glance</h2>
			<div class="row">

				<div class="infoBox col">
					<h4 class="infoHeader">Recent Logins</h4>
					<div class="infoInterior">
					<?php
					foreach ($logins as $user) {
						echo "<a href=\"/user/$user->name_token\"><img src=\"$user->avatarURL\" class=\"avatar thin-border\" width=\"25px\"> $user->name_token</a> <span class=\"postTime\">$user->loginTime</span><br>";
					}
				?>
					</div>
				</div>


				<div class="infoBox col">
					<h4 class="infoHeader">Recent Registrations</h4>
					<div class="infoInterior">
					<?php
					foreach ($registrations as $user) {
						echo "<a href=\"/user/$user->name_token\"><img src=\"$user->avatarURL\" class=\"avatar thin-border\" width=\"25px\"> $user->name_token</a><br>";
					}
				
					?>
					</div>
				</div>

				<div class="infoBox col">
					<h4 class="infoHeader">Analytics</h4>
					<div class="infoInterior">
						<p>Pending Feature</p>
						<ul>
							<li>New registrations</li>
							<li>New syncs</li>
							<li>Total streams seen</li>
							<li>Community activity</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="infoBox col">
					<h4 class="infoHeader">Pending Communitiy Requests</h4>
					<div class="infoInterior">
						<table class="table table-striped">							
							<tr>
								<th scope="col">Community Name</th>
								<th scope="col">URL Slug</th>
								<th scope="col">Requested By</th>
								<th scope="col">Category</th>
								<th scope="col">Requested on</th>
								<th scope="col">Summary</th>
							</tr>
							<?php
							foreach ($pendingCommunities as $community) {
								echo "<tr>";
									echo "<td>$community->long_name</td>";
									echo "<td>$community->slug</td>";
									echo "<td>$community->founder_name</td>";
									echo "<td>$community->category_name</td>";
									echo "<td>".date('M. d, Y', strtotime($community->requestTime))."</td>";
									echo "<td>$community->summary</td>";
								echo "</tr>";
							}						
							?>

						</table>
					</div>
				</div>
			</div>
			
		</div>

		<div id="users" class="mainView inactiveView container col">
			<h2>Users</h2>

			<div class="infoBox col-5">
				<h4 class="infoHeader">Apply Role</h4>
				<div class="infoInterior">
					
				

			<?php 
				$attributes = array('id' => 'applyRole');
				echo form_open('servlet/applyUserRole', $attributes); 
			?>
				<div class="form-row">
				<div class="form-group col-md-6">
				<?php 
					echo form_label('User Name', 'name_token');

					$attributes = array(
						'class' => 'form-control form-control-sm',
						'placeholder' => 'Enter user\'s name.'
					);
					echo form_input('name_token', '', $attributes); 
				?>
				</div>
				<div class="form-group col-md-6">
				<?
				$options = array(
			        'admin' => 'Admin',
			        'dev' => 'Developer',
			        'user' => 'User'
				);
				
				$attributes = array(
					'class' => 'form-control form-control-sm'
				);

				echo form_label('Select a Role', 'roles');
				echo form_dropdown('roles', $options, 'user', $attributes);
				//echo form_submit('submit', 'Apply Role');
			?>
				</div>
			</div><!-- .form-row -->
			<button class="btn btn-primary applyRole">Apply Role</button>
			<?php
				echo form_close();
			?>
				</div>
			</div>
	
		</div>

		<div id="communities" class="mainView inactiveView container col">
			<h2>Pending Communities</h2>

				<?php 
					$itemCount = 0;
					foreach ($pendingCommunities as $community) { ?>
					<div class="infoBox">
						<h4 class="infoHeader"><?php echo $community->long_name; ?></h4>
						<div class="infoInterior">
							<p>Requested By: <a href="/user/<?php echo $community->founder_name; ?>" target="_blank"><?php echo $community->founder_name; ?></a> on <?php echo date('F d, Y', strtotime($community->requestTime)); ?></p>
							<?php 
								$attributes = array(
									'id' => 'communityApproval-'.$community->slug,
									'class' => 'communityApproval'
								);
								$hidden = array(
									'commId' => $community->id,
									'siteAdmin' => $_SESSION['mixer_id']
								);
								echo form_open('servlet/communityApproval', $attributes, $hidden);
							?>
							<div class="form-row">
								<div class="form-group col-md-4">
									<?php 
										echo form_label('Community Name', 'long_name');

										$attributes = array(
											'id' => 'long_name-'.$community->slug,
											'class' => 'long_name form-control form-control-sm',
											'placeholder' => 'Enter the community\'s name.',
											'data-validation' => 'required length',
											'data-validation-length' => 'max32'
										);
										echo form_input('long_name', $community->long_name, $attributes); 
									?>
								</div>
								<div class="form-group col-md-4">
									<?php 
										echo form_label('URL Id', 'slug');

										$attributes = array(
											'id' => 'slug-'.$community->slug,
											'class' => 'slug form-control form-control-sm',
											'placeholder' => 'The text of the url of your community',
											'data-validation' => 'required length alphanumeric',
											'data-validation-length' => 'max32',
											'data-validation-allowing' => '-_'
										);
										echo form_input('slug', $community->slug, $attributes); 
									?>
								</div>
								<div class="form-group col-md-4">
									<?
									$options = array(
								        '' => '-- Select a Category --',
								        '1' => 'Stream Style',
								        '2' => 'Content',
								        '3' => 'Platform',
								        '4' => 'Regional',
								        '5' => 'Games',
								        '6' => 'Misc.',
								        '7' => 'Streamers'
									);
									
									$attributes = array(
										'id' => 'category_id-'.$community->slug,
										'class' => 'category_id form-control form-control-sm',
										'data-validation' => 'required'
									);

									echo form_label('Parent Category', 'category_id');
									echo form_dropdown('category_id', $options, $community->category_id, $attributes);
									//echo form_submit('submit', 'Apply Role');
								?>
								</div>
							</div><!-- .form-row -->
							<div class="form-group">
								<?php 
									echo form_label('Summary', 'summary');

									$attributes = array(
										'id' => 'summary-'.$community->slug,
										'class' => 'summary form-control form-control-sm',
										'placeholder' => 'Shows up as hover text on thumbnails.',
										'data-validation' => 'required length',
										'data-validation-length' => 'max100'
									);
									echo form_input('summary', $community->summary, $attributes); 
								?>
							</div>
							<div class="form-group">
								<?php
									$options = array(
										'name' => 'description',
										'rows' => '3'
									);

									$attributes = array(
										'id' => 'description-'.$community->slug,
										'class' => 'description form-control form-control-sm',
										'placeholder' => 'Shows up on community details page.',
										'data-validation' => 'required length',
										'data-validation-length' => 'max500'
									);

									echo form_label('Description', 'description');
									echo form_textarea($options, $community->description, $attributes);
								?>
							</div>
							<div class="form-group alert alert-warning">
								<p>You must approve or reject this community.</p>
								<?php 
									$attributes = array(
										'id' => 'status-'.$community->slug,
										'class' => 'status',
										'data-validation' => 'required',
									);

										echo form_radio('status', 'approved', FALSE, $attributes);
										echo form_label('Approve', 'status');
								
									echo form_radio('status', 'rejected', FALSE, $attributes); 
									echo form_label('Reject', 'status');
								?>
							</div>
							<div class="form-group">
								<?php 
									echo form_label('Admin Notes', 'adminNote');

									$attributes = array(
										'id' => 'adminNote-'.$community->slug,
										'class' => 'adminNote form-control form-control-sm',
										'placeholder' => 'Put any notes about approval changes or rejection here.',
										'data-validation' => 'length',
										'data-validation-length' => 'max100'
									);
									echo form_input('adminNote', '', $attributes); 
								?>
							</div>
							<button class="setApproval btn btn-primary">Submit Approval</button>
							<?php echo form_close(); ?>
						</div>
					</div>
				<?php $itemCount++; } ?>




	
		</div>


		
		</div>
	</div>


</main>