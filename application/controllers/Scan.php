<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan extends CI_Controller {

	public function index()
	{
		echo "<p>no scan type specified</p>";
	}

	// User's scan is meant to run every 10 minutes.
	// It will collect and add/sync data for all online streamers who have 100 or more followers.
	public function users() {
		$starttime = time();
		$this->load->database();
		$this->load->library('users');
		$this->load->library('news');
		$this->load->library('types');

		// Retreive online streamers from Mixer.
		// Parameter indicates the minimum number of followers required by streamer to be counted.
		$streamersData = $this->users->getListOfOnlineUsers(25);

		// And since we'll want to add a few new games to the database, let's see what we already have.
		$allKnownTypes = $this->types->getAllTypeIdsFromMingler();
		$allNewTypes = array();

		// Starter data for batch UPDATEs
		$inserted_count = 0;
		$update_data = array();
		$lastStarted_data = array();
		$news_data = array();

		// Let's look at each streamer we collected
		foreach ($streamersData as $streamer) {
			// Let's see if we have them in Mingler.
			$minglerData = $this->users->getUserFromMingler($streamer['id']);

			if ($streamer['type'] == null) {
				$streamer['type'] = array(
					'id' => 0,
					'name' => "No game selected",
					'coverUrl' => '',
					'backgroundUrl' => ''
				);
			}

			$newsParams = array(
				'TypeID' => $streamer['type']['id'],
				'MessageParams' => array($streamer['type']['id']));
			
			if ($minglerData->ID == null) {
				// If they don't exist, add them (addUser will ignore any duplicate streamers)
				$this->users->addNewUser($streamer); 

				// They are now in, so let's recollect the local data from Mingler.
				$minglerData = $this->users->getUserFromMingler($streamer['id']);
				$inserted_count = $inserted_count + 1;

				// We know they must be online to be added, so we mark this as the start time of their latest stream for the batch UPDATE.
				$lastStarted_data[] = $this->users->getStartTimeQueryDataArray($streamer);

				// Let's note that they are now a newly synced person on MixMingler
				$news_data[] = $this->news->getNewsArray($minglerData->ID, 'firstSync', 'mingler');

				// So we also want to note for their activity feed that they started streaming something new.
				$news_data[] = $this->news->getNewsArray($minglerData->ID, 'newStreamType', 'type', $newsParams);
			} else {
				// If we do have them in the local database, we want to see if we need to mark the start of a new stream time.

				// If they haven't been seen in over 1 hour, we will mark this as a new stream.
				if (strtotime($minglerData->LastSeenOnline) < (time() - (60*60*1))) {
					$lastStarted_data[] = $this->users->getStartTimeQueryDataArray($streamer);

					// Since this is a new stream, we also want to note that they are starting to stream a game in their activity feed.
					$news_data[] = $this->news->getNewsArray($minglerData->ID, 'newStreamType', 'type', $newsParams);
				} else {
					// In case we are mid-stream, we want to see if they maybe changed games.
					if ($minglerData->LastTypeID != $streamer['type']['id']) {
						// They changed type, so let's note they started streaming something new.
						$news_data[] = $this->news->getNewsArray($minglerData->ID, 'newStreamType', 'type', $newsParams);
					}
				}
			}

			// Now that we've sorted the above out, let's get the data we need for the batch UPDATE.
			$update_data[] = $this->users->getSyncQueryDataArray($streamer);

			// Also, let's toss whatever they are streaming into the database... unless it's already there.
			if (!in_array($streamer['type']['id'], $allKnownTypes)) {
				$this->types->addNewType($streamer['type']);
				$allKnownTypes[] = $streamer['type']['id'];
				$allNewTypes[] = $streamer['type']['name'];
			}			
		}

		// If we have new stream time to note, let's UPDATE those now.
		if (count($lastStarted_data) > 0) {
			$this->db->update_batch('Users', $lastStarted_data, 'ID');
		}

		// Add we now batch insert all the tasty news items we collected.
		$this->db->insert_batch('TimelineEvents', $news_data);

		// And now we batch UPDATE every streamer we just collected data for.
		$this->db->update_batch('Users', $update_data, 'ID');

		// And lastly, a bit of data read out so if we're testing the page, we can make sure it's all working as planned.
		$elapsed_time = time() - $starttime;
		echo "<p>We've synced ".count($update_data)." streamers in $elapsed_time seconds.</p>";
		echo "<p>We created ".count($news_data)." timeline events.</p>";
		echo "<p>We added in $inserted_count new streamers.</p>";
		echo "<p>We added ".count($allNewTypes)." new types.</p>";
		echo "<p>We noted ".count($lastStarted_data)." new streams.</p>";
	}

	public function types() {
		$starttime = time();
		$this->load->database();
		$this->load->library('types');
		$this->load->library('news');


		// And since we'll want to add a few new games to the database, let's see what we already have.
		$allKnownTypes = $this->types->getAllTypeIdsFromMingler();
		$allTypesLiveOnMixer = $this->types->getAllActiveTypesFromMixer();
		$allNewTypes = array();
		$typesToUpdate = array();
		$allUpdatedTypeIds = array();

		foreach($allTypesLiveOnMixer as $type) {
			if (!in_array($type['id'], $allKnownTypes)) {
				$this->types->addNewType($type);
				$allKnownTypes[] = $type['id'];
				$allNewTypes[] =  $type['name'];
			}

			if (!in_array($type['id'], $allUpdatedTypeIds)) {
				$typesToUpdate[] = $this->types->getSyncQueryDataArray($type);
				$allUpdatedTypeIds[] = $type['id'];
			}
		}

		// And now we batch UPDATE every streamer we just collected data for.
		$this->db->update_batch('StreamTypes', $typesToUpdate, 'ID');

		$elapsed_time = time() - $starttime;
		echo "<p>We've found ".count($allTypesLiveOnMixer)." types in $elapsed_time seconds.</p>";
		echo "<p>We added ".count($allNewTypes)." types.</p>";
		echo "<p>We synced ".count($allUpdatedTypeIds)." types.</p>";

		//echo json_encode($typesToUpdate);
	}
}
?>