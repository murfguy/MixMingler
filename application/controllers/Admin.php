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
			switch ($user->minglerRole) {
				case "owner":
				case "admin":
				case "dev":	
					$sql_query = "SELECT * FROM mixer_users WHERE lastLogin>'2017-12-21 00:00:00' ORDER BY lastLogin DESC LIMIT 0,10";
					$query = $this->db->query($sql_query);
					$logins = $query->result();

					foreach($logins as $member) {
						$member->loginTime = $this->news->displayPostTime($member->lastLogin);
					}

					$sql_query = "SELECT * FROM mixer_users WHERE registered=1 ORDER BY id DESC LIMIT 0,10";
					$query = $this->db->query($sql_query);
					$registrations = $query->result();

					$viewData = new stdClass();
					$viewData->logins = $logins;
					$viewData->registrations = $registrations;

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