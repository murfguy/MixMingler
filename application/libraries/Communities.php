<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communities {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
		$this->CI->load->library('types');
	}

	public function getCommunity($communityID) {
		$sql_query = "SELECT * FROM communities WHERE id=$communityID";
		$query = $this->CI->db->query($sql_query, array($communityID));
		return $query->result()[0];
	}

	public function getCommunityBySlug($communitySlug) {
		$sql_query = "SELECT * FROM communities WHERE slug=?";
		$query = $this->CI->db->query($sql_query, array($communitySlug));
		if (!empty($query->result())) {
			return $query->result()[0];
		}
		return null;
	}

	public function getOnlineMembersFromMixer($members) {
		// Check status of all members from Mixer API
		$url = "https://mixer.com/api/v1/channels?limit=100&fields=id,userId,token,online,viewersTotal,viewersCurrent,numFollowers,type&where=token:in:";
		foreach ($members as $member) { 
			$url .= $member->name_token.";";
		}
		$online_members = json_decode(file_get_contents($url));

		// Sort by current views
		usort($online_members, function ($one, $two) {
			if ($one->viewersCurrent === $two->viewersCurrent) { return 0; }
			return $one->viewersCurrent > $two->viewersCurrent ? -1 : 1;
		});

		return $online_members;
	}

	public function getAllCommunities() {

	}

	public function getCommunitiesFromList($communityList) {
		$sql_query = "SELECT * FROM communities WHERE id IN ($communityList)";
		$query = $this->CI->db->query($sql_query);
		return $query->result();
	}


	public function getNewCommunities($timeStamp) {
		$sql_query = "SELECT * FROM communities WHERE timeAdded > ? OR timeAdded > DATE_SUB(now(), INTERVAL 10 DAY)";
		$query = $this->CI->db->query($sql_query, array($timeStamp));
		return $query->result();
	}

	public function getCommunityMembers($communitySlug) {
		$sql_query = "SELECT *  FROM `mixer_users` WHERE FIND_IN_SET((SELECT id FROM communities WHERE slug=?), `joinedCommunities`) > 0 ORDER BY name_token ASC";
		$query = $this->CI->db->query($sql_query, array($communitySlug));
		return $query->result();
	}

	public function setScanTime($communityID) {
		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "UPDATE communities SET lastScanned=?  WHERE id=?";
		$query = $this->CI->db->query($sql_query, array($timestamp, $communityID));
	}

	public function getCommunityNews($communityId) {
		//get list of member ids
		$sql_query = "SELECT *  FROM `mixer_users` WHERE FIND_IN_SET((SELECT id FROM communities WHERE id=?), `joinedCommunities`) > 0 ORDER BY name_token ASC";
		$members = $this->CI->db->query($sql_query, array($communityId));

		$listOfIds = "";
		$membersCounted = 0;
		foreach ($members as $member) {
			$listOfIds .= $member->mixer_id;
			$membersCounted++;
			if ($membersCounted<=count($members)) {
				$listOfIds .= ",";
			}
		}

		$sql_query = "SELECT * FROM timeline_events WHERE mixer_id IN (listOfIds) ORDER BY id DESC LIMIT 0,10;";
		$query = $this->CI->db->query($sql_query, array($timestamp, $communityID));
		return $query->result();
	}

}?>