<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communications {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
		//$this->load->library('communities');
		//$this->load->library('types');
		$this->CI->load->library('email');

		$config['charset'] = "utf-8";
		$config['mailtype'] = "text";
		$config['newline'] = "\r\n";

		$this->CI->email->initialize($config);
		$this->CI->email->from('alerts@mixmingler.com', 'MixMingler Alerts');
	}

	public function sendNewCommunityRequestAlert($emailData, $messageData) {

		
		$this->CI->email->to('murfguy@gmail.com');

		$this->CI->email->subject('New Community Request`');
		$this->CI->email->message("Greetings! It looks like $requester has placed a request to found a new community called '$communityName'. Please log in to MixMingler to approve or deny this request. This is an automated email. Do not respond.");

		$this->CI->email->send();
	}


	public function sendNewPendingMemberAlert($requester, $communityName) {
		
		$this->CI->email->to('murfguy@gmail.com');

		$this->CI->email->subject("New Pending Member for $communityName");
		$this->CI->email->message("Greetings! It looks like $requester is interested in joining '$communityName'. Please log in to MixMingler to approve or deny this request. This is an automated email. Do not respond.");

		$this->CI->email->send();
	}

	public function sendApprovedCommunityAlert($emailData, $messageData) {

	}
}
?>