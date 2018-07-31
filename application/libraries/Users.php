<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
		$this->CI->load->library('types');
	}

	public function getUserFromMingler($mixerId) {
		// Let's get streamer data from Mingler based on the ID value we got from Mixer.
		$data = new stdClass();
		$sql_query = "SELECT * FROM mixer_users WHERE mixer_id=?";
		$query = $this->CI->db->query($sql_query, array($mixerId));

		if (!empty($query->result())) {
			// User is on Mingler
			return $query->result()[0];
		} else {
			// User is not on Mingler
			return null;
		}
	}

	public function getUserFromMinglerByToken($mixerToken) {
		// Let's get streamer data from Mingler based on the ID value we got from Mixer.
		$data = new stdClass();
		$sql_query = "SELECT * FROM mixer_users WHERE name_token=?";
		$query = $this->CI->db->query($sql_query, array($mixerToken));

		if (!empty($query->result())) {
			// User is on Mingler
			return $query->result()[0];
		} else {
			// User is not on Mingler
			return null;
		}
	}

	// Check the Mixer API for this user
	public function getUserFromMixer($mixerToken) {
		// Get Streamer Data from Mixer API
		$url = "https://mixer.com/api/v1/channels/".$mixerToken."?fields=id,userId,token,online,partnered,suspended,viewersTotal,numFollowers,costreamId,createdAt,user,type";
		$content = file_get_contents($url);
		return json_decode($content, true);
	}

	public function getFollowedChannelsFromMixer($userId) {
		$url = "https://mixer.com/api/v1/users/$userId/follows";
		$currentPage = 0;
		$foundAllFollows = false;

		$follows = array();
		
		while (!$foundAllFollows) {
			$urlParameters = "?fields=token,id,userId&order=token:ASC&page=$currentPage&limit=100";
			$content = file_get_contents($url.$urlParameters);
			$newList = json_decode($content, true);

			$follows = array_merge($follows, $newList);

			//$follows = $follows + $newList;
			$currentPage = $currentPage + 1;

			if (count($newList) < 100) {
				// We've got all followed channels
				$foundAllFollows = true;
			}
		}

		return $follows;
	}

	public function syncFollows($userId) {
		$follows = $this->getFollowedChannelsFromMixer($userId);

		$followList = "";
		foreach ($follows as $channel) {
			$followList .= $channel['id'].",";
		}

		$followList = rtrim($followList, ",");

		$sql_query = "UPDATE mixer_users SET followedChannels=? WHERE user_id=?";
		$query = $this->CI->db->query($sql_query, array($followList, $userId));
	} 


	// Takes Mixer API data and adds the user to the database
	// User CANNOT exist in database. 
	public function addUser($mixerApi_data) {
		if ($mixerApi_data['user']['avatarUrl'] == null) {
			$mixerApi_data['user']['avatarUrl'] = "/assets/graphics/blankAvatar.png";
		}

		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "INSERT INTO mixer_users (mixer_id, user_id, name_token, avatarURL, lastSynced, joinedMixer, partner, viewersTotal, numFollowers) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$query = $this->CI->db->query($sql_query, array($mixerApi_data['id'], $mixerApi_data['userId'], $mixerApi_data['token'], $mixerApi_data['user']['avatarUrl'], $timestamp, substr($mixerApi_data['createdAt'], 0, 9), $mixerApi_data['partnered'], $mixerApi_data['viewersTotal'], $mixerApi_data['numFollowers']));
	}

	// Registers User on MixMingler as an official user
	// User MUST exist in database.
	public function registerUser($mixerId) {
		$sql_query = "UPDATE mixer_users SET registered=1 WHERE mixer_id=?";
		$query = $this->CI->db->query($sql_query, array($mixerId));
	}

	public function loggedIn($mixerId) {
		$sql_query = "UPDATE mixer_users SET previousLogin=mixer_users.lastLogin, lastLogin=NOW()  WHERE mixer_id=?";
		$query = $this->CI->db->query($sql_query, array($mixerId));
	}

	public function syncUser($mixerApi_data) {
		if ($mixerApi_data['user']['avatarUrl'] == null) {
			$mixerApi_data['user']['avatarUrl'] = "http://mixmingler.murfguy.com/assets/graphics/blankAvatar.png";
		}

		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "UPDATE mixer_users SET name_token=?, avatarURL=?, lastSynced=?, partner=?, viewersTotal=?, numFollowers=?, lastType=?, lastTypeId=? WHERE mixer_id=?";
		$query = $this->CI->db->query($sql_query, array($mixerApi_data['token'], $mixerApi_data['user']['avatarUrl'], $timestamp, $mixerApi_data['partnered'], $mixerApi_data['viewersTotal'], $mixerApi_data['numFollowers'], $mixerApi_data['type']['name'], $mixerApi_data['type']['id'], $mixerApi_data['id']));
		
		// If current type isn't already stored, let's get it stored.
		$allKnownTypes = $this->CI->types->getAllTypeIdsFromMingler();
		if (!in_array($mixerApi_data['type']['id'], $allKnownTypes )) {
			$this->CI->types->addNewType($mixerApi_data['type']);
		}

		// If streamer is online, we want to mark that
		if ($mixerApi_data['online']) {
			$this->setNewStreamTime($mixerApi_data['id']);
			$this->setOnlineTime($mixerApi_data['id']);
		}
	}

	// This returns a single set of data for when a streamer is synced with current info
	// Primarly used in Scan/users in a batch UDPATE
	public function getSyncQueryDataArray($streamer) {
		if ($streamer['user']['avatarUrl'] == null) {
			$streamer['user']['avatarUrl'] = "/assets/graphics/blankAvatar.png";
		}

		$query_data = array(
			'mixer_id' => $streamer['id'],
			'name_token' => $streamer['token'],
			'avatarURL' => $streamer['user']['avatarUrl'],
			'partner' => $streamer['partnered'],
			'numFollowers' => $streamer['numFollowers'],
			'viewersTotal' => $streamer['viewersTotal'],
			'lastType' => $streamer['type']['name'],
			'lastTypeId' => $streamer['type']['id'],
			'lastSynced' => date('Y-m-d H:i:s')
		);

		if ($streamer['online']) { 
			$query_data['lastSeenOnline'] = date('Y-m-d H:i:s');

			//$this->setNewStreamTime($streamer['id']);
		}
		return $query_data;
	}

	// This returns a single set of data for when a new streamer is added.
	// Primarly used in Scan/users in a batch UDPATE
	public function getStartTimeQueryDataArray($streamer) {
		$query_data = array(
			'mixer_id' => $streamer['id'],
			'lastStreamStart' => date('Y-m-d H:i:s')
		);

		return $query_data;
	}

	// Marks this a current time in which the streamer was seen online
	// Primarily used for single streamer syncing
	public function setOnlineTime($mixerId) {
		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "UPDATE mixer_users SET lastSeenOnline=?  WHERE mixer_id=?";
		$query = $this->CI->db->query($sql_query, array($timestamp, $mixerId));
	}

	// Marks this a current time in which a new stream was detected as long as stream was started two hours since streamer was last seen online.
	// Primarily used for single streamer syncing
	public function setNewStreamTime($mixerId) {
		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "UPDATE mixer_users SET lastStreamStart=? WHERE mixer_id=? AND lastSeenOnline<DATE_SUB(NOW(), INTERVAL 2 HOUR)";
		$query = $this->CI->db->query($sql_query, array($timestamp, $mixerId));
	}

	// Retrieves a list of online streamers from Mixer who meet a minimum follower treshold.
	// Threshold exists as to not get a "too many requests" error from Mixer API
	public function getListOfOnlineUsers($minFollows = 25) {
		$url = "https://mixer.com/api/v1/channels";
		$currentPage = 0;
		$foundAllStreamers = false;
		$allStreamers = array();

		$streamers = array();
		
		$urlParameters = "?limit=100";
		$urlParameters .="&where=numFollowers:gte:".$minFollows;
		$urlParameters .="&order=viewersCurrent:DESC";
		//$urlParameters .="&fields=featured,id,userId,token,name,partnered,viewersTotal,viewersCurrent,numFollowers,typeId,user,type";
		$urlParameters .="&fields=id,userId,token,online,partnered,suspended,viewersTotal,numFollowers,costreamId,createdAt,user,type";

		while (!$foundAllStreamers) {

			$content = file_get_contents($url.$urlParameters."&page=".$currentPage);
			$newList = json_decode($content, true);

			$allStreamers = array_merge($allStreamers, $newList);

			$currentPage = $currentPage + 1;

			if ($currentPage % 20 == 0) {
				sleep(5);
			}

			if (count($newList) < 100) {
				// We've got all followed channels 
				$foundAllStreamers = true;
			}
		}

		return $allStreamers;
	}


	public function getUsersRecentStreamTypes($mixer_id) {
		$sql_query = "SELECT *, (SELECT name_token FROM mixer_users WHERE mixer_users.mixer_id= timeline_events.mixer_id) as name_token, 
(SELECT typeName FROM stream_types WHERE stream_types.typeId= timeline_events.extraVars) as typeName, 
(SELECT typeId FROM stream_types WHERE stream_types.typeId= timeline_events.extraVars) as typeId,
(SELECT slug FROM stream_types WHERE stream_types.typeId= timeline_events.extraVars) as slug,
(SELECT coverUrl FROM stream_types WHERE stream_types.typeId= timeline_events.extraVars) as coverUrl,
COUNT(DISTINCT DATE(eventTime)) as stream_count
FROM timeline_events
WHERE eventType='type' AND eventTime > DATE_SUB(NOW(), INTERVAL 30 DAY) AND mixer_id=?
GROUP BY extraVars
ORDER BY stream_count DESC";
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();
	}

	public function getUsersPendingCommunities($mixer_id) {
		$sql_query = "SELECT * FROM `communities` WHERE status='pending' AND founder=? ORDER BY id DESC";
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();
	}

	public function getUsersApprovedCommunities($mixer_id) {
		$sql_query = "SELECT *, mixer_users.name_token as adminName FROM `communities`
		JOIN mixer_users ON mixer_users.mixer_id = communities.siteAdminApprover WHERE communities.status='approved' AND communities.founder=? ORDER BY communities.id DESC";
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();
	}


	public function getUsersRejectedCommunities($mixer_id) {
		$sql_query = "SELECT *, mixer_users.name_token as adminName FROM `communities`
		JOIN mixer_users ON mixer_users.mixer_id = communities.siteAdminApprover WHERE communities.status='rejected' AND communities.founder=? ORDER BY communities.id DESC";
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();
	}
	
	public function getUsersAdminedOrModeratedCommunities($mixer_id) {
		$sql_query = "SELECT communities.*
FROM communities
JOIN mixer_users ON FIND_IN_SET(communities.id, mixer_users.adminCommunities) OR FIND_IN_SET(communities.id, mixer_users.modCommunities)
WHERE mixer_users.mixer_id = ? AND (communities.status='open' OR communities.status='closed')";	
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();
	}
}
?>