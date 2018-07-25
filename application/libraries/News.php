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
	}

	public function addNews($mixer_id, $eventText, $eventType, $extraVars = "") {
		$sql_query = "INSERT INTO timeline_events (mixer_id, eventText, eventType, extraVars) VALUES (?, ?, ?, ?)";
		$values = array($mixer_id, $eventText, $eventType, $extraVars);
		$query = $this->CI->db->query($sql_query, $values);
	}

	public function getTypeNewsFeed($typeId) {
		$sql_query = "SELECT *, (SELECT name_token FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) as username FROM timeline_events WHERE eventType='type' AND extraVars=? ORDER BY id DESC LIMIT 0, 10";
		$query = $this->CI->db->query($sql_query, array($typeId));

		return $query->result();
	}

	public function getCommunityNewsFeed($max = 10) {
		$sql_query = "SELECT *, (SELECT name_token FROM mixer_users WHERE mixer_users.mixer_id=timeline_events.mixer_id) as username FROM timeline_events WHERE mixer_id IN (276998,265097,273268,205053,222346,255317,217203,2333,249896,534267,261799,280222,13163285,462135,35942,6114513) ORDER BY id DESC LIMIT 0,$max";
		$query = $this->CI->db->query($sql_query);

		return  $query->result();
	}

	public function getNewsInsertQueryDataArray ($mixer_id, $eventText, $eventType, $extraVars = "") {
		$query_data = array(
			'mixer_id' => $mixer_id,
			'eventTime' => date('Y-m-d H:i:s'),
			'eventText' => $eventText,
			'eventType' => $eventType,
			'extraVars' => $extraVars
		);
		return $query_data;
	}

	public function getNewsDisplay($newsEvent, $avatar = "", $size="normal") {
		// Convert string bits

		// Add username link to item.
		$eventText = str_replace("{username}", "<a href=\"/user/$newsEvent->username\">$newsEvent->username</a>", $newsEvent->eventText); 
		// Convert a {commId} string
		$eventText = $this->convertCommunityString($eventText);
		// Convert a {typeId} string
		$eventText = $this->convertTypeString($eventText);

		
		switch ($size) {
			case "normal":
			default:
				$newsContainer = "<div class=\"userFeedItem\">";
				$newsContainer .= "<div class=\"feedItemHeader\">";
					$newsContainer .= "<img src=\"$avatar\" class=\"avatar thin-border\" width=\"42\" />";
					$newsContainer .=  "<p class=\"postHead\"><a href=\"/user/$newsEvent->username\">$newsEvent->username</a><br><span class=\"postTime\">".$this->displayPostTime($newsEvent->eventTime)."</span></p>";
				$newsContainer .= "</div>";
				$newsContainer .= "<p class=\"post\">$eventText</p>";

				$newsContainer .= "</div>";
				break;

			
			case "condensed":
				$newsContainer = "<div class=\"userFeedItem condensedNews\">";
					$newsContainer .= "<p class=\"post\">$eventText</p>";
					$newsContainer .=  "<p class=\"postHead\"><span class=\"postTime\">".$this->displayPostTime($newsEvent->eventTime)."</span></p>";
				$newsContainer .= "</div>";
				break;

			case "mini":
				$newsContainer = "<div class=\"userFeedItem miniNews\">";
				$newsContainer .= "<p class=\"post\">$eventText</p>";
					$newsContainer .= "<div class=\"feedItemHeader\">";
						$newsContainer .=  "<p class=\"postHead\"><span class=\"postTime\">".$this->displayPostTime($newsEvent->eventTime)."</span></p>";
					$newsContainer .= "</div>";

				$newsContainer .= "</div>";
				break;

		}

		return $newsContainer;
	}

	public function getEventString($event, $params = "") {
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
			$communityLink = "<a href=\"/community/$community->slug\">".$community->long_name."</a>";

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
				$typeLink = "<a href=\"/type/$type->typeId/$type->slug\">".$type->typeName."</a>";

				$eventText = str_replace("{typeId:".$id."}", $typeLink, $eventText);
			} else {
				$eventText = str_replace("{typeId:".$id."}", "<span class=\"mixBlue\">[Unknown Stream Type]</span>", $eventText);
			}
		} 
		
		return $eventText;
	}
}
?>