<main role="main" class="container">
	<div class="pageHeader">
		<h1><?php echo $community_info->long_name; ?> Moderation Page</h1>
	</div>




	<div class="container">
		<?php 
			$hasAccess = true;
			if ($community_info->status == 'approved') { $hasAccess = false; ?>
			<div id="foundCommunityNotice" class="alert alert-success">
				<h4>Congrats! Your community was approved!</h4>
				<p>You're just a few short steps away from making this a full-fledged MixMingler community! Simply finalize your details and then hit the "Found Community" button. Once founded, you'll need to wait a bit before you can make another community. Until then, let's work towards making this the best community you can make it!</p>

				<?php 
					$attributes = array('id' => 'foundCommunity');
					$hidden = array(
						'commId' => $community_info->id,
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
							echo form_label(' No', 'requireApproval');
							echo "<br>";
							echo form_radio('requireApproval', 'yes', FALSE, $attributes); 
							echo form_label(' Yes', 'requireApproval');
						?>
						</div>
				</div>
				<ul>
					<li>Coming soon: Add cover art!</li>
				</ul>
				<button class="foundButton btn btn-lg btn-primary">Found the "<?php echo $community_info->long_name; ?>" Community!</button>
				<?php echo form_close(); ?>
			</div>
		<?php } ?>

		<?php if ($community_info->status == 'rejected') { $hasAccess = false; ?>
			<div class="alert alert-danger">
				<h4>Sorry! Your community was rejected!</h4>
				<p>Alas, there was something that made us decide that this community doesn't quite work right now.</p>
				<p>The admin who rejected your community left this note:</p>
					<blockquote><?php echo $community_info->siteAdminNote; ?></blockquote>
				<p>The only action you have right now is to delete this community. However, once you do, you are free to try and make a new commmunity again. Please note that repeat attempts to found a community in direct opposition to admin reasoning can result in being banned from making communities.</p>
				<p><button class="btn btn-lg btn-danger">Delete Community</button></p>
			</div>
		<?php } ?>

		<?php if ($community_info->status == 'pending') { $hasAccess = false; ?>
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

		<div class="btn-group d-flex" role="group">
			<button type="button" class="btn btn-info displayToggle" target="summaryView">Summary</button>
			<button type="button" class="btn btn-info displayToggle" target="memberManager" disabled>Members</button>
			<button type="button" class="btn btn-info displayToggle" target="settingsManager">Settings</button>
		</div>


	<div class="row">

		<div class="col">
			<div id="summaryView" class="inactiveView">
				<h2>Community Summary</h2>
				<p class="devNote">coming soon</p>
			</div>

			<div id="memberManager">

				<div class="pageHeader">
					<h2>Member Management</h2>
				</div>

				<div class="btn-group btn-group-justified" style="width:50%" role="group">
					<button type="button" class="btn btn-info displayToggle" target="allMembers" disabled>All Members</button>
					<?php if ($community_info->approveMembers) { ?><button type="button" class="btn btn-info displayToggle" target="pendingMembers">Pending Members</button><?php } ?>
					<button type="button" class="btn btn-info displayToggle" target="bannedMembers">Banned Members</button>
				</div>


				<div class="windowGroup">

					<?php
						$buttonParams = [
							'communityId' => $community_info->id,
							'btnType' => 'mini',
							'displayType' => 'icon'
						];
					?>
					<div id="allMembers">
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
							$moderatorIds = explode(",", $community_info->moderators);

							foreach ($members as $member) { 

								$memberIs = "user";
								if (in_array($member->mixer_id, $moderatorIds)) { $memberIs = "mod"; }
								if ($member->mixer_id == $community_info->admin) { $memberIs = "admin"; }

								$buttonParams['disabled'] = false;
								$buttonParams['confirm'] = false;

							?>
							<tr>
								<td><a href="/user/<?php echo $member->name_token; ?>"><?php echo $member->name_token; ?></a>
									
								

								<?php 
									if ($currentUser->isAdmin) {
										switch ($memberIs) {
											case "admin":
												?> <i class="fas fa-crown" style="color:gold"></i></td><td><?php 
												$buttonParams['state'] = 'secondary';
												$buttonParams['action'] = 'promoteMember';
												$buttonParams['disabled'] = true;
												$buttonParams['content'] = 'minus-circle';
												$buttonParams['userId'] = $member->mixer_id;

												echo action_button($buttonParams); ?></td><?php
												break;

											case "mod":
												?> <i class="fas fa-chess-knight" style="color: silver"></i></td><td><?php 
													$buttonParams['state'] = 'secondary';
													$buttonParams['confirm'] = true;
													$buttonParams['action'] = 'demoteMember';
													$buttonParams['content'] = 'user';
													$buttonParams['userId'] = $member->mixer_id;

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
													$buttonParams['userId'] = $member->mixer_id;

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
										$buttonParams['userId'] = $member->mixer_id;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'danger';
										$buttonParams['confirm'] = true;
										$buttonParams['action'] = 'banMember';
										$buttonParams['content'] = 'ban';
										$buttonParams['userId'] = $member->mixer_id;

										echo action_button($buttonParams); ?></td>
								<?php } else { ?>
									<td><?php 
										$buttonParams['state'] = 'secondary';
										$buttonParams['action'] = 'kickMember';
										$buttonParams['disabled'] = true;
										$buttonParams['content'] = 'minus-circle';
										$buttonParams['userId'] = $member->mixer_id;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'secondary';
										$buttonParams['action'] = 'banMember';
										$buttonParams['disabled'] = true;
										$buttonParams['content'] = 'minus-circle';
										$buttonParams['userId'] = $member->mixer_id;

										echo action_button($buttonParams); ?></td>

								<?php } ?>
								
							</tr>
						<?php }  ?>

						</table>
					<?php }	else { ?>
						<p>No one is in this community.... which is odd, because the admin should be.</p>
					<?php }  ?>
					</div> <!-- allMembers -->

					<div id="pendingMembers" class="inactiveView">
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
							<?php foreach ($pendingMembers as $member) { ?>
								<tr>
									<td><a href="/user/<?php echo $member->name_token; ?>"><?php echo $member->name_token; ?></a></td>


									<td><?php 
										$buttonParams['state'] = 'success';
										$buttonParams['action'] = 'approveMember';
										$buttonParams['content'] = 'thumbs-up';
										$buttonParams['userId'] = $member->mixer_id;
										$buttonParams['confirm'] = false;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'danger';
										$buttonParams['action'] = 'denyMember';
										$buttonParams['content'] = 'thumbs-down';
										$buttonParams['userId'] = $member->mixer_id;
										$buttonParams['confirm'] = true;

										echo action_button($buttonParams); ?></td>

									<td><?php 
										$buttonParams['state'] = 'danger';
										$buttonParams['action'] = 'banMember';
										$buttonParams['content'] = 'ban';
										$buttonParams['userId'] = $member->mixer_id;
										$buttonParams['confirm'] = true;

										echo action_button($buttonParams); ?></td>
								</tr>
							<?php } ?>

							</table>
						<?php } else { ?>
							<p>There are no currently pending members.</p>
						<?php } ?>
					</div>

					<div id="bannedMembers" class="inactiveView">
						<h4>Banned Members</h4>
							<?php if (!empty($bannedMembers)) { ?>
							
							<table class="table table-striped table-bordered table-hover table-sm ">
								<thead class="thead-dark">
									<tr>
										<th width="90%">User</th>
										<th width="10%">Unban</th>
									</tr>
								</thead>
							<?php foreach ($bannedMembers as $member) { ?>
								<tr>
									<td><a href="/user/<?php echo $member->name_token; ?>"><?php echo $member->name_token; ?></a></td>

									
										<td><?php 
										$buttonParams['state'] = 'primary';
										$buttonParams['confirm'] = true;
										$buttonParams['action'] = 'unbanMember';
										$buttonParams['content'] = 'backspace';
										$buttonParams['userId'] = $member->mixer_id;

										echo action_button($buttonParams); ?></td>
									


									<!--<td id="approveUser-<?php echo $member->mixer_id ?>"><button class="modAction btn btn-success" btnAction="approve" memberId="<?php echo $member->mixer_id ?>" commId="<?php echo $community_info->id; ?>" memberName="<?php echo $member->name_token ?>" data-toggle="tooltip" title="Approve Member"><i class="fas fa-thumbs-up"></i></button></td>-->
									<!--<td id="denyUser-<?php echo $member->mixer_id ?>"><button  class="modAction btn btn-warning" btnAction="deny" memberId="<?php echo $member->mixer_id ?>" commId="<?php echo $community_info->id; ?>"  memberName="<?php echo $member->name_token ?>" data-toggle="tooltip" title="Deny Member"><i class="fas fa-thumbs-down"></i></button></td>
									<td><button class="btn btn-danger" data-toggle="tooltip" title="Ban Member"><i class="fas fa-ban"></i></td>-->
								</tr>
							<?php } ?>

							</table>
						<?php } else { ?>
							<p>There are no banned members. Lucky you!</p>
						<?php } ?>
					</div>
				</div>
			</div>


			<div id="settingsManager" class="inactiveView">
				<h2>Community Settings</h2>
				<p class="devNote">coming soon</p>
			</div>
		</div>
	</div>

		
		<?php } ?>
		
	
		
		
	</div>
</main>