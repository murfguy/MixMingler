<main role="main" class="container">
	<div class="pageHeader">
		<h1><img src="<?php echo $user->AvatarURL; ?>" <?php echo imgBackup('streamer'); ?> class="avatar thin-border" width="60" />Welcome <?php echo $user->Username; ?> <?php echo devNotes('main'); ?></h1>
	</div>

	<div class="alert alert-success">
		<h2>Welcome to the <span class="mixBlue">Mix</span>Mingler Alpha Test!!!</h2>
			<p>The site is currently in development, and is in super early alpha testing. If you are using the site, it is highly requested you join the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a> so you can help with testing and provide feedback.</p>
	</div>

	<div class="row">
		<div class="col-3">
			
			<?php if (!empty($alerts)) { ?> 

			<div class="infoBox">
				<h4 class="infoHeader bg-danger">Alerts <i class="fas fa-bell"></i></h4>
				<div class="infoInterior">

					<?php if (!empty($alerts['pendingRequests'])) { ?>
						<h6>Site Admin</h6>
						<p><a href="/admin/">Community Requests</a> <span class="badge badge-danger"><?php echo $alerts['pendingRequests']; ?></span></p>
					<?php } ?>
					
					<?php if (!empty($alerts['unfoundedCommunities'])) { ?>
						<h6>Unfounded Communities</h6>
						<?php foreach ($alerts['unfoundedCommunities'] as $community) { ?>
							<p><a href="/community/<?php echo $community->Slug ?>/mod"><?php echo $community->Name ?></a>
							<?php switch ($community->Status) {
								case "approved":
									echo ' <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>';
									break;
								case "rejected":
									echo ' <span class="badge badge-danger"><i class="fas fa-times-circle"></i></span>';
									break;
								case "pending":
									echo ' <span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i></span>';
								default:
									break;
							} ?>
							</p>
						<?php } // foreach unfounded communities ?>
					<?php } // if !empty unfounded communities ?>

					<?php if (!empty($alerts['userIsNewAdmin']) || !empty($alerts['userIsOldAdmin']) ) { ?>
						<h6>Transfer Requests</h6>

						<?php if (!empty($alerts['userIsNewAdmin'])) { foreach ($alerts['userIsNewAdmin'] as $community) { ?>
							<p><a href="/community/<?php echo $community->Slug ?>/mod"><?php echo $community->Name ?></a> <span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i></span></p>
						<?php }} ?>	
						<?php if (!empty($alerts['userIsOldAdmin'])) { foreach ($alerts['userIsOldAdmin'] as $community) { ?>
							<p><a href="/community/<?php echo $community->Slug ?>/mod"><?php echo $community->Name ?></a> <span class="badge badge-info"><i class="fas fa-circle-notch fa-spin"></i></span></p>
						<?php }} ?>	
					<?php } ?>

					<?php if (!empty($alerts['pendingMembers'])) { ?>
						<h6>Pending Members</h6>
						<?php foreach ($alerts['pendingMembers'] as $community) { ?>
							<p><a href="/community/<?php echo $community->Slug ?>/mod"><?php echo $community->Name ?></a>  <span class="badge badge-danger"><?php echo $community->PendingCount; ?></span></p>
						<?php } // foreach pending members
					 } // if !empty pending members ?>
				</div>
			</div><!-- .infoBox Alerts -->
			<?php } ?>


			<div class="infoBox">
				<h4 class="infoHeader">Communities You Follow</h4>
				<div class="infoInterior">
					<?php
						if (!empty($communities->follower)) {
							echo "<p>";
							foreach ($communities->follower as $community) { 
								if (in_array($community->Status, ['open', 'closed'])) { ?>
								<a href="#" class="newsToggle" data-newstype="community" data-id="<?php echo $community->ID; ?>"><?php echo $community->Name; ?></a><br>
								<?php }} // foreach 
							echo "</p>"; 
						} else {
							echo "<p>You haven't followed any communities.</p>";
						}
					?>
				</div>
			</div><!-- .infoBox -->

			<div class="infoBox">
				<h4 class="infoHeader">Games You Follow</h4>
				<div class="infoInterior">
					<?php 
						if (!empty($mixerTypeData)) {	
						echo "<p>Click icons to show news for that game.</p>";	
						echo "<div class=\"icons row\">";
							foreach ($mixerTypeData as $type) {
								if (empty($type['coverUrl'])) { $type['coverUrl'] = 'https://mixer.com/_latest/assets/images/main/types/default.jpg'; }
								echo "<div class=\"miniTypeInfo \"><a class=\"newsToggle\" data-newstype=\"type\" data-id=\"".$type['id']."\"><img class=\"miniCover";

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
				<h6 class="infoHeader">New Communities!</h6>
				<div class="infoInterior">
					<?php
					if (!empty($newCommunities)) {
						$str = "";
						foreach ($newCommunities as $community) {
							if ($str != "") { $str .= ", ";}
							$str .= communityListLink($community);
						}
						echo "<p>$str</p>";
					} else {
						echo "<p>No new communities.</p>";
					}
					?>

					<button class="btn btn-sm btn-primary btn-block" onclick="window.location.href = '/community/create/';">Request A Community!</button>
				</div> <!-- .infoInterior -->
			</div><!-- .infoBox -->

		</div> <!-- #leftColumn -->
	
		<div class="col-7 userFeed" id="centerColumn">
			<div class="infoBox">
				<h4 class="infoHeader">News Feed</h4>
				<div class="infoInterior">
					<p>Select a <b>Game</b> to the left in order to see specific information.</p>
					<!--<div id="communityNewsFeed" data-feedtype="community" data-limit="25" data-communityid="1" data-displaysize="lrg">-->
					<?php 
						
						if (!empty($communities->follower)) {	
							foreach ($communities->follower as $community) { ?>
								
								<div class="newsFeed gamesFeed" id="community-<?php echo $community->ID; ?>">
									<h4 class="subHeader"><a href="/community/<?php echo $community->Slug; ?>"><?php echo $community->Name; ?></a></h4>

									<h5>Top Streams</h5>
									<div class="topStreams" id="streams-<?php echo $community->ID; ?>">
										<div class="spinner streams alert alert-warning">
											<p><i class="fas fa-circle-notch fa-spin"></i> Checking Mixer for top streams. One moment please.</p>
										</div><!-- alert -->
									</div><!-- .topStreams -->

									<h5>Recent Activity</h5>
									<div class="typeNews" id="news-<?php echo $community->ID; ?>" data-feedtype="community" data-limit="15" data-communityid="<?php echo $community->ID; ?>" data-displaysize="med">
										<div class="spinner news alert alert-warning">
											<p><i class="fas fa-circle-notch fa-spin"></i> Checking MixMingler for recent news. One moment please.</p>
										</div><!-- alert -->
									</div><!-- .typeNews -->
								</div><!-- .newsFeed -->

							<?php } /* foreach */

						} else { ?>
							<div class="alert alert-warning"><p>You haven't followed any games yet!</p></div>
					<?php }	


						if (!empty($followedTypes)) {	
							foreach ($followedTypes as $type) { ?>
								
								<div class="newsFeed gamesFeed" id="type-<?php echo $type->ID; ?>">
									<h4 class="subHeader"><a href="/type/<?php echo $type->ID."/". $type->Slug; ?>"><?php echo $type->Name; ?></a></h4>

									<h5>Top Streams</h5>
									<div class="topStreams" id="streams-<?php echo $type->ID; ?>">
										<div class="spinner streams alert alert-warning">
											<p><i class="fas fa-circle-notch fa-spin"></i> Checking Mixer for top streams. One moment please.</p>
										</div><!-- alert -->
									</div><!-- .topStreams -->

									<h5>Recent Activity</h5>
									<div class="typeNews" id="news-<?php echo $type->ID; ?>" data-feedtype="type" data-limit="15" data-typeid="<?php echo $type->ID; ?>" data-displaysize="med"> 
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
				<h4 class="infoHeader">MixMingler Alpha Development Notices</h4>
				<div class="infoInterior">
					<div class="userFeedItem notices alert alert-success">
						<h5 class="postTime">30 August 2018</h5>
						<p class="post">v0.2.6 is released and is a Release Candidate for v0.3-Communities. A stable and full suite of features revolving around Communities are now available. Please visit the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a> OR report issues on the <a href="https://github.com/murfguy/MixMingler/issues">MixMingler GitHub page</a>.</p>

						<p class="post">Here is a summary of features and updates for v0.3:</p>
						<ul>
							<li>Users may join/follow Communities of similar streamers.</li>
							<li>Users can manage thier communities from the Account Management area.</li>
							<li>Users can now select up to four Core Communities, which are communities most associated to the type of content they stream.</li>
							<li>Users can now view top streams and news feed for followed communities from their home page.</li>
							<li>Users may opt to request a new community, which will be approved by site admins.</li>
							<li>Community Admins can update and modify community details.</li>
							<li>Community Admin/Moderators can moderate community members</li>
							<li>The community details page has received a layout and functionality overhaul.</li>
							<li>Users can follow/ignore games directly from thumbnails on the "All Types" page.</li>
							<li>Users now receive email communications around personal community activity.</li>
							<li>Users may also modify individual email settings from their account management page.</li>
							<li>A complete overhaul to the database was performed in v0.2.2, resulting in a clean slate of data starting on Aug. 16, 2018</li>
						</ul>
					</div>
					<div class="userFeedItem notices alert alert-danger">
						<h5 class="postTime">16 August 2018</h5>
						<p class="post">v0.2.2 is released. This update included a large scale overhaul to the database struture. Due to this, all data has been purged from the database in order to accommodate these changes. This includes games followed, communities created, even registration to the site. This is a clean slate.</p>
					</div>
				</div><!-- .infoInterior -->
			</div><!-- .infoBox -->

	
		</div> <!-- #centerColumn -->

		<div class="col-2" id="rightColumn">
			
			<?php
			//$modCommunities = null;
			if (!empty($communities->manager)) { ?>
			<div class="infoBox">
				<h6 class="infoHeader">Communities You Manage</h6>
				<div class="infoInterior">
						<?php	foreach ($communities->manager as $community) {
								if (in_array($community->Status, ['open', 'closed'])) { 
									echo '<p>'.communityListLink($community, true).'</p>';
								}
							
							}
					?>
				</div>
			</div>
			<?php } ?>

			<div class="infoBox">
				<h6 class="infoHeader">Core Communities</h4>
				<div class="infoInterior">
					<?php
						if (!empty($communities->core)) {
							foreach ($communities->core as $community) {
								echo '<p>'.communityListLink($community).'</p>';
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
						if (!empty($communities->member)) {
							foreach ($communities->member as $community) {
								if (in_array($community->Status, ['open', 'closed'])) { 
									echo '<p>'.communityListLink($community).'</p>';
								}
							}
						} else {
							echo "<p>You haven't joined any communities.</p>";
						}
					?>
				</div>
			</div>

			
		</div>
	</div><!-- .row -->
</main>