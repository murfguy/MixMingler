<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
		$this->CI->load->library('communities');
		$this->CI->load->library('types');
		$this->CI->load->library('teams');

		$this->db = $this->CI->db;
		$this->communities = $this->CI->communities;
		$this->types = $this->CI->types;
		$this->teams = $this->CI->teams;
	}

	public function addNews($mixer_id, $event, $eventType, $params = array()) {
		if (empty($params)) {
			$params = array('TypeID' => null, 'CommunityID' => null, 'MessageParams' => null);
		} else {
			if (empty($params['TypeID'])) { $params['TypeID'] = null; };
			if (empty($params['CommunityID'])) { $params['CommunityID'] = null; };
			if (empty($params['MessageParams'])) { $params['MessageParams'] = null; };
		}

		$eventText = $this->getEventString($event, $params['MessageParams']);

		$sql_query = "INSERT INTO TimelineEvents (MixerID, Content, Type, TypeID, CommunityID) VALUES (?, ?, ?, ?, ?)";
		$values = array($mixer_id, $eventText, $eventType, $params['TypeID'], $params['CommunityID']);
		$query = $this->CI->db->query($sql_query, $values);
	}

	public function getTypeNewsFeed($typeId, $limit=10) {
		$query = $this->db
			->select('TimelineEvents.*')
			->select('Users.Username as Username')
			->select('Users.AvatarURL as AvatarURL')
			->from('TimelineEvents')
			->join('Users', 'Users.ID = TimelineEvents.MixerID')
			->where('TimelineEvents.Type', 'type')
			->where('TimelineEvents.TypeID', $typeId)
			->order_by('TimelineEvents.ID', 'DESC')
			->limit($limit)
			->get();

		return $query->result();
	}

	public function getCommunityNewsFeed($communityId, $limit=35, $isFullNews = false) {

		$this->db->select('*')->from('TimelineEvents')->where('CommunityID', $communityId);

		if (!$isFullNews) {
			// all members
			$allMemberIds = $this->communities->getArrayOfMemberIDs($this->communities->getCommunityMembersByGroup($community->ID, 'member'));
			$this->db->or_where_in('MixerID', $communityId);
		}

		$query = $this->db->limit($limit)->get();
		return $query->result();
	}

	public function getNewsArray($mixer_id, $event, $eventType, $params = array()) {
		if (empty($params)) {
			$params = array('TypeID' => null, 'CommunityID' => null, 'MessageParams' => null);
		} else {
			if (empty($params['TypeID'])) { $params['TypeID'] = null; };
			if (empty($params['CommunityID'])) { $params['CommunityID'] = null; };
			if (empty($params['MessageParams'])) { $params['MessageParams'] = null; };
		}

		$eventText = $this->getEventString($event, $params['MessageParams']);

		$query_data = array(
			'MixerID' => $mixer_id,
			'TypeID' => $params['TypeID'],
			'CommunityID' => $params['CommunityID'],
			'EventTime' => date('Y-m-d H:i:s'),
			'Content' => $eventText,
			'Type' => $eventType
		);
		return $query_data;
	}


	public function getFormattedEventText($newsEvent) {
		// Add username link to item.
		$eventText = str_replace("{username}", "<a href=\"/user/$newsEvent->Username\">$newsEvent->Username</a>", $newsEvent->Content); 
		// Convert a {commId} string
		$eventText = $this->convertCommunityString($eventText);
		// Convert a {typeId} string
		$eventText = $this->convertTypeString($eventText);

		return $eventText;
	}

	private function getEventString($event, $params = "") {
		switch ($event) {
			case "newSiteRole":
				return "{username} became a MixMingler ".$params[0].".";
				break;

			case "firstSync":
				return "{username} was first synced with MixMingler.";
				break;

			case "joinMixMingler":
				return "{username} joined MixMingler.";
				break;

			case "joinCommunity":
				return "{username} joined the {commId:".$params[0]."} community.";
				break;

			case "newStreamType":
				return "{username} started streaming {typeId:".$params[0]."}.";
				break;

			case "foundedCommunity":
				return "{username} founded the {commId:".$params[0]."} community!";
				break;

			case "newCommRole":
				return "{username} became a ".$params[0]." for the {commId:".$params[1]."} community.";
				break;

			case "badge-followers":
				return "{username} reached {followers:".$params[0]."} followers!";
				break;

			case "badge-views":
				return "{username} surpassed {views:".$params[0]."} views!";
				break;

			case "badge-partner":
				return "{username} became a Mixer Partner.";
				break;
		}
	}

	public function displayPostTime($eventTime) {
		$timeDiff = time() - strtotime($eventTime);
		$now = date("Y-n-d h:i:s");

		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$twoWeeks = $day * 14;


		if ($timeDiff < $minute) {
			return $timeDiff." seconds ago";

		} elseif ($timeDiff <= $hour) {
			return floor($timeDiff/$minute)." minutes ago";

		} elseif ($timeDiff <= $day) {
			return floor($timeDiff/$hour)." hours ago";

		} elseif ($timeDiff <= $twoWeeks) {
			return floor($timeDiff/$day)." days ago";

		} else {
			return date('D. F j, Y', strtotime($eventTime));
		}
	}
	
	private function convertCommunityString($eventText) {
		if (strpos($eventText, '{commId:') !== false) {
			// This should have a community link.
			// Let's do some Regex to find out which community we are need to find.
			$pattern = '{commId:[0-9]+}';
			$communityId = preg_match($pattern, $eventText, $matches, PREG_OFFSET_CAPTURE);
			$id = $matches[0][0];
			$id = str_replace("commId:","", $id);

			$community = $this->CI->communities->getCommunity($id);

			if ($community != null) {
				$communityLink = "<a href=\"/community/$community->Slug\">".$community->Name."</a>";
			} else {
				$communityLink = "<span style='color:red'>{UNKNOWN}</span>";
			}
			$eventText = str_replace("{commId:".$id."}", $communityLink, $eventText);
		} 
		
		return $eventText;
	}

	private function convertTypeString($eventText) {
		if (strpos($eventText, '{typeId:') !== false) {
			// This should have a community link.
			// Let's do some Regex to find out which community we are need to find.
			$pattern = '{typeId:[0-9]+}';
			$typeId = preg_match($pattern, $eventText, $matches, PREG_OFFSET_CAPTURE);
			$id = $matches[0][0];
			$id = str_replace("typeId:","", $id);

			$type = $this->CI->types->getTypeById($id);

			if ($type != null) {
				//$typeLink = "<a href=\"/type/$type->slug\">".$type->name."</a>";
				$typeLink = "<a href=\"/type/$type->ID/$type->Slug\">".$type->Name."</a>";

				$eventText = str_replace("{typeId:".$id."}", $typeLink, $eventText);
			} else {
				$eventText = str_replace("{typeId:".$id."}", "<span class=\"mixBlue\">[Unknown Stream Type]</span>", $eventText);
			}
		} 
		
		return $eventText;
	}

	public function getNewsFeedForUser($mixerId, $limit = 15) {
		/*$query = $this->db
			->select('*')
			->from('TimelineEvents')
			->where('MixerID', $mixerId)
			->get();*/

		$query = $this->db
			->select('TimelineEvents.*')
			->select('Users.Username as Username')
			->select('Users.AvatarURL as AvatarURL')
			->from('TimelineEvents')
			->join('Users', 'Users.ID = TimelineEvents.MixerID')
			->where('TimelineEvents.MixerID', $mixerId)
			->order_by('TimelineEvents.EventTime', 'DESC')
			->limit($limit)
			->get();

		return $query->result();
	}

	public function getNewsFeedForCommunity($communityId, $limit = 25) {
		$allMemberIds = $this->communities->getArrayOfMemberIDs($this->communities->getCommunityMembersByGroup($communityId, 'member'));

		$query = $this->db
			->select('TimelineEvents.*')
			->select('Users.Username as Username')
			->select('Users.AvatarURL as AvatarURL')
			->from('TimelineEvents')
			->join('Users', 'Users.ID = TimelineEvents.MixerID')
			//->where('TimelineEvents.CommunityID', $communityId)
			->or_where_in('TimelineEvents.MixerID', $allMemberIds)
			->order_by('TimelineEvents.EventTime', 'DESC')
			->limit($limit)
			->get();
		return $query->result();
	}

	public function getNewsFeedForTeam($teamId, $limit = 25) {
		$allMemberIds = $this->communities->getArrayOfMemberIDs($this->teams->getTeamMembers($teamId));

		$query = $this->db
			->select('TimelineEvents.*')
			->select('Users.Username as Username')
			->select('Users.AvatarURL as AvatarURL')
			->from('TimelineEvents')
			->join('Users', 'Users.ID = TimelineEvents.MixerID')
			//->where('TimelineEvents.CommunityID', $communityId)
			->or_where_in('TimelineEvents.MixerID', $allMemberIds)
			->order_by('TimelineEvents.EventTime', 'DESC')
			->limit($limit)
			->get();
		return $query->result();
	}

	public function getNewsFeedForType($typeId, $limit = 10) {
		$query = $this->db
			->select('TimelineEvents.*')
			->select('Users.Username as Username')
			->select('Users.AvatarURL as AvatarURL')
			->from('TimelineEvents')
			->join('Users', 'Users.ID = TimelineEvents.MixerID')
			->where('TimelineEvents.Type', 'type')
			->where('TimelineEvents.TypeID', $typeId)
			->order_by('TimelineEvents.EventTime', 'DESC')
			->limit($limit)
			->get();

		return $query->result();
	}
}
?>