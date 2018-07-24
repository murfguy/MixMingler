<main role="main" class="container">
	<div class="pageHeader">
		<h1><img src="<?php echo $avatarUrl = $user->avatarURL; ?>" class="avatar thin-border" width="60" />Welcome <?php echo $user->name_token; ?></h1>
	</div>

	<div class="alert alert-success">
		<h2>Welcome to the <span class="mixBlue">Mix</span>Mingler Alpha Test!!!</h2>
			<p>The site is currently in development, and is in super early alpha testing. If you are using the site, it is highly requested you join the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a> so you can help with testing and provide feedback.</p>
	</div>

	<div class="row">
		<div class="col-3 userInfo">

			<div class="infoBox">
				<h4 class="infoHeader">Communities You Follow</h4>
				<div class="infoInterior">
					<?php
						if (!empty($communitiesData->followed)) {
							foreach ($communitiesData->followed as $community) {
								echo "<p><a href=\"/community/$community->slug\">$community->long_name</a></p>";
							}
						} else {
							echo "<p>You haven't followed any communities.</p>";
						}
					?>
					<p class="devNote" data-toggle="tooltip" title="Planned for v0.3" data-placement="left">[Planned Feature] Toggleable views of "Community" based news.</p>
				</div>
			</div>

			<div class="infoBox">
				<h4 class="infoHeader">Games You Follow</h4>
				<div class="infoInterior">
					<?php 
						if (!empty($followedTypes)) {	
						echo "<p>Click icons to show news for that game.</p>";	
						echo "<div class=\"icons row\">";
							foreach ($followedTypes as $type) {
								echo "<div class=\"miniTypeInfo \"><a class=\"newsToggle\" data-newstype=\"typeNews\" data-typeid=\"".$type['id']."\"><img class=\"miniCover";

								if ($type['online'] == 0) {
									echo " offline";
								}

								echo "\" src=\"".$type['coverUrl']."\" width=\"35\"  data-toggle=\"tooltip\" title=\"".$type['name']."\r\nStreams: ".$type['online']." | Views: ".$type['viewersCurrent']."\" /></a></div>";
							}
							echo "</div>";
						} else {
							echo "<p>You haven't followed any games yet!</p>";
						}
					?>
				</div>
			</div>

			<div class="infoBox">
				<h4 class="infoHeader">Your Activity</h4>
				<div class="infoInterior">
					<p class="devNote" data-toggle="tooltip" title="Planned for v0.3" data-placement="left">Coming soon</p>
				</div>
			</div>
			<!--<div class="infoBox">
				<h6 class="infoHeader">Followed Communities</h4>
				<div class="infoInterior">
					<?php
						if (!empty($communitiesData->followed)) {
							foreach ($communitiesData->followed as $community) {
								echo "<p><a href=\"/community/$community->slug\">$community->long_name</a></p>";
							}
						} else {
							echo "<p>You haven't followed any communities.</p>";
						}
					?>
				</div>
			</div>-->
		</div>
		<div class="col-7 userFeed">
			
			
			
			<div class="infoBox">
				<h4 class="infoHeader">News Feed</h4>
				<div class="infoInterior">
					<p>Select a <b>Game</b> to the left in order to see specific information.</p>
					<?php 
						if (!empty($followedTypes)) {	


							foreach ($followedTypes as $type) {
								echo "<div class=\"newsFeed gamesFeed\" id=\"typeNews-".$type['id']."\" />";
								echo "<h3><a href=\"/type/".$type['id']."/".$slugs[$type['id']]."\">".$type['name']."</a></h3>";

								
								echo "<div class=\"topStreams\" id=\"type-".$type['id']."\">";
									echo "<p>Getting top streams, please wait one moment.</p>";
								echo "</div>";

								$gameNewsFeed = $gameNews[$type['id']];

								echo "<h4>Recent Streams</h4>";
								foreach ($gameNewsFeed as $feed) {
									echo "<p>".$feed."</p>";
								}

								echo "</div>";
							}

						} else {
							echo "<p>You haven't followed any games yet!</p>";
						}
					?>

					<p class="devNote" data-toggle="tooltip" title="Planned for v0.3" data-placement="left">The planned default view for this area will be an amalgamation of news based on games and communities you follow.</p>


				</div>

			</div>
			<p class="devNote" data-toggle="tooltip" title="Planned for v0.3" data-placement="left">[Planned] AJAX system for loading news feeds in order to improve performance and load time.</p>

			<div class="infoBox">
				<h4 class="infoHeader">MixMingler Notices</h4>
				<div class="infoInterior">
					<div class="userFeedItem">
						<p class="post">v0.2.0-Type Released!!!! (see <a href="/alpha/">Version History for notes</a>). We are officially moving into development on v0.3-Communities! Please visit the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a> to provide any feedback.</p>
						<p class="postHead"><span class="postTime">24 July 2018</span></p>
					</div>
				</div>
			</div>

			<!--<p>This page should make it easy to see at a glance the following bits of info:</p>
			<ul>
				<li>Your communities</li>
				<li>activity feed for communities you follow</li>
				<li>activity feed for games you follow</li>
			</ul>-->
		</div>
		<div class="col-2">


			<div class="infoBox">
				<h6 class="infoHeader">Core Communities</h4>
				<div class="infoInterior">
					<?php
						if (!empty($communitiesData->core)) {
							foreach ($communitiesData->core as $community) {
								echo "<p><a href=\"/community/$community->slug\">$community->long_name</a></p>";
							}
						} else {
							echo "<p>You haven't marked any core communities.</p>";
						}
					?>
				</div>
			</div>
			<div class="infoBox">
				<h6 class="infoHeader">Your Communities</h4>
				<div class="infoInterior">
					<?php
						if (!empty($communitiesData->joined)) {
							foreach ($communitiesData->joined as $community) {
								echo "<p><a href=\"/community/$community->slug\">$community->long_name</a></p>";
							}
						} else {
							echo "<p>You haven't joined any communities.</p>";
						}
					?>
				</div>
			</div>

			<div class="infoBox">
				<h6 class="infoHeader">New Communities!</h6>
				<div class="infoInterior">
					<?php
					if (!empty($communitiesData->new)) {
						foreach ($communitiesData->new as $community) {
							echo "<p><a href=\"/community/$community->slug/\">$community->long_name</a></p>";
						}
					} else {
						echo "<p>No new communities.</p>";
					}
					?>
				</div>
			</div>

		</div>
	</div>

	<!--<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>Feed should be broken down into followed Type and Community feeds</li>
			<li>Events should be displayed for when a streamer:
				<ul>
					<li>Joined MixMingler</li>
					<li>goes live</li>
					<li>starts playing a new game</li>
					<li>joins a community</li>
					<li>Surpass certain view/follower milestones</li>
					<li>Becomes a partner</li>
					<li>Get featured</li>
				</ul>
			</li>
			<li>Quick access to Communities that you follow</li>
			<li>Tools to sync your Mixer data to MixMingler</li>
		</ul>
	</div>-->
</main>