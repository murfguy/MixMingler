<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getMemberStateBooleans')) {
	function getMemberStateBooleans($states) {
		$states_array = explode(",", $states);
		return array(
			'isAdmin' => in_array('admin', $states_array),
			'isBanned' => in_array('banned', $states_array),
			'isCore' => in_array('core', $states_array),
			'isFounder' => in_array('founder', $states_array),
			'isFollower' => in_array('follower', $states_array),
			'isMember' => in_array('member', $states_array),
			'isModerator' => in_array('moderator', $states_array),
			'isPending' => in_array('pending', $states_array));
	}
}

if (! function_exists('createCommunityObjects')) {
	function createCommunityObjects($communitiesData) {
		$communities = new stdClass();
		$communities->admin = array();
		$communities->banned = array();
		$communities->core = array();
		$communities->founder = array();
		$communities->follower = array();
		$communities->member = array();
		$communities->moderator = array();
		$communities->pending = array();
		$communities->manager = array();

		foreach($communitiesData as $community) {
			$states = explode(",", $community->MemberStates);
				if (in_array('admin', $states)) { $communities->admin[] = $community; }
				if (in_array('banned', $states)) { $communities->banned[] = $community; }
				if (in_array('core', $states)) { $communities->core[] = $community; }
				if (in_array('founder', $states)) { $communities->founder[] = $community; }
				if (in_array('follower', $states)) { $communities->follower[] = $community; }
				if (in_array('member', $states)) { $communities->member[] = $community; }
				if (in_array('moderator', $states)) { $communities->moderator[] = $community; }
				if (in_array('pending', $states)) { $communities->pending[] = $community; }

				if (in_array('admin', $states) || in_array('moderator', $states)) {
					$communities->manager[] = $community;}
		}

		return $communities;

	}
}

if (! function_exists('getTypeIDList')) {
	function getTypeIDList($typesData, $seperator = ";") {
		$typeIDList = array();
		foreach ($typesData as $type) {
			$typeIDList[] = $type->ID;
		}

		return implode($seperator, $typeIDList);
	}
}