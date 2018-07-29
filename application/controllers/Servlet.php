<?php
class Servlet extends CI_Controller {
	var $returnData = null;

	public function __construct() {
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

	// --------------------------------------------------------------- 
	// --- Join/Follow Community Functions --------------------------- 
	// ---------------------------------------------------------------

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

	// --------------------------------------------------------------- 
	// --- Core Community Functions ---------------------------------- 
	// ---------------------------------------------------------------

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

	// --------------------------------------------------------------- 
	// --- Follow/Ignore Type Functions ------------------------------ 
	// ---------------------------------------------------------------

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

	// --------------------------------------------------------------- 
	// --- Site Admin Functions -------------------------------------- 
	// ---------------------------------------------------------------

	public function applyUserRole() {
		$this->returnData->name_token = $_POST['name_token'];
		$this->returnData->success = false;

		$newRole = $_POST['roles'];
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

		$streamer = $this->users->getUserFromMinglerByToken($_POST['name_token']);
		if (!empty($streamer)) {
			if ($newRole != $streamer->minglerRole) {
				
				$this->returnData->message = "Applied the $role Role to ".$_POST['name_token'];

				$sql_query = "UPDATE mixer_users SET minglerRole = ? WHERE name_token=?";
				$query = $this->db->query($sql_query, array($newRole, $_POST['name_token']));

				if ($newRole == "admin" || $newRole == "dev") {
					$news_str = $this->news->getEventString('newSiteRole', array($role));
					$this->news->addNews($streamer->mixer_id, $news_str, "mingler");
				}

				$this->returnData->success = true;

			} else {
				$this->returnData->message = $_POST['name_token']." is already assigned as $role.";
			}
		} else {
			$this->returnData->message = $_POST['name_token']." isn't a valid user.";
		}
		
		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Community Information Collection Functions ---------------- 
	// ---------------------------------------------------------------

	public function getCommunitiesByStatus () {
		$status = $_POST['status'];
		$this->returnData->success = true;
		$this->returnData->status = $status;
		
		$$this->returnData->communities = $this->communities->getCommunitiesByStatus($status);

		if ($$this->returnData->communities == null) {
			$this->returnData->success = false;
			$this->returnData->message = "No communities found with a status of $status.";
		} else {
			$this->returnData->message = "Found $query->numRows commmunities with a status of $status.";
		}

		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Community Creation Functions ------------------------------ 
	// ---------------------------------------------------------------

	public function requestCommunity() {
		$this->returnData->success = true;
		$this->returnData->messages = array();

		if ($this->communities->communityNameExists($_POST['long_name'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A community with this name already exists.";
		}

		if ($this->communities->communitySlugExists($_POST['slug'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A community with this URL already exists.";
		}

		if ($_POST['slug'] == "create") {
			$this->returnData->success = false;
			$this->returnData->messages[] = "That URL is reserved and cannot be used.";
		}

		if (empty($_POST['description'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "Description wasn't provided.";
		} 

		if (empty($_POST['summary'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "Summary wasn't provided.";
		}
		
		if (empty($_POST['category_id'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A category wasn't selected.";
		}
		

		if ($this->returnData->success) {
			$inputData = array(
				$_POST['long_name'], 
				$_POST['slug'], 
				$_POST['category_id'], 
				strip_tags($_POST['summary']), 
				strip_tags($_POST['description']),
				$_SESSION['mixer_id'],
				$_SESSION['mixer_id'],
				$_SESSION['mixer_id'],
				$_SESSION['mixer_id']
			);			

			$sql_query = "INSERT INTO communities (long_name, slug, category_id, summary, description, founder, admin, members, followers) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$query = $this->db->query($sql_query, $inputData);
		}
		
		// ACTION NEEDED: INSERT INTO mixer_users (foundedCommunites, adminCommunites)

		$this->returnData();
	}

	public function approveCommunity() {
		$status = $_POST['status'];

		$this->returnData->success = false;
		$this->returnData->message = "Improper data received.";

		$this->returnData->status = $status;
		$this->returnData->long_name = $_POST['long_name'];


		switch ($status) {
			// Rejected - Closes out a creation request, but must have a reason.
			case "rejected":
				$sql_query = "UPDATE communities SET long_name=?, slug=NULL, category_id=?, summary=?, description=?,  siteAdminApprover=?, siteAdminNote=?, status='rejected' WHERE id=?";
				$inputData = array(
					$_POST['long_name'], 
					$_POST['category_id'], 
					$_POST['summary'], 
					$_POST['description'],
					$_POST['siteAdmin'],
					$_POST['adminNote'],
					$_POST['commId']
				);
				$query = $this->db->query($sql_query, $inputData);

				$this->returnData->success = true;
				$this->returnData->message = $_POST['long_name']."'s status was changed to $status.";
				break;

			// Approved - A community is recently approved for going live. Allows community admin to make edits, and publish thier community.
			case "approved":
				$sql_query = "UPDATE communities SET long_name=?, slug=?, category_id=?, summary=?, description=?,  siteAdminApprover=?, siteAdminNote=?, status='approved' WHERE id=?";
				$inputData = array(
					$_POST['long_name'], 
					$_POST['slug'], 
					$_POST['category_id'], 
					$_POST['summary'], 
					$_POST['description'],
					$_POST['siteAdmin'],
					$_POST['adminNote'],
					$_POST['commId']
				);	
				$query = $this->db->query($sql_query, $inputData);

				$this->returnData->success = true;
				$this->returnData->message = $_POST['long_name']."'s status was changed to $status.";
				break;
		}
		
		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Community Moderation Functions ---------------------------- 
	// ---------------------------------------------------------------

	// --------------------------------------------------------------- 
	// --- Active Stream Information Functions ----------------------- 
	// ---------------------------------------------------------------

	public function getTopStreamsForType($typeId) {
		sleep(1);
		$this->returnData->typeID = $typeId;
		$this->returnData->success = true;
		$this->returnData->message = "Got streams from mixer.";
 		$this->returnData->streams = $this->types->getActiveStreamsFromMixerByTypeId($typeId, 6);

		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- News Collection Functions --------------------------------- 
	// ---------------------------------------------------------------

	public function getNewsForType($typeId) {
		sleep(1);
		$this->returnData->typeID = $typeId;
		$this->returnData->success = false;
		$this->returnData->message = "Failed to get news.";

		$typeNews = $this->news->getTypeNewsFeed($typeId);

		if (!empty($typeNews)) {
			$gameNewsDisplayItems = array();
			foreach($typeNews as $event) {
				$gameNewsDisplayItems[] = $this->news->getNewsDisplay($event, "", "condensed");
			}
			$this->returnData->success = true;
			$this->returnData->newsFeed = $gameNewsDisplayItems;
			$this->returnData->message = "News was collected!";
		} else {
			$this->returnData->message = "There was no news to collect.";
		}

		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Type Information Collection Functions --------------------- 
	// --------------------------------------------------------------- 

	// --------------------------------------------------------------- 
	// --- User Information Collection Functions ---------------------- 
	// ---------------------------------------------------------------


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