<?php
class Account extends CI_Controller {

	public function index()
	{
		$this->load->library('users');
		$this->load->library('communities');
		$this->load->database();
		
		if (isset($_SESSION['mixer_id'])) {
			
			$viewData = new stdClass();

			$this->users->syncFollows($_SESSION['mixer_userId']);

			// Look in Mingler DB for this user. This is the default data set.
			$minglerData = $this->users->getUserFromMingler($_SESSION['mixer_id']);

			// --------------------------------------------------------------------------------
			// Portion #2: Get Communities Data for the current user
			// --------------------------------------------------------------------------------
			$communitiesData = new stdClass();
			$communitiesData->core = null;
			$communitiesData->joined = null;
			$communitiesData->followed = null;
			$communitiesData->core = null;

			if (!empty($minglerData->coreCommunities)) {
				$communitiesData->core = $this->communities->getCommunitiesFromList($minglerData->coreCommunities);
			} 
			if (!empty($minglerData->joinedCommunities)) {
				$communitiesData->joined = $this->communities->getCommunitiesFromList($minglerData->joinedCommunities);
			} 
			if (!empty($minglerData->followedCommunities)) {
				$communitiesData->followed = $this->communities->getCommunitiesFromList($minglerData->followedCommunities);
			} 

			$viewData->minglerData = $minglerData;
			$viewData->communitiesData = $communitiesData;

			//$viewData->follows = $this->users->getFollowedChannelsFromMixer($_SESSION['mixer_userId']);



			$this->load->view('htmlHead');
			$this->load->view('account', $viewData);
			$this->load->library('version');
			$this->load->view('htmlFoot', $this->version->getVersion());
			echo "Account page coming soon.";
		} else {
			header('Location: /');
		}
	}
}
?>