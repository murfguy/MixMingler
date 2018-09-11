<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Team extends CI_Controller {

	public function __construct() {
		parent::__construct();
		//echo 'Hello World!';
		$this->load->database();
		$this->load->library('users');
		$this->load->library('news');
		$this->load->library('version');
		$this->load->library('teams');

		//$onlineData = $this->users->getListOfUsers(100);
		//echo count($onlineData);
		//echo "<hr>";

		//echo json_encode($onlineData);
		//print_r($onlineData);
	}

	public function _remap($method) {
		if ($method != "index") {
			$this->displayTeam($this->teams->getTeam($method)[0]);

		} else {
			$this->displayTeams();
		}
	}

	private function displayTeam($team) {

		$this->teams->syncTeamMembers($team->ID);

		$displayData = new stdClass();
		$displayData->team = $team;
		$displayData->members = $this->teams->getTeamMembers($team->ID);

		$this->load->view('htmlHead', $this->version->getVersion());
		$this->load->view('team', $displayData);

		$this->load->view('htmlFoot', $this->version->getVersion());
	}

	private function displayTeams() {	
		$displayData = null;
		$this->load->view('htmlHead', $this->version->getVersion());
		$this->load->view('teams', $displayData);

		$this->load->view('htmlFoot', $this->version->getVersion());
	}
}
?>