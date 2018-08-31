<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('getElapsedTimeString')) {
	function getElapsedTimeString($timestamp) {
		$elapsedTime = time() - strtotime($timestamp);

		// If under 10 seconds
		if ($elapsedTime < 10) {
			return "Just now!";
		}

		// If under one minute
		if ($elapsedTime < 60) {
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

if ( ! function_exists('createSlug')) {
	function createSlug($typeName) {
		$typeName = preg_replace('/[^a-zA-Z0-9\-\s]/', '', $typeName); // removes non-alphanumeric characters except space and dash
		$typeName = preg_replace('/[\-\s]/', '_', $typeName); // converts spaces and dashes to underscores
		return strtolower($typeName); // returns slugged version, lower case
	}
}