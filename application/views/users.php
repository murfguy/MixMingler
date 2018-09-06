<?php 
	$view = "onlineStreamers";
	//$view="followedStreamers";
?>
<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Streamers  <?php echo devNotes('users'); ?></h1>
	</div>
	
	<?php if (isset($_SESSION['mixer_id'])) { ?>
	<div class="btn-group d-flex" role="group">
		<button type="button" class="btn btn-info displayToggle" target="onlineStreamers" <?php if ($view == "onlineStreamers") { echo 'disabled'; } ?>>Browse Streamers</button>
		<?php if (isset($_SESSION['mixer_id'])) { ?><button type="button" class="btn btn-info displayToggle" target="followedStreamers" <?php if ($view == "followedStreamers") {echo 'disabled'; } ?>>Followed Streamers</button><?php } ?>

		<!--<button type="button" class="btn btn-info displayToggle" target="suggestions" disabled>Suggestions {coming soon}</button>-->
	</div>
	<?php } ?>
	<div class="row">
		<div class="col">
			<div id="onlineStreamers" class="<?php if ($view != "onlineStreamers") { echo 'inactiveView'; } ?>">

				<h1>Browse Streamers</h1>
				<div class="row">

				<div class="col-3">
					<div class="infoBox">
						<h4 class="infoHeader">Filters</h4>
						<div class="infoInterior">
							<?php 
								$attributes = array('id' => 'filterStreamers');
								echo form_open('search/getStreamers', $attributes); ?>
							<table id="filterOptions">
								<tr>
									<td colspan="2">
										Followers: (min,max)<br />
										<input type="text" name="followers" width="100%" value="25,500000"></td>
								</tr>
								<tr>
									<td><label class="switch">
										<input name="onlineOnly" id="onlineOnly"  type="checkbox" checked>
										<span class="slider round"></span>
									</label></td>
									<td>Only Online Streams</td>
								</tr>
								<tr>
									<td><label class="switch">
										<input name="partnersOnly" id="partnersOnly" type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Partners Only</td>
								</tr>
								<tr>
									<td><label class="switch">
										<input name="registeredOnly" id="registeredOnly"  type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Only MixMingler Users</td>
								</tr>
								<?php if (isset($_SESSION['mixer_id'])) { ?>

								
								<tr>
									<td><label class="switch">
										<input name="followedOnly" id="followedOnly"  type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Only Games I Follow</td>
								</tr>

								<tr>
									<td><label class="switch">
										<input name="showIgnored" id="showIgnored" type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Include My Ignored Games</td>
								</tr>

								<tr>
									<td><label class="switch">
										<input name="exactSameTypes" id="exactSameTypes"  type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Last Game Matches My Games</td>
								</tr>

								<tr>
									<td><label class="switch">
										<input name="recentSameTypes" id="recentSameTypes"  type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Recently Streamed Same Games</td>
								</tr>
								<?php } ?>
								<tr>
									<td colspan="2">
										Max Results: <br />
										<input type="text" name="limit" width="100%" value="100"></td>
								</tr>
							</table>


							<!--<p>
								<label for="followers">Follower Count:</label>
  								<input type="text" id="followers" readonly style="border:0; color:#f6931f; font-weight:bold;">
							</p>
							<div id="follower-range"></div>


							<p>Total View Count</p>
							
							<p>
								
							</p>
							
							<p>Stream Age</p>
							<p>Mixer Age</p>

							<p>Games I've Streamed</p>
							<p>Only Games I Follow</p>

							<p>Include Offline</p>
							
							
							<p>Result Limits</p>
							<p>Get by: Popular, Recent, Username</p>-->

							<button class="btn btn-primary btn-small filterStreamers">Get Streamers</button>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>

				<div class="col">
					<table id="streamerSearchList" class="table table-dark table-striped">
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
							<tr class="pendingResults">
								<td colspan="5"><i class="fas fa-spinner fa-pulse"></i> Getting Streamers. One moment please.</td>
							</tr>
							<!--<?php foreach ($onlineStreamers as $streamer) { 

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
							<?php } //foreach ($members as $member) ?>-->
						</tbody>
						
					</table>
					</div>
					</div>
				
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