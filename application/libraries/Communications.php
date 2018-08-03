<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communications {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->load->database();
		//$this->load->library('communities');
		//$this->load->library('types');
		$this->load->library('email');
	}

	public function sendNewCommunityRequestAlert($requester, $communityName) {

		$this->email->from('alerts@mixmingler.com', 'MixMingler Alerts');
		$this->email->to('murfguy@gmail.com');

		$this->email->subject('New Community Request`');
		$this->email->message("Greetings! It looks like $requester has placed a request to found a new community called '$communityName'. Please log in to MixMingler to approve or deny this request. This is an automated email. Do not respond.");

		$this->email->send();
	}


	public function sendNewPendingMemberAlert($requester, $communityName) {
		$this->email->from('alerts@mixmingler.com', 'MixMingler Alerts');
		$this->email->to('murfguy@gmail.com');

		$this->email->subject("New Pending Member for $communityName");
		$this->email->message("Greetings! It looks like $requester is interested in joining '$communityName'. Please log in to MixMingler to approve or deny this request. This is an automated email. Do not respond.");

		$this->email->send();
	}
}
?>