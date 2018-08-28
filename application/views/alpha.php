<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Alpha Version Information</h1>
	</div>

	
	<nav id="categoryNav">
		<a class="viewToggle" category="info">Alpha Info</a> | 
		<a class="viewToggle" category="version">Version History</a> | 
		<a class="viewToggle" category="roadmap">Roadmap</a>
	</nav>


	<div class="container">

		<div id="info" class="mainView activeView">
			<h2>Alpha Information</h2>
			<h3>Quick Info</h3>
			<ul>
				<li>Current Version: <?php echo $currentVersion->version." (".$currentVersion->stage.")"; ?></li>
				<li><a href="https://discord.gg/hcS64t9">MixMingler Discord</a></li>
				<li><a href="https://github.com/murfguy/MixMingler">GitHub</a></li>
			</ul>


			<h3>About</h3>

			<p>This site is currently in development, and is in a sort of loosely open 'alpha' test. All features/styles currently present are not finalized. <span style="color: red;">By using this site you agree that any information/input you provide may kept/deleted/used at the discretion of the development team for testing purposes.</span> If you'd like to help by testing out new features, and staying up-to-date with development, please join the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a>.</p>
			<p>Recent news and notices about the Alpha test will posted on the home page.</p>

		</div>

		<div id="version" class="mainView inactiveView">

			<h2>Version History</h2>
			<div id="accordion">

			<?php
				$itemCount = 0;
				foreach ($versionHistory as $patch) {
					echo "<div class=\"card\">";
						echo "<div class=\"card-header\" id=\"heading$itemCount\">";
							echo "<h5 class=\"mb-0\">";
					        echo "<button class=\"btn btn-link";
					         if ($itemCount > 0) {
					         	echo " collapsed";
					         }

					        echo "\" data-toggle=\"collapse\" data-target=\"#collapse$itemCount\" aria-expanded=\"true\" aria-controls=\"collapse$itemCount\">";
					          echo "<b>$patch->date</b> ($patch->version)";
					          echo "</button></h5>";
						echo "</div>";

						echo "<div id=\"collapse$itemCount\" class=\"collapse";
						 if ($itemCount == 0) {
					         	echo " show";
					         }
					     echo "\" aria-labelledby=\"heading$itemCount\" data-parent=\"#accordion\">";
							echo "<div class=\"card-body\">";
								echo "<ul>";
								foreach ($patch->notes as $note) {
									echo "<li>$note</li>";
								}
								echo "</ul>";
						
							echo "</div>";
						echo "</div>";
					echo "</div>";
					$itemCount++;


					/*echo "<p><b>$patch->date</b> ($patch->version)</p>";
					echo "<ul>";
					foreach ($patch->notes as $note) {
						echo "<li>$note</li>";
					}
					echo "</ul>";*/
				}
			?>
			</div>
		</div>
		<div id="roadmap" class="mainView inactiveView">
			<h2>Development Roadmap</h2>
			<p>Bug fix plans and tweaks are not outlined in roadmap.</p>
			<p>A feature being present on the roadmap means it has a strong likelihood of being development, but may not be guaranteed.</p>
			<h3>v0.1 - Groundworks</h3>
				<p>This version focuses on exploring initial ideas and functionality.</p>
				<p>Status: Complete</p>

			<h3>v0.2 - Types</h3>
				<p>This version focuses on adding functionality related to exploring and utilizing stream types/games.</p>
				<p>Status: Complete</p>

			<h3>v0.3 - Communities</h3>
				<p>This version focuses on adding functionality related to communities, including: creation, moderation, joining/following, etc.</p>
				<p>Status: Now in development!</p>
			<ul>
				<li>General: AJAX-based data loading to improve site performance speeds.</li>
				<li>Home page: View recent activity based on followed communities.</li>
				<li>Overhaul community browsing page</li>
				<li>Provide admin/moderation tools for communities.</li>
				<li>Allow users to request new communities.</li>
				<li>Community Detail: Provide information about: founding member, current owner, current moderators</li>
				<li>Account Management: Select core communities</li>
				<li>Account Management: Manage communities</li>
			</ul>
			<h3>v0.4 - Streamers</h3>
				<p>This version focuses on adding functionality related to streamers, and providing fun florishes, useful tools and recommendations.</p>
			<ul>
				<li>Streamers Page: should attempt to make smart suggestions of streamers based on your followed games and communities</li>
				<li>Login Page: Showcase random communities, streamers and game types</li>
				<li>Profiles: Showcase teams</li>
				<li>General: Track followed Mixer channels</li>
			</ul>
			<h3>v0.5 - Not Even My Final Form</h3>
				<p>This version focuses on implementing final design and user experience features.</p>
			<ul>
				<li>no outline as of yet</li>
			</ul>
			<h3>Unmapped Features</h3>
			<ul>
				<li>Be able to find offline games and games not yet in MixMingler database.</li>
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