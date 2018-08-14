<main role="main" class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Account Management</h1>
	</div>

	<div class="btn-group d-flex" role="group">
		<button type="button" class="btn btn-info displayToggle" target="summaryView" disabled>Summary</button>
		<button type="button" class="btn btn-info displayToggle" target="settingsManager">Settings</button>
		<button type="button" class="btn btn-info displayToggle" target="typeManager">Games/Types</button>
		<button type="button" class="btn btn-info displayToggle" target="commManager">Communities</button>
	</div>

	<p class="devNote">Account Management features are planned to added/implemented alongside appropriate features. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area if dev notes are present for associated tasks.</p>

	<div class="row">

		<div class="col">

			<div id="summaryView">
				<h2>Account Summary</h2>
				<h3>Quick Overview</h3>
				<div class="row">
					<div class="col">
						<h4>MixMingler</h4>
						<ul>
							<li>You are a member of {joinCount} communities.</li>
							<li>You follow {followCount} communities.</li>
						</ul>
						
					</div>
					<div class="col">
						<h4>Mixer.com</h4>
						<ul>
							<li>You've been active {timesActive} over the last {elapsedTime} days.</li>
						</ul>
					</div>
				</div>
				
				<h3>Pending Communities Information</h3>
				<div class="row">
					<div class="col">
						<h4>Requests to Join</h4>
						<p>Coming Soon</p>
					</div>
					<div class="col">
						<h4>Requests to Found</h4>
						<p>Coming Soon</p>
					</div>
				</div>


				<h3>Your Core Communities</h3>
				<div class="row">
					<?php
					if (!empty($communitiesData->core)) {
						foreach ($communitiesData->core as $community) {
							//echo $community->long_name;
							echo card(array(
								'id' => $community->id,
								'name' => $community->long_name,
								'size' => 'med',
								'kind' => 'community',
								'url' => "/community/".$community->slug,
								'stats' => array(
									'members' => count(explode(",", $community->members))
								),
								'cover' => "/assets/graphics/covers/".$community->slug.".jpg"));
						}
					} else {
						echo '<p>You haven\'t marked any core communities yet. Head over to your Communities tab to select some.</p>';
						}
					?>
					
				</div>
			</div>

			<div id="typeManager" class="inactiveView">
					
				<h2>Manage Games/Types</h2>

				<!--<div class="btn-group btn-group-justified" style="width:50%" role="group">
					<button type="button" class="btn btn-info displayToggle" target="typeFollowed" disabled>Followed</button>
					<button type="button" class="btn btn-info displayToggle" target="typeIgnored">Ignored</button>
				</div>-->

				<table class="table table-striped table-bordered table-hover table-sm ">
					<thead class="thead-dark">
							<tr>
								<th width="5%">Cover</th>
								<th width="75%">Type Name</th>
								<th width="10%">Status</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if (!empty($types)) {
									$baseParams = array(
										'userId' => $_SESSION['mixer_id'],
										'btnType' => 'mini',
										'displayType' => 'text');
									foreach ($types as $type) { 
										$typeParams = [
											'typeId' => $type->ID];

										switch($type->FollowState) {
											case 'followed':
												$rowClass = "bg-success";
												$actionParams = ['action'=>'unfollowType', 'content'=>'Unfollow', 'state'=>'danger', 'confirm' => true];
												break;
											case 'ignored':
												$rowClass = "bg-dark";
												$actionParams = ['action'=>'unignoreType', 'content'=>'Unignore', 'state'=>'warning', 'confirm' => false];
												break;
										}

										?>


										<tr>
											<td><img src="<?php echo $type->CoverURL; ?>" width="40"></td>
											<td><a href="/type/<?php echo $type->ID; ?>/<?php echo $type->Slug; ?>\"><?php echo $type->Name; ?></a></td>
											<td class="followState"><?php echo ucfirst($type->FollowState); ?></td>
											<td><?php echo action_button(array_merge($baseParams, $typeParams, $actionParams)); ?>
										</tr> <?php
									}
								} else {
									echo "<tr>";
									echo "<td colspan=\"3\">You haven't followed any games.</td>";
									echo "</tr>";
								}
							?>
						</tbody>
				</table>
			</div>

			<div id="commManager" class="inactiveView">
				<h2>Manage Communities</h2>
				<p>Click a button to toggle status.</p>

				<table class="table table-striped table-bordered table-hover table-sm ">
					<thead class="thead-dark">
						<tr>
							<th scope="col" width="70%">Name</th>
							<th scope="col" width="10%">Joined</th>
							<th scope="col" width="10%">Followed</th>
							<th scope="col" width="10%">Make Core!</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							if (!empty($communities)) {
								foreach ($communities as $community) {
									$baseParams = [
										'communityId' => $community->ID,
										'userId' => $_SESSION['mixer_id'],
										'btnType' => 'mini',
										'displayType' => 'icon'];

									$states = getMemberStateBooleans($community->MemberStates);
									/*$states = explode(",", $community->MemberStates);
										$states['isAdmin'] = in_array('admin', $states);
										$states['isBanned'] = in_array('banned', $states);
										$states['isCore'] = in_array('core', $states);
										$states['isFounder'] = in_array('founder', $states);
										$states['isFollower'] = in_array('follower', $states);
										$states['isMember'] = in_array('member', $states);
										$states['isModerator'] = in_array('moderator', $states);
										$states['isPending'] = in_array('pending', $states);*/


									// Get parameters for joined state button
									if ($states['isMember']) {
										// IF: user is the admin
										// ELSE: User is not admin, and has joined.
										if ($states['isAdmin']) { $joinParams = [
											'disabled'=>true,'content'=>'crown','state'=>'success']; }  
										else { $joinParams = [
											'disabled'=>false,'content'=>'check','state'=>'success','action'=>'leaveCommunity','confirm'=>true]; }
									} else {
										// Not a members
										// Is banned?
										if ($states['isBanned']) { $joinParams = [
											'disabled'=>true,'content'=>'ban','state'=>'dark'];}  
										// Or community is closed?
										elseif ($community->Status == 'closed') { $joinParams = [
											'disabled'=>true,'content'=>'minus-circle','state'=>'danger', 'tooltip'=>'Community is Closed']; }
										// Of membership is pending?
										elseif ($states['isPending']) { $joinParams = [
											'disabled'=>false,'content'=>'circle-notch fa-spin','state'=>'info','action'=>'unpendCommunity','confirm'=>true, 'tooltip'=>'Your Request is Pending']; }
										elseif ($community->isApprovalRequired) { $joinParams = [
											'disabled'=>false,'content'=>'question-circle','state'=>'info','action'=>'joinCommunity', 'tooltip'=>'Requires Approval']; }	
										// Or nothing getting in the way, so user can join?
										else {$joinParams = [
											'disabled'=>false,'content'=>'times','state'=>'primary','action'=>'joinCommunity', 'tooltip'=>'Join Community'];}
									}

									// Get parameters for followed state button
									if ($states['isFollower']) {
										// Is admin?
										if ($states['isAdmin']) { $followParams = [
											'disabled'=>true,'content'=>'crown','state'=>'success'];}
										else { $followParams = [
											'disabled'=>false,'confirm'=>true,'state'=>'success','content'=>'check','action'=>'unfollowCommunity'];}
									} else {
										$followParams = [
											'disabled'=>false,'confirm'=>false,'state'=>'primary','content'=>'times','action'=>'followCommunity'];
									}

									// Get parameters for core community state button
									if ($states['isMember']) {
										if ($states['isCore']) {
											$coreParams = ['disabled'=>true,'confirm'=>true,'state'=>'success','content'=>'check','action'=>'removeAsCore'];
										} else {
											$coreParams = ['disabled'=>false,'confirm'=>false,'state'=>'primary','content'=>'thumbs-up','action'=>'setAsCore'];
										}
									} else {
										$coreParams = ['disabled'=>true,'confirm'=>false,'state'=>'danger','content'=>'minus-circle','action'=>'setAsCore'];
									}

									?>

								<tr>
									<td><?php echo $community->Name; 
										echo roleBadge('banned', $states['isBanned']);
										echo roleBadge('founder', $states['isFounder']);
										echo roleBadge('admin', $states['isAdmin']);
										echo roleBadge('moderator', $states['isModerator']);
										echo roleBadge('core', $states['isCore']); ?>
									</td>
									<td><?php echo action_button(array_merge($baseParams, $joinParams)); ?></td> <!-- joined state -->
									<td><?php echo action_button(array_merge($baseParams, $followParams)); ?></td> <!-- followed state -->
									<td><?php echo action_button(array_merge($baseParams, $coreParams)); ?></td> <!-- core state -->
								</tr>

								<?php } 
							} else {?>
								<td colspan="4">You are not a part of any community.</td>
							<?php }	?>
					</tbody>
				</table>
			</div>

			<div id="settingsManager" class="inactiveView">
				<h2>Manage Settings</h2>
				<p class="devNote">Settings management are pending once more features are implemented.</p>
			</div>
		</div>
	</div>
</main>
