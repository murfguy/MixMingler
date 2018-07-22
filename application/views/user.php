<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $minglerData->name_token; ?></h1>
	</div>

	

	<div class="row">
		<div class="col userInfo">
			<p><img src="<?php echo $avatarUrl = $minglerData->avatarURL; ?>" class="avatar" width="200" /></p>

			<?php 
				switch($minglerData->minglerRole) {
					case "owner":
						echo "<p>MixMingler Owner/Creator</p>";
						break;
					case "admin":
						echo "<p>Site Admin</p>";
						break;
					case "dev":
						echo "<p>Site Developer</p>";
						break;

					case "user":
					default:
						break;
				}

			?>
			<div class="infoBox">
				<h4 class="infoHeader">Status</h4>
				<div class="infoInterior">
				<?php 
					$isOnline = false; 
					if (strtotime($minglerData->lastSeenOnline) > (time()-(60*10)) ) {
						$isOnline = true;
					}

					if ($isOnline) {
						echo "<span style='color:#1bf160'>Streaming Now!</span><br>";
						echo "<a href=\"https://mixer.com/".$minglerData->name_token."\" target=\"_blank\"><img class=\"live-thumb\" src=\"https://thumbs.mixer.com/channel/".$minglerData->mixer_id.".small.jpg\" onerror=\"this.src='/assets/graphics/blankThumb.jpg'\" width=\"200\"/></a><br>";
					} else {
						echo "<span style='color:#ff5454'>Currently Offline</span><br>";
					}
					echo "<a href=\"/type/".$minglerData->lastTypeId."/".$minglerData->lastTypeSlug."\">".$minglerData->lastType."</a>";


					// We don't auto-track any under 25 users, and we also don't want to show this for anyone never online
					if ($minglerData->numFollowers >= 25) {

						if ($minglerData->lastSeenOnline == "0000-00-00 00:00:00") {
							echo "<br>Never seen online";
						} else {
							if ($isOnline) {
								// show "streaming for x time"
								echo "<br>Started streaming: ".$minglerData->lastStartElapsed;
							} else {
								echo "<br>Last Online: ".$minglerData->lastSeenElapsed;
							}
						}
					} else {
							
					}
				?>
				<!--<p class="devNote" data-toggle="tooltip" title="Planned for v0.2" data-placement="left">Planned Tweak: 'last online' should be in elapsed time format (ie: 4 hours ago)</p>
				<p class="devNote" data-toggle="tooltip" title="Planned for v0.2" data-placement="left">Planned Tweak: If user has less than 25 follows, do not show "last online"</p>-->
				</div>
			</div>


			<div class="infoBox">
				<h4 class="infoHeader">Info</h4>
				<div class="infoInterior">
					<p><a href="https://mixer.com/<?php echo $minglerData->name_token; ?>">mixer.com/<?php echo $minglerData->name_token; ?></a></p>

					<p>Followers: <?php echo number_format($minglerData->numFollowers); ?>
					<br>Views: <?php echo number_format($minglerData->viewersTotal); ?></p>
					<p>Joined Mixer: <?php echo date("M. d, Y", strtotime($minglerData->joinedMixer)); ?>			

				<?php 
					if ($minglerData->partner) {
						echo "<br><span class=\"mixerPartner\">Mixer Partner</span>";
					} 

					if (strtotime($minglerData->joinedMixer) < strtotime("2017-05-25")) {
						echo "<br><span class=\"beamLove\" data-toggle=\"tooltip\" title=\"Member of Original Beam Community\">#BeamLove</span>";
					}
					?></p>
				</div>
			</div>

			<div class="infoBox">
				<h4 class="infoHeader">Teams</h4>
				<div class="infoInterior">
					<p class="devNote" data-toggle="tooltip" title="Planned for v0.4" data-placement="left">Coming Soon</p>
				</div>
			</div>
		</div>

		<div class="col-7 userFeed">

			<?php if ($minglerData->numFollowers >= 25) {
				echo "<div class=\"infoBox\">";
					echo "<h2 class=\"infoHeader\">Common Streams</h2>";
					echo "<div class=\"infoInterior\">";

						if (!empty($recentTypes)) {
							echo "<div class=\"row\">";
								foreach ($recentTypes as $type) {
									if (empty($type->coverUrl)) {
										$type->coverUrl = "https://mixer.com/_latest/assets/images/main/types/default.jpg";
									}
									echo "<div class=\"typeInfo xsm\" data-toggle=\"tooltip\" title=\"$type->typeName: Streamed ".$type->stream_count." times\">";
										echo "<a href=\"/type/$type->id/".$type->slug."\"><img src=\"".$type->coverUrl."\" class=\"coverArt\" /></a>";

										//echo "<p class=\"typeName\"><a href=\"/type/".$type->slug."\">".$type->typeName."</a></p>";
										//echo "<p class=\"stats\"><span class=\"onlineStat\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Current Streams\"><i class=\"fas fa-play-circle\"></i>  ".$type->online."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Viewers\"><i class=\"fas fa-eye\"></i> ".$type['viewersCurrent']."</span></p>";
									echo "</div>";
								}
						
							echo "</div>";
						} else {
							echo "<p>Hasn't streamed anything recently.</p>";
						}

					 echo "</div>";
			 	echo "</div>";
				
			} ?>



			<div class="infoBox">
				<h2 class="infoHeader">Activity Feed</h2>
				<div class="infoInterior">
					<?php
					if ($feedData != null) {
							foreach($newsItems as $event) {
								echo $event;
							}
						} else {
							echo "<p>No activity on MixMingler</p>";
						}
				?>
				</div>
			</div>

			
		</div>
		<div class="col communities">
			<?php 
				if ($minglerData->registered > 0) {
					// User is a registered Mingler Member!

					echo "<div class=\"infoBox\">";
						echo "<h6 class=\"infoHeader\">Core Communities</h6>";
						echo "<div class=\"infoInterior\">";
							echo "<p>coming soon</p>";
						echo "</div>";
					echo "</div>";

					if ($communitiesData->joined != null) {
						echo "<div class=\"infoBox\">";
							echo "<h6 class=\"infoHeader\">Member Of</h6>";
							echo "<div class=\"infoInterior\">";
							foreach($communitiesData->joined as $community) {
								echo "<p><a href=\"/community/".$community->slug."/\">".$community->long_name."</a></p>";
								}
							echo "</div>";
						echo "</div>";
					}

					if ($communitiesData->followed != null) {
						echo "<div class=\"infoBox\">";
							echo "<h6 class=\"infoHeader\">Following</h6>";
							echo "<div class=\"infoInterior\">";
								foreach($communitiesData->followed as $community) {
									echo "<p><a href=\"/community/".$community->slug."/\">".$community->long_name."</a></p>";
								}
							echo "</div>";
						echo "</div>";
					}


					
						echo "<div class=\"infoBox\">";
							echo "<h6 class=\"infoHeader\">Followed Games</h6>";
							if (!empty($minglerData->followedTypesData)) {
							echo "<div class=\"infoInterior row\">";
								//echo str_replace(";", ",", $minglerData->followedTypes);
								foreach($minglerData->followedTypesData as $type) {
									//echo "<p>1<a href=\"/type/".$type->slug."/\">".$type->typeName."</a></p>";
								}

									foreach ($minglerData->followedTypesData as $type) {
									echo "<div class=\"miniTypeInfo \"><a href=\"/type/$type->typeId/".$type->slug."\"><img class=\"miniCover\" src=\"".$type->coverUrl."\" width=\"35\" data-toggle=\"tooltip\" title=\"".$type->typeName."\" /></a></div>";
									}
								
								
								
								echo "</div>";
							} else {

								echo "<div class=\"infoInterior\">";
								echo "<p>Hasn't followed any games yet.</p>";
								echo "</div>";
							}
						echo "</div>";
					
					




				} else {
					// No Mingling for you!
					echo "<div class=\"infoBox\">";
						echo "<h4 class=\"infoHeader\">Communities</h4>";
						echo "<div class=\"infoInterior\">";
							echo "<p>".$minglerData->name_token."  hasn't joined MixMingler, and therefore has no community information.</p>";
						echo "</div>";
					echo "</div>";
				}

			?>
		</div>
	</div>

	<!--<div class="alert alert-info">
		<p>Recent Updates:</p>
		<ul>
			<li>Users now do a basic sync with Mixer on a 30 minute time scale. Sync automatically occurs when this page is viewed and user is eligible for syncing.</li>
			<li>Timelines: Joining/Following/Leaving/Unfollowing a community is now marked in your timeline.</li>
		</ul>
	</div>
	<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>Redesign events list redesign to be more streamlined</li>
			<li>Show list of recently streamed types from within the last 30 days</li>
			<li class="done">List joined/followed communities</li>
			<li class="done">List of "Core" Communities</li>
			<li><span class="done">Activity feed of events for this user</span>
				<ul>
					<li class="done">Auto replace {username}</li>
				</ul>
			</li>
			<li class="done">Three columns? [Info + Followers/Followed] [Feed] [Communities]</li>
			<li>Show followers? Show followed? {X} of your followed streamers follow this streamer?</li>
			<li>Friends:
				<ul>
					<li>Send Request</li>
					<li>Show listing of friends</li>
				</ul>
			</li>
		</ul>
	</div>-->

</main>