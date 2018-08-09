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

	public function sendMessage($recipients, $messageType, $msgParams, $recipientParams = array()) {
		switch ($recipients) {
			case 'admins':
				$addressees = $this->getSiteAdminEmailAddresses();
				break;

			case 'mods':
				$addressees = null;
				break;

			case 'user':
				$addressees = null;
				break;
		}


	}

	private function getMessage($type, $params) {
		$msgData = new stdClass();
		$msgData->subject = "[MixMingler] ";
		$msgData->message = "Hello {username},\r\r ";

		switch ($type) {
			case "newCommunityRequest":
				$msgData->subject .= " New Community Request";				
				$msgData->message .= "It looks like ".$params['requester']." has placed a request to found a new community called '".$params['communityName']."'. Please log in to MixMingler to approve or deny this request.";
				break;
		}

		$msgData->subject .= " New Community Request";				
		$msgData->message .= "\r\r This is an automated email. Do not respond as no one will answer.";
	}

	public function sendNewCommunityRequestAlert($requester, $communityName) {
		// Collect site admins
		
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

	private function getSiteAdminEmailAddresses() {
		$sql_query = "SELECT name_token, email FROM mixer_users WHERE minglerRole IN ('owner', 'admin') ORDER BY id ASC";
		$query = $this->CI->db->query($sql_query);

		return $query->result();
	}

	private function getCommunityModsEmailAddresses() {

	}
}
?>