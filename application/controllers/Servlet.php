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
		$this->returnData->message = "Server action was requested, but no action occured.";
	}

	public function index() {
	}

	private function returnData() {
		echo json_encode($this->returnData);
	}

	// --------------------------------------------------------------- 
	// --- Join/Follow Community Functions --------------------------- 
	// ---------------------------------------------------------------

	public function changeCommunityStatus() {
		$this->returnData->requestedAction = "changeCommunityStatus";
		if (isset($_SESSION['mixer_id'])) {
			$currentUserId = $_SESSION['mixer_id'];
			$this->returnData->currentUserId = $currentUserId; 

			if (!empty($_POST['communityId']) && !empty($_POST['action'])) {
				$communityID = $_POST['communityId'];
				$this->returnData->communityID = $communityID;
				$community = $this->communities->getCommunity($communityID);
				$this->returnData->Status = $community->Status;
				$memberData = $this->users->getUsersCommunitiesInformation($currentUserId, $communityID);
				//echo json_encode($memberData);
				//$this->returnData->MemberStates = $memberData[0]->MemberStates;
				if (!empty($memberData)) {
					$states = getMemberStateBooleans($memberData[0]->MemberStates);
				} else {
					$states = getMemberStateBooleans();
				}


				if (!empty($community)) {
					$this->returnData->success = true; // all are succesful actions, except the default
					$this->returnData->completedAction = $_POST['action'];

					$data = array(
						'MixerID' => $currentUserId,
						'CommunityID' => $communityID);

					$emailParams = array(
						'requester'=>$_SESSION['mixer_user'],
						'communityName'=> $community->Name,
						'communityId' => $community->ID);

					switch ($_POST['action']) {
						// =============================================
						// --- Safety Catch ----------------------------
						// =============================================

						default:
							$this->returnData->success = false; // reset success boolean as this fails.
							$this->returnData->completedAction = null;
							$this->returnData->message = "An unknown server action request was provided.";
							break;

						// =============================================
						// --- Record Insertion ------------------------
						// =============================================

						case "joinCommunity":
							// Valid condition: not banned, not a member, is not closed
							// add to pending if 'member approval' is required, otherwise add as member.
							$this->returnData->isApprovalRequired = boolval($community->isApprovalRequired);
							if ($states['isBanned']) {
								$this->returnData->success = false; // reset success boolean as this fails.
								$this->returnData->completedAction = null;
								$this->returnData->message = "You are banned from $community->Name and cannot join.";
							} else {
								if ($states['isMember']) {
									$this->returnData->success = false; // reset success boolean as this fails.
									$this->returnData->completedAction = null;
									$this->returnData->message = "You are already a member of $community->Name.";
								} else {
									if ($community->isApprovalRequired) {
										$data['MemberState'] = 'pending';
										$this->db->insert('UserCommunities', $data);
										$this->returnData->completedAction = "addedToPending";
										$this->returnData->message = "You are now awaiting approval to join $community->Name.";

										// email mods that there is a pending approval			
										$this->communications->sendMessage('mods', 'pendingMember', $emailParams);
									} else {
										$data['MemberState'] = 'member';
										$this->db->insert('UserCommunities', $data);
										$this->returnData->message = "You are now a member $community->Name.";

										$this->communications->sendMessage('mods', 'newMember', $emailParams);

										$params = array (
											'CommunityID' => $communityID,
											'MessageParams' => array($communityID));
										$this->news->addNews($currentUserId, "joinCommunity", "community", $params);
									}

									// If user isn't following, we auto-follow them as well.
									$this->returnData->alsoFollowed = false;
									if (!$states['isFollower']) {
										$this->returnData->alsoFollowed = true;
										$data['MemberState'] = 'follower';
										$this->db->insert('UserCommunities', $data); }
								}
							}
							break;

						case "followCommunity":
							// Valid condition: not following
							if ($states['isFollower']) {
								$this->returnData->success = false; // reset success boolean as this fails.
								$this->returnData->completedAction = null;
								$this->returnData->message = "You are already a following $community->Name.";
							} else {
								$data['MemberState'] = 'follower';
								$this->db->insert('UserCommunities', $data);
								$this->returnData->message = "You are now a following $community->Name.";
							}
							break;


						case "setAsCore":
							$coreCommunities = $this->users->getUserCoreCommunities($_SESSION['mixer_id']);
							if ($states['isCore']) {
								$this->returnData->success = false; // reset success boolean as this fails.
								$this->returnData->completedAction = null;
								$this->returnData->message = "You are already a Core Member of $community->Name.";
							} else {
								if (count($coreCommunities) >= 4) {
									$this->returnData->success = false; // reset success boolean as this fails.
									$this->returnData->completedAction = null;
									$this->returnData->message = "You are at the maximum number of Core Communities (4). Please unmark a different Core Community and try again.";
								} else {
									$data['MemberState'] = 'core';
									$this->db->insert('UserCommunities', $data);
									$this->returnData->message = "You are now a Core Member of $community->Name.";
								}
							}
							break;

						// =============================================
						// --- Record Deleteion ------------------------
						// =============================================

						case "leaveCommunity":
							if ($states['isAdmin']) {
								$this->returnData->success = false; // reset success boolean as this fails.
								$this->returnData->completedAction = null;
								$this->returnData->message = "You are the admin of $community->Name and cannot change your status.";
							} else {
								if (!$states['isMember']) {
									$this->returnData->success = false; // reset success boolean as this fails.
									$this->returnData->completedAction = null;
									$this->returnData->message = "You are not currently a member of $community->Name.";
								} else {
									$this->db
										->where('CommunityID', $communityID)
										->where('MixerID', $currentUserId)
											->group_start()
												->where('MemberState','member')
												->or_where('MemberState', 'core')
												->or_where('MemberState', 'pending')
												->or_where('MemberState', 'moderator')
											->group_end()
										->delete('UserCommunities');
									//$this->db->delete('UserCommunities');
									$this->returnData->message = "You have left $community->Name.";

									$this->db
										->where('CommunityID', $communityID)
										->where('MixerID', $currentUserId)
										->delete('TimelineEvents');
								}
							}							
							break;

						case "unfollowCommunity":
							if ($states['isAdmin']) {
								$this->returnData->success = false; // reset success boolean as this fails.
								$this->returnData->completedAction = null;
								$this->returnData->message = "You are the admin of $community->Name and cannot change your status.";
							} else {
								if (!$states['isFollower']) {
									$this->returnData->success = false; // reset success boolean as this fails.
									$this->returnData->completedAction = null;
									$this->returnData->message = "You are not currently following $community->Name.";
								} else {
									$data['MemberState'] = 'follower';
									$this->db->delete('UserCommunities', $data);
									$this->returnData->message = "You have unfollowed $community->Name.";
								}
							}
							break;

						case "unpendCommunity":
							$this->returnData->isApprovalRequired = boolval($community->isApprovalRequired);
							if ($states['isAdmin']) {
									$this->returnData->success = false; // reset success boolean as this fails.
									$this->returnData->completedAction = null;
									$this->returnData->message = "You are the admin of $community->Name and cannot change your status.";
							} else {
								if (!$states['isPending']) {
									$this->returnData->success = false; // reset success boolean as this fails.
									$this->returnData->completedAction = null;
									$this->returnData->message = "You are not currently pending approval to join $community->Name.";
								} else {
									$data['MemberState'] = 'pending';
									$this->db->delete('UserCommunities', $data);
									$this->returnData->completedAction = 'removedFromPending';
									$this->returnData->message = "You have removed your request to join $community->Name.";

								}
							}
							break;

						case "removeAsCore":
							if (!$states['isCore']) {
								$this->returnData->success = false; // reset success boolean as this fails.
								$this->returnData->completedAction = null;
								$this->returnData->message = "You are not currently a Core Member of $community->Name.";
							} else {
								$data['MemberState'] = 'core';
								$this->db->delete('UserCommunities', $data);
								$this->returnData->message = "You have been removed as a Core Member of $community->Name.";
							}
							break;
					}


				} else { $this->getWarningText("emptyResult"); }// server data is missing
			} else { $this->getWarningText("badData"); } // provided data is bad 
		} else { $this->getWarningText("notLoggedIn"); }// not logged in

		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Follow/Ignore Type Functions ------------------------------ 
	// ---------------------------------------------------------------

	public function changeTypeStatus() {
		$this->returnData->requestedAction = "changeTypeStatus";
		if (isset($_SESSION['mixer_id'])) {
			$currentUserId = $_SESSION['mixer_id'];
			$this->returnData->currentUserId = $currentUserId;

			if (!empty($_POST['typeId']) || !empty($_POST['action'])) {
				$typeID = $_POST['typeId'];
				$this->returnData->typeID = $typeID;
				$type = $this->types->getTypeById($typeID);

				if (!empty($type)) {
					// execute community action

					$this->returnData->success = true; // all are succesful actions, except the default
					$this->returnData->completedAction = $_POST['action'];
					$data = array(
						'MixerID' => $currentUserId,
						'TypeID' => $typeID);

					switch ($_POST['action']) {

						default:
							$this->returnData->success = false; // reset success boolean as this fails.
							$this->returnData->completedAction = null;
							$this->returnData->message = "An unknown server action request was provided.";
							break;

						case "followType":
							$data['FollowState'] = 'followed';
							$this->db->insert('UserTypes', $data);
							$this->returnData->message = "You are now following $type->Name streams.";
							break;

						case "ignoreType":
							$data['FollowState'] = 'ignored';
							$this->db->insert('UserTypes', $data);
							$this->returnData->message = "You are now ignoring $type->Name streams.";
							break;

						case "unfollowType":
							$data['FollowState'] = 'followed';
							$this->db->delete('UserTypes', $data);
							$this->returnData->message = "You are no longer following $type->Name streams.";
							break;
						case "unignoreType":
							$data['FollowState'] = 'ignored';
							$this->db->delete('UserTypes', $data);
							$this->returnData->message = "You are no longer ignoring $type->Name streams.";
							break;

					}

				} else { $this->getWarningText("emptyResult"); }// server data is missing
			} else { $this->getWarningText("badData"); } // provided data is bad
		} else { $this->getWarningText("notLoggedIn"); }// not logged in
		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Site Admin Functions -------------------------------------- 
	// ---------------------------------------------------------------

	public function applyUserRole() {
		$this->returnData->requestAction = "applyUserRole";

		if (isset($_SESSION['mixer_id'])) {
			if (in_array($_SESSION['site_role'], array('owner','admin','dev'))) {
				$this->returnData->username = $_POST['username'];

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

				$streamer = $this->users->getUserFromMinglerByToken($_POST['username']);

				if (!empty($streamer)) {
					if ($newRole != $streamer->SiteRole) {
						

						$this->users->setUserSiteRole($streamer->ID, $newRole);

						if ($newRole == "admin" || $newRole == "dev") {
							$params = array ('MessageParams' => array($role));
							$this->news->addNews($streamer->ID, 'newSiteRole', "mingler", $params);
						}

						$this->returnData->success = true;
						$this->returnData->message = "Applied the <i>$role</i> site role to ".$_POST['username'];

					} else {
						$this->returnData->message = $_POST['username']." is already assigned as $role.";
					}
				} else {
					$this->returnData->message = $_POST['username']." isn't a valid user.";
				}
			} else {
				// insufficient role
				$this->returnData->message = "You do not have permission to perform this action.";
			}
		} else {
			//not logged in
			$this->returnData->message = "You are not logged in.";
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
		$this->returnData->requestedAction = 'requestCommunity';
		$this->returnData->success = true;
		$this->returnData->message = "";
		// Check for any errors
		
		// Does that name exist? 
		if ($this->communities->communityNameExists($_POST['name'])) {
			$this->returnData->success = false;
			$this->returnData->message .= "<p>A community with this name already exists.</p>";
		}

		// Does that slug exist?
		if ($this->communities->communitySlugExists($_POST['slug'])) {
			$this->returnData->success = false;
			$this->returnData->message .= "<p>A community with this URL already exists.</p>";
		}

		// Did they try and use 'create' as a slug?
		if ($_POST['slug'] == "create") {
			$this->returnData->success = false;
			$this->returnData->message .= "<p>That URL is reserved and cannot be used.</p>";
		}

		// Was there a description?
		if (empty($_POST['description'])) {
			$this->returnData->success = false;
			$this->returnData->message .= "<p>Description wasn't provided.</p>";
		} 

		// Was there a summary?
		if (empty($_POST['summary'])) {
			$this->returnData->success = false;
			$this->returnData->message .= "<p>Summary wasn't provided.</p>";
		}
		
		// Did they pick a category?
		if (empty($_POST['category_id'])) {
			$this->returnData->success = false;
			$this->returnData->message .= "<p>A category wasn't selected.</p>";
		}
		

		// If all criteria passed:
		if ($this->returnData->success) {
			// Add new community request into database
			$this->communities->createNewCommunity($_POST);

			// Send an email alert to site admins about new community request
			$emailParams = array(
				'requester'=>$_SESSION['mixer_user'],
				'communityName'=>$_POST['name'],
				'singleUserId' => $_SESSION['mixer_id']);


			$this->communications->sendMessage('admins', 'newCommunityRequest', $emailParams);
			$this->communications->sendMessage('user', 'communityRequestReceived', $emailParams);

			$this->returnData->message = "Your request has been submitted, and will be processed by a site admin soon.";
		} 

		$this->returnData();
	}

	public function processCommunity() {
		$this->returnData->requestedAction = 'processCommunity';
		if (isset($_SESSION['mixer_id'])) {
			$community = $this->communities->getCommunity($_POST['communityId']);
			$this->returnData->originalSlug = $community->Slug;

			if (in_array($_SESSION['site_role'], array('owner','admin','dev'))) {
				$status = $_POST['status'];

				$this->returnData->status = $status;

				$this->communities->setCommunityStatus($_POST['communityId'], $_POST['status']);

				if (empty($_POST['isQuickApprove'])) {
					$this->returnData->name = $_POST['name'];
					$details = array(
						'Name' => $_POST['name'],
						'CategoryID' => $_POST['category_id'],
						'Summary' => $_POST['summary'],
						'Description' => $_POST['description'],
						'AdminApprover' => $_POST['siteAdmin'],
						'AdminNote' => $_POST['adminNote']
						);
					$this->returnData->completedAction = 'processCommunity';
				} else {
					$this->returnData->isQuickApprove = true;
					$this->returnData->completedAction = 'quickApproveCommunity';
					$details = array('AdminApprover' => $_POST['userId']);
				}

				$this->communities->updateCommunityDetails($_POST['communityId'], $details);

				$community = $this->communities->getCommunity($_POST['communityId']);
				$this->returnData->Slug = $community->Slug;
				$this->returnData->Name = $community->Name;

				$this->returnData->success = true;
				$this->returnData->message = $community->Name."'s status was changed to $status.";


				$emailParams = array(
					'communityName'=> $community->Name,
					'singleUserId' => $community->Admin);

				if ($status == 'approved') {
					$this->communications->sendMessage('user', 'communityApproved', $emailParams);
				} elseif ($status == 'rejected') {
					$emailParams['adminNote'] = $_POST['adminNote'];
					$this->communications->sendMessage('user', 'communityDenied', $emailParams);
				}

			} else {$this->getWarningText("insufficientRights"); } // insufficient role
		} else { $this->getWarningText("notLoggedIn"); } //not logged in

		
		$this->returnData();
	}

	public function foundCommunity() {
		if (isset($_SESSION['mixer_id'])) {
			$mixerID = $_SESSION['mixer_id'];

			if (!empty($_POST)) {
				$community = $this->communities->getCommunity($_POST['commId']);
				$this->returnData->community = $community;
				if ($mixerID == $community->Admin) {
					if (!in_array($community->Status, array('open', 'closed', 'rejected'))) {
						$this->returnData->success = true;
						$this->returnData->message = "Community has been founded! You will be redirected to the full admin page in a few seconds.";

						$requireApproval = 0;
						if ($_POST['requireApproval'] == "yes") {
							$requireApproval = 1;
						}

						$this->returnData->status = $_POST['status'];
						$this->returnData->isApprovalRequired = $requireApproval;
						$this->returnData->community_id = $_POST['commId'];


						// Found community!
						$data = array(
							'Status' => $_POST['status'], 
							'isApprovalRequired' => $requireApproval,
							'FoundationTime' => date("Y-m-d H:i:s"));
						$this->db->where('ID', $_POST['commId']);
						$this->db->update('Communities', $data);

						// Set User's last foundation timestamp
						$data = array('LastFoundationTime' => date("Y-m-d H:i:s"));
						$this->db->where('ID', $_POST['mixerUser_id']);
						$this->db->update('Users', $data);

						// Add news item 
						$newsParams = array(
							'CommunityID' => $_POST['commId'],
							'MessageParams' => array($_POST['commId']));
						$this->news->addNews($_POST['mixerUser_id'], "foundedCommunity", "community", $newsParams);
					} else { $this->returnData->message = "Community's status is '$community->Status' and cannot be founded."; }
				} else { $this->returnData->message = "Form data was incomplete.";}			
			} else { $this->returnData->message = "You do not have permission to perform this action."; }
		} else { $this->returnData->message = "You are not logged in."; }

		$this->returnData();

	}

	public function deleteCommunity() {
		$this->returnData->requestedAction = "deleteCommunity";
		// criteria
		// logged in > data is valid > is site admin OR community owner > sucess
		if (isset($_SESSION['mixer_id'])) {
			$currentUserId = $_SESSION['mixer_id'];
			$currentUserRole = $_SESSION['site_role'];
			$this->returnData->currentUserId = $currentUserId;
			$this->returnData->currentUserRole = $currentUserRole;

			if (!empty($_POST['communityId'])) {
				$communityID = $_POST['communityId'];
				$this->returnData->communityID = $communityID;
				$community = $this->communities->getCommunity($communityID);
				$this->returnData->slug = $community->Slug;

				if (!empty($community)) {
					if ($community->Admin == $currentUserId || in_array($currentUserRole, array('admin', 'owner'))) {
						$this->db->delete('Communities', array('ID' => $communityID));

						$this->returnData->success = true;
						$this->returnData->message = "Community was deleted.";
						$this->returnData->completedAction = "deleteCommunity";
					} else {  $this->getWarningText("insufficientRights"); } // user is not allowed to do this.
				} else { $this->getWarningText("emptyResult"); }// server data is missing
			} else { $this->getWarningText("badData"); } // provided data is bad
		} else { $this->getWarningText("notLoggedIn"); }// not logged in

		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Community Moderation Functions ---------------------------- 
	// ---------------------------------------------------------------

	public function editCommunity() {
		// Only for admins
		$this->returnData->requestedAction = 'editCommunity';
		$this->returnData->receivedData = $this->input->post();//$_POST;
		if (isset($_SESSION['mixer_id'])) {
			if (!empty($_POST)) {
				$community = $this->communities->getCommunity($_POST['communityId']);
				
				if (!empty($community)) {
					if ($community->Admin == $_SESSION['mixer_id']) {
						//$uploadSuccess = true;
						//$this->returnData->hasUpload = false;

			        	$requireApproval = 0;
						if ($_POST['requireApproval'] == "yes") {
							$requireApproval = 1;
						}

						$submitedData = [
								'Description' => strip_tags($_POST['description']),
								'Summary' => strip_tags($_POST['summary']),
								'Status' => $_POST['status'],
								'isApprovalRequired' => $requireApproval,
								'Discord' => strip_tags($_POST['discord'])];

						$config['upload_path']          = './assets/graphics/covers/';
						$config['allowed_types']        = 'gif|jpg|png';
						$config['max_size']             = 256;
						$config['max_width']            = 512;
						$config['max_height']           = 512;
						$config['overwrite'] = TRUE;
						$config['file_name'] = $community->Slug;

						$runQuery = true;

						if (isset($_FILES['file']['name'])) {
							$runQuery = false;							
				            if (0 < $_FILES['file']['error']) {
				                $this->returnData->message =  'Error during file upload' . $_FILES['file']['error'];
				            } else {
				                if (file_exists('uploads/' . $_FILES['file']['name'])) {
				                     $this->returnData->message =  'File already exists : ' . $_FILES['file']['name'];
				                } else {
				                    $this->load->library('upload', $config);
				                    if (!$this->upload->do_upload('file')) {
				                         $this->returnData->message =  $this->upload->display_errors();
				                    } else {
				                    	$runQuery = true;
				                    	$submitedData['CoverFileType'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
				                         $this->returnData->message =  'Cover art successfully uploaded.';
				                    }
				                }
				            }
				        }

				        if ($runQuery) {							
							$this->db
								->where('ID', $_POST['communityId'])
								->update('Communities', $submitedData);

							$this->returnData->submitedData = $submitedData;

							$this->returnData->success = true;
							$this->returnData->message = "Community details were succesfully edited.";
				        }
						

					} else { $this->getWarningText("insufficientRights"); }
				} else { $this->getWarningText("emptyResult"); }
			} else { $this->getWarningText("badData"); }
		} else { $this->getWarningText("notLoggedIn"); } //not logged in

		$this->returnData();
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

				$currentUser = $this->users->getUserFromMingler($currentUserId);
				$currentUserCommunityData = $this->users->getUsersCommunitiesInformation($currentUserId, $communityID)[0];
				$currentUserStates = getMemberStateBooleans($currentUserCommunityData->MemberStates);
				$this->returnData->currentUserStates = $currentUserCommunityData->MemberStates;

				// Now we collect relevant data to our target user.
				$member = $this->users->getUserFromMingler($memberId);
				$memberCommunityData = $this->users->getUsersCommunitiesInformation($memberId, $communityID)[0];
				$states = getMemberStateBooleans($memberCommunityData->MemberStates);
				$this->returnData->states = $states;

				//$member = $this->users->getUserFromMingler($memberId);
				$community = $this->communities->getCommunity($communityID);
				$this->returnData->Status = $community->Status;

				// Did we collect good data?
				if (!empty($community) && !empty($member)) {
					$this->returnData->memberName = $member->Username;

					// Is the current user allowed to do this?
					if ($currentUserStates['isModerator'] || $currentUserStates['isAdmin']) {

						// Email parameters
						$emailParams = array(
							'communityName'=> $community->Name,
							'communityId' => $communityID,
							'requester'=> $member->Username,
							'singleUserId' => $memberId);

						// ----------------------------------------------------------------------------------------
						// -- Update database based on requested action
						// ----------------------------------------------------------------------------------------

						// At this point, we only catching pre-existing exceptions. As such, all attempts are successful.
						$this->returnData->success = true;
						$this->returnData->completedAction = $memberStatus;

						$data = ['MixerID' => $memberId, 'CommunityID'=>$communityID];

						switch ($memberStatus) {
							case "approveMember":
								if (!$states['isPending']) {
									$this->returnData->message = $member->Username." is not pending.";
								} else {
									// Add as member
									$data['MemberState'] = 'member';
									$this->db->insert('UserCommunities', $data);

									// remove as pending
									$this->db
										->where('CommunityID', $community->ID)
										->where('MixerID', $member->ID)
										->where('MemberState', 'pending')
										->delete('UserCommunities');

									$this->returnData->message = $member->Username." was added as a new member to ".$community->Name;

									// Add news item 
									//$newsText = $this->news->getEventString("joinCommunity", array($communityID));
									//$this->news->addNews($memberId, $newsText, "community", $communityID);
									$params = array (
										'CommunityID' => $communityID,
										'MessageParams' => array($communityID));
									$this->news->addNews($memberId, "joinCommunity", "community", $params);

									// notify: new member, community admin/mods
									$this->communications->sendMessage('user', 'approvedMembership', $emailParams);
								}

								break;

							case "denyMember":
								if (!$states['isPending']) {
									$this->returnData->message = $member->Username." is not pending.";
								} else {
									// remove as pending
									$this->db
										->where('CommunityID', $community->ID)
										->where('MixerID', $member->ID)
										->where('MemberState', 'pending')
										->delete('UserCommunities');

									$this->returnData->message = $member->Username." was denied membership to ".$community->Name;

									// notify: new member, community admin/mods
									$this->communications->sendMessage('user', 'deniedMembership', $emailParams);
								}

								break;

							case "promoteMember":
								// fail condition: isModerator OR not isMember
								if ($states['isAdmin'] || $states['isModerator'] || !$states['isMember']) {
									$msg = $member->Username." is ";
									if ($states['isAdmin']) { $msg .= "the Community Admin and cannot be promoted."; }
									if ($states['isModerator']) { $msg .= $member->Username."already a Moderator."; }
									if (!$states['isMember']) { $msg .= $member->Username."no longer a member of ".$community->Name."."; }
									$this->returnData->message = $msg;
								} else {
									// Add as moderator
									$data['MemberState'] = 'moderator';
									$this->db->insert('UserCommunities', $data);

									$this->returnData->message = $member->Username." was changed to a 'moderator' in ".$community->Name;

									// Add news item 
									$params = ['CommunityID'=>$communityID, 'MessageParams'=>array('moderator', $communityID)];
									$this->news->addNews($memberId, "newCommRole", "community", $params);

									// Notify moderators about promotion
									$this->communications->sendMessage('mods', 'newMod', $emailParams);
								}

								break;

							case "demoteMember":
								// fail condition: not isModerator OR not isMember
								if ($states['isAdmin'] || !$states['isModerator'] || !$states['isMember']) {
									$msg = $member->Username." is ";
									if ($states['isAdmin']) { $msg .= "the Community Admin and cannot be demoted."; }
									if (!$states['isModerator']) { $msg .= "not a Moderator."; }
									if (!$states['isMember']) { $msg .= " no longer a member of ".$community->Name."."; }
									$this->returnData->message = $msg;
								} else {
									$data['MemberState'] = 'moderator';
									$this->db->delete('UserCommunities', $data);

									$this->returnData->message = $member->Username."'s status as a moderator in ".$community->Name." was removed.";

									// Notify moderators about demotion
									$this->communications->sendMessage('mods', 'removedMod', $emailParams);
								}

								// notify user about demotion
								break;

							case "kickMember":
								// fail condition: isModerator OR not isMember
								if ($states['isAdmin'] || $states['isModerator'] || !$states['isMember']) {
									$msg = $member->Username." is ";

									if ($states['isModerator']) { 
										$msg .= "a Moderator and cannot be kicked."; 
										$this->returnData->completedAction = "promoteMember"; 
									}
									if ($states['isOwner']) { 
										$msg .= "the Community Admin and cannot be  kicked."; 
									}

									if (!$states['isMember']) { $msg .= " not a member of ".$community->Name."."; }
									$this->returnData->message = $msg;
								} else {
									$sql_query = "DELETE FROM UserCommunities WHERE (MixerID=? AND CommunityID=?) AND MemberState != 'founder'";
									$query = $this->db->query($sql_query, array($memberId, $communityID));

									$this->db
										->where('CommunityID', $communityID)
										->where('MixerID', $memberId)
											->group_start()
												->where('MemberState','member')
												->or_where('MemberState', 'core')
												->or_where('MemberState', 'pending')
												->or_where('MemberState', 'moderator')
											->group_end()
										->delete('UserCommunities');

									$this->returnData->message = $member->Username." was kicked from ".$community->Name;

									$this->db
										->where('CommunityID', $communityID)
										->where('MixerID', $memberId)
										->delete('TimelineEvents');
								}

								// notify user about removal
								break;

							case "banMember":
								// fail condition: isModerator OR not isMember
								if ($states['isAdmin'] || $states['isModerator']) {
									$msg = $member->Username." is ";

									if ($states['isModerator']) { 
										$msg .= "a Moderator and cannot be banned."; 
										$this->returnData->completedAction = "promoteMember"; 
									}

									if ($states['isAdmin']) { 
										$msg .= "the Community Admin and cannot be  kicked."; 
									}

									//if (!$isMember) { $msg .= "not a member of ".$community->Name."."; }
									$this->returnData->message = $msg;
								} else {
									$this->db
										->where('CommunityID', $communityID)
										->where('MixerID', $memberId)
											->group_start()
												->where('MemberState','member')
												->or_where('MemberState', 'core')
												->or_where('MemberState', 'pending')
												->or_where('MemberState', 'moderator')
											->group_end()
										->delete('UserCommunities');


									$data['MemberState'] = 'banned';
									$this->db->insert('UserCommunities', $data);

									$this->returnData->message = $member->Username." was banned from ".$community->Name;

									$this->db
										->where('CommunityID', $communityID)
										->where('MixerID', $memberId)
										->delete('TimelineEvents');

									// message admins/moderators
								}
								break;

							case "unbanMember":
								if (!$states['isBanned']) {
									$this->returnData->message = $member->Username." is not banned from ".$community->Name.".";
								} else {
									$data['MemberState'] = 'banned';
									$this->db->delete('UserCommunities', $data);

									$this->returnData->message = $member->Username." was unbanned from ".$community->Name;
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
		//sleep(1);
		$this->returnData->group = 'type';
		$this->returnData->id = $typeId;
		$this->returnData->success = true;
		$this->returnData->message = "Got streams from mixer.";
		$this->returnData->streams = $this->types->getActiveStreamsFromMixerByTypeId($typeId, 6);

		$this->returnData();
	}

	public function getTopStreamsForCommunity($communityId) {
		//sleep(1);

		$members = $this->communities->getCommunityMembersByGroup($communityId, 'member');


		$this->returnData->group = 'community';
		$this->returnData->id = $communityId;
		//$this->returnData->members = $members;
		$this->returnData->success = true;
		$this->returnData->message = "Got streams from mixer.";
		$this->returnData->streams = $this->types->getActiveStreamsFromMixer('userID', $members, 6);

		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- News Collection Functions --------------------------------- 
	// ---------------------------------------------------------------

	public function getNewsForType($typeId, $size = 'med') {
		//sleep(1);
		$this->returnData->typeID = $typeId;
		$this->returnData->success = false;
		$this->returnData->message = "Failed to get news.";
		$this->returnData->group = 'type';
		$this->returnData->id = $typeId;

		$typeNews = $this->news->getNewsFeedForType($typeId);

		if (!empty($typeNews)) {
			$displayItems = array();
			foreach($typeNews as $event) {
				$eventText = $this->news->getFormattedEventText($event);
				$displayItems[] = newsDisplay($event, $eventText, $size);
			}
			$this->returnData->success = true;
			$this->returnData->newsFeed = $typeNews;
			$this->returnData->displayItems = $displayItems;
			$this->returnData->message = "News was collected!";
		} else {
			$this->returnData->message = "There was no news to collect.";
		}

		$this->returnData();
	}

	public function getNewsFeed() {
		$this->returnData->message = "Failed to collect news.";
		//$feedType, $feedParams, $queryLimit = 25, $displaySize = 'med'
		$feedType = $_POST['feedType'];
		$feedParams = $_POST['feedParams'];
		if (empty($_POST['queryLimit'])) { $queryLimit = 25; } else { $queryLimit = (int)$_POST['queryLimit']; }
		if (empty($_POST['displaySize'])) { $displaySize = 'med'; } else { $displaySize = $_POST['displaySize']; }

		switch ($feedType) {
			case "user":
				$news = $this->news->getNewsFeedForUser($feedParams['mixerId'], $feedParams['limit']);
				break;

			case "community":
				$news = $this->news->getNewsFeedForCommunity($feedParams['communityId'], $feedParams['limit']);
				break;

			case "type":
				$news = $this->news->getNewsFeedForType($feedParams['typeId'], $feedParams['limit']);
				break;
		}

		if (!empty($news)) {
			$displayItems = array();
			foreach($news as $event) {
				$eventText = $this->news->getFormattedEventText($event);
				$displayItems[] = newsDisplay($event, $eventText, $displaySize);
			}
			$this->returnData->success = true;
			$this->returnData->newsFeed = $news;
			$this->returnData->displayItems = $displayItems;
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
	// --- User Information Functions -------------------------------- 
	// ---------------------------------------------------------------

	public function applyUserSettings() {
		if (isset($_SESSION['mixer_id'])) {
			if (!empty($_POST)) {

				$this->returnData->group = $_POST['group'];
				$this->returnData->settings = $_POST['settings'];

				$this->users->applyUserSettings($_POST['group'], $_POST['settings']);
				$this->returnData->message = "Settings Applied";
				//$this->returnData->success = true;

			} else { $this->getWarningText("badData"); } // provided data is bad 
		} else { $this->getWarningText("notLoggedIn"); }// not logged in



		$this->returnData();
	}

	// --------------------------------------------------------------- 
	// --- Return Functions ------------------------------------------ 
	// ---------------------------------------------------------------

	private function getWarningText($criteria) {
		switch ($criteria) {
			case "notLoggedIn":
				$this->returnData->message = "You are not currently logged in.";
				break;

			case "badData":
				$this->returnData->message = "Incomplete data was provided.";
				break;

			case "emptyResult":
				$this->returnData->message = "A database query came back empty.";
				break;

			case "emptyCommunity":
				$this->returnData->message = "Community data came back empty from the database.";
				break;

			case "emptyUser":
				$this->returnData->message = "User data came back empty from the database.";
				break;

			case "emptyType":
				$this->returnData->message = "Stream Type data came back empty from the database.";
				break;

			case "insufficientRights":
				$this->returnData->message = "You do not have sufficient access rights to perform this action.";
				break;

			case "invalidPermissions":
				$this->returnData->message = "We attempted to verify permissions, but the permissions request was invalid.";
				break;

			default:
				$this->returnData->message = "An unidentified issue occured.";
				break;
		}
	}

	// --------------------------------------------------------------- 
	// --- Debug/Test Functions -------------------------------------- 
	// ---------------------------------------------------------------

	private function verifyPermissions($permissions, $formData) {
		/*
			array(
			'LoggedIn' => true,
			'FormData' => true,
			'DatabaseQuery' => array(
				'community', 'user'),
			'Role' => 'siteAdmin,')
		*/


		$success = true;
		$database = array();
		if (!empty($permissions)) {

			foreach ($permissions as $index => $permissionData) {
				switch ($index) {

					case "LoggedIn":
						if (!isset($_SESSION['mixer_id'])) { 
							$success = false;
							$this->getWarningText("notLoggedIn"); }
						break;

					case "FormData":
						if (empty($formData)) {
							$success = false;
							$this->getWarningText("badData"); }
						break;

					case "DatabaseQuery":
						foreach ($permissionData as $dbQuery) {
							switch ($dbQuery) {
								case "community":
									if (empty($database['community'] = $this->communities->getCommunity($formData['communityId']))) {
										$success = false; $this->getWarningText("emptyCommunity");};
									break;
								
								case "user":
									if (empty($database['user'] = $this->users->getUserFromMingler($formData['userId']))) {
										$success = false; $this->getWarningText("emptyUser");};
									break;

								case "type":
									if (empty($database['type'] = $this->types->getTypeById($formData['typeId']))) {
										$success = false; $this->getWarningText("emptyType");};
									break;
							}
						}

						if (empty($database) && $success) { $success = false; $this->getWarningText("emptyResult"); }
						break;

					case "SiteAdmin":
						// requires a database
						if (in_array($_SESSION['site_role'], ['admin', 'owner'])) {
							$success = false; $this->getWarningText("insufficientRights");};
						break;

					case "Admin":
						// requires a database query for communities AND site admins
						if ($database['community']->Admin == $_SESSION['mixer_id']) {
							$success = false; $this->getWarningText("insufficientRights");};
						break;

					case "Mod":

						break;

					case "UserIsSelf":
						// requires a database query for communities
						break;
				}

				if (!$success) { break; } // stop the loop if we hit a bad value
			}

			return $success;
		} else {

		}
	}

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