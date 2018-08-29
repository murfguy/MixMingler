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
		//$this->email->reply_to('contact@mixmingler.com', 'MixMingler Contact');
		
		$this->email->set_header('From', 'MixMingler Alerts < alerts@mixmingler.com >');
		$this->email->set_header('Reply-To', 'MixMingler Admin < contact@mixmingler.com >');
	}

	public function sendMessage($recipientGroup, $messageType, $msgParams) {
		switch ($recipientGroup) {
			case 'admins':
				$addressees = $this->getSiteAdminEmailAddresses();
				break;

			case 'mods':
				$addressees = $this->getCommunityModsEmailAddresses($msgParams['communityId']);
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

			$this->email->send(FALSE);
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

			case "newMember":
				$msgData->subject .= $params['requester']." has joined ".$params['communityName'];
				$msgData->message .= "Look out ".$params['communityName']."! It would appear that a wild \"".$params['requester']."\" appeared. It used Join Community! It's super effective!";
				break;

			case "pendingMember":
				$msgData->subject .= $params['communityName']." has a new member request!";
				$msgData->message .= "It would appear that someone named \"".$params['requester']."\" is trying to join ".$params['communityName']."! Since you're either the admin or a moderator of that community, you'll need to log in and approve or deny their membership from the Moderator page.";
				break;

			case 'approvedMembership':
				$msgData->subject .= "You have been approved to join ".$params['communityName'];
				$msgData->message .= "It would seem that your request to join ".$params['communityName']." has been accepted! You are now a full-fledged member of that community. Go and have fun with your new stream crew!";
				break;

			case 'deniedMembership':
				$msgData->subject .= "Your request to join ".$params['communityName']. "has been denied";
				$msgData->message .= "Alas, your request to join ".$params['communityName']." has been denied! We're sorry that the community didn't accept you. BUT HEY! There are still plenty of awesome communities on MixMingler to explore and join. Heck, you could even create your own and reject the snobs who thought you weren't good enough!";
				break;

			case 'newMod':
				$msgData->subject .= $params['requester']." is now a Moderator of ".$params['communityName'];
				$msgData->message .= $params['communityName']."'s Moderation Team is now a bit bigger now that ".$params['requester']." has joined the crew! Good luck out there Mod Squad!";
				break;

			case "removedMod":
				$msgData->subject .= $params['requester']." has been removed as Moderator of ".$params['communityName'];
				$msgData->message .= $params['requester']." has been excised as a member of the moderation team for ".$params['communityName'].". We hope it was mutual, but if you have any issues, please let the Site Admins know.";
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

	private function getCommunityModsEmailAddresses($communityId) {
		$query = $this->db
			->select('Username, Email')
			->from('Users')
			->join('UserCommunities', 'UserCommunities.MixerID=Users.ID')
			->where('UserCommunities.CommunityID', $communityId)
			 	->group_start()
					->where('UserCommunities.MemberState', 'mod')
					->or_where('UserCommunities.MemberState', 'admin')
				->group_end()
			->get();
		return $query->result();
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

	public function getFreshCommunicationSettings() {
		return [
			"requestCommunity" => 1,
			"requestProcessed" => 1,
			"newMemberJoined" => 1,
			"newMemberRequest" => 1,
			"pendingMembershipProcessed" => 1,
			"moderatorStatusChanged" => 1];
	}
}
?>