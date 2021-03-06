<?php 
$followingGames = false;
	if (isset($_SESSION['mixer_id'])) { 
		$followingGames = (!empty($currentUser->followedTypeList) || !empty($currentUser->offlineFollowedTypeList)); }

	if ($followingGames) { 
		$view = "followed";
	} else {
		$view = "allActive";
	}

	//$view = "allActive";
?>
<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Games & Stream Types <?php echo devNotes('types'); ?></h1>
	</div>

	<div class="btn-group d-flex" role="group">
		<button type="button" class="btn btn-info displayToggle" target="followed" <?php if ($view == "followed") { echo "disabled"; }?>>Following</button>
		<button type="button" class="btn btn-info displayToggle" target="allActive"  <?php if ($view == "allActive") { echo "disabled"; }?>>All Online Games</button>
	</div>
	<div class="row">
		<div class="col">
			<?php if ($followingGames) { ?>
			<div id="followed" class="typeView <?php if ($view != "followed") { echo "inactiveView"; }?>">
				<h2>Followed Games</h2>
				<div class="typeList"> 
					<div class="streamerList row">
						<?php if (!empty($currentUser->followedTypeList)) {
							foreach ($currentUser->followedTypeList as $type) {
								echo card(array(
									'id' => $type['id'],
									'name' => $type['name'],
									'kind' => 'type',
									'followState' => 'followed',
									'typeid' => $type['id'],
									'url' => "/type/".$type['id']."/".$type['slug'],
									'stats' => array(
										'online' => $type['online'],
										'viewers' => $type['viewersCurrent']
									),
									'cover' => $type['coverUrl']));
							}
						} else {
							echo "<p>None of the types you follow are online. Sorry about that.</p>";
						} ?>
					</div> <!-- online types -->

					<h2>Offline Followed Games</h2>
					<div class="row">
						<?php if (!empty($currentUser->offlineFollowedTypeList)) {
							foreach ($currentUser->offlineFollowedTypeList as $type) {
								echo card(array(
									'id' => $type['id'],
									'name' => $type['name'],
									'size' => 'sml',
									'kind' => 'type',
									'followState' => 'followed',
									'typeid' => $type['id'],
									'url' => "/type/".$type['id']."/".$type['slug'],
									'stats' => array(
										'online' => $type['online'],
										'viewers' => $type['viewersCurrent']
									),
									'cover' => $type['coverUrl']));
							}
						} else {
							echo "<p>All your followed types are online! Go watch some!</p>";
						}?>
					</div><!-- offline types -->

				</div> <!-- type list -->
			</div> <!-- followed -->
			<?php } else { ?>
			<div id="followed" class="typeView <?php if ($view != "followed") { echo "inactiveView"; }?>"> 
				<h4>You haven't followed any games yet!</h4>
				<p>Visit any game to follow them!</p>
			</div> <!-- followed -->
			<?php } ?>

			<div id="allActive" class="typeView <?php if ($view != "allActive") { echo "inactiveView"; }?>">
				<?php if (isset($_SESSION['mixer_id'])) { ?>
				<h2>Online Games</h2>
				<?php } ?>
				<div class="streamerList row">
					<?php foreach ($allTypes as $type) {
					echo card(array(
						'id' => $type['id'],
						'name' => $type['name'],
						'size' => 'med',
						'kind' => 'type',
						'followState' => $type['followState'],
						'typeid' => $type['id'],
						'url' => "/type/".$type['id']."/".$type['slug'],
						'stats' => array(
							'online' => $type['online'],
							'viewers' => $type['viewersCurrent']
						),
						'cover' => $type['coverUrl']));
					}?>
				</div> <!-- online list -->
			</div> <!-- all active -->

		</div><!-- col --> 
	</div><!-- row -->
</main>