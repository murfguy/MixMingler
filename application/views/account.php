<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Account Management</h1>
	</div>

	<p class="devNote">Account Management features are planned to added/implemented alongside appropriate features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area if dev notes are present for associated tasks.</p>




	<div class="row">
		<div class="col-3">
			<h2>Tools</h2>	
			<p class="devNote" data-toggle="tooltip" title="Planned for v0.2" data-placement="left">Type Managment Tools</p>
			<p class="devNote" data-toggle="tooltip" title="Planned for v0.3" data-placement="left">Community Managment Tools</p>
			<p class="devNote" data-toggle="tooltip" title="Planned for v0.4" data-placement="left">Personal Managment Tools</p>
			<!--<ul>
				<li><a href="" data-toggle="tooltip" title="You may sync with Mixer every 30 minutes.">Sync with Mixer</a> [pending]</li>
				<li><a href="" data-toggle="tooltip" title="You may fetch your followers once per day.">Fetch</a> Follows [pending]</li>
			</ul>-->
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
