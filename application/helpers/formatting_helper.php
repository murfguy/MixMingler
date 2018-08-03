<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('removeValueFromList')) {
	function removeValueFromList($value, $list) {
		//Convert list list to PHP array
		$array = explode(",", $list);
		
		// Remove the value from the array
		if (($key = array_search($value, $array)) !== false) { unset($array[$key]); }
		
		// Restore to string.list and return
		return implode(',', $array);
	}
}

if ( ! function_exists('valueIsInList')) {
	function valueIsInList($value, $list) {
		$array = explode(",", $list);
		if (($key = array_search($value, $array)) !== false) { 
			return true;
		}
		return false;
	}
}

if ( ! function_exists('getElapsedTimeString')) {
	function getElapsedTimeString($timestamp) {
		$elapsedTime = time() - $timestamp;

		// If under one minute
		if ($elapsedTime < 60) {
			if ($elapsedTime == 1) {
				return $elapsedTime." second ago";
			}
			return $elapsedTime." seconds ago";
		}

		// If under one hour
		if ($elapsedTime < 60 * 60) {
			if (ceil($elapsedTime/60) == 1) {
				return ceil($elapsedTime/60)." minute ago";
			} 
			return ceil($elapsedTime/60)." minutes ago";
		}

		// If under one day ago
		if ($elapsedTime < 60 * 60 * 24) {
			if (ceil($elapsedTime/(60*60)) == 1) {
				return ceil($elapsedTime/(60*60))." hour ago";
			} 
			return ceil($elapsedTime/(60*60))." hours ago";
		}

		// If over 24 hours
		if ($elapsedTime >= 60 * 60 * 24) {
			if (ceil($elapsedTime/(60*60)) == 1) {
				return ceil($elapsedTime/(60*60*24))." day ago";
			} 
			return number_format(ceil($elapsedTime/(60*60*24)))." days ago";
		}
	}
}