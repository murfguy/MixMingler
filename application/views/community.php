<main role="main" class="container">
<div class="container">
		<p class="devNote"  data-toggle="tooltip" title="Planned for v0.3" data-placement="left">General updates and full fledged community features are planned for development during v0.3 (Communities). See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>

	<div id="userHeader" class="pageHeader">
		<h1><?php echo $community->Name; ?></h1>
		<p><?php echo $community->Description; ?></p>
	</div>


	<div class="row">

		<div class="col-9 streamerList">
			<h1>Streamers Online</h1>
			<?php 
			//$online_members = array();

			if (count($online_members) > 0) {
				echo "<div class=\"row\">";
				foreach ($online_members as $member) { 
					echo "<div class=\"streamerListing col-sm\">";
							//$online_members[] = $member->Username;
							echo "<h3><a href=\"/user/".$member->Username."\">".$member->Username."</a></h3>";
							//echo "<img src=\"https://thumbs.mixer.com/channel/".$member->ID.".m4v\" />";
							echo "<a href=\"https://mixer.com/".$member->Username."\" target=\"_blank\"><img src=\"https://thumbs.mixer.com/channel/".$member->ID.".small.jpg\" style=\"width:100%\" /></a>";
							echo "<p class=\"gameName\">".$member->type->Name."</p>";
							echo "<p>Current Views: ".$member->ViewersCurrent."</p>";
					echo "</div>";
				 } 
				 echo "</div> <!-- .row -->";
			} else {
				echo "<h2 class=\"noStream\">No one is currently streaming.</h2>";
			}

			echo "<h1>Community Members</h1>";
			//echo "<table class=\"membersList\">";
			if (!empty($members)) {
			echo "<div class=\"row\" id=\"memberListing\">";

				foreach ($members as $member) {
					echo "<div class=\"\">";
					echo "<a href=\"/user/".$member->Username."\" data-toggle=\"tooltip\" title=\"Followers: ".number_format($member->NumFollowers)."\"><img class=\"avatar thin-border\" src=\"".$member->AvatarURL."\" width=\"25px\">".$member->Username."</a>";
					
					if ($member->ID == $community->Founder) {
						echo ' <i class="fas fa-star" style="color: gold"></i>';
					}
					if ($member->ID == $community->Admin) {
						echo ' <i class="fas fa-crown" style="color: gold"></i>';
					}

					if ($member->ID == in_array($member->ID, $memberIdLists['moderators'])){
						echo ' <i class="fas fa-chess-knight" style="color: silver"></i>';
					}

					echo "</div>";
				}
			echo "</div>";
			} else {
				echo "<h2 class=\"noStream\">No has joined this community yet! Be the first?</h2>";
			}


			?>

		</div>

		<div class="col-3">
			<div>

				<?php 
					if ($currentUser != null) {

						if ($community->Admin != $_SESSION['mixer_id']) {
							if ($currentUser->isMod) { ?>
								<button type="button" data-toggle="tooltip" id="moderateLink" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community->slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</a>
							<?php }

								if ($currentUser->isMember) { ?>
									<button type="button" class="action confirm btn btn-sm btn-danger" action="leaveCommunity" communityId="<?php echo $community->ID; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Leave</button>
									<?php
								} else { 
									if ($currentUser->isBanned) {
										$params = [
											'displayType' => 'text',
											'content' => 'Banned',
											'state' => 'dark',
											'disabled' => true,
											'size' => 'sm'
										];
										echo action_button($params);
									} else {
										if ($community->status == 'closed') {
											//echo "secondary\" disabled>Closed</button>";
											?><button type="button" class="btn btn-sm btn-secondary" action="joinCommunity" communityId="<?php echo $community->ID; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>" disabled>Closed</button><?php
										} else {
											if ($currentUser->isPending) {
												//echo "info\" id=\"unpend\">Pending</button>";
												?><button type="button" class="action confirm btn btn-sm btn-info" action="unpendCommunity" communityId="<?php echo $community->ID; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-circle-notch fa-spin"></i> Pending</button><?php
											} else {
												//echo "primary\" id=\"join\" >Join</button>";
												?><button type="button" class="action btn btn-sm btn-primary" action="joinCommunity" communityId="<?php echo $community->ID; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Join</button><?php
											}
										}
									}
									

								}
							 

							if ($currentUser->isFollower) { ?>
								<button type="button" class="action confirm btn btn-sm btn-danger" action="unfollowCommunity" communityId="<?php echo $community->ID; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Unfollow</button><?php
							} else {?>
								<button type="button" class="action btn btn-sm btn-primary" action="followCommunity" communityId="<?php echo $community->ID; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Follow</button><?php
							
							}

							
						} else { ?>
							<button type="button" data-toggle="tooltip" id="moderateLink" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community->Slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</button>
						<?php }

						
					}

				?>
			</div>

			<div class="infoBox">
				<h4 class="infoHeader">Community Info</h4>
				<div class="infoInterior">
					<p><span data-toggle="tooltip" title="Members"><i class="fas fa-users"></i> <?php echo count($members); ?></span> &nbsp;&nbsp;|&nbsp;&nbsp; <span data-toggle="tooltip" title="Followers"><i class="fas fa-heart"></i> <?php echo count($followers); ?></span></p>
					<p>Founded By: <a href="/user/<?php echo $admin->Username; ?>" data-toggle="tooltip" title="Founder"><i class="fas fa-star" style="color: gold"></i> <?php echo $admin->Username; ?></a></p>
					<hr style="background-color: white">
					<p>Moderated By:</p>
					<p><a href="/user/<?php echo $admin->Username; ?>" data-toggle="tooltip" title="Admin"><i class="fas fa-crown" style="color: gold"></i> <?php echo $admin->Username; ?></a> 

					<?php if (!empty($moderators)) { foreach ($moderators as $moderator) { ?>
						, <a href="/user/<?php echo $moderator->Username; ?>" data-toggle="tooltip" title="Moderator"><i class="fas fa-chess-knight" style="color: silver"></i> <?php echo $moderator->Username; ?> </a>
					<?php } } ?> 
				</p>
				</div>
			</div>

			<h3>News Feed</h3>
			<?php
				if (!empty($newsDisplayItems)) {
					foreach ($newsDisplayItems as $newsItem) {
						echo $newsItem;
					}
				} else {
					echo "<p>No members yet!</p>";
				}
			?>
		</div>

	</div>
</main>