<main role="main" class="container-fluid">
	<div id="userHeader" class="pageHeader">
		<h1>Account Management</h1>
	</div>

	<p class="devNote">Account Management features are planned to added/implemented alongside appropriate features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area if dev notes are present for associated tasks.</p>

	<div class="row">
		<div class="col-2">

			<div class="btn-group-vertical" role="group" style="width: 100%;">
				<button type="button" class="btn btn-info displayToggle" target="summaryView">Summary</button>
				<button type="button" class="btn btn-info displayToggle" target="typeManager">Games</button>
				<button type="button" class="btn btn-info displayToggle" target="commManager" disabled>Communities</button>
				<button type="button" class="btn btn-info displayToggle" target="settingsManager">Settings</button>
			</div>
		</div>
		<div class="col-10">

			<div id="summaryView" class="inactiveView">
				<h2>Account Summary</h2>
				<p>Coming soon!</p>
				<ul>
					<li>overview of types followed/ignored</li>
					<li>communities core/joined/followed</li>
					<li>overview of pending new communites + pending to join</li>
				</ul>
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
									foreach ($followedTypesData as $type) {
										echo "<tr>";
										echo "<td><img src=\"$type->coverUrl\" width=\"40\"></td>";
										echo "<td><a href=\"/type/$type->typeId/$type->slug\">$type->typeName</a></td>";
										echo "<td><button type=\"button\" data-toggle=\"tooltip\" title=\"Stop getting updates about this game.\" id=\"unfollow\" typeId=\"".$type->typeId."\" class=\"typeAction btn-sm btn-danger\">Unfollow</button></td>";
										echo "</tr>";
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
					

					<div id="typeIgnored">
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
									foreach ($ignoredTypesData as $type) {
										echo "<tr>";
										echo "<td><img src=\"$type->coverUrl\" width=\"40\"></td>";
										echo "<td><a href=\"/type/$type->typeId/$type->slug\">$type->typeName</a></td>";
										echo "<td><button type=\"button\" data-toggle=\"tooltip\" title=\"Have this game show up in lists again.\" id=\"unignore\" typeId=\"".$type->typeId."\" class=\"typeAction btn-sm btn-danger\">Unignore</button></td>";
										echo "</tr>";
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

			<div id="commManager">
				<h2>Manage Communities</h2>

				<h3>Your Core Communities</h3>
				<div class="row">
					<p>Coming soon: see your current core communities.</p>
				</div>

				<h3>Your Communities</h3>
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

									if ($community->joined) {
										?><td><button class="btn btn-success"><i class="fas fa-check"></i></button></td><?php
									} else{ 
										if ($community->status = 'closed') {
											?><td><button class="btn btn-danger" disabled><i class="fas fa-minus-circle"></i></button></td><?php
										} else {
											if ($community->pending){
												?><td><button class="btn btn-info"><i class="fas fa-circle-notch fa-spin"></i></button></td><?php
											} else {
												?><td><button class="btn btn-primary"><i class="fas fa-times"></i></button></td><?php
											}
										}
									}
							

									if ($community->followed) {
										?><td><button class="btn btn-success"><i class="fas fa-check"></i></button></td><?php
									} else {
										?><td><button class="btn btn-primary"><i class="fas fa-times"></i></button></td><?php
									}
									if ($community->joined) {
										if ($community->core) {
											?><td><button class="btn btn-success"><i class="fas fa-check"></i></button></td><?php
										} else {
											?><td><button class="btn btn-primary"><i class="fas fa-thumbs-up"></i></i></button></td><?php
										}
									} else {
										?><td><button class="btn btn-danger" disabled><i class="fas fa-minus-circle"></i></button></td><?php
									}

								?>

								
								<!--<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
								<td><button class="btn btn-primary"><i class="fas fa-thumbs-up"></i></i></button></td>-->
							</tr>
						<?php } ?>

						<!--<tr>
							<td>[img]</td>
							<td>Joined/Followed, Not Core</td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-primary"><i class="fas fa-thumbs-up"></i></i></button></td>
						</tr>
						<tr>
							<td>[img]</td>
							<td>Followed Only</td>
							<td><button class="btn btn-primary"><i class="fas fa-times"></i></button></td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-secondary" disabled><i class="fas fa-minus-circle"></i></button></td>
						</tr>
						<tr>
							<td>[img]</td>
							<td>Joined Only, Not Core</td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-primary"><i class="fas fa-times"></i></button></td>
							<td><button class="btn btn-primary"><i class="fas fa-thumbs-up"></i></i></button></td>
						</tr>
						<tr>
							<td>[img]</td>
							<td>Joined/Followed/Core</td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
						</tr>
						<tr>
							<td>[img]</td>
							<td>Pending Join</td>
							<td><button class="btn btn-info"><i class="fas fa-circle-notch fa-spin"></i></button></td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-secondary" disabled><i class="fas fa-minus-circle"></i></button></td>
						</tr>
						<tr>
							<td>[img]</td>
							<td>Closed, but followed</td>
							<td><button class="btn btn-secondary" disabled><i class="fas fa-minus-circle"></i></button></td>
							<td><button class="btn btn-success"><i class="fas fa-check"></i></button></td>
							<td><button class="btn btn-secondary" disabled><i class="fas fa-minus-circle"></i></button></td>
						</tr>-->
					</tbody>
				</table>

				<!--<div class="btn-group btn-group-justified" style="width:50%" role="group">
					<button type="button" class="btn btn-info displayToggle" target="typeFollowed" disabled>Joined</button>
					<button type="button" class="btn btn-info displayToggle" target="typeIgnored">Followed</button>
				</div>

				<div class="windowGroup">
					<div id="joinedComms">
						<h4>Joined Communities</h4>
						<p>coming soon!</p>
					</div>
					<div id="followedComms">
						<h4>Joined Communities</h4>
						<p>coming soon!</p>
					</div>
				</div>-->
			</div>

			<div id="settingsManager" class="inactiveView">
				<h2>Manage Settings</h2>
			</div>
		</div>
	</div>
</main>
