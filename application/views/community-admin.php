<?php
	$view = "memberManager";
	$subView = "allMembers";
?>
<main role="main" class="container">
	<div class="pageHeader">
		<h1><?php echo $community->Name; ?> Moderation Page <?php echo devNotes('community-admin'); ?></h1>
	</div>

	<div class="container">
		<?php 
			$hasAccess = true;
			if ($community->Status == 'approved') { $hasAccess = false; ?>
			<div id="foundCommunityNotice" class="alert alert-success">
				<h4>Congrats! Your community was approved!</h4>
				<p>You're just a few short steps away from making this a full-fledged MixMingler community! Simply finalize your details and then hit the "Found Community" button. Once founded, you'll need to wait a bit before you can make another community. Until then, let's work towards making this the best community you can make it!</p>

				<?php 
					$attributes = array('id' => 'foundCommunity');
					$hidden = array(
						'commId' => $community->ID,
						'mixerUser_id' => $_SESSION['mixer_id']
					);
					echo form_open('servlet/foundCommunity', $attributes, $hidden); 
				?>
				<div class="form-row">
					<div class="form-group col-md-4">
						<p>What state would you like the community to be?</p>
						<?php 
							$attributes = array(
								'id' => 'statusOnFoundation',
								'class' => 'statusOnFoundation',
								'name' => 'status',
								'data-validation' => 'required',
							);

							echo form_radio('status', 'open', TRUE, $attributes);
							echo form_label(' Open (accepting members)', 'requireApproval');
							echo "<br>";
							echo form_radio('status', 'closed', FALSE, $attributes); 
							echo form_label(' Closed (not accepting members)', 'requireApproval');
						?>
						</div>

						<div class="form-group col-md-4">
							<p>Require members to be approved before joining?</p>
						<?php 
							$attributes = array(
								'id' => 'requireApproval',
								'class' => 'requireApproval',
								'data-validation' => 'required',
							);

							echo form_radio('requireApproval', 'no', TRUE, $attributes);
							echo form_label(' No (members may join freely)', 'requireApproval');
							echo "<br>";
							echo form_radio('requireApproval', 'yes', FALSE, $attributes); 
							echo form_label(' Yes (members require admin/moderator approval before joining)', 'requireApproval');
						?>
						</div>
				</div>
				<p>After you've founded your community, you'll be able to edit some additional details such as adding cover art and setting a Discord channel.</p>
				<button class="foundButton btn btn-lg btn-primary">Found the "<?php echo $community->Name; ?>" Community!</button>
				<?php echo form_close(); ?>
			</div>
		<?php } ?>

		<?php if ($community->Status == 'rejected') { $hasAccess = false; ?>
			<div class="alert alert-danger">
				<h4>Sorry! Your community was rejected!</h4>
				<p>Alas, there was something that made us decide that this community doesn't quite work right now.</p>
				<p>The admin who rejected your community left this note:</p>
					<blockquote><?php 
						if (!empty( $community->AdminNote)) {
							echo $community->AdminNote; 
						} else {
							echo "No note was left.";
						}
					?></blockquote>
				<p>The only action you have right now is to delete this community. However, once you do, you are free to try and make a new commmunity again. Please note that repeat attempts to found a community in direct opposition to admin reasoning can result in being banned from making communities.</p>
				<p><?php 
					$buttonParams = [
						'communityId' => $community->ID,
						'confirm' => true,
						'action' => 'deleteCommunity',
						'content' => 'Delete Community',
						'state' => 'danger'
					];
					echo action_button($buttonParams); ?></p>
			</div>
		<?php } ?>

		<?php if ($community->Status == 'pending') { $hasAccess = false; ?>
			<div class="alert alert-warning">
				<h4>Your community is pending approval!</h4>
				<p>WOAH!!!! Slow your held horse roll there! Your community is still awaiting admin approval. Once it's ready though, we'll let you know!</p>
			</div>
		<?php } ?>

		<?php 
			if ($hasAccess) {
		?>
		<p>Welcome, <?php echo $currentUser->token; ?>.<?php 
			if ($currentUser->isAdmin) {
				echo " You are the admin.";
			}

			if ($currentUser->isMod) {
				echo " You are a moderator.";
			}
		?></p>

		<?php if ($currentUser->isNewAdmin) { ?>
			<div id="processTransfer" class="alert alert-warning">
				<h2>Community Ownership Transfer Request</h2>
				<p>The community admin has initiated a transfer of ownership to you. Please approve or deny the request.</p>
				<p>
					<?php 
					$baseParams = [
						'communityId' 		=> $community->ID,
						'userId'			=> $_SESSION['mixer_id'],
						'confirm'			=> 'true',
						'displayType'		=> 'text',
					];
					
					$buttonParams = [
						'state'				=> 'success',
						'action'			=> 'acceptTransfer',
						'content'			=> 'I Accept Ownership',];

					echo action_button(array_merge($baseParams, $buttonParams)); 

					$buttonParams = [
						'state'				=> 'danger',
						'action'			=> 'rejectTransfer',
						'content'			=> 'I Reject Ownership',];

					echo " ".action_button(array_merge($baseParams, $buttonParams)); 


					?></p>
			</div>

		<?php } ?>


		<div class="btn-group d-flex" role="group">
			<button type="button" class="btn btn-primary" onclick="window.location.href = '/community/<?php echo $community->Slug; ?>/';" data-toggle="tooltip" title="Back to Community Page"><i class="fas fa-arrow-left"></i></button>
			<button type="button" class="btn btn-info displayToggle" target="summaryView" <?php if ($view == "summaryView") { echo "disabled"; } ?>>Summary</button>
			<button type="button" class="btn btn-info displayToggle" target="memberManager" <?php if ($view == "memberManager") { echo "disabled"; } ?>>Members</button>
			<?php if ($currentUser->isAdmin) { ?><button type="button" class="btn btn-info displayToggle" target="settingsManager"<?php if ($view == "settingsManager") { echo "disabled"; } ?>>Settings</button><?php } ?>
		</div>


	<div class="row">

		<div class="col">
			<div id="summaryView" class="<?php if ($view != "summaryView") { echo "inactiveView"; } ?>">
				<h2>Community Summary</h2>
				<p class="devNote">coming soon</p>
			</div> <!-- summary view -->

			<div id="memberManager" class="<?php if ($view != "memberManager") { echo "inactiveView"; } ?>">

				<div class="pageHeader">
					<h2>Member Management</h2>
				</div>

				<div class="btn-group btn-group-justified" style="width:50%" role="group">
					<button type="button" class="btn btn-info displayToggle" target="allMembers" <?php if ($subView == "allMembers") { echo "disabled"; } ?>>All Members</button>
					<?php if ($community->isApprovalRequired) { ?><button type="button" class="btn btn-info displayToggle" target="pendingMembers" <?php if ($subView == "pendingMembers") { echo "disabled"; } ?>>Pending Members</button><?php } ?>
					<button type="button" class="btn btn-info displayToggle" target="bannedMembers" <?php if ($subView == "bannedMembers") { echo "disabled"; } ?>>Banned Members</button>
				</div>


				<div class="windowGroup">

					<?php
						$buttonParams = [
							'communityId' => $community->ID,
							'btnType' => 'mini',
							'displayType' => 'icon'
						];
					?>
					<div id="allMembers" class="<?php if ($subView != "allMembers") { echo "inactiveView"; } ?>">
						<?php
						if (!empty($members)) { ?>
						<h4>All Members</h4>
						<table class="table table-striped table-bordered table-hover table-sm ">
							<thead class="thead-dark">
								<tr>
								<?php if (!$currentUser->isAdmin) { ?>
									<th width="80%">User</th>
									<th width="10%">Kick</th>
									<th width="10%">Ban</th>
								<?php } else { ?>
									<th width="70%">User</th>
									<th width="10%">Promote/Demote</th>
									<th width="10%">Kick</th>
									<th width="10%">Ban</th>
								<?php } ?>
								</tr>
							</thead>
						<?php 
							foreach ($members as $member) { 
								$memberIs = "user";
								if (in_array($member->ID, $memberIdLists['moderators'])) { $memberIs = "mod"; }
								if ($member->ID == $community->Admin) { $memberIs = "admin"; }

								$buttonParams['disabled'] = false;
								$buttonParams['confirm'] = false;

							?>
							<tr>
								<td><a href="/user/<?php echo $member->Username; ?>"><?php echo $member->Username; ?></a>
									
								

								<?php 
									if ($currentUser->isAdmin) {
										switch ($memberIs) {
											case "admin":
												?> <i class="fas fa-crown" style="color:gold"></i></td><td><?php 
												$buttonParams['state'] = 'secondary';
												$buttonParams['action'] = 'promoteMember';
												$buttonParams['disabled'] = true;
												$buttonParams['content'] = 'minus-circle';
												$buttonParams['userId'] = $member->ID;

												echo action_button($buttonParams); ?></td><?php
												break;

											case "mod":
												?> <i class="fas fa-chess-knight" style="color: silver"></i></td><td><?php 
													$buttonParams['state'] = 'secondary';
													$buttonParams['confirm'] = true;
													$buttonParams['action'] = 'demoteMember';
													$buttonParams['content'] = 'user';
													$buttonParams['userId'] = $member->ID;

													if (!$currentUser->isAdmin) {
														$buttonParams['disabled'] = true;
														$buttonParams['content'] = 'minus-circle';
													}

													echo action_button($buttonParams);	
												break;

											case "user":
											default:
												?></td><td><?php 
													$buttonParams['state'] = 'success';
													$buttonParams['confirm'] = true;
													$buttonParams['action'] = 'promoteMember';
													$buttonParams['content'] = 'chess-knight';
													$buttonParams['userId'] = $member->ID;

													echo action_button($buttonParams);	
												break;
										}
									}
									$buttonParams['disabled'] = false;?>
								</td>
								
								<?php if ($memberIs == "user") { ?>
									<td><?php 
										$buttonParams['state'] = 'danger';
										$buttonParams['confirm'] = true;
										$buttonParams['action'] = 'kickMember';
										$buttonParams['content'] = 'trash';
										$buttonParams['userId'] = $member->ID;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'danger';
										$buttonParams['confirm'] = true;
										$buttonParams['action'] = 'banMember';
										$buttonParams['content'] = 'ban';
										$buttonParams['userId'] = $member->ID;

										echo action_button($buttonParams); ?></td>
								<?php } else { ?>
									<td><?php 
										$buttonParams['state'] = 'secondary';
										$buttonParams['action'] = 'kickMember';
										$buttonParams['disabled'] = true;
										$buttonParams['content'] = 'minus-circle';
										$buttonParams['userId'] = $member->ID;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'secondary';
										$buttonParams['action'] = 'banMember';
										$buttonParams['disabled'] = true;
										$buttonParams['content'] = 'minus-circle';
										$buttonParams['userId'] = $member->ID;

										echo action_button($buttonParams); ?></td>

								<?php } ?>
								
							</tr>
						<?php }  ?>

						</table>
						<?php }	else { ?>
							<p>No one is in this community.... which is odd, because the admin should be.</p>
						<?php }  ?>
					</div> <!-- allMembers -->

					<div id="pendingMembers" class=" <?php if ($subView != "pendingMembers") { echo "inactiveView"; } ?>">
						<h4>Pending Members</h4>
							<?php if (!empty($pendingMembers)) { ?>
							
							<table class="table table-striped table-bordered table-hover table-sm ">
								<thead class="thead-dark">
									<tr>
										<th width="70%">User</th>
										<th width="10%">Approve</th>
										<th width="10%">Deny</th>
										<th width="10%">Ban</th>
									</tr>
								</thead>
							<?php foreach ($pendingMembers as $member) { 
									$buttonParams['disabled'] = false;
									$buttonParams['confirm'] = false;
								?>
								<tr>
									<td><a href="/user/<?php echo $member->Username; ?>"><?php echo $member->Username; ?></a></td>


									<td><?php 
										$buttonParams['state'] = 'success';
										$buttonParams['action'] = 'approveMember';
										$buttonParams['content'] = 'thumbs-up';
										$buttonParams['userId'] = $member->ID;
										$buttonParams['confirm'] = false;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'danger';
										$buttonParams['action'] = 'denyMember';
										$buttonParams['content'] = 'thumbs-down';
										$buttonParams['userId'] = $member->ID;
										$buttonParams['confirm'] = true;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'danger';
										$buttonParams['action'] = 'banMember';
										$buttonParams['content'] = 'ban';
										$buttonParams['userId'] = $member->ID;
										$buttonParams['confirm'] = true;

										echo action_button($buttonParams); ?></td>
								</tr>
							<?php } ?>

							</table>
						<?php } else { ?>
							<p>There are no currently pending members.</p>
						<?php } ?>
					</div> <!-- pending members -->

					<div id="bannedMembers" class=" <?php if ($subView != "bannedMembers") { echo "inactiveView"; } ?>">
						<h4>Banned Members</h4>
							<?php if (!empty($bannedMembers)) { ?>
							
							<table class="table table-striped table-bordered table-hover table-sm ">
								<thead class="thead-dark">
									<tr>
										<th width="90%">User</th>
										<th width="10%">Unban</th>
									</tr>
								</thead>
							<?php foreach ($bannedMembers as $member) {
									$buttonParams['disabled'] = false;
									$buttonParams['confirm'] = false; ?>
								<tr>
									<td><a href="/user/<?php echo $member->Username; ?>"><?php echo $member->Username; ?></a></td>

									
										<td><?php 
										$buttonParams['state'] = 'primary';
										$buttonParams['confirm'] = true;
										$buttonParams['action'] = 'unbanMember';
										$buttonParams['content'] = 'backspace';
										$buttonParams['userId'] = $member->ID;

										echo action_button($buttonParams); ?></td>
								</tr>
							<?php } ?>

							</table>
						<?php } else { ?>
							<p>There are no banned members. Lucky you!</p>
						<?php } ?>
					</div> <!-- banned members -->
				</div> <!-- window group -->
			</div> <!-- member mananger -->

			<?php if ($currentUser->isAdmin) { ?>
			<div id="settingsManager" class="<?php if ($view != "settingsManager") { echo "inactiveView"; } ?>">
				<h2>Community Settings</h2>

				<div id="editCommunityForm">
				
				<?php 
					$attributes = array('id' => 'editCommunity');
					$hidden = array(
						'communityId' => $community->ID,
						'mixerUser_id' => $_SESSION['mixer_id']
					);
					echo form_open_multipart('servlet/editCommunity', $attributes, $hidden); 
				?>

				<div class="form-row">
					<div class="col-4">

						<div class="infoBox">

						<h4 class="infoHeader">Community Info</h4>
						<div class="infoInterior">
						<div class="form-group">
							<?php 
								echo form_label('Summary/Slogan', 'summary');

								$attributes = array(
									'id' => 'summary',
									'class' => 'form-control form-control-sm',
									'placeholder' => 'Shows up as hover text on thumbnails.',
									'data-validation' => 'required length',
									'data-validation-length' => 'max100'
								);
								echo form_input('summary', $community->Summary, $attributes); 
							?>
						</div> <!-- group: summary -->

						<div class="form-group">
							<?php
								$options = array(
									'name' => 'description',
									'rows' => '4');

								$attributes = array(
									'id' => 'description',
									'class' => 'form-control form-control-sm',
									'placeholder' => 'Shows up on community details page.',
									'data-validation' => 'required length',
									'data-validation-length' => 'max300'
								);

								echo form_label('Description', 'description');
								echo form_textarea($options, $community->Description, $attributes);
							?>
						</div> <!-- group: description -->

						<div class="form-group">

							<?php

								$attributes = array(
									'id' => 'coverart',
									'class' => 'form-control form-control-sm',
									/*'data-validation' => 'mime size dimension',
									'data-validation-max-size' => '256kb',
									'data-validation-allowing' => "jpg, png, gif",
									'data-validation-dimension' => 'max512x512',
									'data-validation-error-msg-size' => "You can not upload images larger than 512kb.",
									'data-validation-error-msg-mime' => "You can only upload a jpg, png or gif.",
									'data-validation-error-msg-dimension' => "You can only upload images unders 512x512 pixels."*/);

								echo form_label('Cover Art', 'coverart');
								echo form_upload('coverart', '', $attributes);
							?>
						</div> <!-- group coverart -->
						</div><!-- info interior -->
						</div><!-- info box-->

					</div> <!-- col1-->
					
					<div class="col-4">

						<div class="infoBox">

						<h4 class="infoHeader">Social Connections</h4>
						<div class="infoInterior">
						
						<h5>Discord</h5>
						<div class="form-group form-inline">
						<?php 

							echo form_label('https://discord.gg/', 'discord');
							$attributes = array(
								'id' => 'discord',
								'class' => 'form-control form-control-sm',
								'placeholder' => 'Link to an associated Discord channel.',
								'data-validation' => 'length',
								'data-validation-length' => 'max10',
								'size' => '5',
								'maxlength' => '10',
								'style'=> 'width:25%'
							);
							echo form_input('discord', $community->Discord, $attributes); 
						?>
						</div><!--group: discord -->

						<h5>Mixer Team</h5>
						<div class="form-group">
						<?php
							echo form_label('Team', 'team');
							echo '<br><p class="devNote">coming soon</p>';
						?>
						</div><!-- group: team -->
						</div><!-- info interior -->
						</div><!-- info box -->
					</div><!-- col3-->
					<div class="col-4">
						<div class="form-group">
						<div class="infoBox">

						<h4 class="infoHeader">Membership Status</h4>
						<div class="infoInterior">
						<h5>Community Status</h5>

							<div class="form-group">
								<?php 
									$attributes = array(
										'id' => 'communityStatus',
										'class' => 'communityStatus',
										'name' => 'communityStatus'
									);

									echo form_radio('status', 'open', ($community->Status == "open"), $attributes);
									echo form_label(' Open (accepting members)', 'requireApproval');
									echo "<br>";
									echo form_radio('status', 'closed', ($community->Status == "closed"), $attributes); 
									echo form_label(' Closed (not accepting members)', 'requireApproval');
								?>
							</div> <!-- group status -->

							<div class="form-group">
								<h5>Require Approval of new Members?</h5>
								<?php 
									$attributes = array(
										'id' => 'requireApproval',
										'class' => 'requireApproval',
										'data-validation' => 'required',
									);

									$requireApproval = (boolval($community->isApprovalRequired));

									/*$isClosed = false;
									if ($community->Status == "closed") {
										$isClosed = true;}*/

									echo form_radio('requireApproval', 'no', !$requireApproval, $attributes);
									echo form_label(' No (members may join freely)', 'requireApproval');
									echo "<br>";
									echo form_radio('requireApproval', 'yes', $requireApproval, $attributes); 
									echo form_label(' Yes (admin/moderators must approve new members)', 'requireApproval');
								?>
							</div> <!-- group approval -->
						</div> <!-- info interior -->
						</div> <!-- info box -->
					</div> <!-- col2-->
				</div><!-- form row -->



				<button class="editButton btn btn-lg btn-primary">Save Settings</button>
				<?php echo form_close(); ?>
			</div> <!-- edit community -->
				<h2>Transfer Community Ownership</h2>
				<div id="transferForm">
				<?php if (empty($newAdmin)) { ?>
				<p>You may transfer community administration rights to one of your moderators. The moderator you select will approve the request, at which point you will be demoted to a moderator.</p>
		
					<?php if (!empty($moderators)) { ?>
					
					<p>Select a moderator to transfer ownership to:</p>

						<?php
							$attributes = array('id' => 'setNewAdmin');
							
							$hidden = array(
								'communityId' => $community->ID,
								'mixerUser_id' => $_SESSION['mixer_id']
							);

							echo form_open('', $attributes, $hidden); 

							$modList = array();
							foreach ($moderators as $mod) {
								$modList[$mod->ID] = $mod->Username;
							}

							$js = array(
								'class'	=> 'form-control',
							    'id'       => 'newAdmin'
							);

							echo "<p>".form_dropdown('newAdmin', $modList, null, $js)."</p>";


							$js = array(
								'class'	=> 'form-control btn btn-primary',
							    'id'       => 'startTransfer'
							);
							echo "<p>".form_submit('startTransfer', 'Start Transfer', $js)."<p>";

							echo form_close();
						 } else { ?>
						<p>Your community has no moderators.</p>
					<?php } // if !empty moderators
					} else {  // if empty $newAdmin  ?>
					<p>You have a pending transfer in progress. We are waiting for <?php echo $newAdmin[0]->Username; ?> to approve the transfer.</p>
				<?php }  // if empty $newAdmin ?>
				</div> <!-- transferForm -->

				<h2>Delete Community</h2>
				<p class="alert alert-danger">This action is non-reversable. You may delete this community as you please. All related news, memberships, etc will also be deleted. Site Admins cannot restore a deleted community, when it's gone, it's gone. You will be warned ONCE and only ONCE if you hit the button below. This is your second-to-last warning.</p><p><?php 
					$buttonParams = [
						'communityId' => $community->ID,
						'confirm' => true,
						'action' => 'deleteCommunity',
						'content' => 'Delete Community',
						'state' => 'danger'
					];
					echo action_button($buttonParams); ?></p>
			</div> <!-- settings -->
			<?php } // is admin ?>
		</div> <!-- col -->
	</div> <!-- row -->

		
	<?php } // has access ?>
		
				<!--<div class="row">
					<div class="infoBox col-6">
						<h4 class="infoHeader">Community Details</h4>
						<div class="infoInterior">
							
							
							<h5>Cover Art</h5>
							<p>Soon!</p>

							<h5>Discord</h5>
							<h5>Mixer Team</h5>
						</div>
					</div>
	
				</div>-->
	
		
		
	</div>
</main>