<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Online extends CI_Controller {

	public function index()
	{
		//echo 'Hello World!';
		$this->load->database();
		$this->load->library('users');
		$this->load->library('news');

		$onlineData = $this->users->getListOfUsers(100);
		//echo count($onlineData);
		//echo "<hr>";

		echo json_encode($onlineData);
		//print_r($onlineData);
	}



	private function displayOnline($onlineInfo) {
		$this->load->view('htmlHead', $this->version->getVersion());

		$this->load->view('user', $user);

		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}
}
?>