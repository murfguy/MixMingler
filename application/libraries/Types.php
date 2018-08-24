<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Types {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database(); $this->db = $this->CI->db;
		$this->CI->load->library('tools'); $this->tools = $this->CI->tools;
	}

	public function getTypeById($typeId) {
		$data = new stdClass();
		$sql_query = "SELECT * FROM StreamTypes WHERE ID=?";
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
		$sql_query = "SELECT * FROM StreamTypes WHERE slug=?";
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
		$sql_query = "SELECT * FROM StreamTypes ORDER BY typeName ASC";
		$query = $this->CI->db->query($sql_query);
		$types = $query->result();

		return $types;
	}

	public function getAllTypeIdsFromMingler() {
		$sql_query = "SELECT ID FROM StreamTypes ORDER BY ID ASC";
		$query = $this->CI->db->query($sql_query);
		$types = $query->result();

		$typeIds = array();
		foreach ($types as $type) {
			$typeIds[] = $type->ID;
		}

		return $typeIds;
	}

	public function getTypesByIdsFromMingler($typeIds) {
		if (!empty($typeIds)) {

			$typeIds = str_replace(";", ",", $typeIds);

			$sql_query = "SELECT * FROM StreamTypes WHERE typeId IN ($typeIds) ORDER BY typeName ASC";
			$query = $this->CI->db->query($sql_query);
			$types = $query->result();

			return $types;
		} else {
			return null;
		}
	}

	public function getTypeFromMixer($typeId) {
		$content = file_get_contents("https://mixer.com/api/v1/types/$typeId");
		return json_decode($content, true);
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
			$urlParameters = "?limit=50";
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

			foreach ($newList as $type) {
				$type['slug'] = createSlug($type['name']);
			}

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
		$query = $this->db
			->select('TimelineEvents.*')
			->select('Users.Username as Username')
			->select('Users.AvatarUrl as AvatarURL')
			->select('MAX(EventTime) as EventTime')
			->from('TimelineEvents')
			->join('Users', 'Users.ID = TimelineEvents.MixerID')
			->where('Type', 'type')
			->where('TypeID', $typeId)
			->where('EventTime > DATE_SUB(NOW(), INTERVAL 7 DAY)')
			->group_by('MixerID')
			->limit(50)
			->get();
		return $query->result();
	}

	public function getLastMonthsMostFrequentStreamersForType($typeId) {
		$query = $this->db
			->select('MixerID')
			->select('Users.Username AS Username')
			->select('Users.NumFollowers AS NumFollowers')
			->select('Users.AvatarURL AS AvatarURL')
			->select('COUNT(DISTINCT DATE(eventTime)) as StreamCount')
			->from('TimelineEvents')
			->join('Users', 'Users.ID = TimelineEvents.MixerID')
			->where('Type', 'type')
			->where('TypeID', $typeId)
			->where('EventTime>DATE_SUB(NOW(), INTERVAL 30 DAY) ')
			->group_by('MixerID')
			->order_by('StreamCount', 'DESC')
			->order_by('NumFollowers', 'DESC')
			->limit(50)
			->get();

		return $query->result();
	}

	public function addNewType($typeData) {
		if ($typeData == null) {
			$typeData = $this->getEmptyType();
		}

		$sql_query = "INSERT IGNORE INTO StreamTypes (ID, Name, Slug, CoverURL, BackgroundURL) VALUES (?, ?, ?, ?, ?)";
		$query = $this->CI->db->query($sql_query, array($typeData['id'], $typeData['name'], createSlug($typeData['name']), $typeData['coverUrl'], $typeData['backgroundUrl']));
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
	

	/*public function createSlug($typeName) {
		$typeName = preg_replace('/[^a-zA-Z0-9\-\s]/', '', $typeName); // removes non-alphanumeric characters except space and dash
		$typeName = preg_replace('/[\-\s]/', '_', $typeName); // converts spaces and dashes to underscores
		return strtolower($typeName); // returns slugged version, lower case
	}*/

	public function getTypeURL($typeId, $typeSlug) {
		return "/type/$typeId/$typeSlug/";
	}

	public function getSyncQueryDataArray($type) {
		if ($type['coverUrl'] == null) {
			$type['coverUrl']  = "";
		}

		$query_data = array(
			'ID' => $type['id'],
			'Name' => $type['name'],
			'Slug' => createSlug($type['name']),
			'CoverUrl' => $type['coverUrl'],
			'BackgroundUrl' => $type['backgroundUrl']
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
			'slug' => createSlug($type['name']),
			'online' => $type['online'],
			'viewersCurrent' => $type['viewersCurrent'],
			'coverUrl' => $type['coverUrl'],
			'backgroundUrl' => $type['backgroundUrl']
		);
	}
}
?>