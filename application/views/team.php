<?php 
	/*if (count($online_members) > 0) { 
		$view = "onlineMembers";
	} else {
		$view = "communityNews";
	}

	
	if (!empty($_GET['view'])) {
	 	if (in_array($_GET['view'], ['onlineMember', 'communityNews', 'allMembers'])) {
			$view = $_GET['view'];
		}
	}*/

	$view = "allMembers";

?>
<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $team->Name; ?> <?php echo devNotes('team'); ?></h1>
	</div>
	<div class="row">
		<div id="sidebar" class="col-3 streamerList">
			<img src="<?php $team->LogoURL; ?>" <?php echo imgBackup('team') ?>class="coverArt lrg" />
			
	

			<div class="infoBox">
				<h4 class="infoHeader">Team Info</h4>
				<div class="infoInterior">
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
				<button type="button" class="btn btn-info displayToggle" target="teamNews" <?php if ($view == "communityNews") { ?>disabled<?php } ?>>Community News</button>
				<button type="button" class="btn btn-info displayToggle" target="allMembers" <?php if ($view == "allMembers") { ?>disabled<?php } ?>>All Members</button>
			</div>

			<div id="onlineMembers" <?php if ($view != "onlineMembers") { ?>class="inactiveView"<?php } ?>>				
				<h3>Currently Streaming Members</h3>
				<!--<?php if (count($online_members) > 0) { ?>
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
				<?php } // if (count($online_members) > 0) ?>-->
			</div><!-- onlineMembers -->
			
			<div id="teamNews" <?php if ($view != "teamNews") { ?>class="inactiveView"<?php } ?>>				
				<h3>Team News</h3>

				<!--<div id="communityNewsFeed" data-feedtype="community" data-limit="25" data-communityId="<?php echo $community->ID; ?>" data-displaysize="lrg">
				</div>-->
			</div><!-- recentlyOnline -->

			<div id="allMembers" <?php if ($view != "allMembers") { ?>class="inactiveView"<?php } ?>>				
				<h3>All Community Members</h3>

				<?php echo streamerTable(['type'=>'team', 'id'=>$team->ID]); ?>
			</div><!-- allMembers -->
		</div>

	</div>
</main>