<main role="main" class="container">
	<div class="pageHeader">
		<h1><?php echo $community_info->long_name; ?> Moderation Page</h1>
	</div>
	<div class="container">
		<?php 
			if ($community_info->status == 'approved') { ?>
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

		<?php if ($community_info->status == 'rejected') { ?>
			<div class="alert alert-danger">
				<h4>Sorry! Your community was rejected!</h4>
				<p>Alas, there was something that made us decide that this community doesn't quite work right now.</p>
				<p>The admin who rejected your community left this note:</p>
					<blockquote><?php echo $community_info->siteAdminNote; ?></blockquote>
				<p>The only action you have right now is to delete this community. However, once you do, you are free to try and make a new commmunity again. Please note that repeat attempts to found a community in direct opposition to admin reasoning can result in being banned from making communities.</p>
				<p><button class="btn btn-lg btn-danger">Delete Community</button></p>
			</div>
		<?php } ?>

		<?php if ($community_info->status == 'pending') { ?>
			<div class="alert alert-warning">
				<h4>Your community is pending approval!</h4>
				<p>WOAH!!!! Slow your held horse roll there! Your community is still awaiting admin approval. Once it's ready though, we'll let you know!</p>
			</div>
		<?php } ?>

		<div class="row">
			<div class="col-2">
				<ul>
					<li><button class="btn btn-sm btn-secondary">At a Glance</button></li>
					<li><button class="btn btn-sm btn-secondary">Settings</button></li>
					<li><button class="btn btn-sm btn-secondary">Members</button></li>
				</ul>
			</div>


		</div>
		
		<p>Welcome, <?php echo $currentUser->token; ?>.<?php 
			if ($currentUser->isAdmin) {
				echo "You are the admin.";
			}

			if ($currentUser->isMod) {
				echo "You are a moderator.";
			}
		?></p>
	
		<h3>Admin Only Features</h3>
		<ul>
			<li>Found/Open/Close Community</li>
			<li>Edit Details:
				<ul>
					<li>Change Cover Art</li>
					<li>Update summary/slogan</li>
					<li>update description</li>
					<li>Set Membership Approval</li>
				</ul></li>
			<li>Transfer Ownership</li>
		</ul>
		<h3>Admin + Moderator Features</h3>
		<ul>
			<li>Member Management:
				<ul>
					<li>Approve/Remove/Ban Members</li>
					<li>Assign Community Roles</li>
				</ul>
			</li>
			
			<li>See Analytics
				<ul>
					<li>New Members + total member count</li>
					<li>Core Members + total core member count</li>
					<li>Recent Followers + total follow count</li>
				</ul>
			</li>
		</ul>
	</div>
</main>