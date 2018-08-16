<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $typeData->Name; ?> <?php echo devNotes('type'); ?></h1>
	</div>
	<div class="row">
		<div class="col userInfo">
			<p><img src="<?php echo $typeData->CoverURL; ?>" width="200" class="gameCover" /></p>
			<!--<p>Current # of streams: <?php echo count($activeStreams); ?></p>-->
			<div class="infoBox">
				<h4 class="infoHeader">Status</h4>
				<div class="infoInterior">
					<?php echo "<p style\"text-align: center; font-size:20px\"><span class=\"onlineStat\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Current Streams\"><i class=\"fas fa-play-circle\"></i>  ".$mixerData['online']."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Viewers\"><i class=\"fas fa-eye\"></i> ".$mixerData['viewersCurrent']."</span></p>";

					?>
					<p><a href="https://mixer.com/browse/games/<?php echo "$typeData->ID"; ?>">View <?php echo $typeData->Name; ?> on Mixer</a></p>
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
								'typeId' => $typeData->ID,
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


							echo action_button(array_merge($baseParams, $followParams));
							echo action_button(array_merge($baseParams, $ignoreParams));
						?>
					</div><?php
				}

			?>
			<div class="infoBox">
				<h6 class="infoHeader">Frequent Streamers</h6>
				<div class="infoInterior">
					<?php
					foreach ($frequentStreamers as $streamer) {
						$linkParams = array(
							'AvatarURL' => $streamer->AvatarURL,
							'Username' =>  $streamer->Username,
							'Tooltip' => "Streamed ".$streamer->StreamCount." times");
						echo "<p>".userListLink($linkParams)."</p>";
					}
				?>
				</div>
			</div>

		</div>
		<div class="col-7">
			<?php

				if (!empty($activeStreams)) {
					echo "<div class=\"row\">";
					foreach($activeStreams as $stream) {
						if (empty($stream['user']['avatarUrl'])) {
							$stream['user']['avatarUrl'] = "http://mixmingler.murfguy.com/assets/graphics/blankAvatar.png";
						}

						echo "<div class=\"streamerListing\">";
						//echo "<img src=\"".$stream['user']['avatarUrl']."\" width=\"100\" class=\"avatar\" />";
						echo "<a href=\"/user/".$stream['token']."\"><img class=\"live-thumb list\" src=\"https://thumbs.mixer.com/channel/".$stream['id'].".small.jpg\" onerror=\"this.src='/assets/graphics/blankThumb.jpg'\" width=\"200\"/></a>";
						echo "<p class=\"streamerName\"><a href=\"/user/".$stream['token']."\">".$stream['token']."</a></p>";
						echo "<p class=\"streamerStats\">Current Views: ".$stream['viewersCurrent']." | Followers: ".$stream['numFollowers']."</p>";
						echo "</div>";
					}
					echo "</div>";
				} else {
					echo "<h2>No one is streaming this right now.</h2>";
				}

				
			?>			
		</div>
		<div class="col userInfo">
				
			<div class="infoBox">
				<h6 class="infoHeader">Recent Streamers</h6>
				<div class="infoInterior">
					<?php
					foreach($recentStreams as $streamer) {
						$linkParams = array(
							'AvatarURL' => $streamer->AvatarURL,
							'Username' =>  $streamer->Username,);
						echo "<p>".userListLink($linkParams)."</p>";
						//echo "<p><img src=\"".$stream->avatarUrl."\" class=\"avatar list thin-border\" width=\"30\"> <a href=\"/user/".$stream->username."\">".$stream->username."</a></p>";
					}
					?>
				</div>
			</div>

		</div>
	</div>
</main>