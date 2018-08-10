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
		$this->load->library('communications');

		$this->returnData = new stdClass();
		$this->returnData->success = false;
		$this->returnData->message = "Pinged server, but no action was executed";
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
		$this->returnData->requestedAction = "joinCommunity";

		if (!empty($_POST['userId']) && !empty($_POST['communityId'])) {
			$communityID = $_POST['communityId'];
			$mixer_id = $_POST['userId'];

			$sql_query = "SELECT long_name, approveMembers, status FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));
			$community = null; if (!empty($query->result())) { $community = $query->result()[0]; };

			$sql_query = "SELECT name_token, followedCommunities FROM mixer_users WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($mixer_id));
			$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

			// We had succesful data collection
			if (!empty($community) && !empty($member)) {
				//$mixer_id = $_SESSION['mixer_id'];
				$this->returnData->username = $member->name_token;
				$this->returnData->communityName = $community->long_name;
				$this->returnData->approveMembers = boolval($community->approveMembers);
				$this->returnData->communityStatus = $community->status;

				// Let's see if the current user follows this community
				$this->returnData->followsCommunity = false;
				if ($this->tools->valueIsInList($communityID, $member->followedCommunities)) {
					$this->returnData->followsCommunity = true;
				}

				if ($community->approveMembers) {
					// Add community ID to end of "joinedCommunities" in mixer_user
					$sql_query = "UPDATE mixer_users SET pendingCommunities = IF(pendingCommunities='', ?, concat(pendingCommunities, ',', ?)) WHERE name_token=?";
					$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_user']));

					// Add member into list of joined members
					$sql_query = "UPDATE communities SET pendingMembers = IF(pendingMembers='', ?, concat(pendingMembers, ',', ?)) WHERE id=?";
					$query = $this->db->query($sql_query, array($mixer_id, $mixer_id, $communityID));

					// And now we return all the important data
					$this->returnData->success = true;
					$this->returnData->message = $member->name_token." is now awaiting approval to join the <i>".$community->long_name."</i> community.";
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
					$this->returnData->message = $member->name_token." joined the <i>".$community->long_name."</i> community.";
					$this->returnData->completedAction = "join";

					$this->news->addNews($_SESSION['mixer_id'], "{username} joined the {commId:$communityID} community.", "community", $communityID);
				}

			} else {
				$this->returnData->message = "";
				if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
				if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
			}
		} else {
			$this->returnData->message = "No user or community id was provided.";
		}

		$this->returnData();
	}

	public function unpendCommunity() {
		$this->returnData->requestedAction = "unpendCommunity";

		if (!empty($_POST['userId']) && !empty($_POST['communityId'])) {
			$communityID = $_POST['communityId'];
			$mixer_id = $_POST['userId'];

			// Is user the admin?
			$sql_query = "SELECT long_name, admin, status, pendingMembers  FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));
			$community = null; if (!empty($query->result())) { $community = $query->result()[0]; };

			// First, let's check the pending communities for the logged in user.
			$sql_query = "SELECT name_token, pendingCommunities FROM mixer_users WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($mixer_id));
			$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

			if (!empty($community) && !empty($member)) {
				$this->returnData->username = $member->name_token;
				$this->returnData->mixer_id = $mixer_id;
				$this->returnData->communityID = $communityID;
				$this->returnData->communityStatus = $community->status;

				if ($community->admin != $mixer_id) {
					// Then we remove the left community from list any pertinent communiuty lists.
					$pendingCommunities = $this->tools->removeValueFromList($communityID, $member->pendingCommunities);

					// UPDATE Database with updated list of communities
					$sql_query = "UPDATE mixer_users SET pendingCommunities=? WHERE mixer_id=?";
					$query = $this->db->query($sql_query, array($pendingCommunities, $mixer_id));

					// Remove member id from all lists
					$pendingMembers = $this->tools->removeValueFromList($mixer_id, $community->pendingMembers);

					// Update community data to remove member
					$sql_query = "UPDATE communities SET pendingMembers=? WHERE id=?";
					$query = $this->db->query($sql_query, array($pendingMembers, $communityID));

					
					$this->returnData->success = true;
					$this->returnData->message = $member->name_token." has been removed from the list of pending members for <i>".$community->long_name."</i>.";
					$this->returnData->completedAction = "removedFromPending";

				} else {
					$this->returnData->message = $member->name_token." is the community admin and cannot change their status.";
				}
			} else {
				$this->returnData->message = "";
				if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
				if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
			}
		} else {
			$this->returnData->message = "No user or community id was provided.";
		}
		$this->returnData();
	}

	public function leaveCommunity() {
		$this->returnData->requestedAction = "leaveCommunity";
		if (!empty($_POST['userId']) && !empty($_POST['communityId'])) {
			$communityID = $_POST['communityId'];
			$mixer_id = $_POST['userId'];

			$this->returnData->mixer_id = $mixer_id;
			$this->returnData->communityID = $communityID;
			
			// Let's select the data we need from the community.
			$sql_query = "SELECT long_name, admin, status, moderators, members, coreMembers, pendingMembers FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));
			$community = null; if (!empty($query->result())) { $community = $query->result()[0]; };

			// And now we collect relevant data to our target user.
			$sql_query = "SELECT name_token, modCommunities, joinedCommunities FROM mixer_users WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($mixer_id));
			$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

			// We had succesful data collection
			if (!empty($community) && !empty($member)) {
				$this->returnData->userName = $member->name_token;
				$this->returnData->communityName = $community->long_name;
				$this->returnData->communityAdminId = $community->admin;
				$this->returnData->communityStatus = $community->status;

				// If user is not admin, they may leave the community.
				if ($community->admin != $mixer_id) {				

					// Then we remove the left community from list any pertinent lists.
					$modCommunities = $this->tools->removeValueFromList($communityID, $member->modCommunities);
					$joinedCommunities = $this->tools->removeValueFromList($communityID, $member->joinedCommunities);
				
					// UPDATE User date with updated list of communities
					$sql_query = "UPDATE mixer_users SET modCommunities=?, joinedCommunities = ? WHERE mixer_id=?";
					$query = $this->db->query($sql_query, array($modCommunities, $joinedCommunities, $mixer_id));
					
					// Now we need to remove the user from the list of members in the community database
					// Remove member id from all pertinent lists
					$moderators = $this->tools->removeValueFromList($mixer_id, $community->moderators);
					$members = $this->tools->removeValueFromList($mixer_id, $community->members);
					$coreMembers = $this->tools->removeValueFromList($mixer_id, $community->coreMembers);
					$pendingMembers = $this->tools->removeValueFromList($mixer_id, $community->pendingMembers);

					// Update community data to remove member
					$sql_query = "UPDATE communities SET moderators=?, members=?, coreMembers=?, pendingMembers=? WHERE id=?";
					$query = $this->db->query($sql_query, array($moderators, $members, $coreMembers, $pendingMembers, $communityID));

					// Remove any news related to this community
					$sql_query = "DELETE FROM timeline_events WHERE mixer_id=? AND eventType='community' AND extraVars=?";
					$query = $this->db->query($sql_query, array($mixer_id, $communityID));

					$this->returnData->success = true;
					$this->returnData->message = $member->name_token." has been removed from the <i>".$community->long_name."</i> community.";
					$this->returnData->completedAction = "leave";
				} else {
					// User is the commmunity admin, and cannot leave.
					$this->returnData->message = "User is the community admin and cannot leave.";
				}
			} else {
				$this->returnData->message = "";
				if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
				if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
			}

		} else {
			$this->returnData->message = "No user or community id was provided.";
		}
		$this->returnData();
	}

	public function followCommunity() {
		$this->returnData->requestedAction = "followCommunity";
		if (!empty($_POST['userId']) && !empty($_POST['communityId'])) {
			$communityID = $_POST['communityId'];
			$mixer_id = $_POST['userId'];

			// Let's select the data we need from the community.
			$sql_query = "SELECT long_name FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));
			$community = null; if (!empty($query->result())) { $community = $query->result()[0]; };

			// And now we collect relevant data to our target user.
			$sql_query = "SELECT name_token FROM mixer_users WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($mixer_id));
			$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };
			
			// We had succesful data collection
			if (!empty($community) && !empty($member)) {
				// Add community ID to end of "joinedCommunities" in mixer_user
				$sql_query = "UPDATE mixer_users SET followedCommunities = IF(followedCommunities='', ?, concat(followedCommunities, ',', ?)) WHERE name_token=?";
				$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_user']));

				// Add member into list of joined members
				$sql_query = "UPDATE communities SET followers = IF(followers='', ?, concat(followers, ',', ?)) WHERE id=?";
				$query = $this->db->query($sql_query, array($_SESSION['mixer_id'], $_SESSION['mixer_id'], $communityID));

				$this->returnData->userName = $member->name_token;
				$this->returnData->mixer_id = $mixer_id;
				$this->returnData->communityID = $communityID;
				$this->returnData->communityName = $community->long_name;
				$this->returnData->success = true;
				$this->returnData->message = $member->name_token." followed the <i>".$community->long_name."</i> community";
				$this->returnData->completedAction = "follow";
			} else {
				$this->returnData->message = "";
				if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
				if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
			}
		} else {
			$this->returnData->message = "No user or community id was provided.";
		}

		$this->returnData();
	}

	public function unfollowCommunity() {
		$this->returnData->requestedAction = "unfollowCommunity";
		if (!empty($_POST['userId']) && !empty($_POST['communityId'])) {
			$communityID = $_POST['communityId'];
			$mixer_id = $_POST['userId'];

			$this->returnData->mixer_id = $mixer_id;
			$this->returnData->communityID = $communityID;
			
			// Let's select the data we need from the community.
			$sql_query = "SELECT long_name, admin, followers FROM communities WHERE id=?";
			$query = $this->db->query($sql_query, array($communityID));
			$community = null; if (!empty($query->result())) { $community = $query->result()[0]; };

			// And now we collect relevant data to our target user.
			$sql_query = "SELECT name_token, followedCommunities FROM mixer_users WHERE mixer_id=?";
			$query = $this->db->query($sql_query, array($mixer_id));
			$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

			// We had succesful data collection
			if (!empty($community) && !empty($member)) {
				if ($community->admin != $mixer_id) {
					$this->returnData->userName = $member->name_token;
					$this->returnData->communityName = $community->long_name;

					// Remove community id from member's followed list
					$followedCommunities = $this->tools->removeValueFromList($communityID, $member->followedCommunities);
					// Remove member id from following list
					$followers = $this->tools->removeValueFromList($mixer_id, $community->followers);

					// UPDATE Database with updated list of communities
					$sql_query = "UPDATE mixer_users SET followedCommunities = ? WHERE mixer_id=?";
					$query = $this->db->query($sql_query, array($followedCommunities, $mixer_id));

					// Update community data to remove member
					$sql_query = "UPDATE communities SET followers=? WHERE id=?";
					$query = $this->db->query($sql_query, array($followers, $communityID));


					$this->returnData->success = true;
					$this->returnData->message = $member->name_token." has unfollowed the <i>".$community->long_name."</i> community";
					$this->returnData->completedAction = "unfollow";

				} else {
					$this->returnData->message = $member->name_token = " is the community admin and cannot change their status.";
				}
			} else {
				$this->returnData->message = "";
				if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
				if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
			}
		} else {
			$this->returnData->message = "No user or community id was provided.";
		}
		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Core Community Functions ---------------------------------- 
	// ---------------------------------------------------------------

	public function setAsCore() {
		$this->returnData->requestedAction = "setAsCore";

 		// If user is logged in
		if (isset($_SESSION['mixer_id'])) {
			if (!empty($_POST['userId']) && !empty($_POST['communityId'])) {
				$communityID = $_POST['communityId'];
				$mixer_id = $_POST['userId'];
				$this->returnData->communityID = $communityID;
				$this->returnData->mixer_id = $mixer_id;


				// Let's select the data we need from the community.
				/*$sql_query = "SELECT long_name, admin, coreMembers FROM communities WHERE id=?";
				$query = $this->db->query($sql_query, array($communityID));
				$community = null; if (!empty($query->result())) { $community = $query->result()[0]; };

				// And now we collect relevant data to our target user.
				$sql_query = "SELECT name_token, coreCommunities FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));
				$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };*/


				$sql_query = "SELECT C.admin, C.long_name, M.name_token, M.email, GROUP_CONCAT(UC.MemberState) as states
					FROM `UserCommunities` as UC
					JOIN mixer_users AS M ON M.mixer_id = UC.MixerID
					JOIN communities AS C ON C.id = UC.CommunityID
					WHERE CommunityID=? AND MixerID=?";
				//$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

				$member = $this->users->getUserFromMingler($mixer_id);

				if (!empty($member)) {
					$this->returnData->userName = $member->name_token;
					$this->returnData->communityName = $community->long_name;

					if (count(explode(",", $member->coreCommunities)) >= 4) {
						$this->returnData->message = "You are already at the maximum number of core communities. You cannot mark more until you unmark another.";
					} else {
						// Add community ID to end of "joinedCommunities" in mixer_user
						$sql_query = "UPDATE mixer_users SET coreCommunities = IF(coreCommunities='', ?, concat(coreCommunities, ',', ?)) WHERE mixer_id=?";
						$query = $this->db->query($sql_query, array($communityID, $communityID, $_SESSION['mixer_id']));

						// Add member into list of joined members
						$sql_query = "UPDATE communities SET coreMembers = IF(coreMembers='', ?, concat(coreMembers, ',', ?)) WHERE id=?";
						$query = $this->db->query($sql_query, array($mixer_id, $mixer_id, $communityID));

						$this->returnData->success = true;
						$this->returnData->message = $community->long_name." is now one of your Core Communities.";
						$this->returnData->completedAction = "setAsCore";
					}
				} else {
					$this->returnData->message = "";
					if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
					if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
				}
			}  else {
				$this->returnData->message = "No user or community id was provided.";
			}
		} else {
			$this->returnData->message = "User must be logged in.";
		}
		$this->returnData();
	}

	public function removeAsCore() {
		// Add community ID to end of "followedCommunities" in mixer_user
		// Increment follow count by one in communities

		// If user is logged in
		if (isset($_SESSION['mixer_id'])) {
			if (!empty($_POST['userId']) && !empty($_POST['communityId'])) {
				$communityID = $_POST['communityId'];
				$mixer_id = $_POST['userId'];
				$this->returnData->communityID = $communityID;
				$this->returnData->mixer_id = $mixer_id;
				// Let's select the data we need from the community.
				$sql_query = "SELECT long_name, admin, coreMembers FROM communities WHERE id=?";
				$query = $this->db->query($sql_query, array($communityID));
				$community = null; if (!empty($query->result())) { $community = $query->result()[0]; };

				// And now we collect relevant data to our target user.
				$sql_query = "SELECT name_token, coreCommunities FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));
				$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

				if (!empty($community) && !empty($member)) {
					$this->returnData->userName = $member->name_token;
					$this->returnData->communityName = $community->long_name;

					// Remove community id from member's followed list
					$coreCommunities = $this->tools->removeValueFromList($communityID, $member->coreCommunities);
					// Remove member id from following list
					$coreMembers = $this->tools->removeValueFromList($mixer_id, $community->coreMembers);

					// UPDATE Database with updated list of communities
					$sql_query = "UPDATE mixer_users SET coreCommunities = ? WHERE mixer_id=?";
					$query = $this->db->query($sql_query, array($coreCommunities, $mixer_id));

					// Update community data to remove member
					$sql_query = "UPDATE communities SET coreMembers=? WHERE id=?";
					$query = $this->db->query($sql_query, array($coreMembers, $communityID));


					$this->returnData->success = true;
					$this->returnData->message = "<i>".$community->long_name."</i> is no longer one of your core communities.";
					$this->returnData->completedAction = "removeAsCore";					
				} else {
					$this->returnData->message = "";
					if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
					if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
				}
			} else {
				$this->returnData->message = "No user or community id was provided.";
			}
		} else {
			$this->returnData->message = "User must be logged in.";
		}
		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Follow/Ignore Type Functions ------------------------------ 
	// ---------------------------------------------------------------

	public function followType() {
		$this->returnData->requestedAction = "followType";
		
		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			if (!empty($_POST['userId']) && !empty($_POST['typeId'])) {
				$typeId = $_POST['typeId'];
				$mixer_id = $_POST['userId'];
				$this->returnData->typeId = $typeId;
				$this->returnData->mixer_id = $mixer_id;

				// Let's select the data we need from the community.
				$sql_query = "SELECT typeName FROM stream_types WHERE typeId=?";
				$query = $this->db->query($sql_query, array($typeId));
				$type = null; if (!empty($query->result())) { $type = $query->result()[0]; };

				// And now we collect relevant data to our target user.
				$sql_query = "SELECT name_token FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));
				$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

				if (!empty($type) && !empty($member)) {
					$this->returnData->userName = $member->name_token;
					$this->returnData->typeName = $type->typeName;

					// Add community ID to end of "joinedCommunities" in mixer_user
					$sql_query = "UPDATE mixer_users SET followedTypes = IF(followedTypes='', ?, concat(followedTypes, ';', ?)) WHERE name_token=?";
					$query = $this->db->query($sql_query, array($typeId, $typeId, $_SESSION['mixer_user']));

					$this->returnData->success = true;
					$this->returnData->message = "You are now following ".$type->typeName." streams.";
					$this->returnData->completedAction = "followType";

				} else {
					$this->returnData->message = "";
					if (empty($type)) { $this->returnData->message .= "The stream type you were trying to access was not found in the database. "; }
					if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
				}
			} else {
				$this->returnData->message = "No user or stream type id was provided.";
			}
		} else {
			$this->returnData->message = "You are not logged in.";
		}
		$this->returnData();
	}

	public function unfollowType() {
		$this->returnData->requestedAction = "unfollowType";
		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			if (!empty($_POST['userId']) && !empty($_POST['typeId'])) {
				$typeId = $_POST['typeId'];
				$mixer_id = $_POST['userId'];
				$this->returnData->typeId = $typeId;
				$this->returnData->mixer_id = $mixer_id;

				// Let's select the data we need from the community.
				$sql_query = "SELECT typeName FROM stream_types WHERE typeId=?";
				$query = $this->db->query($sql_query, array($typeId));
				$type = null; if (!empty($query->result())) { $type = $query->result()[0]; };

				// And now we collect relevant data to our target user.
				$sql_query = "SELECT name_token, followedTypes FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));
				$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

				if (!empty($type) && !empty($member)) {
					$this->returnData->userName = $member->name_token;
					$this->returnData->typeName = $type->typeName;

					$followedTypes = $this->tools->removeValueFromList($typeId, $member->followedTypes);
					$followedTypes = str_replace(",", ";", $followedTypes);

					// UPDATE User date with updated list of communities
					$sql_query = "UPDATE mixer_users SET followedTypes=? WHERE mixer_id=?";
					$query = $this->db->query($sql_query, array($followedTypes, $mixer_id));

					$this->returnData->success = true;
					$this->returnData->message = "You are no longer following ".$type->typeName." streams.";
					$this->returnData->completedAction = "unfollowType";

				} else {
					$this->returnData->message = "";
					if (empty($type)) { $this->returnData->message .= "The stream type you were trying to access was not found in the database. "; }
					if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
				}
			} else {
				$this->returnData->message = "No user or stream type id was provided.";
			}
		} else {
			$this->returnData->message = "You are not logged in.";
		}
		$this->returnData();
	}

	public function ignoreType() {
		$this->returnData->requestedAction = "ignoreType";
		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			if (!empty($_POST['userId']) && !empty($_POST['typeId'])) {
				$typeId = $_POST['typeId'];
				$mixer_id = $_POST['userId'];
				$this->returnData->typeId = $typeId;
				$this->returnData->mixer_id = $mixer_id;

				// Let's select the data we need from the community.
				$sql_query = "SELECT typeName FROM stream_types WHERE typeId=?";
				$query = $this->db->query($sql_query, array($typeId));
				$type = null; if (!empty($query->result())) { $type = $query->result()[0]; };

				// And now we collect relevant data to our target user.
				$sql_query = "SELECT name_token FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));
				$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

				if (!empty($type) && !empty($member)) {
					$this->returnData->userName = $member->name_token;
					$this->returnData->typeName = $type->typeName;

					// Add community ID to end of "joinedCommunities" in mixer_user
					$sql_query = "UPDATE mixer_users SET ignoredTypes = IF(ignoredTypes='', ?, concat(ignoredTypes, ',', ?)) WHERE name_token=?";
					$query = $this->db->query($sql_query, array($typeId, $typeId, $_SESSION['mixer_user']));

					$this->returnData->success = true;
					$this->returnData->message = "You are now ignoring ".$type->typeName." streams.";
					$this->returnData->completedAction = "ignoreType";

				} else {
					$this->returnData->message = "";
					if (empty($type)) { $this->returnData->message .= "The stream type you were trying to access was not found in the database. "; }
					if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
				}
			} else {
				$this->returnData->message = "No user or stream type id was provided.";
			}
		} else {
			$this->returnData->message = "You are not logged in.";
		}
		$this->returnData();
	}

	public function unignoreType() {		
		$this->returnData->requestedAction = "unignoreType";
		// If user is logged in
		if (isset($_SESSION['mixer_user'])) {
			if (!empty($_POST['userId']) && !empty($_POST['typeId'])) {
				$typeId = $_POST['typeId'];
				$mixer_id = $_POST['userId'];
				$this->returnData->typeId = $typeId;
				$this->returnData->mixer_id = $mixer_id;

				// Let's select the data we need from the community.
				$sql_query = "SELECT typeName FROM stream_types WHERE typeId=?";
				$query = $this->db->query($sql_query, array($typeId));
				$type = null; if (!empty($query->result())) { $type = $query->result()[0]; };

				// And now we collect relevant data to our target user.
				$sql_query = "SELECT name_token, ignoredTypes FROM mixer_users WHERE mixer_id=?";
				$query = $this->db->query($sql_query, array($mixer_id));
				$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

				if (!empty($type) && !empty($member)) {
					$this->returnData->userName = $member->name_token;
					$this->returnData->typeName = $type->typeName;

					$ignoredTypes = $this->tools->removeValueFromList($typeId, $member->ignoredTypes);

					// UPDATE User date with updated list of communities
					$sql_query = "UPDATE mixer_users SET ignoredTypes=? WHERE mixer_id=?";
					$query = $this->db->query($sql_query, array($ignoredTypes, $mixer_id));

					$this->returnData->success = true;
					$this->returnData->message = "You are no longer ignoring ".$type->typeName." streams.";
					$this->returnData->completedAction = "unignoreType";
				} else {
					$this->returnData->message = "";
					if (empty($type)) { $this->returnData->message .= "The stream type you were trying to access was not found in the database. "; }
					if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
				}
			} else {
				$this->returnData->message = "No user or stream type id was provided.";
			}
		} else {
			$this->returnData->message = "You are not logged in.";
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
		// Check for any errors
		
		// Does that name exist? 
		if ($this->communities->communityNameExists($_POST['long_name'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A community with this name already exists.";
		}

		// Does that slug exist?
		if ($this->communities->communitySlugExists($_POST['slug'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A community with this URL already exists.";
		}

		// Did they try and use 'create' as a slug?
		if ($_POST['slug'] == "create") {
			$this->returnData->success = false;
			$this->returnData->messages[] = "That URL is reserved and cannot be used.";
		}

		// Was there a description?
		if (empty($_POST['description'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "Description wasn't provided.";
		} 

		// Was there a summary?
		if (empty($_POST['summary'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "Summary wasn't provided.";
		}
		
		// Did they pick a category?
		if (empty($_POST['category_id'])) {
			$this->returnData->success = false;
			$this->returnData->messages[] = "A category wasn't selected.";
		}
		

		// If all criteria passed:
		if ($this->returnData->success) {
			// Add new community request into database
			$sql_query = "INSERT INTO communities (long_name, slug, category_id, summary, description, founder, admin) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$inputData = array(
				$_POST['long_name'], 
				$_POST['slug'], 
				$_POST['category_id'], 
				strip_tags($_POST['summary']), 
				strip_tags($_POST['description']),
				$_SESSION['mixer_id'],
				$_SESSION['mixer_id']
			);			
			$query = $this->db->query($sql_query, $inputData);

			// get new community's id value
			$newCommunityId = $this->db->insert_id();

			// Update requesting user's data to become founder, admin, member and follower of their new community.
			$inputData = array(
				$_SESSION['mixer_id'], $newCommunityId,
				$_SESSION['mixer_id'], $newCommunityId,
				$_SESSION['mixer_id'], $newCommunityId,
				$_SESSION['mixer_id'], $newCommunityId,
			);
			$sql_query = "INSERT INTO UserCommunities (MixerID, CommunityID, MemberState) VALUES (?, ?, 'founder'), (?, ?, 'admin'), (?, ?, 'member'), (?, ?, 'follower')";
			$query = $this->db->query($sql_query, $inputData);

			// Send an email alert to site admins about new community request
			$this->communications->sendNewCommunityRequestAlert($_SESSION['mixer_user'], $_POST['long_name']);
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

		$requireApproval = 0;
		if ($_POST['requireApproval'] == "yes") {
			$requireApproval = 1;
		}

		$this->returnData->status = $_POST['status'];
		$this->returnData->requireApproval = $requireApproval;
		$this->returnData->mixer_id = $_POST['mixerUser_id'];
		$this->returnData->community_id = $_POST['commId'];

		// Found community!
		$sql_query = "UPDATE communities SET status=?, timeFounded=NOW(), approveMembers=? WHERE id=?";
		$inputData = array(
			$_POST['status'], 
			$requireApproval, 
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
		// Is someone logged in?
		if (isset($_SESSION['mixer_user'])) {

			// Did we get usuable data with which to check/update database?
			if (!empty($_POST['communityId']) || !empty($_POST['userId']) || !empty($_POST['status'])) {

				$currentUserId = $_SESSION['mixer_id'];
				$memberId = (int)$_POST['userId'];
				$communityID = $_POST['communityId'];
				$memberStatus = $_POST['status'];

				$this->returnData->currentUserId = $currentUserId;
				$this->returnData->memberId = $memberId;
				$this->returnData->memberStatus = $memberStatus;
				// Now we collect relevant data to our target user.
				$sql_query = "SELECT 
						C.admin,
						C.long_name,
						M.name_token,
						M.email, 
						GROUP_CONCAT(UC.MemberState) as states
					FROM `UserCommunities` as UC
					JOIN mixer_users AS M ON M.mixer_id = UC.MixerID
					JOIN communities AS C ON C.id = UC.CommunityID
					WHERE CommunityID=? AND MixerID=?";
				$query = $this->db->query($sql_query, array($communityID, $memberId));
				$member = null; if (!empty($query->result())) { $member = $query->result()[0]; };

				//$member = $this->users->getUserFromMingler($memberId);
				$community = $this->communities->getCommunity($communityID);

				// Did we collect good data?
				if (!empty($community) && !empty($member)) {
					$this->returnData->memberName = $member->name_token;
					// Member's states related to this community
					$states = explode(",", $member->states);
					$this->returnData->memberStates = $states;

					$isMod = in_array('moderator', $states);
					$isAdmin = ($member->admin == $currentUserId);

					// Is the current user allowed to do this?
					if ($isMod || $isAdmin) {

						// ----------------------------------------------------------------------------------------
						// -- Update database based on requested action
						// ----------------------------------------------------------------------------------------

						// At this point, we only catching pre-existing exceptions. As such, all attempts are successful.
						$this->returnData->success = true;
						$this->returnData->completedAction = $memberStatus;

						$isOwner = in_array('admin', $states);
						$isModerator = in_array('moderator', $states);
						$isMember = in_array('member', $states);
						$isPending = in_array('pending', $states);
						$isBanned = in_array('banned', $states);

						switch ($memberStatus) {
							case "approveMember":
								if (!$isPending) {
									$this->returnData->message = $member->name_token." is not pending.";
								} else {
									$sql_query = "INSERT INTO UserCommunities (MixerID, CommunityID, MemberState) VALUES (?, ?, 'member')";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$sql_query = "DELETE FROM UserCommunities WHERE MixerID=? AND CommunityID=? AND MemberState='pending'";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->returnData->message = $member->name_token." was added as a new member to ".$community->long_name;

									// Add news item 
									$newsText = $this->news->getEventString("joinCommunity", array($communityID));
									$this->news->addNews($memberId, $newsText, "community", $communityID);

									// notify: new member, community admin/mods
								}

								break;

							case "denyMember":
								if (!$isPending) {
									$this->returnData->message = $member->name_token." is not pending.";
								} else {
									$sql_query = "DELETE FROM UserCommunities WHERE MixerID=? AND CommunityID=? AND MemberState='pending'";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->returnData->message = $member->name_token." was denied membership to ".$community->long_name;

									// notify: new member, community admin/mods
								}

								break;

							case "promoteMember":
								// fail condition: isModerator OR not isMember
								if ($isOwner || $isModerator || !$isMember) {
									$msg = $member->name_token." is ";
									if ($isOwner) { $msg .= "the Community Admin and cannot be promoted."; }
									if ($isModerator) { $msg .= $member->name_token."already a Moderator."; }
									if (!$isMember) { $msg .= $member->name_token."no longer a member of ".$community->long_name."."; }
									$this->returnData->message = $msg;
								} else {
									$sql_query = "INSERT INTO UserCommunities (MixerID, CommunityID, MemberState) VALUES (?, ?, 'moderator')";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->returnData->message = $member->name_token." was changed to a 'moderator' in ".$community->long_name;

									// Add news item 
									$newsText = $this->news->getEventString("newCommRole", array("moderator", $communityID));
									$this->news->addNews($memberId, $newsText, "community", $communityID);
								}

								// notify user about promotion
								break;

							case "demoteMember":
								// fail condition: not isModerator OR not isMember
								if ($isOwner || !$isModerator || !$isMember) {
									$msg = $member->name_token." is ";
									if ($isOwner) { $msg .= "the Community Admin and cannot be demoted."; }
									if (!$isModerator) { $msg .= "not a Moderator."; }
									if (!$isMember) { $msg .= " no longer a member of ".$community->long_name."."; }
									$this->returnData->message = $msg;
								} else {
									$sql_query = "DELETE FROM UserCommunities WHERE MixerID=? AND CommunityID=? AND MemberState='moderator'";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->returnData->message = $member->name_token."'s status as a moderator in ".$community->long_name." was removed.";
								}

								// notify user about demotion
								break;

							case "kickMember":
								// fail condition: isModerator OR not isMember
								if ($isOwner || $isModerator || !$isMember) {
									$msg = $member->name_token." is ";

									if ($isModerator) { 
										$msg .= "a Moderator and cannot be kicked."; 
										$this->returnData->completedAction = "promoteMember"; 
									}
									if ($isOwner) { 
										$msg .= "the Community Admin and cannot be  kicked."; 
									}

									if (!$isMember) { $msg .= " not a member of ".$community->long_name."."; }
									$this->returnData->message = $msg;
								} else {
									$sql_query = "DELETE FROM UserCommunities WHERE (MixerID=? AND CommunityID=?) AND MemberState != 'founder'";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->returnData->message = $member->name_token." was kicked from ".$community->long_name;
								}

								// notify user about removal
								break;

							case "banMember":
								// fail condition: isModerator OR not isMember
								if ($isOwner || $isModerator) {
									$msg = $member->name_token." is ";

									if ($isModerator) { 
										$msg .= "a Moderator and cannot be banned."; 
										$this->returnData->completedAction = "promoteMember"; 
									}

									if ($isOwner) { 
										$msg .= "the Community Admin and cannot be  kicked."; 
									}

									//if (!$isMember) { $msg .= "not a member of ".$community->long_name."."; }
									$this->returnData->message = $msg;
								} else {
									$sql_query = "DELETE FROM UserCommunities WHERE (MixerID=? AND CommunityID=?) AND MemberState != 'founder'";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$sql_query = "INSERT INTO UserCommunities (MixerID, CommunityID, MemberState) VALUES (?, ?, 'banned')";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->returnData->message = $member->name_token." was banned from ".$community->long_name;

									// message admins/moderators
								}
								break;

							case "unbanMember":
								if (!$isBanned) {
									$this->returnData->message = $member->name_token." is not banned from ".$community->long_name.".";
								} else {
									$sql_query = "DELETE FROM UserCommunities WHERE MixerID=? AND CommunityID=? AND MemberState='banned'";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->returnData->message = $member->name_token." was unbanned from ".$community->long_name;
								}
								break;
						}
						

					} else {
						$this->returnData->message = "You must be an admin/moderator of this community to perform this action.";
					}
				} else {
					$this->returnData->message = "";
					if (empty($community)) { $this->returnData->message .= "The community you were trying to access was not found in the database. "; }
					if (empty($member)) { $this->returnData->message .= "The mixer user you were trying to access was not found in the database. "; }
				}

			} else {
				$this->returnData->message = "Community ID, User ID or desired status were not provided.";
				$this->returnData->postData = $_POST;
			}
		} else {
			$this->returnData->message = "No user is logged in to be able to perform this action.";
		}


		$this->returnData();

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


	// --------------------------------------------------------------- 
	// --- Debug/Text Functions -------------------------------------- 
	// ---------------------------------------------------------------

	public function testServlet() {
		$this->returnData->success = false;
		$this->returnData->message = "Servlet was succesfully pinged, put a variable wasn't provided.";

		if (!empty($_POST['variable'])) {
			$this->returnData->success = true;
			$this->returnData->message = "Servlet was succesfully pinged.";
			$this->returnData->variable = $_POST['variable'];
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

	public function queryBuildTester() {
		//$target = "UserCommunity";
		$target = "UserCommunities";

		//$target = "UserCommunity";
		/*$sql_query = "SELECT mixer_users.name_token, communities.long_name
FROM $target
JOIN mixer_users ON mixer_users.mixer_id = $target.UserID
JOIN communities ON communities.id = $target.CommunityID
WHERE communities.id=?";
		//$params = array($target, 1);
		$query = $this->db->query($sql_query, $params);
		//$this->returnData->queryData = $query->result();*/

		/*$this->db->select('mixer_users.name_token');
		$this->db->select('communities.long_name');
		
		$this->db->join('mixer_users', "mixer_users.mixer_id = UserCommunities.MixerID");
		$this->db->join('communities', "communities.id = UserCommunities.CommunityID");

		$this->db->where('CommunityID', 1);
		$this->db->where('MemberState', 'admin');

		$query = $this->db->get($target);*/

		/*SELECT communities.*
			FROM communities
			JOIN mixer_users ON FIND_IN_SET(communities.id, mixer_users.adminCommunities) OR FIND_IN_SET(communities.id, mixer_users.modCommunities)
			WHERE mixer_users.mixer_id = ? AND (communities.status='open' OR communities.status='closed')*/

		$this->db->select('communities.*');
		$this->db->join('mixer_users', "mixer_users.mixer_id = UserCommunities.MixerID");
		$this->db->join('communities', "communities.id = UserCommunities.CommunityID");

		$this->db->where('CommunityID', 1);
		$this->db->where('MemberState', 'admin');
		$this->db->or_where('MemberState', 'mod');

		$query = $this->db->get($target);



		$this->returnData->queryData = $query->result();



		$this->returnData();
	}

}
?>