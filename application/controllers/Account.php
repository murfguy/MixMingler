<?php
class Account extends CI_Controller {

	public function index()
	{
		$this->load->library('users');
		$this->load->library('communities');
		$this->load->library('types');
		$this->load->library('version');
		$this->load->library('communications');
		$this->load->database();
		
		if (isset($_SESSION['mixer_id'])) {
			
			$viewData = new stdClass();

			// Look in Mingler DB for this user. This is the default data set.
			$viewData->user = $this->users->getUserFromMingler($_SESSION['mixer_id']);

			if (is_null($viewData->user->Settings_Communications)) {
				$this->users->applyUserSettings('communications', $this->communications->getFreshCommunicationSettings());
				$viewData->user = $this->users->getUserFromMingler($_SESSION['mixer_id']);}	

			// Get Communities
			$viewData->communities = $this->users->getUsersCommunitiesInformation($_SESSION['mixer_id']);

			// Get Followed/Ignored Games
			$viewData->types = $this->users->getUserTypesInformation($_SESSION['mixer_id']);


			$this->load->view('htmlHead', $this->version->getVersion());
			$this->load->view('account', $viewData);
			$this->load->view('htmlFoot', $this->version->getVersion());
		} else {
			header('Location: /');
		}
	}
}
?>