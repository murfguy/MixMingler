<main role="main" class="container">
	<div class="pageHeader">
		<h1><img src="<?php echo $avatarUrl = $user->avatarURL; ?>" class="avatar thin-border" width="60" />Welcome <?php echo $user->name_token; ?></h1>
	</div>

	<div class="alert alert-success">
		<h2>Welcome to the <span class="mixBlue">Mix</span>Mingler Alpha Test!!!</h2>
			<p>The site is currently in development, and is in super early alpha testing. If you are using the site, it is highly requested you join the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a> so you can help with testing and provide feedback.</p>
	</div>

	<div class="row">
		<div class="col-3 userInfo" id="leftColumn">
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
			</div><!-- .infoBox -->

			<div class="infoBox">
				<h4 class="infoHeader">Games You Follow</h4>
				<div class="infoInterior">
					<?php 
						if (!empty($followedTypes)) {	
						echo "<p>Click icons to show news for that game.</p>";	
						echo "<div class=\"icons row\">";
							foreach ($followedTypes as $type) {
								if (empty($type['coverUrl'])) { $type['coverUrl'] = 'https://mixer.com/_latest/assets/images/main/types/default.jpg'; }
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
				</div><!-- .infoInterior -->
			</div><!-- .infoBox -->

			<div class="infoBox">
				<h4 class="infoHeader">Your Activity</h4>
				<div class="infoInterior">
					<p class="devNote" data-toggle="tooltip" title="Planned for v0.3" data-placement="left">Coming soon</p>
				</div><!-- .infoInterior -->
			</div><!-- .infoBox -->
		</div> <!-- #leftColumn -->
	
		<div class="col-7 userFeed" id="centerColumn">
			<?php 
				$hasNotices = false;
				if ($approvedCommunities != null) { $hasNotices = true; } 
				if ($pendingCommunities != null) { $hasNotices = true; } 

				if ($hasNotices) { ?>

				<div class="infoBox">
					<h4 class="infoHeader">Community Notices</h4>
					<div class="infoInterior">

					<?php if ($approvedCommunities != null) { ?>
						<h4>Your community has been approved.</h4>

						<?php foreach ($approvedCommunities as $community) { ?>
							<table>
								<tr>
									<td><a href="/community/<?php echo $community->slug; ?>"><?php echo $community->long_name; ?></td></td>
									<td>Approved by: <?php echo $community->adminName; ?></td>
								</tr>

								<tr>
									<td colspan="2">
										<p>You may now visit your <a href="/community/<?php echo $community->slug; ?>/mod">Moderation Page</a> to finalize and publish your community!</p>
									</td>
								</tr>

								<?php if (!empty($community->siteAdminNote)) { ?>
								<tr>
									<td colspan="2">
										<p>Admin Note: <?php echo $community->siteAdminNote; ?></p>
									</td>
								</tr>
								<?php } ?>
							</table>
						<?php } ?>
					<?php } // if (approvedCommunites) ?>

					<?php if ($rejectedCommunities != null) { ?>
						<h4>Your community has been rejected.</h4>

						<?php foreach ($rejectedCommunities as $community) { ?>
							<table>
								<tr>
									<td><?php echo $community->long_name; ?></td>
									<td>Approved by: <?php echo $community->adminName; ?></td>
								</tr>

								<?php if (!empty($community->siteAdminNote)) { ?>
								<tr>
									<td colspan="2">
										<p>Admin Note: <?php echo $community->siteAdminNote; ?></p>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td colspan="2">
										<button class="deleteCommunity btn btn-sm btn-danger" data-commId="<?php echo $community->id; ?>">Delete This Now (no warning)</button> You will be able to request again immediatly.
									</td>
								</tr>
							</table>
						<?php } ?>
					<?php } // if (approvedCommunites) ?>

					<?php if ($pendingCommunities != null) { ?>
						<h4>Your new community pending approval.</h4>

						<?php foreach ($pendingCommunities as $community) { ?>
							<table>
								<tr>
									<td><?php echo $community->long_name; ?></td>
									<td>Requested on: <?php echo date('F. d, Y', strtotime($community->requestTime)); ?></td>
								</tr>
							</table>
						<?php } ?>

					<?php } ?>


					</div> <!-- .infoInterior -->
				</div><!-- .infoBox/Community Notices -->

			<?php } // if (hasNotices) ?>
			
			<div class="infoBox">
				<h4 class="infoHeader">News Feed</h4>
				<div class="infoInterior">
					<p>Select a <b>Game</b> to the left in order to see specific information.</p>
					<?php 
						if (!empty($followedTypes)) {	
							foreach ($followedTypes as $type) { ?>
								
								<div class="newsFeed gamesFeed" id="typeNews-<?php echo $type['id']; ?>">
									<h3><a href="/type/<?php echo $type['id']."/".$slugs[$type['id']]; ?>"><?php echo $type['name']; ?></a></h3>
									<div class="topStreams" id="type-<?php echo $type['id']; ?>">
										<div class="spinner type alert alert-warning">
											<p><i class="fas fa-circle-notch fa-spin"></i> Checking Mixer for top streams. One moment please.</p>
										</div><!-- alert -->
									</div><!-- .topStreams -->

									<h3>Recent Activity</h3>
									<div class="typeNews" id="news-<?php echo $type['id']; ?>">
										<div class="spinner news alert alert-warning">
											<p><i class="fas fa-circle-notch fa-spin"></i> Checking MixMingler for recent news. One moment please.</p>
										</div><!-- alert -->
									</div><!-- .typeNews -->
								</div><!-- .newsFeed -->

							<?php } /* foreach */

						} else { ?>
							<div class="alert alert-warning"><p>You haven't followed any games yet!</p></div>
					<?php }	?>
				</div><!-- .infoInterior/News Feed -->
			</div><!-- .infoBox/News Feed -->
			

			<div class="infoBox">
				<h4 class="infoHeader">MixMingler Notices</h4>
				<div class="infoInterior">
					<div class="userFeedItem notices alert alert-danger">
						<h5 class="postTime">1 August 2018</h5>
						<p class="post">v0.2.1 is released. This update has made substantial changes to the backend functionality for communities. As such, in preparation for this release, all data related to communities has been purged.</p>
					</div>
					<div class="userFeedItem notices alert alert-success">
						<h5 class="postTime">24 July 2018</h5>
						<p class="post">v0.2.0-Type Released!!!! (see <a href="/alpha/">Version History for notes</a>). We are officially moving into development on v0.3-Communities! Please visit the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a> to provide any feedback.</p>
					</div>
				</div><!-- .infoInterior -->
			</div><!-- .infoBox -->
	
		</div> <!-- #centerColumn -->

		<div class="col-2" id="rightColumn">


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
				</div> <!-- .infoInterior -->
			</div><!-- .infoBox -->
		</div>
	</div><!-- .row -->

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