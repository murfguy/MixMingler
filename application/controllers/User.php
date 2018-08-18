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
					$this->users->addNewUser($mixerData);
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
				$syncThreshold = time() - (60);
				$lastSync = strtotime($minglerData->LastSynced);

				if ($lastSync <= $syncThreshold) {
					// User can sync with Mixer now.
					$mixerData = $this->users->getUserFromMixer($userToken);
					$this->users->syncUser($mixerData);

					// Get updated data from Mingler
					$minglerData = $this->users->getUserFromMinglerByToken($userToken);
				}
			}

			$minglerData->LastTypeSlug = createSlug($minglerData->LastType);

			$minglerData->LastStartElapsed = getElapsedTimeString($minglerData->LastStreamStart);
			$minglerData->LastSeenElapsed = getElapsedTimeString($minglerData->LastSeenOnline);	


			// --------------------------------------------------------------------------------
			// Portion #4: Prep Data, and Display in Single User view
			// --------------------------------------------------------------------------------

			$displayData = new stdClass();
			//$displayData->minglerData = $minglerData;
			//$displayData->communities = $communities;
				//$displayData->feedData = $feedData;
				//$displayData->newsItems = $newsDisplayItems;


			$displayData->member = $minglerData;
			$displayData->mixerData = $mixerData;
			$displayData->types = $this->users->getUserTypesInformation($minglerData->ID);
			$displayData->communities = createCommunityObjects($this->users->getUsersCommunitiesInformation($minglerData->ID));
			$displayData->recentTypes = $this->users->getUsersRecentStreamTypes($minglerData->ID);
			//$displayData->news = $this->users->getUserTimeline($minglerData->ID);

			$this->displayUser($displayData);


		} else {
			$this->displayUsers();
		}
	}

	private function displayUsers() {
			$displayData = new stdClass();

			$sql_query = "SELECT * FROM Users WHERE isRegistered=1 AND LastSeenOnline>DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY LastStreamStart DESC";
			$query = $this->db->query($sql_query);
			$displayData->regStreamers = $query->result();

			foreach ($displayData->regStreamers as $streamer) { 
				$streamer->LastStartElapsed = getElapsedTimeString($streamer->LastStreamStart);
				$streamer->LastSeenElapsed = getElapsedTimeString($streamer->LastSeenOnline);
			}

			$sql_query = "SELECT * FROM Users WHERE isRegistered=0 AND LastSeenOnline>DATE_SUB(NOW(), INTERVAL 30 MINUTE) ORDER BY LastStreamStart DESC";
			$query = $this->db->query($sql_query);
			$displayData->nonRegStreamers = $query->result();

			foreach ($displayData->nonRegStreamers as $streamer) { 
				$streamer->LastStartElapsed = getElapsedTimeString($streamer->LastStreamStart);
				$streamer->LastSeenElapsed = getElapsedTimeString($streamer->LastSeenOnline);
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