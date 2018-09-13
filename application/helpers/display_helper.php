<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('userListLink')) {
	function userListLink($params = array()) {

		$str = '<img src="'.$params['AvatarURL'].'" '.imgBackup('streamer').' class="avatar thin-border" width="25px" />';

		$str .= '<a href="/user/'.$params['Username'].'"';
		if (!empty($params['Tooltip'])) {
			$str .= 'data-toggle="tooltip" title="'.$params['Tooltip'].'"';	}
		$str .= '>';
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
	// Required Parameters
		// url
		// cover
		// kind
		// name
	// optional parameters
		// size
		// stats
			//.online
			//.members
			//.viewers
			//.views
			//.followers

	function card($params = array()) {
		if (empty($params)) {
			$str = '<div class="typeInfo sml">';
				$str .= "<p>Bad card data.</p>";
			$str .= '</div>';
		} else {
			if (empty($params['category'])) { $params['category']= ""; }
			if (empty($params['size'])) { $params['size'] = 'med'; }
			if (empty($params['followState'])) { $params['followState'] = "none"; }

			if (!empty($params['stats'])) {
				$stats = "";
				$lastStat = "";
				foreach ($params['stats'] as $key => $value) {

					if ($params['size'] != 'xsm') {
						if ($stats != '' && $lastStat != 'currentType') { $stats .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; }

						$lastStat = $key;
						$stats .= '<span class="miniStat" data-toggle="tooltip" title="';
						switch ($key) {
							case "currentType":
								$stats .= 'Currently Streaming"><i class="fas fa-play-circle"></i> '.$params['stats']['currentType'].'<br>';
								break;

							case "online":
								$stats .= 'Streams Online"><i class="fas fa-play-circle"></i> '.number_format($params['stats']['online']);
								break;

							case "streamCount":
								$stats .= 'Times Streamed"><i class="fas fa-play-circle"></i> '.number_format($params['stats']['streamCount']);
								break;

							case "members":
								$stats .= 'Members"><i class="fas fa-users"></i> '.number_format($params['stats']['members']);
								break;

							case "viewers":
								$stats .= 'Current Viewers"><i class="fas fa-eye"></i> '.number_format($params['stats']['viewers']);
								break;

							case "views":
								$stats .= 'Total Views"><i class="fas fa-eye"></i> '.number_format($params['stats']['views']);
								break;

							case "followers":
								$stats .= 'Followers"><i class="fas fa-heart"></i> '.number_format($params['stats']['followers']);
								break;
						} //switch ($key)
						$stats .= '</span>';
					} else {
						if ($stats != '') { $stats .= '<br>'; }
						switch ($key) {
							case "online":
								$stats = $params['stats']['online'].' streams online.';
								break;

							case "streamCount":
								$stats = 'Streamed '.$params['stats']['streamCount'].' times';
								break;
						} //switch ($key)
					} 
				}  // foreach
			}// if params['stats']

			$str = '<div class="typeInfo';
				$str.= ' '.$params['size'];
				$str.= ' '.lcfirst($params['category']);
				if (!empty($params['extraClasses'])) {  
					foreach ($params['extraClasses'] as $class) {
						$str.= ' '.$class;	}}

				if (in_array($params['followState'], ["followed", "ignored"])) {  
					$str .= ' '.$params['followState']; }

			$str .= '" ';
			if ($params['size'] == 'xsm') {
				$str .= 'data-toggle="tooltip" data-placement="top" data-html="true" title="'.$params['name']."<br>".$stats.'"';
			} elseif (!empty($params['tooltip'])) {
				$str .= 'data-toggle="tooltip" data-placement="top" data-html="true" title="'.$params['tooltip'].'"';
			}

			$str .= '>';

			if (isset($_SESSION['mixer_id']) && !empty($params['typeid'])) {
				$str .= '<div class="btnGroupContainer">';
				$str .= '<div class="cardActionButtons btn-group d-flex" role="group">';

				switch ($params['followState']) {
					case "followed":
						$str .= '<button type="button" btnType="mini" class="action no-alert btn btn-danger" action="unfollowType" userid="'.$_SESSION['mixer_id'].'" typeid="'.$params['typeid'].'" data-toggle="tooltip" title="Unfollow" ><i class="fas fa-thumbs-down"></i></button>';
						$str .= '<button type="button" btnType="mini" class="action btn btn-warning" action="ignoreType" userid="'.$_SESSION['mixer_id'].'" typeid="'.$params['typeid'].'" data-toggle="tooltip" title="Ignore" style="display:none;"><i class="fas fa-ban"></i></button>';
						break;

					case "ignored":
						$str .= '<button type="button" btnType="mini" class="action no-alert btn btn-primary" action="followType" userid="'.$_SESSION['mixer_id'].'" typeid="'.$params['typeid'].'" data-toggle="tooltip" title="Follow" style="display:none;"><i class="fas fa-thumbs-up"></i></button>';
						$str .= '<button type="button" btnType="mini" class="action no-alert btn btn-danger" action="unignoreType" userid="'.$_SESSION['mixer_id'].'" typeid="'.$params['typeid'].'" data-toggle="tooltip" title="Unignore"><i class="fas fa-thumbs-down"></i></button>';
						break;

					default:
						$str .= '<button type="button" btnType="mini" class="action no-alert btn btn-primary" action="followType" userid="'.$_SESSION['mixer_id'].'" typeid="'.$params['typeid'].'" data-toggle="tooltip" title="Follow"><i class="fas fa-thumbs-up"></i></button>';
						$str .= '<button type="button" btnType="mini" class="action no-alert btn btn-warning" action="ignoreType" userid="'.$_SESSION['mixer_id'].'" typeid="'.$params['typeid'].'" data-toggle="tooltip" title="Ignore"><i class="fas fa-ban"></i></button>';
						break;
				}
				$str .= '</div>';
				$str .= '</div>';
			}

			$str .= '<a href="'.$params['url'].'"><img src="'.$params['cover'].'" '.imgBackup($params['kind']).'class="coverArt" /></a>';
			
			
			if ($params['size'] != 'xsm') {
				$str .= '<p class="typeName"><a href="'.$params['url'].'">'.$params['name'].'</a></p>';
				if (!empty($params['type'])) { $str .= '<p class="subName">'.$params['type']->name.'</p>'; }
				$str .= '<p class="stats">'.$stats.'</p>'; }

			$str .= '</div>'; 
		}

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
			'community' => "/assets/graphics/covers/blankCover.png",
			'team' => "https://mixer.com/_latest/assets/images/teams/logo.png?5b11d2c58"
		);
		;
		return "onerror=\"this.src='".$backupCovers[$kind]."';\"";
	}
}

