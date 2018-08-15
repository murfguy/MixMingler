<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('userListLink')) {
	function userListLink($params = array()) {

		$str = '<a href="/user/'.$params['Username'].'"';
		if (!empty($params['Tooltip'])) {
			$str .= 'data-toggle="tooltip" title="'.$params['Tooltip'].'"';	}
		$str .= '>';


		$str .= '<img src="'.$params['AvatarURL'].'" '.imgBackup('streamer').' class="avatar thin-border" width="25px" />';
		$str .= ' '.$params['Username'];
		$str .= '</a>';

		return $str;
	}
}

if ( ! function_exists('communityListLink')) {
	function communityListLink($community, $mod=false) {
		$str = '<a href="/community/'.$community->Slug;
		if ($mod) { $str.="/mod/"; }
		$str .= '"> '.$community->Name;
		$str .= '</a>';

		if (!empty($community->MemberStates)) {

			$states = getMemberStateBooleans($community->MemberStates);
				$str .= roleBadge('banned', $states['isBanned']);
				$str .= roleBadge('founder', $states['isFounder']);
				$str .= roleBadge('admin', $states['isAdmin']);
				$str .= roleBadge('moderator', $states['isModerator']);
				$str .= roleBadge('core', $states['isCore']);
		} 


		return $str;
	}
}

if ( ! function_exists('roleBadge')) {
	function roleBadge($role, $isRole) {
		$str = "";
		switch ($role) {
			case "founder";
				$str = ' <i class="fas fa-star" style="color: gold"></i>';
				break;
			case "admin";
				$str = ' <i class="fas fa-crown" style="color: gold"></i>';
				break;
			case "moderator":
				$str = ' <i class="fas fa-chess-knight" style="color: silver"></i>';
				break;
			case "core":
				$str = ' <i class="fas fa-user-astronaut"></i>';
				break;
			case "banned":
				$str = ' <i class="fas fa-ban" style="color: red"></i>';
				break;
		}

		if ($isRole) { return $str; }
		return null;
	}
}


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
			'hidden' => false
		];*/

		if (!empty($params)) {
			if (empty($params['state'])) {$params['state'] = 'primary'; }
			if (empty($params['confirm'])) {$params['confirm'] = false; }
			if (empty($params['displayType'])) {$params['displayType'] = 'text'; }
			if (empty($params['disabled'])) {$params['disabled'] = false; }
			if (empty($params['isHidden'])) {$params['isHidden'] = false; }


			$str = '<button';
				if ($params['disabled']) { 
					$str .=' disabled'; 
				}
				$str.= ' class="action btn';

				if ($params['confirm']) { $str .=' confirm'; }
				if ($params['isHidden']) { $str .=' isHidden'; }
				if (!empty($params['size'])) { $str .=' btn-'.$params['size']; }
				if (!empty($params['state'])) { $str .=' btn-'.$params['state']; }
				
			$str .= '"'; // end of class attribute

			if (!empty($params['action'])) { $str .=' action="'.$params['action'].'"'; }

			if (!empty($params['communityId'])) { $str .=' communityId="'.$params['communityId'].'"'; }
			if (!empty($params['userId'])) { $str .=' userId="'.$params['userId'].'"'; }
			if (!empty($params['typeId'])) { $str .=' typeId="'.$params['typeId'].'"'; }
			if (!empty($params['btnType'])) { $str .=' btnType="'.$params['btnType'].'"'; }

			if (!empty($params['tooltip'])) {
			$str .= ' data-toggle="tooltip" title="'.$params['tooltip'].'"';	}


			$str .= '>'; // end of <button>

			if ($params['displayType'] == 'icon') {
				$str .= '<i class="fas fa-'.$params['content'].'"></i>';
			} else {
				$str .= $params['content'];
			}


			$str .= '</button>';

		} else {
			$str .= '<button class="btn btn-outline-danger>No data</button>';
		}

		

		return $str;
	}
} // actionButton

if ( ! function_exists('imgBackup')) {
	function imgBackup($kind) {
		$backupCovers = array(
			'type' => "https://mixer.com/_latest/assets/images/main/types/default.jpg",
			'stream' => "https://mixer.com/_latest/assets/images/browse/thumbnail.jpg?f877a91",
			'streamer' => "https://mixer.com/_latest/assets/images/main/avatars/default.png",
			'community' => "/assets/graphics/covers/blankCover.png"
		);
		;
		return "onerror=\"this.src='".$backupCovers[$kind]."';\"";
	}
}

if ( ! function_exists('newsDisplay')) {
	function newsDisplay($newsData, $eventText, $size = 'med') {
		return "<p>$eventText</p>";
	}
}