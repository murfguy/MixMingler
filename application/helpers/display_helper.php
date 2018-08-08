<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('card')) {
	function card($params = array()) {
			if (empty($params)) {
				$str = '<div class="typeInfo sml">';
				$str .= "<p>Bad card data.</p>";
			} else {
				if (empty($params['size'])) { 
					$str = '<div class="typeInfo">';
				} else {
					$str = '<div class="typeInfo '.$params['size'].'">';
				}

				$backupCovers = array(
					'type' => "https://mixer.com/_latest/assets/images/main/types/default.jpg",
					'stream' => "https://mixer.com/_latest/assets/images/browse/thumbnail.jpg?f877a91",
					'streamer' => "https://mixer.com/_latest/assets/images/main/avatars/default.png",
					'community' => "/assets/graphics/covers/blankCover.png",
					//'stream' => "/assets/graphics/blankThumb.jpg",
					//'streamer' => "/assets/graphics/blankAvatar.png",
				);

				$str .= '<a href="'.$params['url'].'"><img src="'.$params['cover'].'" onerror="this.onerror=null;this.src=\''.$backupCovers[$params['kind']].'\';" class="coverArt" /></a>';
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

if ( ! function_exists('action_button')) {
	function action_button($params) {
		/*$params = [
			'state' => 'success',
			'confirm' => true,
			'action' => 'promoteMember',
			'disabled' => true,
			'communityId' => 1,
			'userId' => 255317,
			'typeId' => 70323,
			'btnType' => 'mini',
			'displayType' => 'icon',
			'content' => 'chess-knight'
		];*/

		if (!empty($params)) {
			if (empty($params['state'])) {$params['state'] = 'primary'; }
			if (empty($params['confirm'])) {$params['confirm'] = false; }
			if (empty($params['displayType'])) {$params['displayType'] = 'text'; }
			if (empty($params['disabled'])) {$params['disabled'] = false; }

			$str = '<button';
				if ($params['disabled']) { 
					$str .=' disabled class="btn'; 
				} else {
					$str.= ' class="action btn';
				}

				if ($params['confirm']) { $str .=' confirm'; }
				if (!empty($params['size'])) { $str .=' btn-'.$params['size']; }
				if (!empty($params['state'])) { $str .=' btn-'.$params['state']; }
			$str .= '"'; // end of class attribute

			$str .=' action="'.$params['action'].'"';

			if (!empty($params['communityId'])) { $str .=' communityId="'.$params['communityId'].'"'; }
			if (!empty($params['userId'])) { $str .=' userId="'.$params['userId'].'"'; }
			if (!empty($params['typeId'])) { $str .=' typeId="'.$params['typeId'].'"'; }
			if (!empty($params['btnType'])) { $str .=' btnType="'.$params['btnType'].'"'; }


			$str .= '>'; // end of <button>

			if ($params['displayType'] == 'icon') {
				$str .= '<i class="fas fa-'.$params['content'].'"></i>';
			} else {
				$str .= $content;
			}


			$str .= '</button>';

		} else {
			$str .= '<button class="btn btn-outline-danger>No data</button>';
		}

		

		return $str;
	}
} // actionButton

if ( ! function_exists('newsDisplay')) {
	function newsDisplay($params = array()) {
		if (!empty($params)) {

		} else {
			return '<p class="display-error">Bad news data.</p>';
		}
	}
}