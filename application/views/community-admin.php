<main role="main" class="container">
	<div class="pageHeader">
		<h1><?php echo $community_info->long_name; ?> Moderation Page</h1>
	</div>
	<div>
		<p><?php 
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
			<li>Modify Name</li>
			<li>Upload Cover Art</li>
			<li>Update summary</li>
			<li>update description</li>
			<li>Assign Moderators</li>
			<li>Transfer Ownership</li>
			<li>Set Membership Approval</li>
		</ul>
		<h3>Admin + Moderator Features</h3>
		<ul>
			<li>Approve/Remove/Ban Members</li>
			<li>See analytics</li>
		</ul>
	</div>
</main>