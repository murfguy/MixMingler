<?php
class Faq extends CI_Controller {

	public function index()
	{
		$this->load->view('htmlHead');
		$this->load->view('faq');
		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
		// MySQL: Get communities list.
		// sort by community size
		
		// show list of communities 
	}

}
?>