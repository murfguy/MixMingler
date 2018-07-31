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
		$this->load->library('tools');

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

	public function joinCommunity() {
		$communityID = $_POST['communityId'];

		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			$sql_query = "SELECT approveMembers FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));
			$approveMembers = $query->result()[0]->approveMembers;

			$mixer_id = $_SESSION['mixer_id'];
			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->mixer_id = $mixer_id;
			$this->returnData->communityID = $communityID;
		
			// Let's see if the current user follows this community
			$sql_query = "SELECT followedCommunities FROM mixer_users WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($mixer_id));

			$this->returnData->followsCommunity = false;
			if ($this->tools->valueIsInList($communityID, $query->result()[0]->followedCommunities)) {
				$this->returnData->followsCommunity = true;
			}



			// If community requires members to be approved:
			if ($approveMembers) {
				// Add community ID to end of "joinedCommunities" in mixer_user
				$sql_query = "UPDATE mixer_users SET pendingCommunities = IF(pendingCommunities='', ?, concat(pendingCommunities, ',', ?)) WHERE name_token=?";
				$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_user']));

				// Add member into list of joined members
				$sql_query = "UPDATE communities SET pendingMembers = IF(pendingMembers='', ?, concat(pendingMembers, ',', ?)) WHERE id=?";
				$query = $this->db->query($sql_query, array($mixer_id, $mixer_id, $communityID));

				// And now we return all the important data
				$this->returnData->success = true;
				$this->returnData->message = "Added to pending";
				$this->returnData->completedAction = "addedToPending";
			} else {
				// Add community ID to end of "joinedCommunities" in mixer_user
				$sql_query = "UPDATE mixer_users SET joinedCommunities = IF(joinedCommunities='', ?, concat(joinedCommunities, ',', ?)) WHERE name_token=?";
				$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_user']));

				// Add member into list of joined members
				$sql_query = "UPDATE communities SET members = IF(members='', ?, concat(members, ',', ?)) WHERE id=?";
				$query = $this->db->query($sql_query, array($mixer_id, $mixer_id, $communityID));

				// And now we return all the important data
				$this->returnData->success = true;
				$this->returnData->message = "Joined Community";
				$this->returnData->completedAction = "join";

				$this->news->addNews($_SESSION['mixer_id'], "{username} joined the {commId:$communityID} community.", "community", $communityID);
			}


			
		}
		$this->returnData();
	}

	public function unpendCommunity() {
		$communityID = $_POST['communityId'];
		if (isset($_SESSION['mixer_user'])) {
			// Is user the admin?
			$sql_query = "SELECT admin FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));

			$mixer_id = $_SESSION['mixer_id'];
			$this->returnData->communityAdmin = $query->result()[0]->admin;
			$this->returnData->currentUserId = $mixer_id;

			// If user is not admin, they may do this action
			if ($query->result()[0]->admin != $mixer_id) {
				// First, let's check the pending communities for the logged in user.
				$sql_query = "SELECT pendingCommunities FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));

				// Then we remove the left community from list any pertinent communiuty lists.
				$pendingCommunities = $this->tools->removeValueFromList($communityID, $query->result()[0]->pendingCommunities);

				// UPDATE Database with updated list of communities
				$sql_query = "UPDATE mixer_users SET pendingCommunities=? WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($pendingCommunities, $mixer_id));
				
				// Now we need to remove the user from the list of members in the community database
				// So first we get the data from the community
				$sql_query = "SELECT status, pendingMembers FROM communities WHERE id=?";
				$query = $this->db->query($sql_query, array($communityID));
				$status = $query->result()[0]->status;

				// Remove member id from all lists
				$pendingMembers = $this->tools->removeValueFromList($mixer_id, $query->result()[0]->pendingMembers);

				// Update community data to remove member
				$sql_query = "UPDATE communities SET pendingMembers=? WHERE id=?";
				$query = $this->db->query($sql_query, array($pendingMembers, $communityID));

				$this->returnData->username = $_SESSION['mixer_user'];
				$this->returnData->mixer_id = $mixer_id;
				$this->returnData->communityID = $communityID;
				$this->returnData->communityStatus = $status;
				$this->returnData->success = true;
				$this->returnData->message = "User removed themselves from pending list.";
				$this->returnData->completedAction = "removedFromPending";
			} else {
				// User is the commmunity admin, and cannot leave.
				$this->returnData->username = $_SESSION['mixer_user'];
				$this->returnData->mixer_id = $mixer_id;
				$this->returnData->communityID = $communityID;
				$this->returnData->success = false;
				$this->returnData->message = "User is admin and cannot leave community.";
			}
		}
		$this->returnData();
	}

	public function leaveCommunity() {
		$communityID = $_POST['communityId'];
		// Remove community ID from "joinedCommunities" in mixer_user

		if (isset($_SESSION['mixer_user'])) {
			$mixer_id = $_SESSION['mixer_id'];

			// Is user the admin?
			$sql_query = "SELECT admin FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));

			$this->returnData->communityAdmin = $query->result()[0]->admin;
			$this->returnData->currentUserId = $mixer_id;

			// If user is not admin, they may leave the community.
			if ($query->result()[0]->admin != $mixer_id) {
				// First, let's check the communities for the logged in user.
				$sql_query = "SELECT modCommunities, joinedCommunities FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));

				// Then we remove the left community from list any pertinent communiuty lists.
				$modCommunities = $this->tools->removeValueFromList($communityID, $query->result()[0]->modCommunities);
				$joinedCommunities = $this->tools->removeValueFromList($communityID, $query->result()[0]->joinedCommunities);
			
				
				// UPDATE Database with updated list of communities
				$sql_query = "UPDATE mixer_users SET modCommunities=?, joinedCommunities = ? WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($modCommunities, $joinedCommunities, $mixer_id));

				// Now we need to remove the user from the list of members in the community database
				// So first we get the data from the community
				$sql_query = "SELECT status, moderators, members, coreMembers, pendingMembers FROM communities WHERE id=?";
				$query = $this->db->query($sql_query, array($communityID));
				$status = $query->result()[0]->status;

				// Remove member id from all lists
				$moderators = $this->tools->removeValueFromList($mixer_id, $query->result()[0]->moderators);
				$members = $this->tools->removeValueFromList($mixer_id, $query->result()[0]->members);
				$coreMembers = $this->tools->removeValueFromList($mixer_id, $query->result()[0]->coreMembers);
				$pendingMembers = $this->tools->removeValueFromList($mixer_id, $query->result()[0]->pendingMembers);

				// Update community data to remove member
				$sql_query = "UPDATE communities SET moderators=?, members=?, coreMembers=?, pendingMembers=? WHERE id=?";
				$query = $this->db->query($sql_query, array($moderators, $members, $coreMembers, $pendingMembers, $communityID));

				$sql_query = "DELETE FROM timeline_events WHERE mixer_id=? AND eventType='community' AND extraVars=?";
				$query = $this->db->query($sql_query, array($mixer_id, $communityID));


				$this->returnData->username = $_SESSION['mixer_user'];
				$this->returnData->mixer_id = $mixer_id;
				$this->returnData->communityID = $communityID;
				$this->returnData->communityStatus = $status;
				$this->returnData->success = true;
				$this->returnData->message = "Left Community";
				$this->returnData->completedAction = "leave";
			} else {
				// User is the commmunity admin, and cannot leave.
				$this->returnData->username = $_SESSION['mixer_user'];
				$this->returnData->mixer_id = $mixer_id;
				$this->returnData->communityID = $communityID;
				$this->returnData->success = false;
				$this->returnData->message = "User is admin and cannot leave community.";
			}
		}
		$this->returnData();
	}

	public function followCommunity() {
		$communityID = $_POST['communityId'];
		// Add community ID to end of "followedCommunities" in mixer_user
		// Increment follow count by one in communities

		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			// Add community ID to end of "joinedCommunities" in mixer_user
			$sql_query = "UPDATE mixer_users SET followedCommunities = IF(followedCommunities='', ?, concat(followedCommunities, ',', ?)) WHERE name_token=?";
			$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_user']));

			// Add member into list of joined members
			$sql_query = "UPDATE communities SET followers = IF(followers='', ?, concat(followers, ',', ?)) WHERE id=?";
			$query = $this->db->query($sql_query, array($_SESSION['mixer_id'], $_SESSION['mixer_id'], $communityID));

			$this->returnData->username = $_SESSION['mixer_user'];
			$this->returnData->mixer_id = $_SESSION['mixer_id'];
			$this->returnData->communityID = $communityID;
			$this->returnData->success = true;
			$this->returnData->message = "Followed Community";
			$this->returnData->completedAction = "follow";
		}
		$this->returnData();
	}

	public function unfollowCommunity() {
		$communityID = $_POST['communityId'];

		// Remove community ID from "followedCommunities" in mixer_user
		// Decrease follow count by one in communities
		// Remove community ID from "joinedCommunities" in mixer_user
		// Decrease follow count by one in communities

		if (isset($_SESSION['mixer_user'])) {
			$mixer_id = $_SESSION['mixer_id'];

			// Is user the admin?
			$sql_query = "SELECT admin FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));

			$this->returnData->communityAdmin = $query->result()[0]->admin;
			$this->returnData->currentUserId = $mixer_id;

			// If user is not admin, they may leave the community.
			if ($query->result()[0]->admin != $mixer_id) {
				// First, let's check the communities for the logged in user.
				$sql_query = "SELECT followedCommunities FROM mixer_users WHERE name_token=?";
				$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));

				$followedCommunities = $this->tools->removeValueFromList($communityID, $query->result()[0]->followedCommunities);

				// UPDATE Database with updated list of communities
				$sql_query = "UPDATE mixer_users SET followedCommunities = ? WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($followedCommunities, $mixer_id));

				// Now we need to remove the user from the list of members in the community database
				// So first we get the data from the community
				$sql_query = "SELECT followers FROM communities WHERE id=?";
				$query = $this->db->query($sql_query, array($communityID));

				// Remove member id from all lists
				$followers = $this->tools->removeValueFromList($mixer_id, $query->result()[0]->followers);

				// Update community data to remove member
				$sql_query = "UPDATE communities SET followers=? WHERE id=?";
				$query = $this->db->query($sql_query, array($followers, $communityID));

				$this->returnData->username = $_SESSION['mixer_user'];
				$this->returnData->communityID = $communityID;
				$this->returnData->success = true;
				$this->returnData->message = "Unfollowed Community";
				$this->returnData->completedAction = "unfollow";
			} else {
				$this->returnData->username = $_SESSION['mixer_user'];
				$this->returnData->mixer_id = $mixer_id;
				$this->returnData->communityID = $communityID;
				$this->returnData->success = false;
				$this->returnData->message = "User is admin and cannot unfollow community.";
			}


			


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
			// Add new community request into database
			$sql_query = "INSERT INTO communities (long_name, slug, category_id, summary, description, founder, admin, members, followers) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
			
			$query = $this->db->query($sql_query, $inputData);

			// Update requesting user's data to become founder, admin, member and follower of their new community.
			$newCommunityId = $this->db->insert_id();
			$sql_query = "UPDATE mixer_users SET
				foundedCommunities = IF(foundedCommunities='', ?, concat(foundedCommunities, ',', ?)), 
				adminCommunities = IF(adminCommunities='', ?, concat(adminCommunities, ',', ?)),
				joinedCommunities = IF(joinedCommunities='', ?, concat(joinedCommunities, ',', ?)),
				followedCommunities = IF(followedCommunities='', ?, concat(followedCommunities, ',', ?)) 
				WHERE name_token=?";

			$inputData = array(
				$newCommunityId, $newCommunityId,
				$newCommunityId, $newCommunityId,
				$newCommunityId, $newCommunityId,
				$newCommunityId, $newCommunityId,
				$_SESSION['mixer_user']
			);
			$query = $this->db->query($sql_query, $inputData);
		}
		

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

	public function foundCommunity() {
		$this->returnData->success = true;
		$this->returnData->message = "Community has been founded!";

		$this->returnData->status = $_POST['status'];
		$this->returnData->requireApproval = $_POST['requireApproval'];
		$this->returnData->mixer_id = $_POST['mixerUser_id'];
		$this->returnData->community_id = $_POST['commId'];

		// Found community!
		$sql_query = "UPDATE communities SET status=?, timeFounded=NOW(), approveMembers=? WHERE id=?";
		$inputData = array(
			$_POST['status'], 
			$_POST['requireApproval'], 
			$_POST['commId']
		);
		$query = $this->db->query($sql_query, $inputData);

		// UPDATE mixer_users SET last_foundation WHERE mixer_id
		$sql_query = "UPDATE mixer_users SET lastFoundation=NOW() WHERE mixer_id=?";
		$inputData = array($_POST['mixerUser_id']);
		$query = $this->db->query($sql_query, $inputData);

		// Add news item 
		$newsText = $this->news->getEventString("foundedCommunity", array($_POST['commId']));
		$this->news->addNews($_POST['mixerUser_id'], $newsText, "community", $_POST['commId']);

		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Community Moderation Functions ---------------------------- 
	// ---------------------------------------------------------------

	public function editCommunityDetails() {
		// Only for admins
	}

	public function addCommunityModerator() {
		// Only for admins
	}

	public function removeCommunityModerator() {
		// Only for admins
	}

	public function transferCommunityOwnership() {
		// Only for admins
	}

	public function changeMemberStatus() {
		// For Moderators+
	}



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