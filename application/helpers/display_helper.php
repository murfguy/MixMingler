<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('test_method')) {
    function test_method($var = '') {
        return $var;
    }   
}

if ( ! function_exists('card')) {
	function card($params = array()) {
		// This is a test placeholder
		/*$params = array(
			'id' => '70323',
			'name' => 'Fortnite',
			'size' => 'lrg',
			'kind' => 'type',
			'url' => '/type/70323/fortnite',
			'stats' => array(
				'viewers' => '3456',
				'online' => '4567',
				'members' => '12',
				'views' => '12346',
				'followers' => '762',
			),
			'cover' => 'https://gameart.mixer.com/art/70323/cover.jpg?locked'
		);*/
	

			

		$str = '<div class="typeInfo '.$params['size'].'">';
			if (empty($params)) {
				$str .= "<p>Bad card data.</p>";
			} else {
				if (empty($params['cover'])) {
					switch ($params['kind']) {
						case "type":
							$params['cover'] = "https://mixer.com/_latest/assets/images/main/types/default.jpg";
							break;
					}
				}	

				$str .= '<a href="'.$params['url'].'"><img src="'.$params['cover'].'" class="coverArt" /></a>';
				$str .= '<p class="typeName"><a href="'.$params['url'].'">'.$params['name'].'</a></p>';
			

				if (!empty($params['stats'])) {
					$str .= '<p class="stats">';

					$stats = "";
					foreach ($params['stats'] as $key => $value) {
						if ($stats != '') { $stats .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; }
						$stats .= '<span data-toggle="tooltip" title="';
						switch ($key) {
							case "online":
								$stats .= 'Streams"><i class="fas fa-play-circle"></i> '.$params['stats']['online'];
								break;

							case "members":
								$stats .= 'Members"><i class="fas fa-users"></i> '.$params['stats']['members'];
								break;

							case "viewers":
								$stats .= 'Current Viewers"><i class="fas fa-eye"></i> '.$params['stats']['viewers'];
								break;

							case "views":
								$stats .= 'Total Views"><i class="fas fa-eye"></i> '.$params['stats']['views'];
								break;

							case "followers":
								$stats .= 'Followers"><i class="fas fa-heart"></i> '.$params['stats']['followers'];
								break;
						} //switch ($key)
						$stats .= '</span>';
					} // foreach ($params['stats'])


					$str .= $stats;
					$str .= '</p>';
				} // if (!empty($params['stats']))

			} // if (empty($params))

			
		$str .= '</div>';

		return $str;
	}
}