<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $typeData->typeName; ?></h1>
	</div>
	<div class="row">
		<div class="col userInfo">
			<p><img src="<?php echo $typeData->coverUrl; ?>" width="200" class="gameCover" /></p>
			<!--<p>Current # of streams: <?php echo count($activeStreams); ?></p>-->
			<div class="infoBox">
				<h4 class="infoHeader">Status</h4>
				<div class="infoInterior">
					<p class="devNote" data-toggle="tooltip" title="Planned for v0.2" data-placement="left">Coming soon</p>
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
					echo "<div class=\"actionButtons types $state\">";



						echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Gets updates about this game on your homepage!\" id=\"follow\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-primary\">Follow</button>";

						echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Hide this game in listings.\" id=\"ignore\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-danger\">Ignore</button>";
					
					echo "</div>";


					

					/*switch ($state) {
						case "followed":
							echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Unfollow this game.\" id=\"unfollow\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-danger\">Unfollow</button>";
							break;

						case "ignored":
							echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Stop hiding this game.\" id=\"unignore\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-danger\">Unignore</button>";
							break;

						case "neither":
						default:
							echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Gets updates about this game on your homepage!\" id=\"follow\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-primary\">Follow</button>";

							echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Hide this game in listings.\" id=\"ignore\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-primary\">Ignore</button>";
							break;
					}*/

				}

			?>
			<div class="infoBox">
				<h6 class="infoHeader">Frequent Streamers</h6>
				<div class="infoInterior">
					<?php
					foreach ($frequentStreamers as $streamer) {
						echo "<p><img src=\"".$streamer->avatarUrl."\" class=\"avatar list thin-border\" width=\"30\"> <a href=\"/user/".$streamer->username."\" data-toggle=\"tooltip\" data-placement=\"right\" title=\"Streamed ".$streamer->stream_count." times\">".$streamer->username."</a></p>";
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
				<h6 class="infoHeader">Recent Streams</h6>
				<div class="infoInterior">
					<?php
					foreach($recentStreams as $stream) {
						echo "<p><img src=\"".$stream->avatarUrl."\" class=\"avatar list thin-border\" width=\"30\"> <a href=\"/user/".$stream->username."\">".$stream->username."</a></p>";
					}
					?>
				</div>
			</div>

		</div>
	</div>
</main>