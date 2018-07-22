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
					<?php echo "<p style\"text-align: center; font-size:20px\"><span class=\"onlineStat\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Current Streams\"><i class=\"fas fa-play-circle\"></i>  ".$mixerData['online']."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Viewers\"><i class=\"fas fa-eye\"></i> ".$mixerData['viewersCurrent']."</span></p>";

					?>
					<p><a href="https://mixer.com/browse/games/<?php echo "$typeData->typeId"; ?>">View <?php echo $typeData->typeName; ?> on Mixer</a></p>
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

					echo "<div class=\"actionButtons types $state\">";
						echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Gets updates about this game on your homepage!\" id=\"follow\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-primary\">Follow</button>";

						echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Hide this game in listings.\" id=\"ignore\" typeId=\"".$typeData->typeId."\" class=\"typeAction btn-sm btn-danger\">Ignore</button>";
					echo "</div>";
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
				<h6 class="infoHeader">Recent Streamers</h6>
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