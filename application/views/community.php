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
			if (!empty($community_members)) {

			echo "<div class=\"row\" id=\"memberListing\">";
				foreach ($community_members as $member) {
					echo "<div class=\"\">";
					echo "<a href=\"/user/".$member->name_token."\" data-toggle=\"tooltip\" title=\"Followers: ".number_format($member->numFollowers)."\"><img class=\"avatar thin-border\" src=\"".$member->avatarURL."\" width=\"25px\">".$member->name_token."</a>";
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
								<button type="button" data-toggle="tooltip" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community_info->slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</a>
							<?php }

							if ($currentUser->isMember) {
								echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Leave this community.\" id=\"leave\" commId=\"".$community_info->id."\" class=\"commAction btn-sm btn-danger\">Leave</button>";
							} else {
								echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Become a member of this community so viewers can find you.\" id=\"join\" commId=\"".$community_info->id."\" class=\"commAction btn-sm btn-primary\">Join</button>";
							}


							if ($currentUser->isFollower) {
								echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Stop getting updates from this community on your profile.\" id=\"unfollow\" commId=\"".$community_info->id."\" class=\"commAction btn-sm btn-danger\">Unfollow</button>";
							} else {
								echo "<button type=\"button\" data-toggle=\"tooltip\" title=\"Track streamers in this community from your profile page.\" id=\"follow\" commId=\"".$community_info->id."\" class=\"commAction btn-sm btn-primary\">Follow</button>";
							}
						} else { ?>
							<button type="button" data-toggle="tooltip" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community_info->slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</a>
						<?php }

						
					}

				?>
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
			<div class="infoBox">
				<h4 class="infoHeader">Leads</h4>
				<div class="infoInterior">
					<?php foreach ($community_leads as $lead) { ?>
						<p><?php echo $lead->name_token; ?></p>
					<?php } ?> 

				</div>
			</div>
		</div>

	</div>

	

		

	<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>More/better community details</li>
			<li>Implement community graphics (avatar &amp; banner)</li>
			<li>Activity feed for members?</li>
		</ul>
	</div>
</main>