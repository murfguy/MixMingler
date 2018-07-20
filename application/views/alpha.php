<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Alpha Notes &amp; Version History</h1>
	</div>
	<div class="row">
		<div class="col-6 versionHistory" >
			<h2>Version History</h2>
			<?php
				foreach ($versionHistory as $patch) {
					echo "<p><b>$patch->date</b> ($patch->version)</p>";
					echo "<ul>";
					foreach ($patch->notes as $note) {
						echo "<li>$note</li>";
					}
					echo "</ul>";
				}
			?>
		</div>
		<div class="col-4">
			<h2>Development Roadmap</h2>
			<p>Bug fix plans and tweaks are not outlined in roadmap.</p>
			<p>A feature being present on the roadmap means it has a strong likelihood of being development, but may not be guaranteed.</p>
			<h3>v0.1 - Groundworks</h3>
				<p>Status: Complete</p>

			<h3>v0.2 - Types</h3>
				<p>Status: In Development</p>
			<ul>
				<li>Home Page: Show personal activity history on home page.</li>
				<li>Profile pages: Showcase followed types</li>
				<li>Types Page: Showcase followed games at top.</li>
				<li>Types Page: Hide ignored types from large lists.</li>
				<li>Account Management: Manage followed/ignored stream types</li>
				<li>Home page: MixMingler news feed for important site updates.</li>
			</ul>

			<h3>v0.3 - Communities</h3>
			<ul>
				<li>Home page: Global news feed as default view. Collects all news from types + communities a user follows.</li>
				<li>Home page: View recent activity based on community follows.</li>
				<li>Overhaul community browsing page</li>
				<li>Provide admin/moderation tools for communities.</li>
				<li>Allow users to request new communities.</li>
				<li>Provide information about: founding member, current owner, current moderators</li>
				<li>Account Management: Select core communities</li>
				<li>Account Management: Manage communities</li>
			</ul>
			<h3>v0.4 - Streamers</h3>
			<ul>
				<li>Streamers Page: should attempt to make smart suggestions of streamers based on your followed games and communities</li>
				<li>Login Page: Showcase random communities, streamers and game types</li>
				<li>Profiles: Showcase teams</li>
				<li>General: Track followed Mixer channels</li>
				<li>Database: High probability of data reset at before "Beta" phase is live.</li>
			</ul>
			<h3>v0.5 - Beta</h3>
			<ul>
				<li>Phase: Open testing of "live" data</li>
			</ul>
			<h3>Unmapped Features</h3>
			<ul>
				<li>Be able to find offline games/games not yet in MixMingler database.</li>
				<li>Be able to crowd-source linking games to communities to help improve suggestions.</li>
				<li>Types Page: Allow option to follow/ignore from thumbnails.</li>
				<li>Raid Finder: A tool that would help find streams to raid.</li>
			</ul>

			<!--
			<h3>topic</h3>
			<ul>
				<li>item</li>
			</ul>
			-->
		</div>
	</div>
</main>