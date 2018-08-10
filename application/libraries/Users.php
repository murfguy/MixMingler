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

		$this->db = $this->CI->db;
		$this->types = $this->CI->types;
	}

	public function getUserFromMingler($mixerId) {
		// Let's get streamer data from Mingler based on the ID value we got from Mixer.
		$data = new stdClass();
		//$sql_query = "SELECT * FROM mixer_users WHERE mixer_id=?";
		$sql_query = "SELECT U.*,
			GROUP_CONCAT( CASE WHEN MemberState='admin' THEN UC.CommunityID ELSE NULL END) as AdminCommunities,
			GROUP_CONCAT( CASE WHEN MemberState='moderator' THEN UC.CommunityID ELSE NULL END) as ModCommunities,
			GROUP_CONCAT( CASE WHEN MemberState='core' THEN UC.CommunityID ELSE NULL END) as CoreCommunities,
			GROUP_CONCAT( CASE WHEN MemberState='member' THEN UC.CommunityID ELSE NULL END) as JoinedCommunities,
			GROUP_CONCAT( CASE WHEN MemberState='follower' THEN UC.CommunityID ELSE NULL END) as FollowedCommunities,
			GROUP_CONCAT( CASE WHEN MemberState='pending' THEN UC.CommunityID ELSE NULL END) as PendingCommunities,
			GROUP_CONCAT( CASE WHEN MemberState='banned' THEN UC.CommunityID ELSE NULL END) as BannedCommunities
			FROM `UserCommunities` as UC
			JOIN Users AS U ON U.ID = UC.MixerID
			JOIN Communities AS C ON C.ID = UC.CommunityID
			WHERE  UC.MixerID=?";
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
		$sql_query = "SELECT * FROM Users WHERE Username=?";
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
		/*$follows = $this->getFollowedChannelsFromMixer($userId);

		$followList = "";
		foreach ($follows as $channel) {
			$followList .= $channel['id'].",";
		}

		$followList = rtrim($followList, ",");

		$sql_query = "UPDATE Users SET followedChannels=? WHERE user_id=?";
		$query = $this->CI->db->query($sql_query, array($followList, $userId));*/
	} 

	// Takes Mixer API data and adds the user to the database
	// User CANNOT exist in database. 
	public function addUser($mixerApi_data) {
		if ($mixerApi_data['user']['avatarUrl'] == null) {
			$mixerApi_data['user']['avatarUrl'] = "";
		}

		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "INSERT INTO Users (ID, UserID, Username, AvatarURL, LastSynced, JoinedMixer, isPartner, ViewersTotal, NumFollowers) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$query = $this->CI->db->query($sql_query, array($mixerApi_data['id'], $mixerApi_data['userId'], $mixerApi_data['token'], $mixerApi_data['user']['avatarUrl'], $timestamp, substr($mixerApi_data['createdAt'], 0, 9), $mixerApi_data['partnered'], $mixerApi_data['viewersTotal'], $mixerApi_data['numFollowers']));
	}

	// Takes Mixer API data and adds the user to the database
	// User CANNOT exist in database. 
	public function addNewUser($apiData) {
		if ($apiData['user']['avatarUrl'] == null) {
			$apiData['user']['avatarUrl'] = "";
		}

		$timestamp = date('Y-m-d H:i:s');

		$data = array(
			'ID' => $apiData['id'],
			'UserID' => $apiData['userId'],
			'Username' => $apiData['token'],
			'AvatarURL' => $apiData['user']['avatarUrl'],
			'LastSynced' => $timestamp,
			'JoinedMixer' => substr($apiData['createdAt'], 0, 9),
			'isPartner' => $apiData['partnered'],
			'ViewersTotal' => $apiData['viewersTotal'],
			'NumFollowers' => $apiData['numFollowers']);
		$this->db->insert('Users', $data);

		return ($this->db->insert_id() > 0);
	}

	// Registers User on MixMingler as an official user
	// User MUST exist in database.
	public function registerUser($mixerId) {
		//$sql_query = "UPDATE Users SET isRegistered=1 WHERE ID=?";
		//$query = $this->CI->db->query($sql_query, array($mixerId));
		$data = array(
			'isRegistered' => 1,
			'RegistrationTime' => date('Y-m-d H:i:s'));
		//$this->CI->db->set('isRegistered', 1);
		$this->CI->db->where('ID', $mixerId);
		$this->CI->db->update('Users', $data);

		return ($this->db->affected_rows() > 0);
	}

	// Syncs user's email address to database
	public function syncEmailAddress($emailAddress, $mixerId) {
		/*$sql_query = "UPDATE Users SET Email=? WHERE ID=?";
		$query = $this->CI->db->query($sql_query, array($emailAddress, $mixerId));*/

		$this->db->set('Email', $emailAddress);
		$this->db->where('ID', $mixerId);
		$this->db->update('Users');

		return ($this->db->affected_rows() > 0);

	}

	public function loggedIn($mixerId) {
		$sql_query = "UPDATE Users SET PreviousLogin=Users.LastLogin, LastLogin=NOW()  WHERE ID=?";
		$query = $this->CI->db->query($sql_query, array($mixerId));
	}

	public function syncUser($mixerApi_data) {
		if ($mixerApi_data['user']['avatarUrl'] == null) {
			$mixerApi_data['user']['avatarUrl'] = "";
		}

		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "UPDATE Users SET Username=?, AvatarURL=?, LastSynced=?, isPartner=?, ViewersTotal=?, NumFollowers=?, LastType=?, LastTypeId=? WHERE ID=?";
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
			'ID' => $streamer['id'],
			'Username' => $streamer['token'],
			'AvatarURL' => $streamer['user']['avatarUrl'],
			'isPartner' => $streamer['partnered'],
			'NumFollowers' => $streamer['numFollowers'],
			'ViewersTotal' => $streamer['viewersTotal'],
			'LastType' => $streamer['type']['name'],
			'LastTypeId' => $streamer['type']['id'],
			'LastSynced' => date('Y-m-d H:i:s')
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
			'MixerID' => $streamer['id'],
			'LastStreamStart' => date('Y-m-d H:i:s')
		);

		return $query_data;
	}

	// Marks this a current time in which the streamer was seen online
	// Primarily used for single streamer syncing
	public function setOnlineTime($mixerId) {
		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "UPDATE Users SET LastSeenOnline=?  WHERE ID=?";
		$query = $this->CI->db->query($sql_query, array($timestamp, $mixerId));
	}

	// Marks this a current time in which a new stream was detected as long as stream was started two hours since streamer was last seen online.
	// Primarily used for single streamer syncing
	public function setNewStreamTime($mixerId) {
		$timestamp = date('Y-m-d H:i:s');

		$sql_query = "UPDATE Users SET LastStreamStart=? WHERE ID=? AND LastSeenOnline<DATE_SUB(NOW(), INTERVAL 2 HOUR)";
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
		/*$sql_query = "SELECT *, (SELECT name_token FROM mixer_users WHERE mixer_users.mixer_id= timeline_events.mixer_id) as name_token, 
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
		return  $query->result();*/
	}

	public function getCommunitiesByStatus($mixerID, $status) {
		$query = $this->db->get_where('Communities', array('status' => $status, 'Founder' => $mixerID));
		return  $query->result();
	}

	public function getUsersPendingCommunities($mixer_id) {
		/*$sql_query = "SELECT * FROM Communities WHERE status='pending' AND Founder=? ORDER BY ID DESC";
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();*/
		
	}

	public function getUsersApprovedCommunities($mixer_id) {
		/*$sql_query = "SELECT *, mixer_users.name_token as adminName FROM `communities`
		JOIN mixer_users ON mixer_users.mixer_id = communities.siteAdminApprover WHERE communities.status='approved' AND communities.founder=? ORDER BY communities.id DESC";
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();*/

		$query = $this->db->get_where('Communities', array('status' => 'approved', 'Founder' => $mixer_id));
		return  $query->result();
	}


	public function getUsersRejectedCommunities($mixer_id) {
		/*$sql_query = "SELECT *, mixer_users.name_token as adminName FROM `communities`
		JOIN mixer_users ON mixer_users.mixer_id = communities.siteAdminApprover WHERE communities.status='rejected' AND communities.founder=? ORDER BY communities.id DESC";
		$query = $this->CI->db->query($sql_query, array($mixer_id));
		return  $query->result();*/
	}
	
	public function getUsersAdminedOrModeratedCommunities($mixer_id) {
		/*$sql_query = "SELECT communities.* 
			FROM `UserCommunities` 
			JOIN mixer_users ON mixer_users.mixer_id = UserCommunities.MixerID
			JOIN communities ON communities.id = UserCommunities.CommunityID
			WHERE MixerID = ? AND (MemberState = 'moderator' OR MemberState='admin')";
		$query = $this->CI->db->query($sql_query, array($mixer_id));

		return  $query->result();*/
	}

	public function getSiteAdmins() {
		// return data for all site admins
		$sql_query = "SELECT *  FROM Users WHERE SiteRole in ('owner', 'admin') ORDER BY Username DESC";
		$query = $this->CI->db->query($sql_query);
		return  $query->result();
	}
}
?>