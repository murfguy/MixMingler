<?php 
	/*if (count($online_members) > 0) { 
		$view = "onlineMembers";
	} else {
		$view = "newsFeed";
	}

	
	if (!empty($_GET['view'])) {
	 	if (in_array($_GET['view'], ['onlineMembers', 'newsFeed', 'allMembers'])) {
			$view = $_GET['view'];
		}
	}*/

	$view = "onlineMembers";

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
			<div class="btn-group d-flex" role="group">
				<button type="button" class="btn btn-info displayToggle" target="onlineMembers" <?php if ($view == "onlineMembers") { ?>disabled<?php } ?>>Online</button>
				<button type="button" class="btn btn-info displayToggle" target="newsFeed" <?php if ($view == "newsFeed") { ?>disabled<?php } ?>>News</button>
				<button type="button" class="btn btn-info displayToggle" target="allMembers" <?php if ($view == "allMembers") { ?>disabled<?php } ?>>Members</button>
			</div>

			<div id="onlineMembers" <?php if ($view != "onlineMembers") { ?>class="inactiveView"<?php } ?>>				
				<h3>Currently Streaming Members</h3>


				<div id="teamTopStreams" class="fetchTopStreams" data-grouptype="team" data-groupid="<?php echo $team->ID; ?>">
					<p><i class="fas fa-spinner fa-pulse"></i> Fetching online streams. One moment please.</p>
				</div>
			</div><!-- onlineMembers -->
			
			<div id="newsFeed" <?php if ($view != "newsFeed") { ?>class="inactiveView"<?php } ?>>				
				<h3>Team News</h3>

				<div id="teamNewsFeed" class="fetchNewsFeed" data-grouptype="team" data-limit="25" data-groupid="<?php echo $team->ID; ?>" data-displaysize="lrg">
					<p><i class="fas fa-spinner fa-pulse"></i> Fetching news feed. One moment please.</p>
				</div>
			</div><!-- recentlyOnline -->

			<div id="allMembers" <?php if ($view != "allMembers") { ?>class="inactiveView"<?php } ?>>				
				<h3>All Team Members</h3>

				<?php echo streamerTable(['grouptype'=>'team', 'groupid'=>$team->ID]); ?>
			</div><!-- allMembers -->
		</div>

	</div>
</main>