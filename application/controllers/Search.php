<?php
class Search extends CI_Controller {
	public function __construct() {
		parent::__construct();
		// Your own constructor code
		$this->load->database();
		$this->load->library('news');
		$this->load->library('users');
		$this->load->library('types');
		$this->load->library('communities');
		$this->load->library('tools');

		$this->data = new stdClass();

		$this->returnData = new stdClass();
		$this->returnData->success = FALSE;
		$this->returnData->message = "No search parameters were provided.";
	}

	private function returnData() {
		echo json_encode($this->returnData);
	}

	public function getStreamers() {
		//$criteria = array();

		// Search criteria:
			// Stream Age range
			// Mixer Age range

			// Follower count range
			// View count range
			// By current view count range

			// Registered to mingler

			// Only followed types
			// Only partners/non-partners

			// Streamed my games

			// Is online (on by default)

		$followedGames = null;
		$ignoredGames = null;
		$userRecentGames = null;
		if (isset($_SESSION['mixer_id'])) {
			$followedGames = explode(",",getIdList($this->users->getUserTypesInformation($_SESSION['mixer_id'], 'followed')));
			$ignoredGames = explode(",",getIdList($this->users->getUserTypesInformation($_SESSION['mixer_id'], 'ignored')));
			$userRecentGames = explode(",",getIdList($this->users->getUsersRecentStreamTypes($_SESSION['mixer_id']))); }

		$allRecentGames = null;
		if (!empty($_POST['recentlyStreamed'])) { 
			$allRecentGames =  explode(",",getIdList($this->types->findTypesByName($_POST['recentlyStreamed']))); }
		

		// default values
		$onlineOnly = FALSE;
		$limit = 100;
		$showIgnored = FALSE;
		$orderBy = "LastStreamStart";
		$followedOnly = FALSE;
		$partnersOnly = FALSE;
		$nonpartnersOnly = FALSE;
		$registeredOnly = FALSE;
		$showSameTypes = FALSE;
		$checkHistory = FALSE;


		$this->db->select('*')
			->select('TIMESTAMPDIFF(SECOND, LastStreamStart, NOW()) AS LastStreamStart_Elapsed')
			->select('TIMESTAMPDIFF(SECOND, LastSeenOnline, NOW()) AS LastSeenOnline_Elapsed')
			->from('Users');

		if (!empty($_POST)) {
			$criteria = $_POST;

			$this->returnData->criteria = $criteria;

			if (!empty($criteria['registered'])) { $this->db->where('isRegistered', $criteria['registered']); }

			$this->checkValueRange("NumFollowers", $criteria['minFollowers'], $criteria['maxFollowers']);
			$this->checkValueRange("ViewersTotal", $criteria['minViews'], $criteria['maxViews']);
			//$this->checkValueRange("LastSeenOnline_Elapsed", $criteria['minTime'], $criteria['maxTime']);


			if (isset($criteria['onlineOnly'])) { $onlineOnly = filter_var($criteria['onlineOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['showIgnored'])) { $showIgnored = filter_var($criteria['showIgnored'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['followedOnly'])) { $followedOnly = filter_var($criteria['followedOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['partnersOnly'])) { $partnersOnly = filter_var($criteria['partnersOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['nonpartnersOnly'])) { $nonpartnersOnly = filter_var($criteria['nonpartnersOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['registeredOnly'])) { $registeredOnly = filter_var($criteria['registeredOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['sameTypes'])) { $showSameTypes = filter_var($criteria['sameTypes'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['recentSameTypes'])) { $showRecentSameTypes = filter_var($criteria['recentSameTypes'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['checkHistory'])) { $checkHistory = filter_var($criteria['checkHistory'], FILTER_VALIDATE_BOOLEAN); }

			if (!empty($criteria['limit'])) { $limit = $criteria['limit']; }
		}

		if ($onlineOnly) {
			$this->db->where('LastSeenOnline > DATE_SUB(NOW(), INTERVAL 10 MINUTE)');
		}
	
		if (!empty($_POST['minTime'])) {
			$this->db->where('LastStreamStart < DATE_SUB(NOW(), INTERVAL '.$_POST['minTime'].' MINUTE)');}

		if (!empty($_POST['maxTime'])) {
			$this->db->where('LastStreamStart > DATE_SUB(NOW(), INTERVAL '.$_POST['maxTime'].' MINUTE)');}

		if ($partnersOnly) {
			$this->db->where('isPartner', 1);
		} elseif ($nonpartnersOnly) {
			$this->db->where('isPartner', 0);
		}

		if ($registeredOnly) {
			$this->db->where('isRegistered', 1);
		}

		$historicalTypes = null;

		if ($checkHistory) {
			$this->returnData->log = "checking history";
			// if were check game histories
			if (isset($_SESSION['mixer_id']) && !empty($userRecentGames) && $showSameTypes) {
					$this->returnData->log = "check for personal types only";
					$historicalTypes = $userRecentGames;
				}

			if ((isset($_SESSION['mixer_id']) && !empty($userRecentGames) && $showSameTypes) && !empty($allRecentGames)) {
				// if we are looking for a recent game AND matching personal types, MERGE both sets of ids and search those.
				$historicalTypes = array_unique(array_merge($allRecentGames,$userRecentGames), SORT_REGULAR);
				$this->returnData->log = "check for recent type and personal type";
			}

			if (!empty($allRecentGames)) {
				$this->returnData->log = "check for recent types only";
				$historicalTypes = $allRecentGames;
			}

		} else {
			$this->returnData->log = "not checking history";
			
		}

		
		if (!is_null($historicalTypes)) {
			$this->db->join('TimelineEvents', 'TimelineEvents.MixerID = Users.ID')
				->group_start()
					->where_in('TimelineEvents.TypeID', $historicalTypes)
					->or_where_in('LastTypeId', $historicalTypes)
				->group_end()
				->group_by('TimelineEvents.MixerID');
		} else {
			if (!empty($allRecentGames)) { $this->db->where_in('LastTypeId', $allRecentGames); }
			if (isset($_SESSION['mixer_id']) && !empty($userRecentGames) && $showSameTypes) { $this->db->where_in('LastTypeId', $userRecentGames); }
		}		


		if (isset($_SESSION['mixer_id'])) {
			if (!empty($followedGames) && $followedOnly) {
				$this->db->where_in('LastTypeId', $followedGames);}

			if (!empty($ignoredGames) && !$showIgnored) {
				$this->db->where_not_in('LastTypeId', $ignoredGames);}
		}

		//

		$order = explode(",", $_POST['orderBy']);
		
		$this->db->order_by($order[0], $order[1]);
		$this->db->limit($limit);
		$query = $this->db->get();

		$this->returnData->success = true;
		$this->returnData->message = "Search for streamers succeeded.";
		if (isset($_SESSION['mixer_id']) && $_SESSION['mixer_id'] == 217203) {
			$this->returnData->sqlQuery = $this->db->last_query();}

		$this->returnData->results = $query->result();

		$this->returnData();
	}

	private function checkValueRange($target, $minValue, $maxValue) {
		if (!empty($minValue) || !empty($maxValue)) {
			$minCount = null;
			$maxCount = null;

			if (!empty($minValue)) {
				$minCount = $minValue; }

			if (!empty($maxValue)) {
				$maxCount =  $maxValue; }

			if (!empty($minCount) && !empty($maxCount)) {
				// min and max are both defined, so get between those values.
				$this->db->where($target.' BETWEEN '.$minCount.' AND '.$maxCount);
			} elseif (!empty($maxCount)) {
				// max is defined, but min isn't, so get all under max definition
				$this->db->where($target.' <= '.$maxCount);
			} elseif (!empty($minCount)) {
				// min is defined, but max isn't, so get all over min definition
				$this->db->where($target.' >= '.$minCount);
			} else {
				// nothing is defined, so get a default 
				$this->db->where($target.' >= 25');
			}		
		}
	}

	public function getStreamersByGroup() {

		$this->returnData->criteria = $_POST;

		$groupType = $_POST['type']; // type, community, OR team
		$groupId = $_POST['id']; // ID value for group


		if (in_array($groupType, ['type', 'team', 'community'])) {
			$this->db->select('*')
			->select('TIMESTAMPDIFF(SECOND, LastStreamStart, NOW()) AS LastStreamStart_Elapsed')
			->select('TIMESTAMPDIFF(SECOND, LastSeenOnline, NOW()) AS LastSeenOnline_Elapsed')
			->from('Users');

			switch ($groupType) {
				case "type":
					$this->db->where('LastTypeID', $groupId);
					break;

				case "team":
					$this->db->join('UserTeams', 'UserTeams.MixerID = Users.ID')
						->where('UserTeams.TeamID', $groupId);
					break;

				case "community":
					$this->db->join('UserCommunities', 'UserCommunities.MixerID = Users.ID')
						->where('UserCommunities.CommunityID', $groupId)
						->where('UserCommunities.MemberState', 'member');
					break;
			}
			$query = $this->db->order_by('LastStreamStart', 'DESC')->get();
			$this->returnData->results = $query->result();
			$this->returnData->success = true;
			$this->returnData->message = "Search for streamers succeeded.";
			if (isset($_SESSION['mixer_id']) && $_SESSION['mixer_id'] == 217203) {
				$this->returnData->sqlQuery = $this->db->last_query();}

		} else {
			$this->returnData->message = "Invalid Group Type was provided.";
		}

		$this->returnData();
	}
	



} ?>