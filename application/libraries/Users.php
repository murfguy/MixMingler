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
		$this->CI->load->library('news');

		$this->db = $this->CI->db;
		$this->types = $this->CI->types;
		$this->news = $this->CI->news;
	}

	public function getUserFromMingler($mixerId) {
		// Let's get streamer data from Mingler based on the ID value we got from Mixer.
		$query = $this->db->select('*')->from('Users')->where('ID', $mixerId)->get();

		if (!empty($query->result())) {
			// User is on Mingler
			return $query->result()[0];
		} else {
			// User is not on Mingler
			return null;
		}
	}	

	public function getUserFromMinglerByUserID($userId) {
		// Let's get streamer data from Mingler based on the ID value we got from Mixer.
		$query = $this->db->select('*')->from('Users')->where('UserID', $userId)->get();

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
		$sql_query = "SELECT * FROM Users WHERE Username=?";
		$query = $this->db->query($sql_query, array($mixerToken));

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

	// Check the Mixer API for this user
	public function getUserFromMixerByUserId($userId) {

		$url = "https://mixer.com/api/v1/users/$userId";
		if (file_exists($url)) {
		    // rest of code here
		    $content = file_get_contents($url);
			$apiData = json_decode($content, true);

			$returnData = $apiData['channel'];
			$returnData['user'] = ['avatarUrl' => $apiData['avatarUrl'] ];

			return $returnData;//json_decode($content, true);
		} else {
			return null;
		}
	}

	public function getUsersFollowedChannels($mixerId) {
		$query = $this->db
			->select('Users.*')
			->from('Users')
			->join('FollowedStreamers', 'FollowedStreamers.StreamerID = Users.ID')
			->where('FollowedStreamers.FollowerID', $mixerId)
			->order_by('LastStreamStart', 'DESC')
			->order_by('Username', 'ASC')
			->get();

		return $query->result();
	}

	public function getFollowedChannelIds($mixerId) {
		$query = $this->db
			->select('*')
			->from('FollowedStreamers')
			->where('FollowerID', $mixerId)
			->get();

		return $query->result();
	}

	// The user ID is different from the "Mixer ID"
	public function getFollowedChannelsFromMixer($userId) {
		$url = "https://mixer.com/api/v1/users/$userId/follows";
		$currentPage = 0;
		$foundAllFollows = false;

		$follows = array();
		
		while (!$foundAllFollows) {
			$urlParameters = "?fields=id,userId,token,online,partnered,suspended,viewersTotal,numFollowers,costreamId,createdAt,user,type&order=token:ASC&page=$currentPage&limit=100";
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

	public function getAllOnlineStreamers() {
		$query = $this->db
			->select('*')
			->from('Users')
			->where(' LastSeenOnline > DATE_SUB(NOW(), INTERVAL 10 MINUTE)')
			->order_by('LastStreamStart', 'DESC')
			->get();

		return $query->result();
	}

	public function getActiveStreamsFromMixer($groupBy = null, $criteria = null, $totalLimit = 0) {
		$url = "https://mixer.com/api/v1/";
		$currentPage = 0;
		$maxPage = 2;
		$foundAllStreams = false;
		$allStreams = array();

		if ($totalLimit > 0) {
			$urlParameters = "?limit=$totalLimit";
			$maxPage = 1;
		} else {
			$urlParameters = "?limit=100";
		}

		switch ($groupBy) {
			case "userID":
				$url .= "channels";
				$batch = "";
				foreach ($criteria as $streamer) {
					if (!empty($batch)) { $batch .= ";"; }
					$batch .= $streamer->ID; }
				$urlParameters .="&where=id:in:$batch,online:eq:true";
				break;

			case "type":
				$url .= "types/".$criteria."/channels";
				break;

			case "default":
				$url .= "channels";
				break;
		}
				$urlParameters .="&fields=id,token,typeId,audience,numFollowers,viewersCurrent";

		$urlParameters .= "&order=viewersCurrent:DESC,numFollowers:DESC,token:ASC";

		//echo $url.$urlParameters;

		while (!$foundAllStreams) {
			$content = file_get_contents($url.$urlParameters."&page=".$currentPage);
			$newList = json_decode($content, true);

			$allStreams = array_merge($allStreams, $newList);

			$currentPage = $currentPage + 1;

			if (count($newList) < 100 || $currentPage >= $maxPage) {
				// We've got all known types
				$foundAllStreams = true;
			}
		}
		return $allStreams;
	}

	private function getUser($userIndentifier) {
		if (ctype_digit($userIndentifier)) {
			// This is a user id input
			return $this->getUserFromMingler($userIndentifier);
		} else {
			// This is the user token
			return $this->getUserFromMinglerByToken($userIndentifier);
		}
	}

	// Check user and add or update in database.
	// Do not use in Scan for now, as that deals with large query batches.
	public function syncUser($mixerUserData, $registerUser = false) {
		$user = $this->getUserFromMingler($mixerUserData['id']);
		
		if (empty($user)) {
			// If they don't exist, add them (addUser will ignore any duplicate streamers)
			$this->addNewUser($mixerUserData); 

			// Note that this person is brand SPANKIN' new, so we note they've been synced for the first time.
			$this->news->addNews($mixerUserData['id'], 'firstSync', "mingler");

		} 

		$this->syncUserData($mixerUserData);
		$user = $this->getUserFromMingler($mixerUserData['id']);		

		if ($registerUser && !$user->isRegistered) {
			$this->registerUser($mixerUserData['id']);
		}

		return $this->getUserFromMingler($mixerUserData['id']);
	}

	public function syncFollows($mixerId, $userId) {

		// Get the updated follows information from mixer
		$mixerFollows = $this->getFollowedChannelsFromMixer($userId);
		$actualFollows = Array();
		foreach ($mixerFollows as $channel) { 
			$this->syncUser($channel); // we sync everyone followed becuase we need all users in the database. plus, why not?
			$actualFollows[] = (int)$channel['id'];
		}

		// Get the current list of follows from mingler
		$minglerFollows = $this->getFollowedChannelIds($mixerId);
		$currentFollows = Array();
		foreach ($minglerFollows as $channel) { $currentFollows[] = (int)$channel->StreamerID; }

		$newFollows = array();
		$removeFollows = array();

		$usersAdded = array();

		// loop through mixer follows.
		foreach ($actualFollows as $channel) {
			// If mingler doesn't know this follow, add to new follows
			if (!in_array($channel, $currentFollows)) { $newFollows[] = ['FollowerID'=>$mixerId, 'StreamerID'=>$channel]; } 
		}

		if (!empty($newFollows)) { $this->db->insert_batch('FollowedStreamers', $newFollows); }

		foreach ($currentFollows as $channel) {
			// If Mixer doesn't know this follow, then remove from mingler follows
			if (!in_array($channel, $actualFollows)) { 
				$this->db->where('FollowerID', $mixerId)->where('StreamerID',$channel)->delete('FollowedStreamers');
				$removeFollows[] = ['FollowerID'=>$mixerId, 'StreamerID'=>$channel]; } 
		}

		return [
			'userId' => $userId,
			'mixerId' => $mixerId,
			'usersAdded' => $usersAdded,
			'removeFollows' =>$removeFollows, 
			'newFollows' =>$newFollows];
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
			'JoinedMixer' => substr($apiData['createdAt'], 0, 10),
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
		
		if ($this->db->affected_rows() > 0) {
			$this->CI->news->addNews($mixerId, 'joinMixMingler', 'mingler'); }

		return ($this->db->affected_rows() > 0);
	}

	// Syncs user's email address to database
	public function syncEmailAddress($emailAddress, $mixerId) {
		$sql_query = "INSERT INTO UserCommunications (MixerID, Email) VALUES(?, ?) ON DUPLICATE KEY UPDATE MixerID=?, Email=?";
		$query = $this->CI->db->query($sql_query, array($mixerId, $emailAddress, $mixerId, $emailAddress));

		return ($this->db->affected_rows() > 0);
	}

	public function loggedIn($mixerId) {
		$sql_query = "UPDATE Users SET PreviousLogin=Users.LastLogin, LastLogin=NOW()  WHERE ID=?";
		$query = $this->CI->db->query($sql_query, array($mixerId));
	}

	public function syncUserData($mixerApi_data) {
		if ($mixerApi_data['user']['avatarUrl'] == null) {
			$mixerApi_data['user']['avatarUrl'] = "";
		}

		$data = [
			'Username' => $mixerApi_data['token'],
			'AvatarURL' => $mixerApi_data['user']['avatarUrl'],
			'LastSynced' => date('Y-m-d H:i:s'),
			'JoinedMixer' => substr($mixerApi_data['createdAt'], 0, 10),
			'isPartner' => $mixerApi_data['partnered'],
			'ViewersTotal' => $mixerApi_data['viewersTotal'],
			'NumFollowers' => $mixerApi_data['numFollowers'],
			'LastType' => $mixerApi_data['type']['name'],
			'LastTypeId' => $mixerApi_data['type']['id']];

		$this->db->where('ID', $mixerApi_data['id'])
			->update('Users', $data);

		//$sql_query = "UPDATE Users SET Username=?, AvatarURL=?, LastSynced=?, isPartner=?, ViewersTotal=?, NumFollowers=?, LastType=?, LastTypeId=? WHERE ID=?";
		//$query = $this->CI->db->query($sql_query, array($mixerApi_data['token'], $mixerApi_data['user']['avatarUrl'], $timestamp, $mixerApi_data['partnered'], $mixerApi_data['viewersTotal'], $mixerApi_data['numFollowers'], $mixerApi_data['type']['name'], $mixerApi_data['type']['id'], $mixerApi_data['id']));
		
		// If current type isn't already stored, let's get it stored.
		$allKnownTypes = $this->types->getAllTypeIdsFromMingler();
		if (!in_array($mixerApi_data['type']['id'], $allKnownTypes )) {
			$this->types->addNewType($mixerApi_data['type']);
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
			$streamer['user']['avatarUrl'] = "";
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
			$query_data['LastSeenOnline'] = date('Y-m-d H:i:s');
		}
		return $query_data;
	}

	// This returns a single set of data for when a new streamer is added.
	// Primarly used in Scan/users in a batch UDPATE
	public function getStartTimeQueryDataArray($streamer) {
		return array(
			'ID' => $streamer['id'],
			'LastStreamStart' => date('Y-m-d H:i:s'));
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
		$query = $this->db
			->select('TimelineEvents.TypeID as ID')
			->select('TimelineEvents.MixerID')
			->select('TimelineEvents.Type')
			->select('StreamTypes.Name AS Name')
			->select('StreamTypes.Slug AS Slug')
			->select('StreamTypes.CoverURL AS CoverURL')
			->select('COUNT(DISTINCT DATE(TimelineEvents.EventTime)) as StreamCount')
			->from('TimelineEvents')
			->join('StreamTypes', 'StreamTypes.ID = TimelineEvents.TypeID')
			->where('TimelineEvents.Type','type')
			->where('TimelineEvents.EventTime > DATE_SUB(NOW(), INTERVAL 30 DAY)')
			->where('TimelineEvents.MixerID', $mixer_id)
			->group_by('TimelineEvents.TypeID')
			->order_by('StreamCount', 'DESC')
			->order_by('TimelineEvents.EventTime', 'DESC')
			->get();

		return $query->result();
	}

	public function getUserTypesInformation($mixerId, $followState = null) {
		if ($followState != null) { $this->db->where('UserTypes.FollowState', $followState); }
		
		$query = $this->db
			->select('*')
			->from('UserTypes')
			->join('StreamTypes', 'StreamTypes.ID=UserTypes.TypeID')
			->where('UserTypes.MixerID', $mixerId)
			->order_by('StreamTypes.Name', 'ASC')
			->get();
		return $query->result();
	}

	public function getUserCoreCommunities($mixerId) {
		$query = $this->db
			->select('*')
			->from('UserCommunities')
			->where('MixerID', $mixerId)
			->where('MemberState', 'core')
			->get();
		return $query->result();
	}

	public function getUsersNewAdminCommunities($mixerId) {
		$query = $this->db
			->select('Communities.*')
			->from('UserCommunities')
			->join('Communities', 'Communities.ID = UserCommunities.CommunityID')
			->where('MixerID', $mixerId)
			->where('MemberState', 'newAdmin')
			->get();
		return $query->result();
	}

	public function getUsersOutgoingAdminCommunities($mixerId) {
		$query = $this->db
			->select('Communities.*')
			->from('UserCommunities')
			->join('Communities', 'Communities.ID = UserCommunities.CommunityID AND Admin='.$mixerId)
			->where('MemberState', 'newAdmin')
			->get();
		return $query->result();
	}

	public function getUsersCommunitiesInformation($mixerId, $communityID = null) {
		if ($communityID != null) { $this->db->where('UserCommunities.CommunityID', $communityID); }

		$query = $this->db
			->select('Communities.*')
			->select('GROUP_CONCAT( UserCommunities.MemberState) as MemberStates')
			->from('UserCommunities')
			->join('Communities', 'Communities.ID = UserCommunities.CommunityID')
			->where('UserCommunities.MixerID', $mixerId)
			->group_by('UserCommunities.CommunityID')
			->order_by('Communities.Name', 'ASC')
			->get();
		return $query->result();

	}

	public function getUsersCreatedCommunitiesByStatus($mixerID, $status) {
		$this->db->select('Communities.*')
			->from('Communities')
			->where('Founder', $mixerID);
		
		switch ($status) {
			case "unfounded":
				// pending, approved or rejected
				//$this->db->where("Founder=$mixerID AND (Status='pending' OR Status='approved' OR Status='rejected')");
				$this->db->group_start()
							->where('Status', 'pending')
							->or_where('Status', 'approved')
							->or_where('Status', 'rejected')
						->group_end();
				break;

			case "processed":
				$this->db->group_start()
							->where('Status', 'approved')
							->or_where('Status', 'rejected')
						->group_end()
						->select('Users.Username as AdminName')
						->join('Users', 'Users.ID = Communities.AdminApprover');
				break;

			case "founded":
				//open or closed
				//$this->db->where("Founder=$mixerID AND (Status='open' OR Status='closed')");
				$this->db->group_start()
							->where('Status', 'open')
							->or_where('Status', 'closed')
						->group_end()
						->select('Users.Username as AdminName')
						->join('Users', 'Users.ID = Communities.AdminApprover');
				break;

			default:
				// get by single type
				$this->db->where('Status', $status);
				break;
		}

		/*if ($status != 'pending' || $status != "unfounded") {
			$this->db->select('Users.Username as AdminName')
			->join('Users', 'Users.ID = Communities.AdminApprover');
		}*/

		$query = $this->db->get();
		return  $query->result();
	}

	public function getSiteAdmins() {
		// return data for all site admins
		$sql_query = "SELECT *  FROM Users WHERE SiteRole in ('owner', 'admin') ORDER BY Username DESC";
		$query = $this->CI->db->query($sql_query);
		return  $query->result();
	}

	public function getUsersByRecentActivityType($activityType, $limit = 20, $registeredOnly = false) {
		if ($registeredOnly) {$this->db->where('isRegistered', 1);}
		$this->db->order_by($activityType, 'DESC');
		$query = $this->db->get('Users', $limit);
		return $query->result();
	}

	public function setUserSiteRole($mixerID, $role) {
		//$sql_query = "UPDATE mixer_users SET minglerRole = ? WHERE name_token=?";
		//$query = $this->db->query($sql_query, array($newRole, $_POST['name_token']));

		$data = array( 'SiteRole' => $role);
		$this->db->where('ID', $mixerID);
		$this->db->update('Users', $data);

	}

	public function getUserSettings($mixerId, $group = "") {
		$query = $this->db
			->select('Settings')
			->from($group)
			->where('MixerID', $mixerId)
			->get();

		$result = $query->result();

		return $result[0]->Settings;
	}

	public function applyUserSettings($group, $settingsData) {
		if (isset($_SESSION['mixer_id'])) {
			$data = ['Settings' => json_encode($settingsData)];
			$this->db->where('MixerID', $_SESSION['mixer_id']);
			$this->db->update($group, $data);
		}
	}

	public function getUserTeamsFromMixer($userId) {
		$url = "https://mixer.com/api/v1/users/".$userId."/teams";
		$content = file_get_contents($url);
		return json_decode($content, true);		
	}

	public function getUserTeams($mixerId) {
		$query = $this->db
			->select('*')
			->from('UserTeams')
			->join('Teams', 'Teams.ID = UserTeams.TeamID')
			->where('UserTeams.MixerID', $mixerId)
			->get();
		return $query->result();
	}

	public function syncUserTeams($mixerData, $minglerData, $mixerId) {
		// loop through both sets teams and see if that team is in the other

		$actualTeamIDs = array();
		$storedTeamIDs = array();

		foreach ($mixerData as $team) {
			$actualTeamIDs[] = $team['id'];

			// Also, insert team into database. Will ignore dupes.
			$data = [
				'ID'=>$team['id'],
				'OwnerID'=>$team['ownerId'],
				'Slug'=>$team['token'],
				'Name'=>$team['name'],
				'LogoURL'=>$team['logoUrl'],
				'backgroundUrl'=>$team['backgroundUrl'],
				'CreationDate'=>substr($team['createdAt'], 0, 9)];

			$insert_query = $this->db->insert_string('Teams', $data);
			$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
			$this->db->query($insert_query);
		}

		foreach ($minglerData as $team) {
			$storedTeamIDs[] = $team->TeamID;
		}

		// If team is not in Mixer, but is in mingler: remove
		$removeTeams = array_diff($storedTeamIDs, $actualTeamIDs);
		foreach ($removeTeams as $team) {
			$this->db
				->where('TeamID', $team)
				->where('MixerID', $mixerId)
				->delete('UserTeams');
		}


		// If team is in Mixer, but not mingler: add team
		$addTeams = array_diff($actualTeamIDs, $storedTeamIDs);
		foreach ($addTeams as $team) {
			$data = ['TeamID'=>$team, 'MixerID'=>$mixerId];
			$this->db->insert('UserTeams', $data);
		}
	}
}
?>