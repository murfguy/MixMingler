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