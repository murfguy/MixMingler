<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function index()
	{
		//echo 'Hello World!';
	}

	public function _remap($method) {
		$this->load->database();
		$this->load->library('users');
		$this->load->library('news');
		$this->load->library('tools');

		if ($method != "index") {
			// Just want to rename this var for clarity's sake.
			$userToken = $method;

			// --------------------------------------------------------------------------------
			// Portion #1: Check if user is on Mingler and see if they need to be synced with Mixer.
			// --------------------------------------------------------------------------------
			$mixerData = null; // This for when we go to View. We only get Mixer Data if we determine it can be allowed.

			// Look in Mingler DB for this user. This is the default data set.
			$minglerData = $this->users->getUserFromMinglerByToken($userToken);

			if ($minglerData == null) {
				// User Token was not found. So we need to check Mixer next.
				$mixerData = $this->users->getUserFromMixer($userToken);

				// Now that we have the data from Mixer, let's double check Mingler in case this user changed their token.
				$minglerData = $this->users->getUserFromMingler($mixerData['id']);

				if ($minglerData == null) {
					// This user is NOT on Mingler AT ALL. Add them as an unregistered user.
					$this->users->addUser($mixerData);
					$this->users->syncUser($mixerData);

					// Now that they are added, let's finally get the proper data!
					$minglerData = $this->users->getUserFromMingler($mixerData['id']);
					// Let's note that they are now a newly synced person on MixMingler
					$this->news->addNews($minglerData->mixer_id, "{username} was first synced to MixMingler!", "mingler");
				} else {
					// We found the user, so they probably changed their info, so let's sync.
					$this->users->syncUser($mixerData);
				}
			} else {
				// We found this user, so let's see if they are eligible to do a basic sync with Mixer.
				$syncThreshold = time() - (1);
				$lastSync = strtotime($minglerData->lastSynced);

				if ($lastSync <= $syncThreshold) {
					// User can sync with Mixer now.
					$mixerData = $this->users->getUserFromMixer($userToken);
					$this->users->syncUser($mixerData);

					// Get updated data from Mingler
					$minglerData = $this->users->getUserFromMinglerByToken($userToken);
				}
			}

			$minglerData->lastTypeSlug = $this->types->createSlug($minglerData->lastType);

			$minglerData->lastStartElapsed = $this->tools->getElapsedTimeString(strtotime($minglerData->lastStreamStart));
			$minglerData->lastSeenElapsed = $this->tools->getElapsedTimeString(strtotime($minglerData->lastSeenOnline));		

			if (!empty($minglerData->followedTypes)) {
				$minglerData->followedTypesData = $this->types->getTypesByIdsFromMingler($minglerData->followedTypes);
			}


			// --------------------------------------------------------------------------------
			// Portion #2: Get Timeline Events for the current user using data from Mingler.
			// --------------------------------------------------------------------------------
			$feedData = null;
			$newsDisplayItems = null;
			$sql_query = "SELECT *, (SELECT name_token FROM mixer_users WHERE mixer_id=?) as username FROM `timeline_events` WHERE mixer_id=? ORDER BY id DESC, eventTime DESC LIMIT 0,100";
			$query = $this->db->query($sql_query, array($minglerData->mixer_id, $minglerData->mixer_id));
			$feedData = $query->result();

			// We need to get the HTML version of these events so we can display them in the view.
			if ($feedData != null) {
				$newsDisplayItems = array();
				foreach($feedData as $event) {
					$newsDisplayItems[] = $this->news->getNewsDisplay($event, $minglerData->avatarURL, "condensed");
				}
			}

			// --------------------------------------------------------------------------------
			// Portion #3: Get Communities Data for the current user
			// --------------------------------------------------------------------------------

			$communitiesData = new stdClass();
			$communitiesData->core = null;
			$communitiesData->joined = null;
			$communitiesData->followed = null;
			$communitiesData->core = null;

			if (!empty($minglerData->coreCommunities)) {
				$communitiesData->core = $this->communities->getCommunitiesFromList($minglerData->coreCommunities);
			} 
			if (!empty($minglerData->joinedCommunities)) {
				$communitiesData->joined = $this->communities->getCommunitiesFromList($minglerData->joinedCommunities);
			} 
			if (!empty($minglerData->followedCommunities)) {
				$communitiesData->followed = $this->communities->getCommunitiesFromList($minglerData->followedCommunities);
			} 


			// --------------------------------------------------------------------------------
			// Portion #4: Prep Data, and Display in Single User view
			// --------------------------------------------------------------------------------

			$displayData = new stdClass();
			$displayData->mixerData = $mixerData;
			$displayData->minglerData = $minglerData;
			$displayData->communitiesData = $communitiesData;
			$displayData->feedData = $feedData;
			$displayData->newsItems = $newsDisplayItems;
			$displayData->recentTypes = $this->users->getUsersRecentStreamTypes($minglerData->mixer_id);
			$this->displayUser($displayData);


		} else {
			$this->displayUsers();
		}
	}

	private function displayUsers() {
			$displayData = new stdClass();

			$sql_query = "SELECT *, (CHAR_LENGTH(joinedCommunities) - IF(joinedCommunities!='',(CHAR_LENGTH(REPLACE(joinedCommunities,',',''))-1),0)) as joinedCount, (CHAR_LENGTH(followedCommunities) - IF(followedCommunities!='',(CHAR_LENGTH(REPLACE(followedCommunities,',',''))-1),0)) as followedCount FROM mixer_users WHERE registered=1 AND lastSeenOnline>DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY lastStreamStart DESC";
			$query = $this->db->query($sql_query);
			$displayData->regStreamers = $query->result();

			foreach ($displayData->regStreamers as $streamer) { 
				$streamer->lastStartElapsed = $this->tools->getElapsedTimeString(strtotime($streamer->lastStreamStart));
				$streamer->lastSeenElapsed = $this->tools->getElapsedTimeString(strtotime($streamer->lastSeenOnline));
			}

			$sql_query = "SELECT * FROM mixer_users WHERE registered=0 AND lastSeenOnline>DATE_SUB(NOW(), INTERVAL 30 MINUTE) ORDER BY lastStreamStart DESC";
			$query = $this->db->query($sql_query);
			$displayData->nonRegStreamers = $query->result();

			foreach ($displayData->nonRegStreamers as $streamer) { 
				$streamer->lastStartElapsed = $this->tools->getElapsedTimeString(strtotime($streamer->lastStreamStart));
				$streamer->lastSeenElapsed = $this->tools->getElapsedTimeString(strtotime($streamer->lastSeenOnline));
			}

			$this->load->view('htmlHead');
			$this->load->view('users', $displayData);

			$this->load->library('version');
			$this->load->view('htmlFoot', $this->version->getVersion());
	}

	private function displayUser($user) {
		$this->load->view('htmlHead');

		$this->load->view('user', $user);

		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}
}
?>