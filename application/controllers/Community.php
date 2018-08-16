<?php
class Community extends CI_Controller {

	public function index()
	{
		// MySQL: Get communities list.
		// sort by community size
		
		// show list of communities 
		//$this->load->view('htmlHead');
		//$this->load->view('htmlFoot');
	}

	public function _remap($method, $params = array()) {
		$this->load->view('htmlHead');
		$this->load->database();
		$this->load->library('communities');
		$this->load->library('news');
		$this->load->library('users');

		if ($method != "index") {
			if ($method != 'create') {
				$this->loadSingleCommunity($method, $params);
			} else {
				$this->loadCreateForm();
			}
		} else {
			$this->loadAllCommunities();
		}
		
		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}

	private function loadSingleCommunity($communitySlug, $params) {
		$data = new stdClass();

		$community = $this->communities->getCommunityBySlug($communitySlug);

		//$sql_query = "SELECT * FROM communities WHERE slug=?";
		//$query = $this->db->query($sql_query, array($communityName));

		if ($community != null) {
			$data->community = $community;

			// Get all Community Members by their groupings
			$founder = $this->communities->getCommunityMembersByGroup($community->ID, 'founder')[0];
			$admin = $this->communities->getCommunityMembersByGroup($community->ID, 'admin')[0];
			$moderators = $this->communities->getCommunityMembersByGroup($community->ID, 'moderator');
			$coreMembers = $this->communities->getCommunityMembersByGroup($community->ID, 'core');
			$members = $this->communities->getCommunityMembersByGroup($community->ID, 'member');
			$followers = $this->communities->getCommunityMembersByGroup($community->ID, 'follower');
			$pendingMembers = $this->communities->getCommunityMembersByGroup($community->ID, 'pending');
			$bannedMembers = $this->communities->getCommunityMembersByGroup($community->ID, 'banned');

			$data->memberIdLists = array(
				'moderators' => $this->communities->getArrayOfMemberIDs($moderators),
				'core' => $this->communities->getArrayOfMemberIDs($coreMembers),
				'members' => $this->communities->getArrayOfMemberIDs($members),
				'followers' => $this->communities->getArrayOfMemberIDs($followers),
				'pending' => $this->communities->getArrayOfMemberIDs($pendingMembers),
				'banned' => $this->communities->getArrayOfMemberIDs($bannedMembers)
			);

			// Check status of all members from Mixer API
			// channel-search bucket limit is 20 queuries per 5 seconds. 
			$online_members = $this->communities->getOnlineMembersFromMixer($members);

			// if user is logged in
			if (isset($_SESSION['mixer_user'])) {
				$currentUser = new stdClass();
				$currentUser->token = $_SESSION['mixer_user'];
				$currentUser->mixer_id = $_SESSION['mixer_id'];

				$currentUser->isMember = false;
				$currentUser->isPending = false;
				$currentUser->isFollower = false;
				$currentUser->isFounder = false;
				$currentUser->isAdmin = false;
				$currentUser->isMod = false;
				$currentUser->isBanned = false;

				if ($_SESSION['mixer_id'] == $community->Founder) {	$currentUser->isFounder = true;	}
				if ($_SESSION['mixer_id'] == $community->Admin) {$currentUser->isAdmin = true; }
				if (in_array($_SESSION['mixer_id'], $data->memberIdLists['moderators'])) { $currentUser->isMod = true; }				
				if (in_array($_SESSION['mixer_id'], $data->memberIdLists['banned'])) { $currentUser->isBanned = true; }
				if (in_array($_SESSION['mixer_id'], $data->memberIdLists['members'])) { $currentUser->isMember = true; }			
				if (in_array($_SESSION['mixer_id'], $data->memberIdLists['followers'])) { $currentUser->isFollower = true; }			
				if (in_array($_SESSION['mixer_id'], $data->memberIdLists['pending'])) { $currentUser->isPending = true; }

				$data->currentUser = $currentUser;
			} else {
				$data->currentUser = null;
			}

			$feedData = null;
			$newsDisplayItems = null;

			/*$sql_query = "SELECT *, (SELECT name_token AS username FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) AS username, (SELECT avatarURL AS username FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) AS avatar FROM `timeline_events` WHERE eventType=\"community\" AND extraVars=? ORDER BY eventTime DESC LIMIT 0,50";
			$query = $this->db->query($sql_query, array($community->ID));
			$feedData = $query->result();

			// We need to get the HTML version of these events so we can display them in the view.
			if ($feedData != null) {
				$newsDisplayItems = array();
				foreach($feedData as $event) {
					$newsDisplayItems[] = $this->news->getNewsDisplay($event, $event->avatar, "mini");
				}
			}

			$data->feedData = $feedData;
			$data->newsDisplayItems = $newsDisplayItems;*/
			$data->members = $members;
			$data->followers = $followers;
			$data->admin = $admin;
			$data->moderators = $moderators;
			$data->coreMembers = $coreMembers;
			$data->pendingMembers = $pendingMembers;
			$data->bannedMembers = $bannedMembers;
			//$data->community_leads = $community_leads;
			$data->online_members = $online_members;
			//$this->load->view('community-admin', $data);


			if (empty($params[0]) || $data->currentUser == null) {
				// If not trying to access mod page, OR user isn't logged in:
				// Load the community view
				if ($community->Status == 'open' || $community->Status == 'closed' ) {
					$this->load->view('community', $data);
				} else {
					?> 
					<main role="main" class="container">
						<div class="pageHeader">
							<h1><?php echo $community->Name; ?></h1>
						</div>
						<?php if ($community->Status == 'pending')  { ?>
							<div class="alert alert-warning"><h3>This community has been recently requested, and is pending approval.</h3></div>
						<?php } ?>
						<?php if ($community->Status == 'approved')  { ?>
							<div class="alert alert-success"><h3>This community has been approved by site mods, but is waiting for public release by the community owner.</h3></div>
						<?php } ?>
						<?php if ($community->Status == 'rejected')  { ?>
							<div class="alert alert-danger"><h3>This community was denied approval by site administrators and is awaiting deletion.</h3></div>
						<?php } ?>
						</main>


						<?php
				}
				
			} else {
				if (($currentUser->isAdmin || $currentUser->isMod) && $params[0]=='mod')  {
					// If user is admin OR moderator, AND trying to load mod page,
					// Load the community mod view
					$this->load->view('community-admin', $data);
				} else {
					// Load the base community view
					$this->load->view('community', $data);
				}
			}
		} else {
			//echo "<h2>Community does not exist!</h2>";
		}
	}

