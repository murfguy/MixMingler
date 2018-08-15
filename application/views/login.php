<main role="main" class="container">
	<div class="pageHeader">
		<h1>Welcome to <span class="mixBlue">Mix</span>Mingler!</h1>
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

		<p class="devNote" data-toggle="tooltip" title="Planned for v0.4" data-placement="left">[Feature] View of randomized streams, types and communities.</p>
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