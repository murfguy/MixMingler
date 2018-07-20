<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authdata {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		//$this->CI->load->database();
	}

	public function getClientID() {
		return 'd8b9c36eca6617fa3b81cf026fbf479f74c1091139db7fd3';
	}

	public function getClientSecret() {
		return '0bc9da4691a94c9e1e96f5ed9a07ec95fb1465d018d4a45ebfb9aaade059c572';
	}

}?>