<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('test_method')) {
    function test_method($var = '') {
        return $var;
    }   
}

if ( ! function_exists('card')) {
	function card($params = array()) {
		// This is a test placeholder
		$params = array(
			'id' => '70323',
			'name' => 'Fortnite',
			'size' => 'med',
			'kind' => 'type',
			'url' => '/type/70323/fortnite',
			'viewers' => '3456',
			'online' => '4567',
			'members' => '12',
			'views' => '12346',
			'cover' => 'https://gameart.mixer.com/art/70323/cover.jpg?locked'
		);

		$str = '<div class="typeInfo '.$params['size'].'">';
			$str .= '<a href="'.$params['url'].'"><img src="'.$params['cover'].'" class="coverArt" /></a>';
			$str .= '<p class="typeName"><a href="'.$params['url'].'">'.$params['name'].'</a></p>';
			$str .= '<p class="stats">';

			$stats = "";
			if (!empty($params['online'])) { 
				if ($stats != '') { $stats .= '&nbsp;&nbsp;&nbsp;'; } 
				$stats .= '<i class="fas fa-play-circle"></i> '.$params['online']; }

			if (!empty($params['viewers'])) { 
				if ($stats != '') { $stats .= '&nbsp;&nbsp;&nbsp;'; } 
				$stats .= '<i class="fas fa-eye"></i> '.$params['viewers']; }

			$str .= $stats;
			
			$str .= '</p>';
		$str .= '</div>';

		return $str;
	}
}