if ( ! function_exists('newsDisplay')) {
	function newsDisplay($newsData, $eventText, $size = 'med') {
		switch ($size) {
			case "lrg":
				$avatarSize = '40';
				break;

			case "med":
			default:
				$avatarSize = '30';
				break;
		}


		$post = '<p class="post">'.$eventText.'</p>';
		$time = '<p class="postTime">'.getElapsedTimeString($newsData->EventTime).'</p>';
		$avatar = '<img src="'.$newsData->AvatarURL.'" '.imgBackup('streamer').' class="avatar thin-border newsAvatar" width="'.$avatarSize.'" />';


		$str = '<div class="event';
		$str .= '">';
			if (in_array($size, ['med', 'lrg'])) {
				$str .= $avatar;
			}

			$str .= $post;
			$str .= $time;
		$str .= '</div>';

		return $str;
	}
}

if (!function_exists('devNotes')) {
	function devNotes($view = null) {
		if ($view != null) {

			$v = array(
				'0.2'=>'v0.2-Communities',
				'0.3'=>'v0.3-Communities',
				'0.4'=>'v0.4-Streamers',
				'0.5'=>'v0.5-Not Even My Final Form',
				'1.0'=>'v1.0-Release Version',
				'1.x'=>'v1.x-Undetermined Version');

			$devNotes = array();
			//Login/Landing Page
			$devNotes[] = array('view'=>'login', 'version'=>$v['0.4'], 'note'=>'Showcase a random selection of streamers and communities');

			//Home Page
			//$devNotes[] = array('view'=>'main', 'version'=>$v['0.3'], 'note'=>'View event feeds for followed communities.');
			//$devNotes[] = array('view'=>'main', 'version'=>$v['0.3'], 'note'=>'View personal event feed.');

			//All Users
			$devNotes[] = array('view'=>'users', 'version'=>$v['0.4'], 'note'=>'Showcase followed streamers who are online.');
			$devNotes[] = array('view'=>'users', 'version'=>$v['0.4'], 'note'=>'Implement a form of filtering and/or smart suggestions.');

			//Single User
			$devNotes[] = array('view'=>'user', 'version'=>$v['0.4'], 'note'=>'Add icons to indicate special community membership states.');
			$devNotes[] = array('view'=>'user', 'version'=>$v['0.4'], 'note'=>'Showcase teams.');

			//Types Page
			//$devNotes[] = array('view'=>'types', 'version'=>$v['0.3'], 'note'=>'Default to All Online view if no followed games are online.');
			$devNotes[] = array('view'=>'types', 'version'=>$v['0.5'], 'note'=>'Search field to find offline games.');

			//Single Type
			$devNotes[] = array('view'=>'type', 'version'=>$v['0.5'], 'note'=>'Overhaul design to smartly adapt if streamer count is low, or non-existant.');

			//Community List
			$devNotes[] = array('view'=>'communities', 'version'=>$v['0.5'], 'note'=>'Update type filter UI.');

			//Single Community
			//$devNotes[] = array('view'=>'community', 'version'=>$v['0.3'], 'note'=>'General design overhaul.');
			//$devNotes[] = array('view'=>'community', 'version'=>$v['0.3'], 'note'=>'Implement news feed for community only information (joins, moderators, etc).');
			//$devNotes[] = array('view'=>'community', 'version'=>$v['0.3'], 'note'=>'Implement news feed for that showcases all info related to members.');

			//Community Admin
			$devNotes[] = array('view'=>'community-admin', 'version'=>$v['0.4'], 'note'=>'Implement summary view.');
			//$devNotes[] = array('view'=>'community-admin', 'version'=>$v['0.3'], 'note'=>'Implement settings panel.');
			//$devNotes[] = array('view'=>'community-admin', 'version'=>$v['0.3'], 'note'=>'Return to community link/button.'); 

			//Account Management
			$devNotes[] = array('view'=>'account', 'version'=>$v['0.4'], 'note'=>'Implement user summary view.');
			$devNotes[] = array('view'=>'account', 'version'=>$v['0.5'], 'note'=>'Implement user specific settings.');


			$notes = "";
			foreach ($devNotes as $note) {
				if ($note['view'] == $view) {
					$notes .= $note['version']." | ".$note['note']."<br>";
				}
			}

			if (!empty($notes)) {
				return '<button type="button" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="bottom" data-html="true" title="'.$notes.'">Hover for DevNotes</button>';
			}
		}

		return null;
	}
}


