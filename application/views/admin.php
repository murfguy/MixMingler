<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>MixMingler Admin Panel</h1>
	</div>
	<p class="devNote">Admin features are planned to added/implemented alongside Community features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>
	<div id="glance" class="container">

	<div class="row">
		<nav class="nav flex-column col-2">
		  <a class="viewToggle btn btn-secondary" category="glance">At a Glance</a>
		  <a class="viewToggle btn btn-secondary " category="users">User Management</a>
		  <a class="viewToggle btn btn-secondary " category="communities">Communities</a>
		</nav>

		<div id="glance" class="mainView activeView container col">
			<h2>At a Glance</h2>
			<div class="row">

				<div class="infoBox col">
					<h4 class="infoHeader">Recent Logins</h4>
					<div class="infoInterior">
					<?php
					foreach ($logins as $user) {
						echo "<a href=\"/user/$user->name_token\"><img src=\"$user->avatarURL\" class=\"avatar thin-border\" width=\"25px\"> $user->name_token</a> <span class=\"postTime\">$user->loginTime</span><br>";
					}
				?>
					</div>
				</div>


				<div class="infoBox col">
					<h4 class="infoHeader">Recent Registrations</h4>
					<div class="infoInterior">
					<?php
					foreach ($registrations as $user) {
						echo "<a href=\"/user/$user->name_token\"><img src=\"$user->avatarURL\" class=\"avatar thin-border\" width=\"25px\"> $user->name_token</a><br>";
					}
				
				?>
					</div>
				</div>

				<div class="infoBox col">
					<h4 class="infoHeader">Analytics</h4>
					<div class="infoInterior">
						<p>Pending Feature</p>
						<ul>
							<li>New registrations</li>
							<li>New syncs</li>
							<li>Total streams seen</li>
							<li>Community activity</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="infoBox col">
					<h4 class="infoHeader">Pending Communitiy Requests</h4>
					<div class="infoInterior">
						<p>Pending Feature</p>
					</div>
				</div>
			</div>
			
		</div>

		<div id="users" class="mainView inactiveView container col">
			<h2>Users</h2>
			<h5>Apply Role</h5>
			<?php 
				$attributes = array('id' => 'applyRole');
				echo form_open('servlet/applyUserRole', $attributes); 
				echo form_input('name_token', '');

				$options = array(
			        'admin'         => 'Admin',
			        'dev'           => 'Developer',
			        'user'         => 'User'
				);

				$roles = array('small', 'large');
				echo form_dropdown('roles', $options, 'large');
				//echo form_submit('submit', 'Apply Role');
			?>
			<button class="btn btn-primary applyRole">Apply Role</button>
			<?php
				echo form_close();
			?>
		</div>

		<div id="communities" class="mainView inactiveView container col">
			<h2>Communities</h2>
			<p>Show list of pending community requests</p>
			<p>Click item for details.</p>
			<p>Form: Accept/Reject + reason for rejection</p>
		</div>


		
		</div>
	</div>


</main>