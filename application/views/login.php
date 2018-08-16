<main role="main" class="container">
	<div class="pageHeader">
		<h1>Welcome to <span class="mixBlue">Mix</span>Mingler! <?php echo devNotes('login'); ?></h1>
	</div>
<div id="body">
	<div style="margin-bottom: 50px">
		<p>A site to find Mixer streamers based on your preferences!</p>	
	</div>

		<!--<p class="authenticateButton"><a href="/auth/session/">Authenticate with Mixer</a></p>

		<a class="authenticateButton" href="/auth/session/">Authenticate with Mixer</a>

		<form action="/auth/session/">
    		<input class="authenticateButton" type="submit" value="Authenticate with Mixer" />
		</form>-->
		<div class="authBtn">
			<button class="btn btn-secondary" onclick="window.location.href = '/auth/session/';"><i class="fas fa-sign-in-alt"></i> Login w/ Mixer</button>
		</div>

		<div style="margin-top: 20px">
		<div class="alert alert-warning" role="alert">
			<p>This site is currently in development, and is in a sort of loosely open 'alpha' test. All features/styles currently present are not finalized. By using this site you agree that any information/input you provide may kept/deleted/used at the discretion of the development team for testing purposes. If you'd like to help by testing out new features, and staying up-to-date with development, please join the <a href="https://discord.gg/hcS64t9">MixMingler Discord</a>.</p>

			<p>You will also see "Development Notes" sprinkled throughout the pages. These indicate ideas and issues that are intended to be addressed at a later date. Hover over a DevNote will indicate the proposed version when that feature will become available.</p>

			<p>A Version History and Development Roadmap can be read on the <a href="/alpha/">Alpha Information</a> page.</p>
		</div>


			<div class="infoBox">
				<h4 class="infoHeader">MixMingler Alpha Development Notices</h4>
				<div class="infoInterior">
					<div class="userFeedItem notices alert alert-danger">
						<h5 class="postTime">16 August 2018</h5>
						<p class="post">v0.2.2 is released. This update included a large scale overhaul to the database struture. Due to this, all data has been purged from the database in order to accommodate these changes. This includes games followed, communities created, even registration to the site. This is a clean slate.</p>
					</div>
					<div class="userFeedItem notices alert alert-danger">
						<h5 class="postTime">1 August 2018</h5>
						<p class="post">v0.2.1 is released. This update has made substantial changes to the backend functionality for communities. As such, in preparation for this release, all data related to communities has been purged.</p>
					</div>
				</div><!-- .infoInterior -->
			</div><!-- .infoBox -->
		<!--<h1>Proto-Login</h1>
		<form id="protoLogin">
			<div class="form-group">
				<label for="mixerName"></label>
				<input type="mixerName" class="form-control" id="inputMixerName" placeholder="Enter Mixer Username">
			</div>
			<button type="submit" id="loginButton" class="btn btn-primary">Submit</button>
		</form>-->
		</div>


	</div>
</main>