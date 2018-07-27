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

	public function _remap($method) {
		$this->load->view('htmlHead');
		$this->load->database();
		$this->load->library('communities');
		$this->load->library('news');
		$this->load->library('users');

		if ($method != "index") {
			if ($method != 'create') {
				$this->loadSingleCommunity($method);
			} else {
				$this->loadCreateForm();
			}
		} else {
			$this->loadAllCommunities();
		}
		
		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}

	private function loadSingleCommunity($communitySlug) {
		$data = new stdClass();

		$community_info = $this->communities->getCommunityBySlug($communitySlug);

		//$sql_query = "SELECT * FROM communities WHERE slug=?";
		//$query = $this->db->query($sql_query, array($communityName));

		if ($community_info != null) {
			$data->community_info = $community_info;

			// Get all Community Members
			$community_members = $this->communities->getCommunityMembers($communitySlug);

			// Check status of all members from Mixer API
			// channel-search bucket limit is 20 queuries per 5 seconds. 
			$online_members = $this->communities->getOnlineMembersFromMixer($community_members);

			// if user is logged in
			if (isset($_SESSION['mixer_user'])) {
				$currentUser = new stdClass();
				$currentUser->token = $_SESSION['mixer_user'];
				$currentUser->isMember = false;
				$currentUser->isFollower = false;

				$sql_query = "SELECT joinedCommunities,followedCommunities FROM mixer_users WHERE name_token=?";
				$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));

				$joinedCommunities = explode(",", $query->result()[0]->joinedCommunities);
				if (array_search($community_info->id, $joinedCommunities) > - 1) { $currentUser->isMember = true; }

				$followedCommunities = explode(",", $query->result()[0]->followedCommunities);
				if (array_search($community_info->id, $followedCommunities) > - 1) { $currentUser->isFollower = true; }

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
			$data->community_members = $community_members;
			$data->online_members = $online_members;

			// Load the community view
			$this->load->view('community', $data);
		} else {
			//echo "<h2>Community does not exist!</h2>";
		}
	}

	private function loadCreateForm() {
		// Assume all criteria are succesful
		$creationCriteria = array(
			'agedEnough' => true,
			'pendingApproval' => false,
			'recentlyFounded'=> false,
			'bannedFromCreation' => false,
			'isLoggedIn' => true
		);

		if (!empty($_SESSION['mixer_id'])) {
			$user = $this->users->getUserFromMingler($_SESSION['mixer_id']);

			$pending = null;
			if ($user != null) {
				$pending = $this->users->getUsersPendingCommunities($_SESSION['mixer_id']);
			}

			$timespan = 14; // Default, two weeks
			if ($user->numFollowers < 200) { $timespan = 7*4; } // if < 200 followers, 4 weeks
			if ($user->numFollowers < 100) { $timespan = 7*5; } // if < 100 followers, 5 weeks
			if ($user->numFollowers < 50) { $timespan = 7*6; } // if < 50 followers, 6 weeks
			if ($user->minglerRole != 'user') { $timespan = 0; } // site runners can make communities whenever

			// If user is banned from making communities: fail, and no other criteria matter.
			if ($user->bannedFromCreation) { $creationCriteria['bannedFromCreation'] = true; } else {
				// If user isn't banned, then let's look at the other critera.

				// If user's account is under 90 days old: fails
				if (strtotime($user->joinedMixer) > (time() - (60*60*24*90))) { $creationCriteria['agedEnough'] = false; }
				
				// If user has a pending community approval: fails
				if ($pending != null) { $creationCriteria['pendingApproval'] = true; }

				// If user founded a community too recently: fail
				if (strtotime($user->lastFoundation) > (time() - (60*60*24*$timespan))) { $creationCriteria['recentlyFounded'] = true; }
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
		$sql_query = "SELECT *, CEILING((members/3)+(followers/1.25)) AS popularity FROM `communities` ORDER BY popularity DESC, long_name ASC";
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