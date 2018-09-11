<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $member->Username; ?> <?php echo devNotes('user'); ?></h1>
	</div>

	

	<div class="row">
		<div class="col userInfo">
			<p><img src="<?php echo $avatarUrl = $member->AvatarURL; ?>" class="avatar" <?php echo imgBackup('streamer'); ?> width="200" /></p>

			<?php 
				switch($member->SiteRole) {
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
					if (strtotime($member->LastSeenOnline) > (time()-(60*10)) ) {
						$isOnline = true;
					}

					if ($isOnline) {
						echo "<span style='color:#1bf160'>Streaming Now!</span><br>";
						echo "<a href=\"https://mixer.com/".$member->Username."\" target=\"_blank\"><img class=\"live-thumb\" src=\"https://thumbs.mixer.com/channel/".$member->ID.".small.jpg\" onerror=\"this.src='/assets/graphics/blankThumb.jpg'\" width=\"200\"/></a><br>";
					} else {
						echo "<span style='color:#ff5454'>Currently Offline</span><br>";
					}
					echo "<a href=\"/type/".$member->LastTypeID."/".$member->LastTypeSlug."\">".$member->LastType."</a>";


					// We don't auto-track any under 25 users, and we also don't want to show this for anyone never online
					if ($member->NumFollowers >= 25) {

						if ($member->LastSeenOnline == "0000-00-00 00:00:00") {
							echo "<br>Never seen online";
						} else {
							if ($isOnline) {
								// show "streaming for x time"
								echo "<br>Started streaming: ".$member->LastStartElapsed;
							} else {
								echo "<br>Last Online: ".$member->LastSeenElapsed;
							}
						}
					} else {
							
					}
				?>
				</div>
			</div>


			<div class="infoBox">
				<h4 class="infoHeader">Info</h4>
				<div class="infoInterior">
					<p><a href="https://mixer.com/<?php echo $member->Username; ?>">mixer.com/<?php echo $member->Username; ?></a></p>

					<p>Followers: <?php echo number_format($member->NumFollowers); ?>
					<br>Views: <?php echo number_format($member->ViewersTotal); ?></p>
					<p>Joined Mixer: <?php echo date("M. d, Y", strtotime($member->JoinedMixer)); ?>			

				<?php 
					if ($member->isPartner) {
						echo "<br><span class=\"mixerPartner\">Mixer Partner</span>";
					} 

					if (strtotime($member->JoinedMixer) < strtotime("2017-05-25")) {
						echo "<br><span class=\"beamLove\" data-toggle=\"tooltip\" title=\"Member of Original Beam Community\">#BeamLove</span>";
					}
					?></p>
				</div>
			</div>

			<div class="infoBox">
				<h4 class="infoHeader">Teams</h4>
				<div class="infoInterior">
					<?php if (!empty($teams)) { 
						foreach ($teams as $team) { ?>
							<p><a href="/team/<?php echo $team->Slug; ?>"><?php echo $team->Name; ?></a></p>
						<?php } ?>
					<?php } else { ?>
						<p>Not in any teams.</p>
					<?php } ?>
					<p class="devNote" data-toggle="tooltip" title="Planned for v0.4" data-placement="left">Coming Soon</p>
				</div>
			</div>
		</div>

		<div class="col-7 userFeed">

			<?php if ($member->NumFollowers >= 25) {
				echo "<div class=\"infoBox\">";
					echo "<h2 class=\"infoHeader\">Common Streams</h2>";
					echo "<div class=\"infoInterior\">";

						if (!empty($recentTypes)) {
							echo "<div class=\"row\">";
								foreach ($recentTypes as $type) {
									if (empty($type->CoverURL)) {
										$type->CoverURL = "https://mixer.com/_latest/assets/images/main/types/default.jpg";
									}

									$cardParams = [
										'url' => '/type/'.$type->ID.'/'.$type->Slug,
										'cover' => $type->CoverURL,
										'kind' => 'type',
										'name' => $type->Name,
										'size' => 'xsm',
										'stats' => ['streamCount'=>$type->StreamCount]];
									echo card($cardParams);
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
				<div class="infoInterior" id="userNewsFeed" data-feedType="user" data-userId="<?php echo $member->ID; ?>"  data-displaySize="sml">


					<div class="spinner news alert alert-warning">
						<p><i class="fas fa-circle-notch fa-spin"></i> Checking MixMingler for recent news. One moment please.</p>
					</div><!-- alert -->

					<?php
					//print_r($news);
					/*if ($feedData != null) {
							foreach($newsItems as $event) {
								echo $event;
							}
						} else {
							echo "<p>No activity on MixMingler</p>";
						}*/
				?>
				</div>
			</div>

			
		</div>
		<div class="col communities">
			<?php 
				if ($member->isRegistered > 0) {
					// User is a registered Mingler Member!

					if ($communities->core != null) {
						echo "<div class=\"infoBox\">";
							echo "<h6 class=\"infoHeader\">Core Communities</h6>";
							echo "<div class=\"infoInterior\">";
							foreach($communities->core as $community) {
								echo "<p><a href=\"/community/".$community->Slug."/\">".$community->Name."</a></p>";
								}
							echo "</div>";
						echo "</div>";
					}

					if ($communities->member != null) {
						echo "<div class=\"infoBox\">";
							echo "<h6 class=\"infoHeader\">Member Of</h6>";
							echo "<div class=\"infoInterior\">";
							foreach($communities->member as $community) {
								if (in_array($community->Status, ['open', 'closed'])) {
									echo "<p><a href=\"/community/".$community->Slug."/\">".$community->Name."</a></p>";}
							}
							echo "</div>";
						echo "</div>";
					}

					if ($communities->follower != null) {
						echo "<div class=\"infoBox\">";
							echo "<h6 class=\"infoHeader\">Following</h6>";
							echo "<div class=\"infoInterior\">";
								foreach($communities->follower as $community) {
									if (in_array($community->Status, ['open', 'closed'])) {
										echo "<p><a href=\"/community/".$community->Slug."/\">".$community->Name."</a></p>"; }
								}
							echo "</div>";
						echo "</div>";
					}


					
						echo "<div class=\"infoBox\">";
							echo "<h6 class=\"infoHeader\">Followed Games</h6>";
							if (!empty($types)) {
							echo "<div class=\"infoInterior row\">";
								//echo str_replace(";", ",", $member->followedTypes);
								foreach($types as $type) {
									//echo "<p>1<a href=\"/type/".$type->Slug."/\">".$type->Name."</a></p>";
								}

									foreach ($types as $type) {
										if ($type->FollowState == 'followed') {
											echo "<div class=\"miniTypeInfo \"><a href=\"/type/$type->ID/".$type->Slug."\"><img class=\"miniCover\" src=\"".$type->CoverURL."\" width=\"35\" data-toggle=\"tooltip\" title=\"".$type->Name."\" /></a></div>"; }
										
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
							echo "<p>".$member->Username."  hasn't joined MixMingler, and therefore has no community information.</p>";
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