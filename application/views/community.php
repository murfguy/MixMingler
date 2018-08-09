<main role="main" class="container">
<div class="container">
		<p class="devNote"  data-toggle="tooltip" title="Planned for v0.3" data-placement="left">General updates and full fledged community features are planned for development during v0.3 (Communities). See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>

	<div id="userHeader" class="pageHeader">
		<h1><?php echo $community_info->long_name; ?></h1>
		<p><?php echo $community_info->description; ?></p>
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
							//$online_members[] = $member->token;
							echo "<h3><a href=\"/user/".$member->token."\">".$member->token."</a></h3>";
							//echo "<img src=\"https://thumbs.mixer.com/channel/".$member->id.".m4v\" />";
							echo "<a href=\"https://mixer.com/".$member->token."\" target=\"_blank\"><img src=\"https://thumbs.mixer.com/channel/".$member->id.".small.jpg\" style=\"width:100%\" /></a>";
							echo "<p class=\"gameName\">".$member->type->name."</p>";
							echo "<p>Current Views: ".$member->viewersCurrent."</p>";
					echo "</div>";
				 } 
				 echo "</div> <!-- .row -->";
			} else {
				echo "<h2 class=\"noStream\">No one is currently streaming.</h2>";
			}

			echo "<h1>Community Members</h1>";
			//echo "<table class=\"membersList\">";
			if (!empty($members)) {
				$moderatorIds = explode(",", $community_info->moderators);
			echo "<div class=\"row\" id=\"memberListing\">";

				foreach ($members as $member) {
					echo "<div class=\"\">";
					echo "<a href=\"/user/".$member->name_token."\" data-toggle=\"tooltip\" title=\"Followers: ".number_format($member->numFollowers)."\"><img class=\"avatar thin-border\" src=\"".$member->avatarURL."\" width=\"25px\">".$member->name_token."</a>";
					
					if ($member->mixer_id == $community_info->founder) {
						echo ' <i class="fas fa-star" style="color: gold"></i>';
					}
					if ($member->mixer_id == $community_info->admin) {
						echo ' <i class="fas fa-crown" style="color: gold"></i>';
					}

					if ($member->mixer_id == in_array($member->mixer_id, $moderatorIds)){
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

						if ($community_info->admin != $_SESSION['mixer_id']) {
							if ($currentUser->isMod) { ?>
								<button type="button" data-toggle="tooltip" id="moderateLink" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community_info->slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</a>
							<?php }

								if ($currentUser->isMember) { ?>
									<button type="button" class="action confirm btn btn-sm btn-danger" action="leaveCommunity" communityId="<?php echo $community_info->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Leave</button>
									<!--echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Leave this community.\" id=\"leave\" commId=\"".$community_info->id."\" class=\"commAction btn-sm btn-danger\">Leave</button>"; --><?php
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
										if ($community_info->status == 'closed') {
											//echo "secondary\" disabled>Closed</button>";
											?><button type="button" class="btn btn-sm btn-secondary" action="joinCommunity" communityId="<?php echo $community_info->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>" disabled>Closed</button><?php
										} else {
											if ($currentUser->isPending) {
												//echo "info\" id=\"unpend\">Pending</button>";
												?><button type="button" class="action confirm btn btn-sm btn-info" action="unpendCommunity" communityId="<?php echo $community_info->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-circle-notch fa-spin"></i> Pending</button><?php
											} else {
												//echo "primary\" id=\"join\" >Join</button>";
												?><button type="button" class="action btn btn-sm btn-primary" action="joinCommunity" communityId="<?php echo $community_info->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Join</button><?php
											}
										}
									}
									

								}
							 

							if ($currentUser->isFollower) { ?>
								<button type="button" class="action confirm btn btn-sm btn-danger" action="unfollowCommunity" communityId="<?php echo $community_info->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Unfollow</button><?php
								//echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Stop getting updates from this community on your profile.\" id=\"unfollow\" commId=\"".$community_info->id."\" class=\"commAction btn-sm btn-danger\">Unfollow</button>";
							} else {?>
								<button type="button" class="action btn btn-sm btn-primary" action="followCommunity" communityId="<?php echo $community_info->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Follow</button><?php
								//echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Track streamers in this community from your profile page.\" id=\"follow\" commId=\"".$community_info->id."\" class=\"commAction btn-sm btn-primary\">Follow</button>";
							}

							
						} else { ?>
							<button type="button" data-toggle="tooltip" id="moderateLink" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community_info->slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</button>
						<?php }

						
					}

				?>
			</div>

			<div class="infoBox">
				<h4 class="infoHeader">Community Info</h4>
				<div class="infoInterior">
					<p><span data-toggle="tooltip" title="Members"><i class="fas fa-users"></i> <?php echo count($members); ?></span> &nbsp;&nbsp;|&nbsp;&nbsp; <span data-toggle="tooltip" title="Followers"><i class="fas fa-heart"></i> <?php echo count($followers); ?></span></p>
					<p>Founded By: <a href="/user/<?php echo $admin->name_token; ?>" data-toggle="tooltip" title="Founder"><i class="fas fa-star" style="color: gold"></i> <?php echo $admin->name_token; ?></a></p>
					<hr style="background-color: white">
					<p>Moderated By:</p>
					<p><a href="/user/<?php echo $admin->name_token; ?>" data-toggle="tooltip" title="Admin"><i class="fas fa-crown" style="color: gold"></i> <?php echo $admin->name_token; ?></a> 

					<?php if (!empty($moderators)) { foreach ($moderators as $moderator) { ?>
						, <a href="/user/<?php echo $moderator->name_token; ?>" data-toggle="tooltip" title="Moderator"><i class="fas fa-chess-knight" style="color: silver"></i> <?php echo $moderator->name_token; ?> </a>
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