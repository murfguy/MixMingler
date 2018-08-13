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

					echo "<td><a href=\"/user/".$streamer->Username."\"><img class=\"avatar thin-border\" src=\"".$streamer->AvatarURL."\" width=\"30px\">".$streamer->Username."</a></td>";
					if (strtotime($streamer->LastSeenOnline) > (time()-(60*10)) ) {
						echo "<td style=\"color: green\">Online! Started about ".$streamer->LastStartElapsed."</td>";
						//echo "Now Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					} else {

						echo "<td>Last online: ".$streamer->LastSeenElapsed."</td>";
						//echo "Last Seen Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					}
					echo "<td>".$streamer->LastType."</td>";
					echo "</tr>";
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

					echo "<td><a href=\"/user/".$streamer->Username."\"><img class=\"avatar thin-border\" src=\"".$streamer->AvatarURL."\" width=\"30px\">".$streamer->Username."</a></td>";
					if (strtotime($streamer->LastSeenOnline) > (time()-(60*10)) ) {
						echo "<td style=\"color: green\">Online! Started about ".$streamer->LastStartElapsed."</td>";
						//echo "Now Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					} else {

						echo "<td>Last online: ".$streamer->LastSeenElapsed."</td>";
						//echo "Last Seen Streaming: <span class=\"mixBlue\">".$streamer->lastType."</span><br>".$streamer->lastSeenElapsed;
					}
					echo "<td>".$streamer->LastType."</td>";
					echo "</tr>";
				 } 
				 echo "</table>";
			} else {
				echo "<h2 class=\"noStream\">We found no streamers. Odd.</h2>";
			} ?>
		</div>

	<!--<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>Show list of streamers you follow</li>
			<li>Recommend new streamers based on communities and/or streamers you follow</li>
		</ul>
	</div>-->
</main>