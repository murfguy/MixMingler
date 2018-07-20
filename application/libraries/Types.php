<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Types {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
		$this->CI->load->library('tools');
	}

	public function getTypeById($typeId) {
		$data = new stdClass();
		$sql_query = "SELECT * FROM stream_types WHERE typeId=?";
		$query = $this->CI->db->query($sql_query, array($typeId));

		if (!empty($query->result())) {
			// Type is on Mingler
			return $query->result()[0];
		} else {
			// Type is not on Mingler
			return null;
		}
	}

	public function getTypeBySlug($slug) {
		$data = new stdClass();
		$sql_query = "SELECT * FROM stream_types WHERE slug=?";
		$query = $this->CI->db->query($sql_query, array($slug));

		if (!empty($query->result())) {
			// Type is on Mingler
			return $query->result()[0];
		} else {
			// Type is not on Mingler
			return null;
		}
	}

	public function getAllTypesFromMingler() {
		$sql_query = "SELECT * FROM stream_types ORDER BY typeName ASC";
		$query = $this->CI->db->query($sql_query);
		$types = $query->result();

		return $types;
	}

	public function getAllTypeIdsFromMingler() {
		$sql_query = "SELECT typeId FROM stream_types ORDER BY id ASC";
		$query = $this->CI->db->query($sql_query);
		$types = $query->result();

		$typeIds = array();
		foreach ($types as $type) {
			$typeIds[] = $type->typeId;
		}

		return $typeIds;
	}

	public function getTypesByIdsFromMingler($typeIds) {
		$typeIds = str_replace(";", ",", $typeIds);

		$sql_query = "SELECT * FROM stream_types WHERE typeId IN ($typeIds) ORDER BY typeName ASC";
		$query = $this->CI->db->query($sql_query);
		$types = $query->result();

		return $types;
	}

	public function getSpecifiedTypesFromMixer($typesBatch) {
		$url = "https://mixer.com/api/v1/types";
		$currentPage = 0;
		$foundAllTypes = false;
		$allTypes = array();

		$pageLimit = 300;

		$types = array();
		
		$urlParameters = "?limit=100";
		$urlParameters .="&where=id:in:$typesBatch";
		$urlParameters .="&order=online:DESC,viewersCurrent:DESC,name:ASC";
		//$urlParameters .="&fields=id,name,parent,coverUrl,backgroundUrl";

		while (!$foundAllTypes) {

			$content = file_get_contents($url.$urlParameters."&page=".$currentPage);
			$newList = json_decode($content, true);

			$allTypes = array_merge($allTypes, $newList);

			$currentPage = $currentPage + 1;

			if (count($newList) < 100 || $currentPage >= $pageLimit) {
				// We've got all known types
				$foundAllTypes = true;
			}
		}

		return $allTypes;
	}

	public function getActiveStreamsFromMixerByTypeId($typeId, $totalLimit = 0) {
		$url = "https://mixer.com/api/v1/types/".$typeId."/channels";
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
		
		$urlParameters .="&order=viewersCurrent:DESC,numFollowers:DESC,token:ASC";
		$urlParameters .="&fields=id,token,typeId,audience,user,numFollowers,viewersCurrent";



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

	public function getAllActiveTypesFromMixer() {
		$url = "https://mixer.com/api/v1/types";
		$currentPage = 0;
		$foundAllActiveTypes = false;
		$allTypes = array();


		$urlParameters = "?limit=100";
		$urlParameters .="&order=online:DESC,viewersCurrent:DESC,name:ASC";
		$urlParameters .="&where=online:gte:1";

		while (!$foundAllActiveTypes) {
			$content = file_get_contents($url.$urlParameters."&page=".$currentPage);
			$newList = json_decode($content, true);

			/*foreach ($newList as $type) {
				$type['slug'] = $this->createSlug($type['name']);
			}*/

			$allTypes = array_merge($allTypes, $newList);

			$currentPage = $currentPage + 1;

			if (count($newList) < 100) {
				// We've got all known types
				$foundAllActiveTypes = true;
			}
		}
		return $allTypes;
	}

	public function getRecentStreamsForType($typeId) {
		$sql_query = "SELECT *, (SELECT name_token FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) as username, (SELECT avatarUrl FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) as avatarUrl FROM timeline_events WHERE eventType='type' AND extraVars=? AND eventTime > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY eventTime DESC LIMIT 0, 100";
		$query = $this->CI->db->query($sql_query, array($typeId));
		$feedData = $query->result();

		return $feedData;
	}

	public function getLastMonthsMostFrequentStreamersForType($typeId) {

		$sql_query = "SELECT mixer_id, 
(SELECT name_token FROM mixer_users WHERE mixer_users.mixer_id = timeline_events.mixer_id) as username, 
(SELECT numFollowers FROM mixer_users WHERE mixer_users.mixer_id = timeline_events.mixer_id) as numFollowers, 
(SELECT avatarUrl FROM mixer_users WHERE mixer_users.mixer_id = timeline_events.mixer_id) as avatarUrl, 
COUNT(DISTINCT DATE(eventTime)) as stream_count
FROM `timeline_events` 
WHERE eventType='type' AND extraVars=? AND eventTime>DATE_SUB(NOW(), INTERVAL 30 DAY) 
GROUP BY mixer_id 
ORDER BY `stream_count` DESC, numFollowers DESC 
LIMIT 0, 50";

		$query = $this->CI->db->query($sql_query, array($typeId));
		$feedData = $query->result();

		return $feedData;
	}

	public function addNewType($typeData) {
		if ($typeData == null) {
			$typeData = $this->getEmptyType();
		}

		$sql_query = "INSERT IGNORE INTO stream_types (typeId, typeName, slug, coverUrl, backgroundUrl) VALUES (?, ?, ?, ?, ?)";
		$query = $this->CI->db->query($sql_query, array($typeData['id'], $typeData['name'], $this->createSlug($typeData['name']), $typeData['coverUrl'], $typeData['backgroundUrl']));
	}

	public function getEmptyType() {
		$type = array(
			'id' => 0,
			'name' => "No game selected",
			'coverUrl' => '',
			'backgroundUrl' => ''
		);
		
		return $type;
	}
	

	public function createSlug($typeName) {
		$typeName = preg_replace('/[^a-zA-Z0-9\-\s]/', '', $typeName); // removes non-alphanumeric characters except space and dash
		$typeName = preg_replace('/[\-\s]/', '_', $typeName); // converts spaces and dashes to underscores
		return strtolower($typeName); // returns slugged version, lower case
	}

	public function getSyncQueryDataArray($type) {
		if ($type['coverUrl'] == null) {
			$type['coverUrl']  = "https://mixer.com/_latest/assets/images/main/types/default.jpg";
		}

		$query_data = array(
			'typeId' => $type['id'],
			'typeName' => $type['name'],
			'slug' => $this->createSlug($type['name']),
			'coverUrl' => $type['coverUrl'],
			'backgroundUrl' => $type['backgroundUrl']
		);

		return $query_data;
	}


	public function formatTypeDataFromMixer($type) {		
		if (empty($type['coverUrl'])) {
			$type['coverUrl'] == "https://mixer.com/_latest/assets/images/main/types/default.jpg";
		}

		return array(
			'id' => $type['id'],
			'name' => $type['name'],
			'slug' => $this->createSlug($type['name']),
			'online' => $this->CI->tools->formatNumber($type['online']),
			'viewersCurrent' => $this->CI->tools->formatNumber($type['viewersCurrent']),
			'coverUrl' => $type['coverUrl'],
			'backgroundUrl' => $type['backgroundUrl']
		);
	}
}
?>