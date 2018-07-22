<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Account Management</h1>
	</div>

	<p class="devNote">Account Management features are planned to added/implemented alongside appropriate features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area if dev notes are present for associated tasks.</p>




	<div class="row">
		<div class="col-3">
			<h2>Tools</h2>	
			<p>Manage Games/Types</p>
			
			<p class="devNote" data-toggle="tooltip" title="Planned for v0.3" data-placement="left">Community Managment Tools</p>
			<p class="devNote" data-toggle="tooltip" title="Planned for v0.4" data-placement="left">Personal Managment Tools</p>
			<!--<ul>
				<li><a href="" data-toggle="tooltip" title="You may sync with Mixer every 30 minutes.">Sync with Mixer</a> [pending]</li>
				<li><a href="" data-toggle="tooltip" title="You may fetch your followers once per day.">Fetch</a> Follows [pending]</li>
			</ul>-->
		</div>
		<div class="col-7">
			<div id="typeManager">
				<h2>Manage Games/Types</h2>

				<nav id="categoryNav">
					<a class="viewToggle accountTypes" category="followed">Followed Types</a> | <a class="viewToggle accountTypes" category="ignored">Ignored Types</a>
				</nav>
				
				<div id="followed">
					<h3>Followed Games/Types</h3>
					<table>
						<tr>
							<th>Cover</th>
							<th>Name</th>
							<th>Action</th>
						</tr>
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
					</table>
				</div>
				

				<div id="ignored" class="inactiveView">
					<h3>Ignored Games/Types</h3>
					<table>
						<tr>
							<th>Cover</th>
							<th>Name</th>
							<th>Action</th>
						</tr>
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
								echo "<td colspan=\"3\">You haven't followed any games.</td>";
								echo "</tr>";
							}
						?>
					</table>
				</div>
			</div>
		</div>
		<!--<div class="col-5">
			<h2>Communities</h2>
			<p>List communities, with checkboxes for 'core communities'</p>
			<div class="row">
				<div>
					<h4>Joined Communities</h4>
					<p>You may select up to four of these as your Core Communities.</p>
					<?php
					if (!empty($communitiesData->joined)) {
						foreach ($communitiesData->joined as $community) {
							$found = false;
							if (!empty($communitiesData->core)) {
								foreach($communitiesData->core as $key => $value) {
									if ($value->id == $community->id) {
										$found = true;
										break;
									}
								}
							}

							echo "<input class=\"coreCommunities\" type=\"checkbox\" id=\"$community->slug\" name=\"core\" value=\"$community->slug\" commId=\"$community->id\"";
							if ($found) {
								echo " checked";
							}
							echo "><label for=\"core\">$community->long_name</label><br>";
						}
					} else {
						echo "<p>No followed communities.</p>";
					}	
					?>
					
				</div>
			</div>
			
		</div>-->
	</div>
</main>
