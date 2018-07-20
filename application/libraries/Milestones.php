<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Milestones {

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();

		// Load Libraries
		$this->CI->load->library('users');
		$this->CI->load->library('news');
	}

	public function getFollowerMilestones() {
		return array(25, 100, 200, 500, 750, 1000, 5000, 10000, 20000, 50000, 100000, 500000, 1000000);
	}

	public function getViewersMilestones() {
		return array(100, 200, 500, 750, 1000, 5000, 10000, 20000, 50000, 100000, 500000, 1000000);
	}

	// Obtain partner milestone
?>