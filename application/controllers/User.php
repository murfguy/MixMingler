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
			
			$minglerData = $this->users->syncUser($this->users->getUserFromMixer($userToken));

			$minglerData->LastTypeSlug = createSlug($minglerData->LastType);

			$minglerData->LastStartElapsed = getElapsedTimeString($minglerData->LastStreamStart);
			$minglerData->LastSeenElapsed = getElapsedTimeString($minglerData->LastSeenOnline);	


			// --------------------------------------------------------------------------------
			// Portion #4: Prep Data, and Display in Single User view
			// --------------------------------------------------------------------------------

			$displayData = new stdClass();



			$displayData->member = $minglerData;
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