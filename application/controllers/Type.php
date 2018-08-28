<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Type extends CI_Controller {

	public function index()
	{
		//echo 'Hello World!';
		echo "<h1>Coming Soon!</h1>";
	}

	public function _remap($method, $params = array()) {
		$this->load->database();
		$this->load->library('users');
		$this->load->library('news');
		$this->load->library('tools');
		$this->load->library('types');
		$this->load->library('version');

		// if user is logged in
		if (isset($_SESSION['mixer_user'])) {
			$currentUser = new stdClass();
			$currentUser->username = $_SESSION['mixer_user'];
			$currentUser->followsType = false;
			$currentUser->ignoresType = false;

			$user = $this->users->getUserFromMingler($_SESSION['mixer_id']);
			$followedTypes = $this->users->getUserTypesInformation($_SESSION['mixer_id'], 'followed');
			$ignoredTypes = $this->users->getUserTypesInformation($_SESSION['mixer_id'], 'ignored');

			$followedTypeIDs = getTypeIDList($followedTypes);
			$ignoredTypeIDs = getTypeIDList($ignoredTypes);

			$currentUser->followedTypeIDs = $followedTypeIDs;
			$currentUser->ignoredTypeIDs = $ignoredTypeIDs;

			$currentUser = $currentUser;
		} else {
			$currentUser = null;
		}


		if ($method != "index") {
			// use type slug to find display type

			if (ctype_digit($method)) {
				// This is a type id input
				$typeData = $this->types->getTypeById($method);
				$typeId = $method;

				// Let's see if we have a slug, and if not, create the one we'd know
				if (!empty($params)) {
					$slug = $params[0];
				} else {
					$slug = createSlug($typeData->Name);
				}

			} else {
				// This is a slug input
				$typeData = $this->types->getTypeBySlug($method);
				if (!empty($typeData)) {
					$typeId = $typeData->ID;
					$slug = $method;
				}
			}

			if ($typeData == null) {
				echo "<p>We couldn't find the Stream Type you were looking for.</p>";
			} else {
				// If logged in
				if (!empty($currentUser)) {
					// See if user follows or ignores the current type
					if (array_search($typeData->ID, explode(";", $followedTypeIDs)) > - 1) { $currentUser->followsType = true; }
					if (array_search($typeData->ID, explode(";", $ignoredTypeIDs)) > - 1) { $currentUser->ignoresType = true; }
				}

				$displayData = new stdClass();
				$displayData->currentUser = $currentUser;
				$displayData->mixerData = $this->types->getTypeFromMixer($typeData->ID);
				$displayData->typeData = $typeData;
				$displayData->recentStreams = $this->types->getRecentStreamsForType($typeData->ID);
				$displayData->activeStreams = $this->types->getActiveStreamsFromMixerByTypeId($typeData->ID);
				$displayData->frequentStreamers = $this->types->getLastMonthsMostFrequentStreamersForType($typeData->ID);
				
				$this->displayType($displayData);
			}
		} else {
			// default action: display all types
			$allKnownTypes = $this->types->getAllTypeIdsFromMingler();
			$allActiveTypes = $this->types->getAllActiveTypesFromMixer();

			// if user is logged in
			if (!empty($currentUser)) {
				$mixerTypesData = $this->types->getSpecifiedTypesFromMixer($followedTypeIDs);

				$followedTypeList = array();
				$offlineFollowedTypeList = array();

				foreach ($mixerTypesData as $type) {
					if ($type['online'] > 0) {
						$followedTypeList[] = $this->types->formatTypeDataFromMixer($type);	} 
						else {
							$offlineFollowedTypeList[] = $this->types->formatTypeDataFromMixer($type);	}
				}

				$currentUser->followedTypeList = $followedTypeList;
				$currentUser->offlineFollowedTypeList = $offlineFollowedTypeList;
			} else {
				$currentUser = null;
			}



			$fullTypeList = array();
			
			foreach ($allActiveTypes as $type) {
				$displayType = true;
				$type['FollowState'] = 'none';

				// If type is ignored, it shouldn't show up in the full list.
				if ($currentUser != null) {
					if (array_search($type['id'], explode(";", $ignoredTypeIDs)) > - 1) { 
						$displayType = false; 
						$type['FollowState'] = 'ignored'; }

					if (array_search($type['id'], explode(";", $followedTypeIDs)) > - 1) { $type['FollowState'] = 'followed'; }

				}

				// Let's add any types we don't recognize.
				if (!in_array($type['id'], $allKnownTypes)) {
					$this->types->addNewType($type);
					$allKnownTypes[] = $type['id'];
				}

				if ($displayType) {
					$fullTypeList[] = $this->types->formatTypeDataFromMixer($type);
				}
			}

			$displayData = new stdClass();
			$displayData->allTypes = $fullTypeList;
			$displayData->currentUser = $currentUser;

			$this->displayTypes($displayData);
		}

	}

	private function displayType($displayData) {
		$this->load->view('htmlHead', $this->version->getVersion());
		
		$this->load->view('type', $displayData);
		
		$this->load->view('htmlFoot', $this->version->getVersion());
	}

	private function displayTypes($displayData) {
		$this->load->view('htmlHead', $this->version->getVersion());
		
		$this->load->view('types', $displayData);
		
		$this->load->view('htmlFoot', $this->version->getVersion());
	}
}
?>