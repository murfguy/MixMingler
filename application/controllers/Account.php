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
		} else {
			header('Location: /');
		}
	}
}
?>