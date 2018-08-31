<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->library('version');
		$this->load->view('htmlHead', $this->version->getVersion());
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

		$viewData->communities = createCommunityObjects($this->users->getUsersCommunitiesInformation($_SESSION['mixer_id']));
		$viewData->newCommunities = $this->communities->getNewCommunities($user->PreviousLogin);

		$alerts = array();
		if (in_array($_SESSION['site_role'], array('owner', 'admin')) ) {
			$pendingRequests = $this->communities->getCommunitiesByStatus('pending');
			if (!empty($pendingRequests)) {
				$alerts['pendingRequests'] = count($pendingRequests);}}

		$unfoundedCommunities = $this->users->getUsersCreatedCommunitiesByStatus($mixerID, 'unfounded');
		if (!empty($unfoundedCommunities)) {
			$alerts['unfoundedCommunities'] = $unfoundedCommunities;}

		if (!empty($viewData->communities->manager)) {
			$pending = $this->communities->getPendingMemberCounts(getIdList($viewData->communities->manager));
			if (!empty($pending)) { $alerts['pendingMembers'] = $pending; }

			$userIsNewAdmin = $this->users->getUsersNewAdminCommunities($mixerID);
			if (!empty($userIsNewAdmin)) { $alerts['userIsNewAdmin'] = $userIsNewAdmin; }

			$userIsOldAdmin = $this->users->getUsersOutgoingAdminCommunities($mixerID);
			if (!empty($userIsOldAdmin)) { $alerts['userIsOldAdmin'] = $userIsOldAdmin; }
		}


		
		// Collect types infromation
		$viewData->followedTypes = $this->users->getUserTypesInformation($_SESSION['mixer_id'], 'followed');
		$viewData->mixerTypeData = $this->types->getSpecifiedTypesFromMixer(getTypeIDList($viewData->followedTypes));
		
		$viewData->user = $user;
		$viewData->alerts = $alerts;


		$this->load->view('main', $viewData);
	}
}
