<?php
class Admin extends CI_Controller {

	public function index()
	{
		$this->load->library('users');
		$this->load->library('news');
		$this->load->library('communities');
		$this->load->helper('form');
		$this->load->database();
		
		if (isset($_SESSION['mixer_id'])) {
			$user = $this->users->getUserFromMingler($_SESSION['mixer_id']);
			switch ($user->SiteRole) {
				case "owner":
				case "admin":
				case "dev":	
					$viewData = new stdClass();
					$viewData->logins = $this->users->getUsersByRecentActivityType('LastLogin', 20, true); 
					$viewData->registrations = $this->users->getUsersByRecentActivityType('RegistrationTime', 20, true);

					$viewData->pendingCommunities = $this->communities->getCommunitiesByStatus('pending');

					$this->load->view('htmlHead');
					$this->load->view('admin', $viewData);
					$this->load->library('version');
					$this->load->view('htmlFoot', $this->version->getVersion());
					break;

				default:
					header('Location: /');
					break;
			}
		} else {
			header('Location: /');
		}
	}
}
?>