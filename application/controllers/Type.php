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

		// if user is logged in
		if (isset($_SESSION['mixer_user'])) {
			$currentUser = new stdClass();
			$currentUser->username = $_SESSION['mixer_user'];
			$currentUser->followsType = false;
			$currentUser->ignoresType = false;

			$user = $this->users->getUserFromMingler($_SESSION['mixer_id']);
			$currentUser->followedTypesIds = explode(",", $user->FollowedTypes);
			$currentUser->ignoredTypesIds = explode(",", $user->IgnoredTypes);

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
					$slug = $this->types->createSlug($typeData->Name);
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
					// See if user follows the current type
					if (array_search($typeData->ID, $currentUser->followedTypesIds) > - 1) { $currentUser->followsType = true; }
					if (array_search($typeData->ID, $currentUser->ignoredTypesIds) > - 1) { $currentUser->ignoresType = true; }
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
			// default action
			$allKnownTypes = $this->types->getAllTypeIdsFromMingler();
			$allActiveTypes = $this->types->getAllActiveTypesFromMixer();

			// if user is logged in
			if (!empty($currentUser)) {
				$followedTypesData = $this->types->getSpecifiedTypesFromMixer(implode(";",$currentUser->followedTypesIds));

				$followedTypeList = array();
				$offlineFollowedTypeList = array();
				foreach ($followedTypesData as $type) {
					if ($type['online'] > 0) {
						$followedTypeList[] = $this->types->formatTypeDataFromMixer($type);	
					} else {
						$offlineFollowedTypeList[] = $this->types->formatTypeDataFromMixer($type);
					}

				}

				$currentUser->followedTypeList = $followedTypeList;
				$currentUser->offlineFollowedTypeList = $offlineFollowedTypeList;
			} else {
				$currentUser = null;
			}



			$fullTypeList = array();
			
			foreach ($allActiveTypes as $type) {
				$displayType = true;

				// If type is either followed or ignored, it shouldn't show up in the full list.
				if ($currentUser != null) {
					if (array_search($type['id'], $currentUser->ignoredTypesIds) > - 1) { $displayType = false; }
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
		$this->load->view('htmlHead');
		
		$this->load->view('type', $displayData);
		
		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}

	private function displayTypes($displayData) {
		$this->load->view('htmlHead');
		
		$this->load->view('types', $displayData);
		
		$this->load->library('version');
		$this->load->view('htmlFoot', $this->version->getVersion());
	}
}
?>