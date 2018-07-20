<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->library('version');

		$this->load->view('htmlHead');
		if (isset($_SESSION['mixer_user'])) {
			// If logged in:
			// Show main user view
			$this->loadMainView();
		} else {
			// If not logged in:
			// Show login/authenticate form
			$this->load->view('login');
		}
		$this->load->view('htmlFoot', $this->version->getVersion());
	}

	private function loadMainView() {
		$this->load->library('users');
		$this->load->library('communities');
		$this->load->library('news');
		
		$viewData = new stdClass();
		$viewData->userName = $_SESSION['mixer_user'];
		$user = $this->users->getUserFromMingler($_SESSION['mixer_id']);

		$communitiesData = new stdClass();
		$communitiesData->core = null;
		$communitiesData->joined = null;
		$communitiesData->followed = null;
		$communitiesData->new = $this->communities->getNewCommunities($user->previousLogin);

		if (!empty($user->coreCommunities)) {
			$communitiesData->core = $this->communities->getCommunitiesFromList($user->coreCommunities);
		} 
		if (!empty($user->joinedCommunities)) {
			$communitiesData->joined = $this->communities->getCommunitiesFromList($user->joinedCommunities);
		} 
		if (!empty($user->followedCommunities)) {
			$communitiesData->followed = $this->communities->getCommunitiesFromList($user->followedCommunities);
		} 

		$followedTypes = $this->types->getSpecifiedTypesFromMixer($user->followedTypes);
		//$followedTypes = array();
		$gameNews = array();
		$typeData = array();
		$slugs = array();

		foreach ($followedTypes as $type) {
			$slugs[$type['id']] = $this->types->createSlug($type['name']);
			$followedGameNews = $this->news->getTypeNewsFeed($type['id']);
			
			$gameNewsDisplayItems = array();

			foreach($followedGameNews as $event) {
				$gameNewsDisplayItems[] = $this->news->getNewsDisplay($event, "", "condensed");
			}

			$gameNews[$type['id']] = $gameNewsDisplayItems;
		}
		


		$feedData = null;
		$newsDisplayItems = null;
		//$sql_query = "SELECT *, (SELECT name_token FROM mixer_users WHERE mixer_id=?) as username FROM `timeline_events` WHERE mixer_id=? ORDER BY id DESC, eventTime DESC";


		
		if (!empty($user->followedCommunities)) {
			$sql_query = "SELECT *, (SELECT name_token AS username FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) AS username, (SELECT avatarURL AS username FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) AS avatar FROM `timeline_events` WHERE eventType='community' AND extraVars IN ($user->followedCommunities) ORDER BY eventTime DESC LIMIT 0,50";
			$query = $this->db->query($sql_query);
			$feedData = $query->result();

			// We need to get the HTML version of these events so we can display them in the view.
			if ($feedData != null) {
				$newsDisplayItems = array();
				foreach($feedData as $event) {
					$newsDisplayItems[] = $this->news->getNewsDisplay($event, $event->avatar, "mini");
				}
			}
		}

		
		
		$viewData->user = $user;
		$viewData->communitiesData = $communitiesData;
		$viewData->newsItems = $newsDisplayItems;
		$viewData->gameNews = $gameNews;
		$viewData->followedTypes = $followedTypes;
		$viewData->slugs = $slugs;

		$this->load->view('main', $viewData);
	}
}
