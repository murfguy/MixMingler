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
					$slug = $this->types->createSlug($typeData->typeName);
				}

			} else {
				// This is a slug input
				$typeData = $this->types->getTypeBySlug($method);
				if (!empty($typeData)) {
					$typeId = $typeData->typeId;
					$slug = $method;
				}
			}

			if ($typeData == null) {
				echo "<p>Display for types is pending, but sadly, we don't think we've seen this game you're looking for.</p>";
			} else {

				// if user is logged in
				if (isset($_SESSION['mixer_user'])) {
					$currentUser = new stdClass();
					$currentUser->token = $_SESSION['mixer_user'];
					$currentUser->followsType = false;
					$currentUser->ignoresType = false;

					$sql_query = "SELECT followedTypes,ignoredTypes FROM mixer_users WHERE name_token=?";
					$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));

					$followedTypes = explode(";", $query->result()[0]->followedTypes);
					if (array_search($typeData->typeId, $followedTypes) > - 1) { $currentUser->followsType = true; }

					$ignoredTypes = explode(",", $query->result()[0]->ignoredTypes);
					if (array_search($typeData->typeId, $ignoredTypes) > - 1) { $currentUser->ignoresType = true; }

					$currentUser = $currentUser;
				} else {
					$currentUser = null;
				}

				$displayData = new stdClass();
				$displayData->currentUser = $currentUser;
				$displayData->mixerData = $this->types->getTypeFromMixer($typeData->typeId);
				$displayData->typeData = $typeData;
				$displayData->recentStreams = $this->types->getRecentStreamsForType($typeData->typeId);;
				$displayData->activeStreams = $this->types->getActiveStreamsFromMixerByTypeId($typeData->typeId);
				$displayData->frequentStreamers = $this->types->getLastMonthsMostFrequentStreamersForType($typeData->typeId);
				
				$this->displayType($displayData);
			}
			

		} else {
			// default action
			$allKnownTypes = $this->types->getAllTypeIdsFromMingler();
			$allActiveTypes = $this->types->getAllActiveTypesFromMixer();

			// if user is logged in
			if (isset($_SESSION['mixer_user'])) {
				$currentUser = new stdClass();
				$currentUser->token = $_SESSION['mixer_user'];
				$currentUser->followsType = false;
				$currentUser->ignoresType = false;

				$sql_query = "SELECT followedTypes,ignoredTypes FROM mixer_users WHERE name_token=?";
				$query = $this->db->query($sql_query, array($_SESSION['mixer_user']));

				$currentUser->followedTypesIds = explode(";", $query->result()[0]->followedTypes);
				$currentUser->ignoredTypesIds = explode(",", $query->result()[0]->ignoredTypes);

				$followedTypes_str = str_replace(";", ",", $query->result()[0]->followedTypes);

				$followedTypesData = $this->types->getSpecifiedTypesFromMixer($query->result()[0]->followedTypes);

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
					/*if (empty($type['coverUrl'])) {
						$type['coverUrl'] == "https://mixer.com/_latest/assets/images/main/types/default.jpg";
					}

					$fullTypeList[] = array(
						'coverUrl' => $type['coverUrl'],
						'slug' => $this->types->createSlug($type['name']),
						'name' => $type['name'],
						'online' => $this->tools->formatNumber($type['online']),
						'viewersCurrent' => $this->tools->formatNumber($type['viewersCurrent']),
						'id' => $type['id']
					);*/
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