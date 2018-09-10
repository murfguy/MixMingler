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
									<td colspan="2"><h6>Games</h6></td>
								</tr>
								<tr>
									<td colspan="2">
										Recently Streamed: <br />
										<input type="text" name="recentlyStreamed" placeholder="Game Name" width="100%"></td>
								</tr>

								<tr>
									<td><label class="switch">
										<input name="checkHistory" id="checkHistory"  type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Check all recent games</td>
								</tr>


								<?php if (isset($_SESSION['mixer_id'])) { ?>
									<tr>
									<td><label class="switch">
										<input name="sameTypes" id="sameTypes"  type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Matches My Games</td>
								</tr>
								<?php } ?>


								<tr style="border-top: 1px solid white">
									<td colspan="2"><h6>Stats</h6></td>
								</tr>

								<tr>
									<td colspan="2">
										Followers: <br />
										Min: <input type="number" name="minFollowers" style="width: 5em" min="1"> Max: <input type="number" name="maxFollowers" style="width: 5em"></td>
								</tr>

								<tr>
									<td colspan="2">
										Views: <br />
										Min: <input type="number" name="minViews" style="width: 5em" min="1"> Max: <input type="number" name="maxViews" style="width: 5em"></td>
								</tr>

								<tr>
									<td colspan="2">
										Stream Age (minutes): <br />
										Min: <input type="number" name="minTime" style="width: 5em" min="10"> Max: <input type="number" name="maxTime" style="width: 5em"></td>
								</tr>

								<tr style="border-top: 1px solid white">
									<td colspan="2"><h6>Status Options</h6></td>
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
										<input name="nonpartnersOnly" id="nonpartnersOnly" type="checkbox">
										<span class="slider round"></span>
									</label></td>
									<td>Non-Partners Only</td>
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

								
								<?php } ?>


								<tr style="border-top: 1px solid white">
									<td colspan="2"><h6>Display Settings</h6></td>
								</tr>
								<tr>
									<td colspan="2">
										Max Results: <input type="number" name="limit" size="8" placeholder="100" min="1" max="500" style="width: 6em"></td>
								</tr>
								<tr style="border-bottom: 1px solid white">
									<td colspan="2">
										Order By:<br>
										<select name="orderBy">
										  <option value="LastStreamStart,DESC">Recently Started</option>
										  <option value="LastStreamStart,ASC">Longest Streams</option>
										  <option value="NumFollowers,DESC">Most Followers</option>
										  <option value="NumFollowers,ASC">Least Followers</option>
										  <option value="ViewersTotal,DESC">Most Views</option>
										  <option value="ViewersTotal,ASC">Least Views</option>
										</select>
									</td>
								</tr>
							</table>

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
								<td colspan="5"><i class="fas fa-spinner fa-pulse"></i> Fetching streamers. One moment please.</td>
							</tr>
							
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

								$onlineStatus = null;
								if ($streamer->NumFollowers < 25) {
									$onlineStatus = "never";
									$onlineContent = "Not Tracked";
								} else {
									if ($streamer->LastSeenOnline != "0000-00-00 00:00:00") {
										if (strtotime($streamer->LastSeenOnline) < (time()-(60*10)) ) {
											$onlineStatus = "offline";
											$onlineContent = "Offline since: ".getElapsedTimeString($streamer->LastSeenOnline); }
										else {
											$onlineStatus = "online";
											$onlineContent = 'Online since: '.getElapsedTimeString($streamer->LastStreamStart); }
										} else {
											$onlineStatus = "never";
											$onlineContent =  "Never Seen";
										}
									}

								if ($showRow) {
								?>
								
								<tr class="">
									<td data-username="<?php echo $streamer->Username; ?>"><?php echo userListLink(['Username'=>$streamer->Username, 'AvatarURL'=>$streamer->AvatarURL]); ?></td>

									<td data-time="<?php echo strtotime($streamer->LastSeenOnline); ?>" class="<?php echo $onlineStatus; ?>"><?php echo $onlineContent; ?></td>
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