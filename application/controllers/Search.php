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
		$recentGames = null;
		if (isset($_SESSION['mixer_id'])) {
			$followedGames = explode(",",getIdList($this->users->getUserTypesInformation($_SESSION['mixer_id'], 'followed')));
			$ignoredGames = explode(",",getIdList($this->users->getUserTypesInformation($_SESSION['mixer_id'], 'ignored')));
			$recentGames = explode(",",getIdList($this->users->getUsersRecentStreamTypes($_SESSION['mixer_id'])));}

		// default values
		$onlineOnly = FALSE;
		$limit = 100;
		$showIgnored = TRUE;
		$orderBy = "LastStreamStart";
		$followedOnly = FALSE;
		$partnersOnly = FALSE;
		$sameTypes = FALSE;


		$this->db->select('*')
			->select('TIMESTAMPDIFF(SECOND, LastStreamStart, NOW()) AS LastStreamStart_Elapsed')
			->select('TIMESTAMPDIFF(SECOND, LastSeenOnline, NOW()) AS LastSeenOnline_Elapsed')
			->from('Users');

		if (!empty($_POST)) {
			$criteria = $_POST;

			$this->returnData->criteria = $criteria;

			if (isset($criteria['registered'])) { $this->db->where('isRegistered', $criteria['registered']); }
			
			if (isset($criteria['streamAge'])) {
				//$ages = explode(',', $criteria['streamAge']);

				if ($criteria['streamAge'] <= (60 * 24)) {
					$this->db->where('LastStreamStart > DATE_SUB(NOW(), INTERVAL '.$criteria['streamAge'].' MINUTE)'); 
				} else {
					$this->db->where('LastStreamStart < DATE_SUB(NOW(), INTERVAL 24 HOUR)');
				}
			}

			if (isset($criteria['followers'])) {
				$followers = explode(",", $criteria['followers']);
				$this->db->where('NumFollowers BETWEEN '.$followers[0].' AND '.$followers[1]);
			}

			if (isset($criteria['onlineOnly'])) { $onlineOnly = filter_var($criteria['onlineOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['showIgnored'])) { $showIgnored = filter_var($criteria['showIgnored'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['followedOnly'])) { $followedOnly = filter_var($criteria['followedOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['partnersOnly'])) { $partnersOnly = filter_var($criteria['partnersOnly'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['sameTypes'])) { $sameTypes = filter_var($criteria['sameTypes'], FILTER_VALIDATE_BOOLEAN); }
			if (isset($criteria['limit'])) { $limit = $criteria['limit']; }
			if (isset($criteria['orderBy'])) { $orderBy = $criteria['orderBy']; }
		}

		if ($onlineOnly) {
			$this->db->where('LastSeenOnline > DATE_SUB(NOW(), INTERVAL 10 MINUTE)');
		}

		if ($partnersOnly) {
			$this->db->where('isPartner', 1);
		}

		if (!empty($recentGames) && $sameTypes) {
			$this->db->where_in('LastTypeId', $recentGames);
		}

		if (!empty($followedGames) && $followedOnly) {
			//$this->db->where_in('LastTypeId', $followedGames);
		}

		if (!empty($ignoredGames) && !$showIgnored) {
			$this->db->where_not_in('LastTypeId', $ignoredGames);
		}
		
		$this->db->order_by($orderBy, 'DESC');
		$this->db->limit($limit);
		$query = $this->db->get();

		$this->returnData->recentGames = $recentGames;

		$this->returnData->success = true;
		$this->returnData->message = "Search for streamers succeeded.";
		$this->returnData->sqlQuery = $this->db->last_query();
		if (isset($_SESSION['mixer_id']) && $_SESSION['mixer_id'] == 217203) {
			$this->returnData->results = $query->result();}

		$this->returnData();
	}

} ?>