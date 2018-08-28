<?php
class Alpha extends CI_Controller {

	public function index()
	{
		$this->load->library('version');

		$versionHistory = $this->version->getVersionHistory();


		$viewData = new stdClass();
		$viewData->versionHistory = $versionHistory;
		$viewData->currentVersion = $this->version->getVersion();

		$this->load->view('htmlHead', $this->version->getVersion());
		
		$this->load->view('alpha', $viewData);
		
		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}
}
?>