<?php 
	if (count($online_members) > 0) { 
		$view = "onlineMembers";
	} else {
		$view = "newsFeed";
	}

	
	if (!empty($_GET['view'])) {
	 	if (in_array($_GET['view'], ['onlineMember', 'newsFeed', 'allMembers'])) {
			$view = $_GET['view'];
		}
	}

?>

<main role="main" class="container">
<div class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $community->Name; ?>  <?php echo devNotes('community'); ?></h1>
		<p><?php echo $community->Description; ?></p>
	</div>

	<div class="row">
		<div id="sidebar" class="col-3 streamerList">
			<img src="/assets/graphics/covers/<?php echo $community->Slug.'.'.$community->CoverFileType; ?>" <?php echo imgBackup('community') ?>class="coverArt lrg" />
			
	

			<div class="infoBox">
				<h4 class="infoHeader">Community Info</h4>
				<div class="infoInterior">
					<!--<p class="summary"><?php echo $community->Summary ?></p>-->
				

					<p> 
						<?php

							if (!empty($currentUser)) {
								$baseParams = ['displayType' => 'text','size' => 'sm','communityId'=>$community->ID, 'userId'=> $_SESSION['mixer_id']];
								if ($community->Admin != $_SESSION['mixer_id']) {
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
								} // if not admin
							} // if is logged in
						?> <span data-toggle="tooltip" title="Members"><i class="fas fa-users"></i> <span id="memberCount"><?php echo count($members); ?></span> Members</span></p>



						
						<p>
							<?php  
								if (!empty($currentUser)) {
									if ($community->Admin != $_SESSION['mixer_id']) {
										
									if ($currentUser->isFollower) { 
										$buttonParams = ['content' => 'Unfollow','state' => 'danger','confirm' => true, 'action' => 'unfollowCommunity'];}
										else { $buttonParams = ['content' => 'Follow','state' => 'primary','confirm' => false, 'action' => 'followCommunity']; }

									echo action_button(array_merge($baseParams, $buttonParams));}
									} ?> 
						<span data-toggle="tooltip" title="Followers"><i class="fas fa-heart"></i> <span id="followCount"><?php echo count($followers); ?></span> Followers</span></p>






					


					<?php if (!empty($community->Discord)) { ?><button type="button" class="btn btn-sm btn-primary btn-block" data-toggle="tooltip" title="Join the Discord for this community!" onclick="window.open('https://discord.gg/<?php echo $community->Discord; ?>')"><i class="fab fa-discord"></i> Discord Server</button><?php } ?>



					<h6>Moderated By:</h6>
					<p><a href="/user/<?php echo $admin->Username; ?>" data-toggle="tooltip" title="Admin"><i class="fas fa-crown" style="color: gold"></i> <?php echo $admin->Username; ?></a> 

					<?php if (!empty($moderators)) { foreach ($moderators as $moderator) { ?>
						, <a href="/user/<?php echo $moderator->Username; ?>" data-toggle="tooltip" title="Moderator"><i class="fas fa-chess-knight" style="color: silver"></i> <?php echo $moderator->Username; ?> </a>
					<?php } } ?> </p>

					<?php if (!empty($currentUser) && ($currentUser->isMod || $currentUser->isAdmin)) { ?>
						<button type="button" data-toggle="tooltip"  title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community->Slug; ?>/mod';" class="btn btn-sm btn-warning btn-block">Moderate Community</button>
					<?php } ?>
					<p>Founded By: <a href="/user/<?php echo $admin->Username; ?>" data-toggle="tooltip" title="Founder"><i class="fas fa-star" style="color: gold"></i> <?php echo $admin->Username; ?></a></p>
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
				<button type="button" class="btn btn-info displayToggle" target="onlineMembers" <?php if ($view == "onlineMembers") { ?>disabled<?php } ?>>Online Members</button>
				<button type="button" class="btn btn-info displayToggle" target="newsFeed" <?php if ($view == "newsFeed") { ?>disabled<?php } ?>>Community News</button>
				<button type="button" class="btn btn-info displayToggle" target="allMembers" <?php if ($view == "allMembers") { ?>disabled<?php } ?>>All Members</button>
			</div>

			<div id="onlineMembers" <?php if ($view != "onlineMembers") { ?>class="inactiveView"<?php } ?>>				
				<h3>Currently Streaming Members</h3>


				<div id="communityTopStreams" class="fetchTopStreams" data-grouptype="community" data-groupid="<?php echo $community->ID; ?>">
					<p><i class="fas fa-spinner fa-pulse"></i> Fetching online streams. One moment please.</p>
				</div>
			</div><!-- onlineMembers -->
			
			<div id="newsFeed" <?php if ($view != "newsFeed") { ?>class="inactiveView"<?php } ?>>				
				<h3>Community News</h3>

				<div id="communityNewsFeed" class="fetchNewsFeed" data-grouptype="community" data-limit="25" data-groupid="<?php echo $community->ID; ?>" data-displaysize="lrg">
					<p><i class="fas fa-spinner fa-pulse"></i> Fetching news feed. One moment please.</p>
				</div>
			</div><!-- recentlyOnline -->

			<div id="allMembers" <?php if ($view != "allMembers") { ?>class="inactiveView"<?php } ?>>				
				<h3>All Community Members</h3>

				<?php echo streamerTable(['grouptype'=>'community', 'groupid'=>$community->ID]); ?>
			</div><!-- allMembers -->
		</div>

	</div>
</main>