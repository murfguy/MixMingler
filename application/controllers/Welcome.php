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
		$mixerID = $_SESSION['mixer_id'];
		$user = $this->users->getUserFromMingler($mixerID);

		$alerts = array();
		if (in_array($_SESSION['site_role'], array('owner', 'admin')) ) {
			$pendingRequests = $this->communities->getCommunitiesByStatus('pending');
			if (!empty($pendingRequests)) {
				$alerts['pendingRequests'] = count($pendingRequests);
			}
		}

		// Unfouned Communities
		/*$unfoundedCommunities = new stdClass();
		$unfoundedCommunities->pending = $this->users->getUsersCreatedCommunitiesByStatus($mixerID, 'pending');
		$unfoundedCommunities->approved = $this->users->getUsersCreatedCommunitiesByStatus($mixerID, 'approved');
		$unfoundedCommunities->rejected= $this->users->getUsersCreatedCommunitiesByStatus($mixerID, 'rejected');

		if (!empty($unfoundedCommunities)) {
			$alerts['pendingCommunities'] = count($unfoundedCommunities->pending);
			$alerts['approvedCommunities'] = count($unfoundedCommunities->approved);
			$alerts['rejectedCommunities'] = count($unfoundedCommunities->rejected);
		}*/


		$communitiesData = new stdClass();
		$communitiesData->core = null;
		$communitiesData->joined = null;
		$communitiesData->followed = null;
		$communitiesData->new = $this->communities->getNewCommunities($user->PreviousLogin);

		if (!empty($user->CoreCommunities)) {
			$communitiesData->core = $this->communities->getCommunitiesFromList($user->CoreCommunities);
		} 
		if (!empty($user->JoinedCommunities)) {
			$communitiesData->joined = $this->communities->getCommunitiesFromList($user->JoinedCommunities);
		} 
		if (!empty($user->FollowedCommunities)) {
			$communitiesData->followed = $this->communities->getCommunitiesFromList($user->FollowedCommunities);
		} 

		$followedTypes = $this->types->getSpecifiedTypesFromMixer($user->FollowedTypes);
		//$followedTypes = array();
		$gameNews = array();
		$typeData = array();
		$slugs = array();

		foreach ($followedTypes as $type) {
			$slugs[$type['id']] = $this->types->createSlug($type['name']);
		}
		
		$viewData->user = $user;
		$viewData->alerts = $alerts;
		$viewData->communitiesData = $communitiesData;
		$viewData->modCommunities = $this->users->getUsersAdminedOrModeratedCommunities($_SESSION['mixer_id']);


		$viewData->pendingCommunities = $this->users->getUsersCreatedCommunitiesByStatus($_SESSION['mixer_id'], 'pending');
		$viewData->approvedCommunities = $this->users->getUsersCreatedCommunitiesByStatus($_SESSION['mixer_id'], 'approved');
		$viewData->rejectedCommunities = $this->users->getUsersCreatedCommunitiesByStatus($_SESSION['mixer_id'], 'rejected');


		$viewData->gameNews = $gameNews;
		$viewData->followedTypes = $followedTypes;
		$viewData->slugs = $slugs;

		$this->load->view('main', $viewData);
	}
}
