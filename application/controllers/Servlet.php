<?php
class Servlet extends CI_Controller {
	var $returnData = null;

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->database();
		$this->load->library('news');
		$this->load->library('users');
		$this->load->library('types');
		$this->load->library('communities');

		$this->returnData = new stdClass();
		$this->returnData->success = false;
	}

	public function index() {

	}

	private function returnData() {
		echo json_encode($this->returnData);
	}

	public function joinCommunity($communityID) {
		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			// Add community ID to end of "joinedCommunities" in mixer_user
			$sql_query = "UPDATE mixer_users SET joinedCommunities = IF(joinedCommunities='', ?, concat(joinedCommunities, ',', ?)) WHERE name_token=?";
			$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_user']));

			// Increment member count by one in communities
			$sql_query = "UPDATE communities SET members = members+1 WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));


			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->communityID = $communityID;
			$this->returnData->success = true;
			$this->returnData->message = "Joined Community";

			$this->news->addNews($_SESSION['mixer_id'], "{username} joined the {commId:$communityID} community.", "community", $communityID);
		}
		$this->returnData();
	}

	public function leaveCommunity($communityID) {
		// Remove community ID from "joinedCommunities" in mixer_user
		// Decrease follow count by one in communities

		if (isset($_SESSION['mixer_user'])) {
			// First, let's check the communities for the logged in user.
			$sql_query = "SELECT joinedCommunities FROM mixer_users WHERE name_token=?";
			$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));
			//Convert communities listto PHP array
			$communities = explode(",", $query->result()[0]->joinedCommunities);
			// Remove the left community from the array
			if (($key = array_search($communityID, $communities)) !== false) { unset($communities[$key]); }
			// Restore to string.
			$communities = implode(',', $communities);
			
			// UPDATE Database with updated list of communities
			$sql_query = "UPDATE mixer_users SET joinedCommunities = ? WHERE name_token=?";
			$query = $this->db->query($sql_query, array($communities, $_SESSION['mixer_user']));

			// Decrease follow count by one in communities
			$sql_query = "UPDATE communities SET members = members-1 WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));


			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->communityID = $communityID;
			$this->returnData->success = true;
			$this->returnData->message = "Left Community";

			//$this->news->addNews($_SESSION['mixer_id'], "{username} left the {commId:$communityID} community.", "mingler");
		}
		$this->returnData();
	}
	public function followCommunity($communityID) {
		// Add community ID to end of "followedCommunities" in mixer_user
		// Increment follow count by one in communities

		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			// Add community ID to end of "joinedCommunities" in mixer_user
			$sql_query = "UPDATE mixer_users SET followedCommunities = IF(followedCommunities='', ?, concat(followedCommunities, ',', ?)) WHERE name_token=?";
			$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_user']));

			// Increment member count by one in communities
			$sql_query = "UPDATE communities SET followers = followers+1 WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->communityID = $communityID;
			$this->returnData->success = true;
			$this->returnData->message = "Followed Community";


			//$this->news->addNews($_SESSION['mixer_id'], "{username} followed the {commId:$communityID} community.", "mingler");
		}
		$this->returnData();
	}

	public function unfollowCommunity($communityID) {
		// Remove community ID from "followedCommunities" in mixer_user
		// Decrease follow count by one in communities
		// Remove community ID from "joinedCommunities" in mixer_user
		// Decrease follow count by one in communities

		if (isset($_SESSION['mixer_user'])) {
			// First, let's check the communities for the logged in user.
			$sql_query = "SELECT followedCommunities FROM mixer_users WHERE name_token=?";
			$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));
			//Convert communities listto PHP array
			$communities = explode(",", $query->result()[0]->followedCommunities);
			// Remove the left community from the array
			if (($key = array_search($communityID, $communities)) !== false) { unset($communities[$key]); }
			// Restore to string.
			$communities = implode(',', $communities);
			
			// UPDATE Database with updated list of communities
			$sql_query = "UPDATE mixer_users SET followedCommunities = ? WHERE name_token=?";
			$query = $this->db->query($sql_query, array($communities, $_SESSION['mixer_user']));

			// Decrease follow count by one in communities
			$sql_query = "UPDATE communities SET followers = followers-1 WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->communityID = $communityID;
			$this->returnData->success = true;
			$this->returnData->message = "Unfollowed Community";


			//$this->news->addNews($_SESSION['mixer_id'], "{username} unfollowed the {commId:$communityID} community.", "mingler");
		}
		$this->returnData();
	}

	public function setCoreCommunity($communityID) {
		// Add community ID to end of "followedCommunities" in mixer_user
		// Increment follow count by one in communities

		// If user is logged in
		if (isset($_SESSION['mixer_id'])) {
			// Get current set of Core communities


			// Add community ID to end of "joinedCommunities" in mixer_user
			$sql_query = "UPDATE mixer_users SET coreCommunities = IF(coreCommunities='', ?, concat(coreCommunities, ',', ?)) WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_id']));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->communityID = $communityID;
			$this->returnData->success = true;
			$this->returnData->message = "Set Core Community";
			//$this->returnData->coreCommunities = $communities;
		}
		$this->returnData();
	}

	public function unsetCoreCommunity($communityID) {
		// Add community ID to end of "followedCommunities" in mixer_user
		// Increment follow count by one in communities

		// If user is logged in
		if (isset($_SESSION['mixer_id'])) {

			// First, let's check the communities for the logged in user.
			$sql_query = "SELECT coreCommunities FROM mixer_users WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($_SESSION['mixer_id']));
			//Convert communities list to PHP array
			$communities = explode(",", $query->result()[0]->coreCommunities);
			// Remove the left community from the array
			if (($key = array_search($communityID, $communities)) !== false) { unset($communities[$key]); }
			$this->returnData->coreCommunities = $communities;
			// Restore to string.
			$communities = implode(',', $communities);

			// UPDATE Database with updated list of communities
			$sql_query = "UPDATE mixer_users SET coreCommunities = ? WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($communities, $_SESSION['mixer_id']));

			// Increment member count by one in communities
			//$sql_query = "UPDATE communities SET followers = followers+1 WHERE id=?";
			//$query = $this->db->query($sql_query, array($communityID));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->communityID = $communityID;
			$this->returnData->success = true;
			$this->returnData->message = "Unset Core Community";
		}
		$this->returnData();
	}

	public function followType($typeID) {
		// Add type ID to end of "followedCommunities" in mixer_user

		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			// Add community ID to end of "joinedCommunities" in mixer_user
			$sql_query = "UPDATE mixer_users SET followedTypes = IF(followedTypes='', ?, concat(followedTypes, ';', ?)) WHERE name_token=?";
			$query = $this->db->query($sql_query, array($typeID, $typeID, $_SESSION['mixer_user']));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->typeID = $typeID;
			$this->returnData->success = true;
			$this->returnData->message = "Followed Type";


			//$this->news->addNews($_SESSION['mixer_id'], "{username} followed the {commId:$communityID} community.", "mingler");
		}
		$this->returnData();
	}

	public function unfollowType($typeID) {
		// Remove community ID from "followedCommunities" in mixer_user
		// Decrease follow count by one in communities
		// Remove community ID from "joinedCommunities" in mixer_user
		// Decrease follow count by one in communities

		if (isset($_SESSION['mixer_user'])) {
			// First, let's check the communities for the logged in user.
			$sql_query = "SELECT followedTypes FROM mixer_users WHERE name_token=?";
			$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));
			//Convert types listto PHP array
			$types = explode(";", $query->result()[0]->followedTypes);
			// Remove the left community from the array
			if (($key = array_search($typeID, $types)) !== false) { unset($types[$key]); }
			// Restore to string.
			$types = implode(';', $types);
			
			// UPDATE Database with updated list of communities
			$sql_query = "UPDATE mixer_users SET followedTypes = ? WHERE name_token=?";
			$query = $this->db->query($sql_query, array($types, $_SESSION['mixer_user']));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->typeID = $typeID;
			$this->returnData->success = true;
			$this->returnData->message = "Unfollowed Type";

		}
		$this->returnData();
	}

	public function ignoreType($typeID) {
		// Add type ID to end of "followedCommunities" in mixer_user

		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			// Add community ID to end of "joinedCommunities" in mixer_user
			$sql_query = "UPDATE mixer_users SET ignoredTypes = IF(ignoredTypes='', ?, concat(ignoredTypes, ',', ?)) WHERE name_token=?";
			$query = $this->db->query($sql_query, array($typeID, $typeID, $_SESSION['mixer_user']));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->typeID = $typeID;
			$this->returnData->success = true;
			$this->returnData->message = "Ignored Type";


			//$this->news->addNews($_SESSION['mixer_id'], "{username} followed the {commId:$communityID} community.", "mingler");
		}
		$this->returnData();
	}

	public function unignoreType($typeID) {
		// Remove community ID from "followedCommunities" in mixer_user
		// Decrease follow count by one in communities
		// Remove community ID from "joinedCommunities" in mixer_user
		// Decrease follow count by one in communities

		if (isset($_SESSION['mixer_user'])) {
			// First, let's check the communities for the logged in user.
			$sql_query = "SELECT ignoredTypes FROM mixer_users WHERE name_token=?";
			$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));
			//Convert types listto PHP array
			$types = explode(",", $query->result()[0]->ignoredTypes);
			// Remove the left community from the array
			if (($key = array_search($typeID, $types)) !== false) { unset($types[$key]); }
			// Restore to string.
			$types = implode(',', $types);
			
			// UPDATE Database with updated list of communities
			$sql_query = "UPDATE mixer_users SET ignoredTypes = ? WHERE name_token=?";
			$query = $this->db->query($sql_query, array($types, $_SESSION['mixer_user']));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->typeID = $typeID;
			$this->returnData->success = true;
			$this->returnData->message = "Unignored Type";

		}
		$this->returnData();
	}

	public function getTopStreamsForType($typeId) {
		$this->returnData->typeID = $typeId;
		$this->returnData->success = true;
		$this->returnData->message = "Got streams from mixer.";
 		$this->returnData->streams = $this->types->getActiveStreamsFromMixerByTypeId($typeId, 6);

		$this->returnData();
	}

	public function applyUserRole() {
		$this->returnData->name_token = $_REQUEST['name_token'];
		$this->returnData->success = false;

		$newRole = $_REQUEST['roles'];
		switch ($newRole) {
			case 'dev':
				$role = "Developer";
				break;
			case 'admin':
				$role = "Admin";
				break;
			default: 
				$role = "User";
				break;
		}

		$streamer = $this->users->getUserFromMinglerByToken($_REQUEST['name_token']);
		if (!empty($streamer)) {
			if ($newRole != $streamer->minglerRole) {
				
				$this->returnData->message = "Applied the $role Role to ".$_REQUEST['name_token'];

				$sql_query = "UPDATE mixer_users SET minglerRole = ? WHERE name_token=?";
				$query = $this->db->query($sql_query, array($newRole, $_REQUEST['name_token']));

				if ($newRole == "admin" || $newRole == "dev") {
					$news_str = $this->news->getEventString('newSiteRole', array($role));
					$this->news->addNews($streamer->mixer_id, $news_str, "mingler");
				}

				$this->returnData->success = true;

			} else {
				$this->returnData->message = $_REQUEST['name_token']." is already assigned as $role.";
			}
		} else {
			$this->returnData->message = $_REQUEST['name_token']." isn't a valid user.";
		}
		
		$this->returnData();
	}

	public function requestCommunity() {
		$this->returnData->success = true;
		$this->returnData->messages = array();

		if ($this->communities->communityNameExists($_REQUEST['long_name'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A community with this name already exists.";
		} else {
			$this->returnData->messages[] = "Community name was succesful.";
		}

		if ($this->communities->communitySlugExists($_REQUEST['slug'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A community with this URL already exists.";
		}else {
			$this->returnData->messages[] = "Community slug was succesful.";
		}

		$this->returnData();
	}



	public function apiTest() {
		$startTime = time();
		$url = "https://mixer.com/api/v1/channels";
		$currentPage = 0;
		$foundAllStreamers = false;
		$allStreamers = array();

		$streamers = array();
		
		$urlParameters = "?limit=100";
		$urlParameters .="&where=numFollowers:gte:25";
		$urlParameters .="&order=numFollowers:ASC";
		//$urlParameters .="&fields=featured,id,userId,token,name,partnered,viewersTotal,viewersCurrent,numFollowers,typeId,user,type";
		$urlParameters .="&fields=token,numFollowers,viewersCurrent";

		while (!$foundAllStreamers) {
			$this->returnData->success = true;
			$content = file_get_contents($url.$urlParameters."&page=".$currentPage);
			$newList = json_decode($content, true);

			$allStreamers = array_merge($allStreamers, $newList);

			$currentPage = $currentPage + 1;

			if ($currentPage % 20 == 0) {
				//sleep(5);
				//$foundAllStreamers = true;
			}

			if (count($newList) < 100) {
				// We've got all followed channels
				$foundAllStreamers = true;
			}
		}

		$this->returnData->elapsedTime = time() - $startTime;

		$this->returnData->mixerData = $allStreamers;

		$this->returnData();
	}

}
?>