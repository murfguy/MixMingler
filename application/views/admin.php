<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>MixMingler Admin Panel</h1>
	</div>
	<p class="devNote">Admin features are planned to added/implemented alongside Community features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>


	<div class="btn-group d-flex" role="group">
		<button type="button" class="btn btn-info displayToggle" target="summaryView" disabled>Summary</button>
		<button type="button" class="btn btn-info displayToggle" target="userManager">Users</button>
		<button type="button" class="btn btn-info displayToggle" target="commManager">Communities</button>
	</div>

	

	<div class="row">
		<div class="col">
			<div id="summaryView">
				<h2>MixMingler Summary/Analytics</h2>
				<div class="row">
					<div class="infoBox col">
						<h4 class="infoHeader">Recent Logins</h4>
						<div class="infoInterior">
						<?php
						foreach ($logins as $user) {
							echo "<p>";
							echo userListLink(array('Username' => $user->Username,'AvatarURL' => $user->AvatarURL ));
							echo " <span class=\"postTime\">".getElapsedTimeString($user->LastLogin)."</span>";
							echo "</p>";
						}
					?>
						</div>
					</div><!-- infoBox recent Logins -->


					<div class="infoBox col">
						<h4 class="infoHeader">Recent Registrations</h4>
						<div class="infoInterior">
						<?php
						foreach ($registrations as $user) {
							echo "<p>";
							echo userListLink(array('Username' => $user->Username,'AvatarURL' => $user->AvatarURL ));
							echo " <span class=\"postTime\">".getElapsedTimeString($user->RegistrationTime)."</span>";
							echo "</p>";
						}
					
						?>
						</div>
					</div><!-- recent reg -->

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
					</div><!-- infoBox/Analytics -->
				</div> <!-- row -->

				<div class="row">
					<div class="infoBox col">
						<h4 class="infoHeader">Pending Community Requests</h4>
						<div class="infoInterior">
							<?php if (!empty($pendingCommunities)) { ?>
								<p style="font-size: 17px">Here are all pending requests. You can quick approve from here, but if you need to edit details, leave a note or request the request, please go to the Communities tab.</p>
							<table class="table table-bordered table-hover table-dark table-sm adminTable">
								<thead class="thead-dark">						
									<tr>
										<th scope="col" width="15%">Community Name</th>
										<th scope="col" width="15%">URL</th>
										<th scope="col" width="10%">Requester</th>
										<th scope="col" width="8%">Category</th>
										<th scope="col" width="10%">Requested on</th>
										<th scope="col" width="10%">Summary</th>
										<th scope="col">Description</th>
										<th scope="col" width="8%">Quick Approve</th>
									</tr>
								</thead>
								<tbody>
								<?php
								$baseParams = array(
										'userId' => $_SESSION['mixer_id'],
										'btnType' => 'mini',
										'displayType' => 'icon',
										'action'=>'approveCommunity', 
										'content'=>'thumbs-up', 
										'state'=>'primary', 
										'confirm' => true);

								foreach ($pendingCommunities as $community) {
									echo "<tr id=\"notice-".$community->Slug."\">";
										echo "<th scope=\"row\">$community->Name</th>";
										echo "<td>$community->Slug</td>";
										echo "<td>$community->FounderName</td>";
										echo "<td>$community->CategoryName</td>";
										echo "<td>".date('M. d, Y', strtotime($community->RequestTime))."</td>";
										echo "<td>$community->Summary</td>";
										echo "<td>$community->Description</td>";
										echo "<td>";
										echo action_button(array_merge($baseParams, array('communityId'=>$community->ID)));
										echo "</td>";
									echo "</tr>";
								} ?>
								</tbody>
							</table>
					 <?php } else { ?>
					 	<p>No pending communities.</p>
					 <?php } ?>
						</div>
					</div> <!-- infoBox / pending communities-->
				</div><!-- row / pending communities -->
			</div> <!-- summaryView -->

			<div id="userManager" class="inactiveView">
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
						echo form_label('User Name', 'username');

						$attributes = array(
							'class' => 'form-control form-control-sm',
							'placeholder' => 'Enter user\'s name.'
						);
						echo form_input('username', '', $attributes); 
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
			</div> <!-- userManager -->

			<div id="commManager" class="inactiveView">
				<h2>Communities</h2>

					<?php 
						$itemCount = 0;
						foreach ($pendingCommunities as $community) { ?>
						<div class="infoBox" id="process-<?php echo $community->Slug; ?>">
							<h4 class="infoHeader"><?php echo $community->Name; ?></h4>
							<div class="infoInterior">
								<p>Requested By: <a href="/user/<?php echo $community->FounderName; ?>" target="_blank"><?php echo $community->FounderName; ?></a> on <?php echo date('F d, Y', strtotime($community->RequestTime)); ?></p>
								<?php 
									$attributes = array(
										'id' => 'communityApproval-'.$community->Slug,
										'class' => 'communityApproval'
									);
									$hidden = array(
										'communityId' => $community->ID,
										'siteAdmin' => $_SESSION['mixer_id']
									);
									echo form_open('servlet/communityApproval', $attributes, $hidden);
								?>
								<div class="form-row">
									<div class="form-group col-md-4">
										<?php 
											echo form_label('Community Name', 'name');

											$attributes = array(
												'id' => 'name-'.$community->Slug,
												'class' => 'name form-control form-control-sm',
												'placeholder' => 'Enter the community\'s name.',
												'data-validation' => 'required length',
												'data-validation-length' => 'max32'
											);
											echo form_input('name', $community->Name, $attributes); 
										?>
									</div>
									<div class="form-group col-md-4">
										<?php 
											echo form_label('URL Id', 'slug');

											$attributes = array(
												'id' => 'slug-'.$community->Slug,
												'class' => 'slug form-control form-control-sm',
												'placeholder' => 'The text of the url of your community',
												'data-validation' => 'required length alphanumeric',
												'data-validation-length' => 'max32',
												'data-validation-allowing' => '-_'
											);
											echo form_input('slug', $community->Slug, $attributes); 
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
											'id' => 'category_id-'.$community->Slug,
											'class' => 'category_id form-control form-control-sm',
											'data-validation' => 'required'
										);

										echo form_label('Parent Category', 'category_id');
										echo form_dropdown('category_id', $options, $community->CategoryID, $attributes);
										//echo form_submit('submit', 'Apply Role');
									?>
									</div>
								</div><!-- .form-row -->
								<div class="form-group">
									<?php 
										echo form_label('Summary', 'summary');

										$attributes = array(
											'id' => 'summary-'.$community->Slug,
											'class' => 'summary form-control form-control-sm',
											'placeholder' => 'Shows up as hover text on thumbnails.',
											'data-validation' => 'required length',
											'data-validation-length' => 'max100'
										);
										echo form_input('summary', $community->Summary, $attributes); 
									?>
								</div>
								<div class="form-group">
									<?php
										$options = array(
											'name' => 'description',
											'rows' => '3'
										);

										$attributes = array(
											'id' => 'description-'.$community->Slug,
											'class' => 'description form-control form-control-sm',
											'placeholder' => 'Shows up on community details page.',
											'data-validation' => 'required length',
											'data-validation-length' => 'max500'
										);

										echo form_label('Description', 'description');
										echo form_textarea($options, $community->Description, $attributes);
									?>
								</div>
								<div class="form-group alert alert-warning">
									<p>You must approve or reject this community.</p>
									<?php 
										$attributes = array(
											'id' => 'status-'.$community->Slug,
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
											'id' => 'adminNote-'.$community->Slug,
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
			</div> <!-- #commManager -->


		</div><!--.col -->
	</div> <!--.row -->
	


</main>