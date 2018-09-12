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

	$view = "recentStreamers";

?>
<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $type->Name; ?> <?php echo devNotes('type'); ?></h1>
	</div>

	<div class="row">
		<div id="sidebar" class="col-3 streamerList">
			<p><img src="<?php echo $type->CoverURL; ?>" <?php echo imgBackup('type'); ?> width="200" class="coverArt lrg" /></p>
			
	
			<div class="infoBox">
				<h4 class="infoHeader">Status</h4>
				<div class="infoInterior">
					<?php echo "<p style\"text-align: center; font-size:20px\"><span class=\"onlineStat\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Current Streams\"><i class=\"fas fa-play-circle\"></i>  ".$mixerData['online']."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Viewers\"><i class=\"fas fa-eye\"></i> ".$mixerData['viewersCurrent']."</span></p>";

					?>
					<p><a href="https://mixer.com/browse/games/<?php echo "$type->ID"; ?>">View <?php echo $type->Name; ?> on Mixer</a></p>
					<!--<p><i class="fas fa-play-circle"></i> <?php echo $mixerData['online'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<i class="fas fa-eye"></i> <?php echo $mixerData['viewersCurrent'] ?></p>
					<p class="devNote" data-toggle="tooltip" title="Planned for v0.2" data-placement="left">Coming soon</p>-->
				</div>
			</div>

			<?php 

				if ($currentUser != null) {
					$state = "neither";

					if ($currentUser->followsType && $currentUser->ignoresType) {
						$state = "neither";

						$currentUser->followsType = false;
						$currentUser->ignoresType = false;
					}

					if ($currentUser->followsType) {
						$state = "followed";
					}

					if ($currentUser->ignoresType) {
						$state = "ignored";
					}
					?>
					
					<div class="actionButtons types <?php echo $state; ?>">

						<?php
							$baseParams = [
								'typeId' => $type->ID,
								'userId' => $_SESSION['mixer_id'],
								'displayType' => 'text'];

							$followParams = ['state'=>'primary', 'action'=>'followType', 'content'=>"Follow"];
							$ignoreParams = ['state'=>'warning', 'action'=>'ignoreType', 'content'=>"Ignore", 'confirm'=>true];

							switch ($state) {
								case "followed":
									$followParams = ['state'=>'danger', 'action'=>'unfollowType', 'content'=>"Unfollow", 'confirm'=>true];
									$ignoreParams['isHidden'] = true;
									//echo action_button(array_merge($baseParams, $followParams));
									break;
								case "ignored":
									$ignoreParams = ['state'=>'danger', 'action'=>'unignoreType', 'content'=>"Unignore"];
									$followParams['isHidden'] = true;
									//echo action_button(array_merge($baseParams, $followParams));
									break;
							}


							echo "<p>".action_button(array_merge($baseParams, $followParams))."</p>";
							echo "<p>".action_button(array_merge($baseParams, $ignoreParams))."</p>";
						?>
					</div><?php
				}

			?>
		</div>

		<div id="mainView" class="col-9">
			<div class="btn-group d-flex" role="group">
				<button type="button" class="btn btn-info displayToggle" target="frequentStreamers" <?php if ($view == "frequentStreamers") { ?>disabled<?php } ?>>Frequent</button>
				<button type="button" class="btn btn-info displayToggle" target="onlineStreamers" <?php if ($view == "onlineStreamers") { ?>disabled<?php } ?>>Online Now</button>
				<button type="button" class="btn btn-info displayToggle" target="recentStreamers" <?php if ($view == "recentStreamers") { ?>disabled<?php } ?>>Recently Started</button>
			</div>

			<div id="frequentStreamers" <?php if ($view != "frequentStreamers") { ?>class="inactiveView"<?php } ?>>				
				<h3>Frequent Streamers</h3>

				<?php echo streamerTable(['grouptype'=>'type', 'groupid'=>$type->ID, 'collection'=>'frequent']); ?>
			</div><!-- frequentStreamers -->

			<div id="onlineStreamers" <?php if ($view != "onlineStreamers") { ?>class="inactiveView"<?php } ?>>				
				<h3>Currently Streaming Members</h3>

				<div id="typeTopStreams" class="fetchTopStreams" data-grouptype="type" data-groupid="<?php echo $type->ID; ?>">
					<p><i class="fas fa-spinner fa-pulse"></i> Fetching online streams. One moment please.</p>
				</div>
			</div><!-- onlineStreamers -->

			<div id="recentStreamers" <?php if ($view != "recentStreamers") { ?>class="inactiveView"<?php } ?>>				
				<h3>Recent Streamers</h3>

				<?php echo streamerTable(['type'=>'type', 'id'=>$type->ID]); ?>
			</div><!-- frequentStreamers -->
		</div>

	</div>



</main>