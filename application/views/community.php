<main role="main" class="container">
<div class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $community->Name; ?>  <?php echo devNotes('community'); ?></h1>
		<p><?php echo $community->Description; ?></p>
	</div>

	<div class="row">
		<div id="sidebar" class="col-3 streamerList">
			<img src="/assets/graphics/covers/<?php echo $community->Slug.'.'.$community->CoverFileType; ?>" <?php echo imgBackup('community') ?>class="coverArt" />
			<p class="summary"><?php echo $community->Summary ?></p>
			<?php
				if ($currentUser != null) {
					$baseParams = ['displayType' => 'text','size' => 'sm','communityId'=>$community->ID, 'userId'=> $_SESSION['mixer_id']];

					if ($community->Admin != $_SESSION['mixer_id']) {
						if ($currentUser->isMod) { ?>
							<button type="button" data-toggle="tooltip"  title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community->Slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</button><br>
						<?php }

							if ($currentUser->isMember) { 
								$buttonParams = ['content' => 'Leave', 'state' => 'danger', 'confirm' => true, 'action' => 'leaveCommunity'];} 
							elseif ($currentUser->isBanned) {
								$buttonParams = ['content' => 'Banned', 'state' => 'dark', 'disabled' => true]; } 
							elseif ($community->Status == 'closed') {
								$buttonParams = ['content' => 'Closed','state' => 'secondary','disabled' => true];} 
							elseif ($currentUser->isPending) {
								$buttonParams = ['content' => '<i class="fas fa-circle-notch fa-spin"></i> Pending','state' => 'info','confirm' => true, 'action' => 'unpendCommunity'];} 
							elseif ($community->isApprovalRequired) {
								$buttonParams = ['content' => 'Ask to Join','state' => 'info','confirm' => false, 'action' => 'joinCommunity'];}
							else {
								$buttonParams = ['content' => 'Join','state' => 'primary','confirm' => false, 'action' => 'joinCommunity'];} 

							echo action_button(array_merge($baseParams, $buttonParams));
						 

						if ($currentUser->isFollower) { 
							$buttonParams = ['content' => 'Unfollow','state' => 'danger','confirm' => true, 'action' => 'unfollowCommunity'];}
							else { $buttonParams = ['content' => 'Follow','state' => 'primary','confirm' => false, 'action' => 'followCommunity']; }

						echo action_button(array_merge($baseParams, $buttonParams));

						
					} else { ?>
						<br><button type="button" data-toggle="tooltip" id="moderateLink" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community->Slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</button>
					<?php }
				}?>


			<div class="infoBox">
				<h4 class="infoHeader">Community Info</h4>
				<div class="infoInterior">
					<p><span data-toggle="tooltip" title="Members"><i class="fas fa-users"></i> <?php echo count($members); ?></span> &nbsp;&nbsp;|&nbsp;&nbsp; <span data-toggle="tooltip" title="Followers"><i class="fas fa-heart"></i> <?php echo count($followers); ?></span></p>
					<p>Founded By: <a href="/user/<?php echo $admin->Username; ?>" data-toggle="tooltip" title="Founder"><i class="fas fa-star" style="color: gold"></i> <?php echo $admin->Username; ?></a></p>
					
					<?php if (!empty($community->Discord)) { ?><button type="button" class="btn btn-sm btn-primary btn-block" data-toggle="tooltip" title="Join the Discord for this community!" onclick="window.open('https://discord.gg/<?php echo $community->Discord; ?>')"><i class="fab fa-discord"></i> Discord Server</button><?php } ?>

					<hr style="background-color: white">
					<p>Moderated By:</p>
					<p><a href="/user/<?php echo $admin->Username; ?>" data-toggle="tooltip" title="Admin"><i class="fas fa-crown" style="color: gold"></i> <?php echo $admin->Username; ?></a> 

					<?php if (!empty($moderators)) { foreach ($moderators as $moderator) { ?>
						, <a href="/user/<?php echo $moderator->Username; ?>" data-toggle="tooltip" title="Moderator"><i class="fas fa-chess-knight" style="color: silver"></i> <?php echo $moderator->Username; ?> </a>
					<?php } } ?> 
				</p>
				</div>
			</div>
		</div>
		<div id="mainView" class="col-9">
			<!--<div class="btn-group btn-group-justified" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-info" disabled>Online</button>
				<button type="button" class="btn btn-info">Recently Online</button>
				<button type="button" class="btn btn-info">All Members</button>
			</div>-->
			<div class="btn-group d-flex" role="group">
				<button type="button" class="btn btn-info displayToggle" target="onlineMembers" <?php if (count($online_members) > 0) { ?>disabled<?php } ?>>Online Members</button>
				<button type="button" class="btn btn-info displayToggle" target="communityNews"<?php if (count($online_members) <= 0) { ?>disabled<?php } ?>>Community News</button>
				<button type="button" class="btn btn-info displayToggle" target="allMembers">All Members</button>
			</div>

			<div id="onlineMembers"<?php if (count($online_members) <= 0) { ?>class="inactiveView"<?php } ?>>				
				<h3>Currently Streaming Members</h3>
				<?php if (count($online_members) > 0) { ?>
					<div class="row">
						<?php foreach ($online_members as $member) {	
								$params = [
									'url' => '/user/'.$member->token,
									'cover' => 'https://thumbs.mixer.com/channel/'.$member->id.'.small.jpg',
									'kind' => 'stream',
									'name' => $member->token,
									'size' => 'lrg',
									'stats' => [
										'currentType' => $member->type->name,
										'followers' => $member->numFollowers,
										'views' => $member->viewersCurrent]];
								echo card($params);  
						 } // foreach ($online_members as $member) ?>
					</div>
				<?php } else { ?>
					<p>No one is currently online!</p>
				<?php } // if (count($online_members) > 0) ?>
			</div><!-- onlineMembers -->
			
			<div id="communityNews" <?php if (count($online_members) > 0) { ?>class="inactiveView"<?php } ?>">				
				<h3>Community News</h3>
				<p class="devNote">Coming soon</p>
			</div><!-- recentlyOnline -->

			<div id="allMembers" class="inactiveView">				
				<h3>All Community Members</h3>

				<table class="table">
					<thead>
						<tr>
							<th>User</th>
							<th>Last Online</th>
							<th>Last Type</th>
							<th>Followers</th>
							<th>Views</th>
						</tr>	
					</thead>
					<tbody>
						<?php foreach ($members as $member) { ?>
							<tr>
								<td><?php echo userListLink(['Username'=>$member->Username, 'AvatarURL'=>$member->AvatarURL]); ?>
									<?php
										if ($member->ID == $community->Founder) {
											echo ' <i class="fas fa-star" style="color: gold"></i>';}
										if ($member->ID == $community->Admin) {
											echo ' <i class="fas fa-crown" style="color: gold"></i>';}

										if ($member->ID == in_array($member->ID, $memberIdLists['moderators'])){
											echo ' <i class="fas fa-chess-knight" style="color: silver"></i>';}

										// core members here
									?></td>

								<td><?php 
								if ($member->LastSeenOnline != "0000-00-00 00:00:00") {
									if (strtotime($member->LastSeenOnline) < (time()-(60*10)) ) {
										echo getElapsedTimeString($member->LastSeenOnline); }
										else {
											echo '<span style="color: rgb(114, 243, 114);">Online now!</span>'; }
								} else {
									echo "Never Seen";
								}
								?></td>
								<td><?php echo $member->LastType; ?></td>
								<td><?php echo number_format($member->NumFollowers); ?></td>
								<td><?php echo number_format($member->ViewersTotal); ?></td>
							</tr>
						<?php } //foreach ($members as $member) ?>
					</tbody>
					
				</table>
			</div><!-- allMembers -->
		</div>

	</div>
</main>