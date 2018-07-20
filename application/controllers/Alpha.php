<?php
class Alpha extends CI_Controller {

	public function index()
	{
		$this->load->library('version');

		$versionHistory = $this->version->getVersionHistory();


		$viewData = new stdClass();
		$viewData->versionHistory = $versionHistory;

		$this->load->view('htmlHead');
		
		$this->load->view('alpha', $viewData);
		
		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}
}
?>