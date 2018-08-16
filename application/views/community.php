<main role="main" class="container">
<div class="container">
	<div id="userHeader" class="pageHeader">
		<h1><?php echo $community->Name; ?>  <?php echo devNotes('community'); ?></h1>
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
							echo "<h3><a href=\"/user/".$member->token."\">".$member->token."</a></h3>";
							//echo "<img src=\"https://thumbs.mixer.com/channel/".$member->ID.".m4v\" />";
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
			echo "<div class=\"row\" id=\"memberListing\">";

				foreach ($members as $member) {
					echo "<div class=\"\">";
					echo userListLink(['Username'=>$member->Username, 'AvatarURL'=>$member->AvatarURL]);


					//echo "<a href=\"/user/".$member->Username."\" data-toggle=\"tooltip\" title=\"Followers: ".number_format($member->NumFollowers)."\"><img class=\"avatar thin-border\" src=\"".$member->AvatarURL."\" width=\"25px\">".$member->Username."</a>";
					
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
				<p>
				<?php 
					if ($currentUser != null) {

						$baseParams = ['displayType' => 'text','size' => 'sm','communityId'=>$community->ID, 'userId'=> $_SESSION['mixer_id']];

						if ($community->Admin != $_SESSION['mixer_id']) {
							if ($currentUser->isMod) { ?>
								<button type="button" data-toggle="tooltip"  title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community->Slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</button><br>
							<?php }

								if ($currentUser->isMember) { 
									$buttonParams = ['content' => 'Leave', 'state' => 'danger', 'confirm' => true, 'action' => 'leaveCommunity'];} 
								elseif ($currentUser->isBanned) {
									$buttonParams = ['content' => 'Banned', 'state' => 'dark', 'disabled' => true]; } 
								elseif ($community->Status == 'closed') {
									$buttonParams = ['content' => 'Closed','state' => 'secondary','disabled' => true];} 
								elseif ($currentUser->isPending) {
									$buttonParams = ['content' => '<i class="fas fa-circle-notch fa-spin"></i> Pending','state' => 'info','confirm' => true, 'action' => 'unpendCommunity'];} 
								elseif ($community->isApprovalRequired) {
									$buttonParams = ['content' => 'Ask to Join','state' => 'info','confirm' => false, 'action' => 'joinCommunity'];}
								else {
									$buttonParams = ['content' => 'Join','state' => 'primary','confirm' => false, 'action' => 'joinCommunity'];} 

								echo action_button(array_merge($baseParams, $buttonParams));
							 

							if ($currentUser->isFollower) { 
								$buttonParams = ['content' => 'Unfollow','state' => 'danger','confirm' => true, 'action' => 'unfollowCommunity'];}
								else { $buttonParams = ['content' => 'Follow','state' => 'primary','confirm' => false, 'action' => 'followCommunity']; }

							echo action_button(array_merge($baseParams, $buttonParams));

							
						} else { ?>
							<br><button type="button" data-toggle="tooltip" id="moderateLink" title="Moderate this community." onclick="window.location.href = '/community/<?php echo $community->Slug; ?>/mod';" class="btn btn-sm btn-primary">Moderate Community</button>
						<?php }

						
					}

				?>
				</p>
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
			<p class="devNote">coming soon.</p>

		</div>

	</div>
</main>