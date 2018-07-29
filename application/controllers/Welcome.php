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
		}
		
		$viewData->user = $user;
		$viewData->communitiesData = $communitiesData;
		$viewData->modCommunities = $this->users->getUsersAdminedOrModeratedCommunities($_SESSION['mixer_id']);
		$viewData->pendingCommunities = $this->users->getUsersPendingCommunities($_SESSION['mixer_id']);
		$viewData->approvedCommunities = $this->users->getUsersApprovedCommunities($_SESSION['mixer_id']);
		$viewData->rejectedCommunities = $this->users->getUsersRejectedCommunities($_SESSION['mixer_id']);


		$viewData->gameNews = $gameNews;
		$viewData->followedTypes = $followedTypes;
		$viewData->slugs = $slugs;

		$this->load->view('main', $viewData);
	}
}
