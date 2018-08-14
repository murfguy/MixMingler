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

		$this->db = $this->CI->db;
		$this->types = $this->CI->types;
	}

	public function getCommunity($communityID) {
		//$sql_query = "SELECT * FROM Communities WHERE ID=$communityID";
		//$query = $this->db->query($sql_query, array($communityID));

		$query = $this->db->get_where('Communities', array('ID' => $communityID));

		if (!empty($query->result())) {
			return $query->result()[0];
		} else {
			return null;
		}
		
	}

	public function getCommunityBySlug($communitySlug) {
		$sql_query = "SELECT * FROM Communities WHERE slug=?";
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
			$url .= $member->Username.";";
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
		$sql_query = "SELECT * FROM Communities WHERE ID IN ($communityList)";
		$query = $this->CI->db->query($sql_query);
		return $query->result();
	}


	public function getNewCommunities($timestamp) {
		$query = $this->db
			->select('*')
			->from('Communities')
			->where('FoundationTime > ', $timestamp)
			->or_where('FoundationTime > DATE_SUB(now(), INTERVAL 14 DAY)')
			->get();

		return $query->result();
	}

	public function getCommunityMembers($communitySlug) {
		$sql_query = "SELECT *  FROM `mixer_users` WHERE FIND_IN_SET((SELECT id FROM Communities WHERE slug=?), `joinedCommunities`) > 0 ORDER BY name_token ASC";
		$query = $this->CI->db->query($sql_query, array($communitySlug));
		return $query->result();
	}

	/*public function getCommunityMembersFromList($list, $sortOn = 'lastSeenOnline', $direction = 'DESC') {
		if (!empty($list)) {
			$sql_query = "SELECT mixer_id, name_token, numFollowers, avatarURL FROM mixer_users WHERE mixer_id IN ? ORDER BY $sortOn $direction";
			//echo "<p>".str_replace('?', $list, $sql_query)."</p>";
			$query = $this->CI->db->query($sql_query, array(explode(',', $list)));
			//echo "<p>$list</p>";
			//print_r($query->result());
			return $query->result();
		} else {
			return null;
		}
	}*/

	// --- Junction Table Refactor for: getCommunityMembersFromList
	public function getCommunityMembersByGroup($communityId, $group, $sortOn = 'Username', $direction = 'ASC') {
		$sql_query = "SELECT Users.* 
			FROM `UserCommunities` 
			JOIN Users ON Users.ID = UserCommunities.MixerID
			JOIN Communities ON Communities.id = UserCommunities.CommunityID
			WHERE CommunityID=? AND MemberState = ? 
			ORDER BY $sortOn $direction";
		$query = $this->CI->db->query($sql_query, array($communityId, $group));
		return $query->result();
	}

	public function getArrayOfMemberIDs($members) {
		$memberIDs = array();
		foreach ($members as $member) {
			$memberIDs[] = $member->ID;
		}
		return $memberIDs;
	}



	public function getCommunityLeads($communityId) {
		$sql_query = "SELECT m.name_token, c.long_name, MixerID, MemberState 
FROM `UserCommunities` 
JOIN mixer_users AS m ON m.mixer_id = UserCommunities.MixerID
JOIN communities AS c ON c.id = UserCommunities.CommunityID
WHERE (MemberState = 'admin' OR MemberState='moderator') AND CommunityID=?";
		$query = $this->CI->db->query($sql_query, array($communityId));
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

	public function createNewCommunity($commData) {
		// Add new community request into database
		$data = array(
			'Name' => $commData['name'],
			'Slug' => $commData['slug'],
			'CategoryID' => $commData['category_id'],
			'Summary' => strip_tags($commData['summary']),
			'Description' => strip_tags($commData['description']),
			'Founder' => $_SESSION['mixer_id'],
			'Admin' => $_SESSION['mixer_id']);
		$this->db->insert('Communities', $data);

		// get new community's id value
		$newCommunityId = $this->db->insert_id();

		// Update requesting user's data to become founder, admin, member and follower of their new community.
		$data = array(
			array('MixerID'=>$_SESSION['mixer_id'], 'CommunityID'=>$newCommunityId, 'MemberState' => 'founder'),
			array('MixerID'=>$_SESSION['mixer_id'], 'CommunityID'=>$newCommunityId, 'MemberState' => 'admin'),
			array('MixerID'=>$_SESSION['mixer_id'], 'CommunityID'=>$newCommunityId, 'MemberState' => 'member'),
			array('MixerID'=>$_SESSION['mixer_id'], 'CommunityID'=>$newCommunityId, 'MemberState' => 'follower')
		);			
		$this->db->insert_batch('UserCommunities', $data);
	}

	public function communityNameExists($commName) {
		$sql_query = "SELECT * FROM Communities WHERE Name=?";
		$query = $this->db->query($sql_query, array($commName));
		if ($query->num_rows() > 0) {
			return true;
		}
		return false;
	}

	public function communitySlugExists($slug) {
		$sql_query = "SELECT * FROM Communities WHERE Slug=?";
		$query = $this->db->query($sql_query, array($slug));
		
		if ($query->num_rows() > 0) {
			return true;
		}
		return false;
	}

	public function getCommunitiesByStatus($status) {
		$this->db->select('Communities.*');
		$this->db->select('Users.Username AS FounderName');
		$this->db->select('CommunityCategories.Name AS CategoryName');
		$this->db->from('Communities');
		$this->db->join('Users', 'Users.ID = Communities.Founder');
		$this->db->join('CommunityCategories', 'Communities.CategoryID = CommunityCategories.ID');
		$this->db->where('Communities.Status',$status);
		$query = $this->db->get();

		return $query->result();
	}

	public function setCommunityStatus($communityId, $status) {
		$data = array( 'Status' => $status);
		$this->db->where('ID', $communityId);
		$this->db->update('Communities', $data);
	}

	public function updateCommunityDetails($communityId, $details) {
		$this->db->where('ID', $communityId);
		$this->db->update('Communities', $details);
	}

}?>