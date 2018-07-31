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

		$community_info = $this->communities->getCommunityBySlug($communitySlug);

		//$sql_query = "SELECT * FROM communities WHERE slug=?";
		//$query = $this->db->query($sql_query, array($communityName));

		if ($community_info != null) {
			$data->community_info = $community_info;

			// Get all Community Members
			$members = $this->communities->getCommunityMembersFromList($community_info->members);
			$followers = $this->communities->getCommunityMembersFromList($community_info->followers);
			//$coreMembers = $this->communities->getCommunityMembersFromList($community_info->id, $community_info->coreMembers);
			$admin = $this->communities->getCommunityMembersFromList($community_info->admin)[0];
			$founder = $this->communities->getCommunityMembersFromList($community_info->founder)[0];
			$moderators = $this->communities->getCommunityMembersFromList($community_info->moderators, 'name_token', 'ASC');
			$pendingMembers = $this->communities->getCommunityMembersFromList($community_info->pendingMembers);
			//$bannedMembers = $this->communities->getCommunityMembersFromList($community_info->id, $community_info->bannedMembers);

			// Get admin and mods
			$community_leads = $this->communities->getCommunityLeads($data->community_info->id);

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

				if ($_SESSION['mixer_id'] == $community_info->founder) {
					$currentUser->isFounder = true;
				}

				if ($_SESSION['mixer_id'] == $community_info->admin) {
					$currentUser->isAdmin = true;
				}

				if (in_array($_SESSION['mixer_id'], explode(',', $community_info->moderators))) {
					$currentUser->isMod = true;
				}

				$sql_query = "SELECT joinedCommunities,followedCommunities, pendingCommunities FROM mixer_users WHERE name_token=?";
				$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));

				$joinedCommunities = explode(",", $query->result()[0]->joinedCommunities);
				if (array_search($community_info->id, $joinedCommunities) > - 1) { $currentUser->isMember = true; }

				$followedCommunities = explode(",", $query->result()[0]->followedCommunities);
				if (array_search($community_info->id, $followedCommunities) > - 1) { $currentUser->isFollower = true; }

				$pendingCommunities = explode(",", $query->result()[0]->pendingCommunities);
				if (array_search($community_info->id, $pendingCommunities) > - 1) { $currentUser->isPending = true; }

				$data->currentUser = $currentUser;
			} else {
				$data->currentUser = null;
			}

			$feedData = null;
			$newsDisplayItems = null;

			$sql_query = "SELECT *, (SELECT name_token AS username FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) AS username, (SELECT avatarURL AS username FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) AS avatar FROM `timeline_events` WHERE eventType=\"community\" AND extraVars=? ORDER BY eventTime DESC LIMIT 0,50";
			$query = $this->db->query($sql_query, array($community_info->id));
			$feedData = $query->result();

			// We need to get the HTML version of these events so we can display them in the view.
			if ($feedData != null) {
				$newsDisplayItems = array();
				foreach($feedData as $event) {
					$newsDisplayItems[] = $this->news->getNewsDisplay($event, $event->avatar, "mini");
				}
			}

			$data->feedData = $feedData;
			$data->newsDisplayItems = $newsDisplayItems;
			$data->members = $members;
			$data->followers = $followers;
			$data->admin = $admin;
			$data->moderators = $moderators;
			//$data->coreMembers = $members;
			$data->pendingMembers = $pendingMembers;
			//$data->bannedMembers = $members;
			$data->community_leads = $community_leads;
			$data->online_members = $online_members;
			//$this->load->view('community-admin', $data);


			if (empty($params[0]) || $data->currentUser == null) {
				// If not trying to access mod page, OR user isn't logged in:
				// Load the community view
				if ($community_info->status == 'open' || $community_info->status == 'closed' ) {
					$this->load->view('community', $data);
				} else {
					?> 
					<main role="main" class="container">
						<div class="pageHeader">
							<h1><?php echo $community_info->long_name; ?></h1>
						</div>
						<?php if ($community_info->status == 'pending')  { ?>
							<div class="alert alert-warning"><h3>This community has been recently requested, and is pending approval.</h3></div>
						<?php } ?>
						<?php if ($community_info->status == 'approved')  { ?>
							<div class="alert alert-success"><h3>This community has been approved by site mods, but is waiting for public release by the community owner.</h3></div>
						<?php } ?>
						<?php if ($community_info->status == 'rejected')  { ?>
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
			'bannedFromCreation' => false,
			'isLoggedIn' => true
		);

		if (!empty($_SESSION['mixer_id'])) {
			$user = $this->users->getUserFromMingler($_SESSION['mixer_id']);

			$pending = null;
			if ($user != null) {
				$pending = $this->users->getUsersPendingCommunities($_SESSION['mixer_id']);
				$approved = $this->users->getUsersApprovedCommunities($_SESSION['mixer_id']);
				$rejected = $this->users->getUsersRejectedCommunities($_SESSION['mixer_id']);
			}

			$timespan = 14; // Default, two weeks
			if ($user->numFollowers < 200) { $timespan = 7*4; } // if < 200 followers, 4 weeks
			if ($user->numFollowers < 100) { $timespan = 7*5; } // if < 100 followers, 5 weeks
			if ($user->numFollowers < 50) { $timespan = 7*6; } // if < 50 followers, 6 weeks

			// Debug/Alpha Value
			$timespan = 2; // 2 days

			if ($user->minglerRole != 'user') { $timespan = 0; } // site runners can make communities whenever

			// If user is banned from making communities: fail, and no other criteria matter.
			if ($user->bannedFromCreation) { $creationCriteria['bannedFromCreation'] = true; } else {
				// If user isn't banned, then let's look at the other critera.

				// If user's account is under 90 days old: fails
				if (strtotime($user->joinedMixer) > (time() - (60*60*24*90))) { $creationCriteria['agedEnough'] = false; }
				
				// If user has a pending community approval: fails
				if ($pending != null) { $creationCriteria['pendingApproval'] = true; }

				// If user has a community approved but not finalized: fails
				if ($approved != null) { $creationCriteria['recentlyApproved'] = true; }

				// If user has a community that was rejected: fails
				if ($rejected != null) { $creationCriteria['rejected'] = true; }

				// If user founded a community too recently: fail
				if (strtotime($user->lastFoundation) > (time() - ((60*60*24)*$timespan))) { $creationCriteria['recentlyFounded'] = true; }
			}

		} else {
			// If user isn't logged in: fail
			 $creationCriteria['isLoggedIn'] = false;
		}
		
		$data = new stdClass();
		$data->creationCriteria = $creationCriteria;

		$this->load->view('community-add', $data);
	}

	private function loadAllCommunities() {
		$sql_query = "SELECT communities.*, (CHAR_LENGTH(communities.members) - CHAR_LENGTH(REPLACE(communities.members, ',', '')) + 1) as memberCount,
community_categories.name as category_name,
community_categories.slug as category_slug
FROM `communities`
JOIN community_categories ON communities.category_id = community_categories.id
WHERE communities.status='open' OR communities.status='closed'";
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