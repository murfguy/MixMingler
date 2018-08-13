<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communications {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
		$this->db = $this->CI->db;
		$this->CI->load->library('email');
		$this->email = $this->CI->email;


		//$this->load->library('communities');
		//$this->load->library('types');

		$config['charset'] = "utf-8";
		$config['mailtype'] = "text";
		$config['wordwrap'] = TRUE;
		$config['newline'] = "\r\n";
		$config['bcc_batch_mode'] = TRUE;
		$config['bcc_batch_size'] = TRUE;


		$this->email->initialize($config);
		$this->email->from('alerts@mixmingler.com', 'MixMingler Alerts');
	}

	public function sendMessage($recipientGroup, $messageType, $msgParams) {
		switch ($recipientGroup) {
			case 'admins':
				$addressees = $this->getSiteAdminEmailAddresses();
				break;

			case 'mods':
				$addressees = null;
				break;

			case 'user':
				$addressees = $this->getSingleUserEmailAddress($msgParams['singleUserId']);
				break;
		}

		//$recipientAddressList = array();
		foreach ($addressees as $recipient) {
			//$recipientAddressList[] = $recipient->Email;

			$this->email->to($recipient->Email);
			$msg = $this->getMessage($recipient->Username, $messageType, $msgParams);
			$this->email->subject($msg->subject);
			$this->email->message($msg->message);

			$this->CI->email->send(FALSE);
		}

	}

	private function getMessage($recipientName, $type, $params) {
		$msgData = new stdClass();
		$msgData->subject = "[MixMingler Alert] ";

		$msgData->message = "Hello $recipientName,\n\n";
		$signOff = true;

		switch ($type) {
			case "newCommunityRequest":
				$msgData->subject .= "Request for New Community: ".$params['communityName'];				
				$msgData->message .= "It looks like ".$params['requester']." has placed a request to found a new community called '".$params['communityName']."'. Please log in to MixMingler to process this request.";
				break;


			case "communityRequestReceived":
				$msgData->subject .= "Request for ".$params['communityName']." Was Received";
				$msgData->message .= "Thanks for submiting your request to create the ".$params['communityName']." community. One of the Site Admins will take a look and process it soon. Once they've made a decision, you'll get another notice and your next set of instructions. You'll also find alerts and notices pertaining to your request on your MixMingler home page.";
				break;

			case "communityApproved":
				$msgData->subject .= $params['communityName']." Was Approved!";
				$msgData->message .= "GOOD NEWS! It looks like ".$params['communityName']." was approved! This means that your new community is awaiting your final touches for Foundation! When you log in to MixMingler, there will be a notice on your home page leading you to your next steps! Congrats, and have fun!";
				break;

			case "communityDenied":
				$msgData->subject .= $params['communityName']." Was Denied!";
				$msgData->message .= "We regret to inform you that your request for ".$params['communityName']." was denied! It could have been for a variety of reasons. Maybe there's a similar community. Maybe it included something inappropriate. Maybe the winds of fate are not in your favor. Either way, the Admin who processed your request left this note:\n\n".$params['adminNote']."\n\nYou won't be allowed to make a new community until you've deleted your current request. Please log in to MixMingler at your earliest convenience and do so. Once you do, you are free to try again. But please note that repeated efforts to request a community admins have denied can lead to being banned from creating communities.";
				break;

			case "pendingMember":
				$msgData->subject .= $params['communityName']." has a new member request!";
				$msgData->message .= "It would appear that someone named \"".$params['requester']."\" is trying to join ".$params['communityName']."! Since you're either the admin or a moderator of that community, you'll need to log in and approve or deny their membership from the Moderator page.";
				break;
		}
		$msgData->message .= "\n\nHappy Streaming!\n- The MixMingler Team";				
		$msgData->message .= "\n\n-- This is an automated email. Do not respond as no one will answer. --";

		return $msgData;
	}

	private function getSiteAdminEmailAddresses() {
		$sql_query = "SELECT Username, Email FROM Users WHERE SiteRole IN ('owner', 'admin') ORDER BY id ASC";
		$query = $this->db->query($sql_query);
		return $query->result();
	}

	private function getCommunityModsEmailAddresses() {

	}

	private function getSingleUserEmailAddress($mixerID) {
		//$sql_query = "SELECT Username, Email FROM Users WHERE ID IN ('owner', 'admin') ORDER BY id ASC";
		//$query = $this->db->query($sql_query);

		$this->db->select('Username, Email')
					->from('Users')
					->WHERE('ID', $mixerID);
		$query = $this->db->get();
		return $query->result();
	}
}
?>