	private function loadCreateForm() {
		// Assume all criteria are succesful
		$creationCriteria = array(
			'agedEnough' => true,
			'pendingApproval' => false,
			'rejected' => false,
			'recentlyApproved' => false,
			'recentlyFounded'=> false,
			'isBannedCreator' => false,
			'isLoggedIn' => true
		);

		if (!empty($_SESSION['mixer_id'])) {
			$mixerID = $_SESSION['mixer_id'];
			$user = $this->users->getUserFromMingler($_SESSION['mixer_id']);

			$pending = null;
			if ($user != null) {
				$pending = $this->users->getUsersCreatedCommunitiesByStatus($mixerID, 'pending');
				$approved = $this->users->getUsersCreatedCommunitiesByStatus($mixerID, 'approved');
				$rejected = $this->users->getUsersCreatedCommunitiesByStatus($mixerID, 'rejected');
				//$pending = $this->users->getUsersPendingCommunities($_SESSION['mixer_id']);
				//$approved = $this->users->getUsersApprovedCommunities($_SESSION['mixer_id']);
				//$rejected = $this->users->getUsersRejectedCommunities($_SESSION['mixer_id']);
			}

			$timespan = 14; // Default, two weeks
			if ($user->NumFollowers < 200) { $timespan = 7*4; } // if < 200 followers, 4 weeks
			if ($user->NumFollowers < 100) { $timespan = 7*5; } // if < 100 followers, 5 weeks
			if ($user->NumFollowers < 50) { $timespan = 7*6; } // if < 50 followers, 6 weeks

			// Debug/Alpha Value
			$timespan = 2; // 2 days

			if ($user->SiteRole != 'user') { $timespan = 0; } // site runners can make communities whenever

			// If user is banned from making communities: fail, and no other criteria matter.
			if ($user->isBannedCreator) { $creationCriteria['isBannedCreator'] = true; } else {
				// If user isn't banned, then let's look at the other critera.

				// If user's account is under 90 days old: fails
				if (strtotime($user->JoinedMixer) > (time() - (60*60*24*90))) { $creationCriteria['agedEnough'] = false; }
				
				// If user has a pending community approval: fails
				if ($pending != null) { $creationCriteria['pendingApproval'] = true; }

				// If user has a community approved but not finalized: fails
				if ($approved != null) { $creationCriteria['recentlyApproved'] = true; }

				// If user has a community that was rejected: fails
				if ($rejected != null) { $creationCriteria['rejected'] = true; }

				// If user founded a community too recently: fail
				if (strtotime($user->LastFoundationTime) > (time() - ((60*60*24)*$timespan))) { $creationCriteria['recentlyFounded'] = true; }
			}

		} else {
			// If user isn't logged in: fail
			 $creationCriteria['isLoggedIn'] = false;
		}
		
		$data = new stdClass();
		$data->creationCriteria = $creationCriteria;

		$this->load->view('community-add', $data);
	}

	// Returns data for all communites, with member counts
	private function loadAllCommunities() {
		$sql_query = "SELECT C.*,
			CC.name as CategoryName,
			CC.slug as CategorySlug,
			count(UC.MixerID) as MemberCount
			FROM `Communities` as C
			JOIN CommunityCategories as CC ON C.CategoryID = CC.ID
			JOIN UserCommunities as UC ON C.ID = UC.CommunityID AND UC.MemberState = 'member'
			WHERE C.status='open' OR C.status='closed'
			GROUP BY CommunityID
			ORDER BY memberCount DESC";

		$query = $this->db->query($sql_query);
		$displayData = new stdClass();
		$displayData->communities = $query->result();

		$this->load->view('communities', $displayData);
	}

	public function sortOnlineMembers($a, $b) {
		if ($a->viewersCurrent == $b->viewersCurrent) {
			return 0;
		}
		return ($a->viewersCurrent < $b->viewersCurrent) ? -1: 1;
	}
}
?>