if (! function_exists('devNotesButton')) {
	function devNotesButton($devNotes = null) {
		if ($devNotes == null) {
			return null;
		}

		$str = '<button type="button" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="bottom" data-html="true" title="';
		foreach ($devNotes as $note) {
			$str .= $note[0]." | ".$note[1]."<br>";
		}
		$str .= '"">Hover for DevNotes</button>';

		return $str;
	}
}

if (! function_exists('settingSelection')) {
	function settingSelection($data) {
		//$settings =$data['values'][$data['name']];
		$key = $data['name'];

		if (array_key_exists ($key, $data['values'])) {
			$isChecked = $data['values']->{$key};
		} else {
			// set default check state by group
			switch ($data['group']) {
				case "UserCommunications":
					$isChecked = true;
					break;

				default:
					$isChecked = false;
					break;
			}
		}


		$str = "<tr>";
		$str .= "<td>".form_label($data['summary'], $data['name'])."</td>";

			$formData = array(
				'name'          => $data['name'],
		        'id'            => $data['name'],
		        'class'			=> 'changeSettings '.$data['group'],
		        'value'         => '1',
		        'checked'       => $isChecked
			);

		$str .= "<td>".form_checkbox($formData)."</td>";
		$str .= "</tr>";

		return $str;
	}
}

if (! function_exists('streamerTable')) {
	function streamerTable($ajaxParams = "") {
		if (!empty($ajaxParams)) {
			$parameters = array();
			foreach ($ajaxParams as $key => $param) {
				//$parameters[] = 'data-'.$key.'="'.$param.'"';
				if ($key != "id") {
					$parameters[] = "data-$key=\"$param\"";
				} else {
					$parameters[] = "id=\"$param\"";
				}
			}

			$str = '<table class="table table-dark table-striped userList fetchStreamerList" '.implode(" ", $parameters).'>';

			//$str = '<table class="table table-dark table-striped userList fetchStreamerList" data-grouptype="'.$ajaxParams['type'].'" data-groupid="'.$ajaxParams['id'].'">';
		} else {
			$str = '<table class="table table-dark table-striped userList fetchStreamerList">';
		}

			$str .= '<thead><tr>';
				$str .= '<th width="20%">User</th>';
				$str .= '<th width="10%">Stream Count</th>';
				$str .= '<th width="20%">Last Streamed</th>';
				$str .= '<th width="30%">Last Game</th>';
				$str .= '<th width="10%">Followers</th>';
				$str .= '<th width="10%">Views</th>';
			$str .= '</tr></thead>';
			$str .= '<tbody>';
				$str .= '<tr class="pendingResults">';
					$str .= '<td colspan="6"><i class="fas fa-spinner fa-pulse"></i> Fetching streamers. One moment please.</td>';
				$str .= '</tr>';
			$str .= '</tbody>';
		$str .= '</table>';
		return $str;
	}
}