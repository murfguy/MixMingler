<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Stream Types</h1>
	</div>

	<!--<p class="devNote" data-toggle="tooltip" title="Not yet roadmapped" data-placement="left">Add type search tool</p>

	<p class="devNote" data-toggle="tooltip" title="Not yet roadmapped" data-placement="left">Be able to add missing types</p>-->

	<!--<nav id="categoryNav">
		<a class="typeToggle" category="followed">All Followed Types</a> |
		<a class="typeToggle" category="active">All Active Types</a>
	</nav>-->

	<?php
		if ($currentUser != null) {
			// followed games
			echo "<div id=\"followed\">";
				echo "<div class=\"typeList large row\">"; 
					echo "<h2>Followed Games</h2>";
					echo "<div class=\"streamerList row\">";
						foreach ($currentUser->followedTypeList as $type) {
							if (empty($type['coverUrl'])) {
								$type['coverUrl'] = "https://mixer.com/_latest/assets/images/main/types/default.jpg";
							}
							echo "<div class=\"typeInfo\">";
							echo "<a href=\"/type/".$type['slug']."\"><img src=\"".$type['coverUrl']."\" class=\"coverArt\" /></a>";

								echo "<p class=\"typeName\"><a href=\"/type/".$type['slug']."\">".$type['name']."</a></p>";
								echo "<p class=\"stats\"><span class=\"onlineStat\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Current Streams\"><i class=\"fas fa-play-circle\"></i>  ".$type['online']."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Viewers\"><i class=\"fas fa-eye\"></i> ".$type['viewersCurrent']."</span></p>";
							echo "</div>";
						}
					echo "</div>";

					echo "<h2>Followed Games with no online streams</h2>";
					echo "<div class=\"streamerList row\">";
						foreach ($currentUser->offlineFollowedTypeList as $type) {
							if (empty($type['coverUrl'])) {
								$type['coverUrl'] = "https://mixer.com/_latest/assets/images/main/types/default.jpg";
							}
							echo "<div class=\"typeInfo sml offline\">";
							echo "<a href=\"/type/".$type['slug']."\"><img src=\"".$type['coverUrl']."\" class=\"coverArt\" /></a>";

								echo "<p class=\"typeName\"><a href=\"/type/".$type['slug']."\">".$type['name']."</a></p>";
								//echo "<p class=\"stats\"><span class=\"onlineStat\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Current Streams\"><i class=\"fas fa-play-circle\"></i>  ".$type['online']."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Viewers\"><i class=\"fas fa-eye\"></i> ".$type['viewersCurrent']."</span></p>";
							echo "</div>";
						}
					echo "</div>";
				echo "</div>";
			echo "</div>";
		}
	?>
	
	<h2>All Active Games</h2>
	<div class="streamerList row">
			<?php
				
				foreach ($allTypes as $type) {

					if (empty($type['coverUrl'])) {
						$type['coverUrl'] = "https://mixer.com/_latest/assets/images/main/types/default.jpg";
					}

					echo "<div class=\"typeInfo med\">";
					echo "<a href=\"/type/".$type['slug']."\"><img src=\"".$type['coverUrl']."\" class=\"coverArt\" /></a>";

						echo "<p class=\"typeName\"><a href=\"/type/".$type['slug']."\">".$type['name']."</a></p>";
						echo "<p class=\"stats\"><span class=\"onlineStat\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Current Streams\"><i class=\"fas fa-play-circle\"></i>  ".$type['online']."</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Viewers\"><i class=\"fas fa-eye\"></i> ".$type['viewersCurrent']."</span></p>";
					echo "</div>";
				}


					
			?>
	</div>
</main>