<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>MixMingler Admin Panel</h1>
	</div>
	<p class="devNote">Admin features are planned to added/implemented alongside Community features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>
	<div class="row">
		<div class="col-3">
			<h4>Recent Logins</h4>

			<p>
			<?php
				foreach ($logins as $user) {
					echo "<a href=\"/user/$user->name_token\"><img src=\"$user->avatarURL\" class=\"avatar thin-border\" width=\"25px\">$user->name_token</a><span class=\"postTime\">$user->loginTime</span><br>";
				}
			?>
			</p>
			<h4>Recent Registrations</h4>

			<p>
			<?php
				foreach ($registrations as $user) {
					echo "<a href=\"/user/$user->name_token\"><img src=\"$user->avatarURL\" class=\"avatar thin-border\" width=\"25px\">$user->name_token</a><br>";
				}
			?>
			</p>
		</div>
		<div class="col">
			<h2>Pending Community Requests</h2>
			<p>Functionality Coming Soon. This is a place holder.</p>
			<p>Here are currently requested communities. Each request's information can be adjusted before approving. Use the notes field to address any changes or reasons for denial.</p>
			<table>
				<tr>
					<th>Community Name</th>
					<th>Category</th>
					<th>Description</th>
					<th>Requester</th>
					<th>Request Time</th>
					<th>Admin Notes</th>
					<th>Action</th>
				</tr>
				<tr>
					<td><input type="text" value="Placeholder"></td>
					<td>Misc.</td>
					<td>[text field for description]</td>
					<td><a href="/user/murfGUY">murfGUY</a></td>
					<td>2 hours ago</td>
					<td>[text field for writing notes]</td>
					<td><button type="button" class="btn btn-success">Approve</button><button type="button" class="btn btn-danger">Deny</button></td>
				</tr>

			</table>
			<h2>Recent Community Additions</h2>
			<p>Functionality Coming Soon. This is a place holder.</p>
		</div>
	</div>

	<div class="alert alert-info">
		<p>Recent Updates:</p>
		<ul>
			<li>Dec. 14: Admin Panel page created</li>
		</ul>
	</div>
	<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>List of recently registered streamers</li>
			<li>Show pending community creation requests</li>
		</ul>
	</div>
</main>