<?php 
	$view = "onlineStreamers";
	//$view="followedStreamers";
?>
<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Streamers  <?php echo devNotes('users'); ?></h1>
	</div>
	

	<div class="btn-group d-flex" role="group">
		<button type="button" class="btn btn-info displayToggle" target="onlineStreamers" <?php if ($view == "onlineStreamers") { echo 'disabled'; } ?>>Online Streamers</button>
		<?php if (isset($_SESSION['mixer_id'])) { ?><button type="button" class="btn btn-info displayToggle" target="followedStreamers" <?php if ($view == "followedStreamers") {echo 'disabled'; } ?>>Followed Streamers</button><?php } ?>

		<!--<button type="button" class="btn btn-info displayToggle" target="suggestions" disabled>Suggestions {coming soon}</button>-->
	</div>
	<div class="row">
		<div class="col">
			<div id="onlineStreamers" class="<?php if ($view != "onlineStreamers") { echo 'inactiveView'; } ?>">

				<h1>Online Streamers</h1>
					<table class="table table-dark table-striped">
						<thead>
							<tr>
								<th data-toggle="tooltip" title="Click to sort">User</th>
								<th data-toggle="tooltip" title="Click to sort">Started</th>
								<th data-toggle="tooltip" title="Click to sort">Last Type</th>
								<th data-toggle="tooltip" title="Click to sort">Followers</th>
								<th data-toggle="tooltip" title="Click to sort">Views</th>
							</tr>	
						</thead>
						<tbody>
							<?php foreach ($onlineStreamers as $streamer) { 

								//$streamer->LastTypeID

								$showRow = true;
								$state = null;

								//$key = array_search($streamer->LastTypeID, $userTypes);
								if (isset($_SESSION['mixer_id'])) {

									$key = array_search($streamer->LastTypeID, array_column($userTypes, 'TypeID'));
									
									if (!empty($key)) { 
										$state = $userTypes[$key]->FollowState; }

									if ($state == "ignored") { $showRow = false; }
								}

								if ($showRow) {
								?>
								
								<tr class="<?php echo $state; ?>">
									<td data-username="<?php echo $streamer->Username; ?>"><?php echo userListLink(['Username'=>$streamer->Username, 'AvatarURL'=>$streamer->AvatarURL]); ?></td>

									<td data-time="<?php echo strtotime($streamer->LastStreamStart); ?>"><?php 
									if ($streamer->LastStreamStart != "0000-00-00 00:00:00") {
										echo getElapsedTimeString($streamer->LastStreamStart);
									} else {
										echo "Never Seen";
									}
									?></td>
									<td><a href="/type/<?php echo $streamer->LastTypeID.'/'.createSlug($streamer->LastType); ?>"><?php echo $streamer->LastType; ?></a></td>
									<td data-followers="<?php echo $streamer->NumFollowers; ?>"><?php echo number_format($streamer->NumFollowers); ?></td>
									<td data-viewers="<?php echo $streamer->ViewersTotal; ?>"><?php echo number_format($streamer->ViewersTotal); ?></td>
								</tr>
								<?php }?> 
							<?php } //foreach ($members as $member) ?>
						</tbody>
						
					</table>
				
			</div><!-- online streamers -->
			<div id="followedStreamers" class="<?php if ($view != "followedStreamers") { echo 'inactiveView'; } ?>">
				<h1>Followed Streamers</h1>

				<?php if (!empty($followedStreamers)) { ?> 
				<table class="table table-dark table-striped">
						<thead>
							<tr>
								<th data-toggle="tooltip" title="Click to sort" >User</th>
								<th data-toggle="tooltip" title="Click to sort">Started</th>
								<th data-toggle="tooltip" title="Click to sort">Last Type</th>
								<th data-toggle="tooltip" title="Click to sort">Followers</th>
								<th data-toggle="tooltip" title="Click to sort">Views</th>
							</tr>	
						</thead>
						<tbody>
							<?php foreach ($followedStreamers as $streamer) { 

								//$streamer->LastTypeID

								$showRow = true;

								//$key = array_search($streamer->LastTypeID, $userTypes);
								$key = array_search($streamer->LastTypeID, array_column($userTypes, 'TypeID'));
								$state = null;	
								if (!empty($key)) { 
									$state = $userTypes[$key]->FollowState; }

								//if ($state == "ignored") { $showRow = false; }

								if ($showRow) {
								?>
								
								<tr class="<?php echo $state; ?>">
									<td data-username="<?php echo $streamer->Username; ?>"><?php echo userListLink(['Username'=>$streamer->Username, 'AvatarURL'=>$streamer->AvatarURL]); ?></td>

									<td data-time="<?php echo strtotime($streamer->LastSeenOnline); ?>"><?php 
									if ($streamer->LastSeenOnline != "0000-00-00 00:00:00") {
										if (strtotime($streamer->LastSeenOnline) < (time()-(60*10)) ) {
											echo "Offline since: ".getElapsedTimeString($streamer->LastSeenOnline); }
											else {
												echo '<span style="color: rgb(114, 243, 114);">Started: '.getElapsedTimeString($streamer->LastStreamStart).'</span>'; }
									} else {
										echo "Never Seen";
									}
									?></td>
									<td><a href="/type/<?php echo $streamer->LastTypeID.'/'.createSlug($streamer->LastType); ?>"><?php echo $streamer->LastType; ?></a></td>
									<td data-followers="<?php echo $streamer->NumFollowers; ?>"><?php echo number_format($streamer->NumFollowers); ?></td>
									<td data-viewers="<?php echo $streamer->ViewersTotal; ?>"><?php echo number_format($streamer->ViewersTotal); ?></td>
								</tr>
								<?php }?> 
							<?php } //foreach ($members as $member) ?>
						</tbody>
					</table>
					<?php }  else { ?>
						<p>You are not following anyone. Go find some streamer first, then try here again.</p>
					<?php } ?>
				
			</div><!-- followedStreamers  -->
		</div>
	</div>

	<!--<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>Show list of streamers you follow</li>
			<li>Recommend new streamers based on communities and/or streamers you follow</li>
		</ul>
	</div>-->
</main>