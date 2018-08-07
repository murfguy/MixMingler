<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Account Management</h1>
	</div>

			<div class="btn-group d-flex" role="group">
				<button type="button" class="btn btn-info displayToggle" target="summaryView" disabled>Summary</button>
				<button type="button" class="btn btn-info displayToggle" target="settingsManager">Settings</button>
				<button type="button" class="btn btn-info displayToggle" target="typeManager">Games/Types</button>
				<button type="button" class="btn btn-info displayToggle" target="commManager">Communities</button>
			</div>

	<p class="devNote">Account Management features are planned to added/implemented alongside appropriate features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area if dev notes are present for associated tasks.</p>

	<div class="row">

		<div class="col">

			<div id="summaryView">
				<h2>Account Summary</h2>
				<h3>Quick Overview</h3>
				<div class="row">
					<div class="col">
						<h4>MixMingler</h4>
						<ul>
							<li>You are a member of {joinCount} communities.</li>
							<li>You follow {followCount} communities.</li>
						</ul>
						
					</div>
					<div class="col">
						<h4>Mixer.com</h4>
						<ul>
							<li>You've been active {timesActive} over the last {elapsedTime} days.</li>
						</ul>
					</div>
				</div>
				
				<h3>Pending Communities Information</h3>
				<div class="row">
					<div class="col">
						<h4>Requests to Join</h4>
						<p>Coming Soon</p>
					</div>
					<div class="col">
						<h4>Requests to Found</h4>
						<p>Coming Soon</p>
					</div>
				</div>


				<h3>Your Core Communities</h3>
				<div class="row">
					<?php
						foreach ($communitiesData->core as $community) {
							//echo $community->long_name;
							echo card(array(
								'id' => $community->id,
								'name' => $community->long_name,
								'size' => 'med',
								'kind' => 'community',
								'url' => "/community/".$community->slug,
								'stats' => array(
									'members' => count(explode(",", $community->members))
								),
								'cover' => "/assets/graphics/covers/".$community->slug.".jpg"));
						}
					?>
				</div>
			</div>

			<div id="typeManager" class="inactiveView">
					
				<h2>Manage Games/Types</h2>

				<div class="btn-group btn-group-justified" style="width:50%" role="group">
					<button type="button" class="btn btn-info displayToggle" target="typeFollowed" disabled>Followed</button>
					<button type="button" class="btn btn-info displayToggle" target="typeIgnored">Ignored</button>
				</div>

				<div class="windowGroup">
					<div id="typeFollowed">
						<h3>Followed Games/Types</h3>
					<table class="table table-striped table-bordered table-hover table-sm ">
						<thead class="thead-dark">
							<tr>
								<th>Cover</th>
								<th>Name</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if (!empty($followedTypesData)) {
									foreach ($followedTypesData as $type) { ?>
										<tr>
											<td><img src="<?php echo $type->coverUrl; ?>" width="40"></td>
											<td><a href="/type/<?php echo $type->typeId; ?>/<?php echo $type->slug; ?>\"><?php echo $type->typeName; ?></a></td>
											<td><button class="action confirm btn btn-danger" btnType="mini" action="unfollowType" typeId="<?php echo $type->typeId; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Unfollow</button></td>
										</tr> <?php
										/*echo "<tr>";
										echo "<td><img src=\"$type->coverUrl\" width=\"40\"></td>";
										echo "<td><a href=\"/type/$type->typeId/$type->slug\">$type->typeName</a></td>";
										echo "<td><button type=\"button\" data-toggle=\"tooltip\" title=\"Stop getting updates about this game.\" id=\"unfollow\" typeId=\"".$type->typeId."\" class=\"typeAction btn btn-sm btn-danger\">Unfollow</button></td>";
										echo "</tr>";*/
									}
								} else {
									echo "<tr>";
									echo "<td colspan=\"3\">You haven't followed any games.</td>";
									echo "</tr>";
								}
							?>
						</tbody>
						</table>
					</div>
					

					<div id="typeIgnored" class="inactiveView">
						<h3>Ignored Games/Types</h3>
					<table class="table table-striped table-bordered table-hover table-sm ">
						<thead class="thead-dark">
							<tr>
								<th>Cover</th>
								<th>Name</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if (!empty($ignoredTypesData)) {
									foreach ($ignoredTypesData as $type) { ?>
										<tr>
											<td><img src="<?php echo $type->coverUrl; ?>" width="40"></td>
											<td><a href="/type/<?php echo $type->typeId; ?>/<?php echo $type->slug; ?>\"><?php echo $type->typeName; ?></a></td>
											<td><button class="action btn btn-danger" btnType="mini" action="unignoreType" typeId="<?php echo $type->typeId; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>">Unignore</button></td>
										</tr>
										<?php 
										//echo "<tr>";
										//echo "<td><img src=\"$type->coverUrl\" width=\"40\"></td>";
										//echo "<td><a href=\"/type/$type->typeId/$type->slug\">$type->typeName</a></td>";
										//echo "<td><button type=\"button\" data-toggle=\"tooltip\" title=\"Have this game show up in lists again.\" id=\"unignore\" typeId=\"".$type->typeId."\" class=\"typeAction btn btn-sm btn-danger\">Unignore</button></td>";
										//echo "</tr>";
									}
								} else {
									echo "<tr>";
									echo "<td colspan=\"3\">You haven't ignored any games.</td>";
									echo "</tr>";
								}
							?>
							</tbody>
						</table>
					</div>

				</div>
			</div>

			<div id="commManager" class="inactiveView">
				<h2>Manage Communities</h2>
				<p>Click a button to toggle status.</p>

				<table class="table table-striped table-bordered table-hover table-sm ">
					<thead class="thead-dark">
						<tr>
							<th scope="col">Name</th>
							<th scope="col">Joined</th>
							<th scope="col">Followed</th>
							<th scope="col">Make Core!</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($communityData as $community) { ?>
							<tr>
								<td><?php echo $community->long_name; ?></td>
								<?php 
									// Joined Community Button
									if ($community->joined) {
										if ($community->admin == $_SESSION['mixer_id']) {
												?><td><button btnType="mini" class="btn btn-success" disabled><i class="fas fa-crown" style="color: gold"></i></button></td><?php
										} else {
											?><td><button class="action confirm btn btn-success" btnType="mini" action="leaveCommunity" communityId="<?php echo $community->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-check"></i></button></td><?php
										}
									} else{ 
										if ($community->status == 'closed') {
											?><td><button btnType="mini" class="btn btn-danger" disabled><i class="fas fa-minus-circle"></i></button></td><?php
										} else {
											if ($community->pending){
												?><td><button btnType="mini" class="confirm btn btn-info" action="unpendCommunity"><i class="fas fa-circle-notch fa-spin"></i></button></td><?php
											} else {
												?><td><button btnType="mini" class="action btn btn-primary" action="joinCommunity" communityId="<?php echo $community->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-times"></i></button></td><?php
											}
										}
									}
							

									// Followed Community Button
									if ($community->followed) {
										if ($community->admin == $_SESSION['mixer_id']) {
											?><td><button class="btn btn-success" disabled><i class="fas fa-crown" style="color: gold"></i></button></td><?php
										} else {
											?><td><button btnType="mini" class="confirm action btn btn-success" action="unfollowCommunity" communityId="<?php echo $community->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-check"></i></button></td><?php
										}
									} else {
										?><td><button btnType="mini" class="action btn btn-primary"action="followCommunity" communityId="<?php echo $community->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-times"></i></button></td><?php
									}


									// Core Community Button
									if ($community->joined) {
										if ($community->core) {
											?><td><button class="action confirm btn btn-success" action="removeAsCore" communityId="<?php echo $community->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-check"></i></button></td><?php
										} else {
											?><td><button class="action btn btn-primary" action="setAsCore" communityId="<?php echo $community->id; ?>" userId="<?php echo $_SESSION['mixer_id']; ?>"><i class="fas fa-thumbs-up"></i></button></td><?php
										}
									} else {
										?><td><button class="btn btn-danger" disabled><i class="fas fa-minus-circle"></i></button></td><?php
									}

								?>

							</tr>
						<?php } ?>

						
					</tbody>
				</table>
			</div>

			<div id="settingsManager" class="inactiveView">
				<h2>Manage Settings</h2>
				<p class="devNote">Settings management are pending once more features are implemented.</p>
			</div>
		</div>
	</div>
</main>
