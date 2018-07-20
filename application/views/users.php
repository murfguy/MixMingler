<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Streamers</h1>
	</div>
	<p class="devNote" data-toggle="tooltip" title="Planned for v0.4" data-placement="left">[PLAN] This page should suggest streamers based on communities and types followed. For debug/testing, we are simply showcasing registered streamers and recently active streams.</p>
	

		<h1>Registered Members</h1>
		<div class="streamerList row">
			<?php 
			//$online_members = array();
			$spanCount = 1;
			if (count($regStreamers) > 0) {
				echo "<table>";
				echo "<tr>";
				echo "<th>Streamer</th>";
				echo "<th>Status</th>";
				echo "<th>Stream Type</th>";
				echo "</tr>";
				foreach ($regStreamers as $streamer) { 

					echo "<tr>";

					echo "<td><a href=\"/user/".$streamer->name_token."\"><img class=\"avatar thin-border\" src=\"".$streamer->avatarURL."\" width=\"30px\">".$streamer->name_token."</a></td>";
					if (strtotime($streamer->lastSeenOnline) > (time()-(60*10)) ) {
						echo "<td style=\"color: green\">Online! Started about ".$streamer->lastStartElapsed."</td>";
						//echo "Now Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					} else {

						echo "<td>Last online: ".$streamer->lastSeenElapsed."</td>";
						//echo "Last Seen Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					}
					echo "<td>".$streamer->lastType."</td>";
					echo "</tr>";



						/*echo "<div class=\"col-md streamerListing";
						if (strtotime($streamer->lastSeenOnline) > (time()-(60*10)) ) {
							echo " online";
						}
						echo "\">";						
						echo "<h5><a href=\"/user/".$streamer->name_token."\">".$streamer->name_token."</a></h5>";
						echo "<img class=\"avatar thin-border\" src=\"".$streamer->avatarURL."\" width=\"42px\" style=\"float: left\">";
						echo "<p>";
						//" In ".$streamer->joinedCount." Communities<br>Follows ".$streamer->followedCount." Communities";
						if (strtotime($streamer->lastSeenOnline) > (time()-(60*10)) ) {
							echo "Now Streaming: <span class=\"mixBlue\">".."</span><br>".$streamer->lastSeenElapsed;
						} else {
							echo "Last Seen Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
						}
						echo "</p>";
						echo "</div>";*/
				 } 
				 echo "</table>";
			} else {
				echo "<h2 class=\"noStream\">We found no streamers. Odd.</h2>";
			} ?>
		</div>
		
		<h1>Non-Mingler Streamers [Debug only]</h1>
		<div class="streamerList row">
			<?php 
			//$online_members = array();
			$spanCount = 1;
			if (count($nonRegStreamers) > 0) {
				echo "<table>";
				echo "<tr>";
				echo "<th>Streamer</th>";
				echo "<th>Status</th>";
				echo "<th>Stream Type</th>";
				echo "</tr>";
				foreach ($nonRegStreamers as $streamer) { 

					echo "<tr>";

					echo "<td><a href=\"/user/".$streamer->name_token."\"><img class=\"avatar thin-border\" src=\"".$streamer->avatarURL."\" width=\"30px\">".$streamer->name_token."</a></td>";
					if (strtotime($streamer->lastSeenOnline) > (time()-(60*10)) ) {
						echo "<td style=\"color: green\">Online! Started about ".$streamer->lastStartElapsed."</td>";
						//echo "Now Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					} else {

						echo "<td>Last online: ".$streamer->lastSeenElapsed."</td>";
						//echo "Last Seen Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					}
					echo "<td>".$streamer->lastType."</td>";
					echo "</tr>";



						/*echo "<div class=\"col-md streamerListing";
						if (strtotime($streamer->lastSeenOnline) > (time()-(60*10)) ) {
							echo " online";
						}
						echo "\">";						
						echo "<h5><a href=\"/user/".$streamer->name_token."\">".$streamer->name_token."</a></h5>";
						echo "<img class=\"avatar thin-border\" src=\"".$streamer->avatarURL."\" width=\"42px\" style=\"float: left\">";
						echo "<p>";
						//" In ".$streamer->joinedCount." Communities<br>Follows ".$streamer->followedCount." Communities";
						if (strtotime($streamer->lastSeenOnline) > (time()-(60*10)) ) {
							echo "Now Streaming: <span class=\"mixBlue\">".."</span><br>".$streamer->lastSeenElapsed;
						} else {
							echo "Last Seen Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
						}
						echo "</p>";
						echo "</div>";*/
				 } 
				 echo "</table>";
			} else {
				echo "<h2 class=\"noStream\">We found no streamers. Odd.</h2>";
			} ?>
		</div>

	<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>Show list of streamers you follow</li>
			<li>Recommend new streamers based on communities and/or streamers you follow</li>
		</ul>
	</div>
</main>