<?php
class Account extends CI_Controller {

	public function index()
	{
		$this->load->library('users');
		$this->load->library('communities');
		$this->load->library('types');
		$this->load->database();
		
		if (isset($_SESSION['mixer_id'])) {
			
			$viewData = new stdClass();

			$this->users->syncFollows($_SESSION['mixer_userId']);

			// Look in Mingler DB for this user. This is the default data set.
			$viewData->user = $this->users->getUserFromMingler($_SESSION['mixer_id']);;

			// Get Communities
			$viewData->communities = $this->users->getUsersCommunitiesInformation($_SESSION['mixer_id']);

			// Get Followed/Ignored Games
			$viewData->types = $this->users->getUserTypesInformation($_SESSION['mixer_id']);


			$this->load->view('htmlHead');
			$this->load->view('account', $viewData);
			$this->load->library('version');
			$this->load->view('htmlFoot', $this->version->getVersion());

			// --------------------------------------------------------------------------------
			// Portion #2: Get Communities Data for the current user
			// --------------------------------------------------------------------------------
			//$communities = $this->users->getUsersCommunitiesInformation($_SESSION['mixer_id']);

			/*$communitiesData = new stdClass();
			$communitiesData->core = null;
			$communitiesData->joined = null;
			$communitiesData->followed = null;
			$communitiesData->core = null;

			$communityList = array();
			$communityData = array();

			$followedList = null;
			if (!empty($minglerData->followedCommunities)) {
				$communitiesData->followed = $this->communities->getCommunitiesFromList($minglerData->followedCommunities);
				$followedList = explode(",", $minglerData->followedCommunities);
				$communityList = array_unique(array_merge($communityList,$followedList), SORT_REGULAR);
			} 

			$joinedList = null;
			if (!empty($minglerData->joinedCommunities)) {
				$communitiesData->joined = $this->communities->getCommunitiesFromList($minglerData->joinedCommunities);
				$joinedList = explode(",", $minglerData->joinedCommunities);
				$communityList = array_unique(array_merge($communityList,$joinedList), SORT_REGULAR);
			} 

			$pendingList = null;
			if (!empty($minglerData->pendingCommunities)) {
				$communitiesData->pending = $this->communities->getCommunitiesFromList($minglerData->pendingCommunities);
				$pendingList = explode(",", $minglerData->pendingCommunities);
				$communityList = array_unique(array_merge($communityList,$pendingList), SORT_REGULAR);
			} 

			$coreList = null;
			if (!empty($minglerData->coreCommunities)) {
				$communitiesData->core = $this->communities->getCommunitiesFromList($minglerData->coreCommunities);
				$coreList = explode(",", $minglerData->coreCommunities);
				$communityList = array_unique(array_merge($communityList,$coreList), SORT_REGULAR);
			}

			$communityData = $this->communities->getCommunitiesFromList(implode(",",$communityList)); 

			$communities = array();

			foreach ($communityData as $community) {
				if (empty($communities[$community->id])) {
					$communities[$community->id] = $community;
					$communities[$community->id]->followed = false;
					$communities[$community->id]->pending = false;
					$communities[$community->id]->joined = false;
					$communities[$community->id]->core = false;

					if (in_array($community->id, explode(",",$minglerData->followedCommunities))) {$communities[$community->id]->followed = true;}
					if (in_array($community->id, explode(",",$minglerData->pendingCommunities))) {$communities[$community->id]->pending = true;}
					if (in_array($community->id, explode(",",$minglerData->joinedCommunities))) {$communities[$community->id]->joined = true;}
					if (in_array($community->id, explode(",",$minglerData->coreCommunities))) {$communities[$community->id]->core = true;}
				}
			}

			
			$viewData->communitiesData = $communitiesData;
			$viewData->communityList = $communityList;*/
	
			
		} else {
			header('Location: /');
		}
	}
}
?>