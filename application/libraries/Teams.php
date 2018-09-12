<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teams {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
		$this->db = $this->CI->db;

		// get user's teams: https://mixer.com/api/v1/users/$userId/teams
	}

	public function getTeam($teamId) {

		if (ctype_digit($teamId)) {
			// This is an id input
			$query = $this->db
				->select("*")
				->from("Teams")
				->where("ID", $teamId)
				->get();
		} else {
			// This is a token input
			$query = $this->db
				->select("*")
				->from("Teams")
				->where("Slug", $teamId)
				->get();
		}

		return $query->result();

	}

	public function syncTeam($teamData) {
		//$sql_query = "INSERT INTO UserCommunications (MixerID, Email) VALUES(?, ?) ON DUPLICATE KEY UPDATE MixerID=?, Email=?";
		//$query = $this->CI->db->query($sql_query, array($mixerId, $emailAddress, $mixerId, $emailAddress));

		$insertData = [
			$teamData['id'], 
			$teamData['name'],
			$teamData['token'],
			$teamData['ownerId'],
			
			substr($teamData['createdAt'], 0, 9),
			$teamData['logoUrl'],
			$teamData['backgroundUrl']];

		$updateData = [
			$teamData['name'],
			$teamData['token'],
			$teamData['ownerId'],
			
			$teamData['logoUrl'],
			$teamData['backgroundUrl']];

		$sql_query = "INSERT INTO Teams (ID, Name, Slug, OwnerID, CreationDate, LogoURL, BackgroundURL) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE Name=?, Slug=?, OwnerID=?,LogoURL=?, Slug=?, BackgroundURL=?";
		$query = $this->db->query($sql_query, array_merge($insertData, $updateData));

		return ($this->db->affected_rows() > 0);
	}

	public function batchSyncTeams($teamsData) {
		$sql_query = "INSERT INTO Teams (ID, Name, Slug, OwnerID, CreationDate, LogoURL, BackgroundURL) VALUES ";

		$allData = array();

		foreach ($teamsData as $team) {
			$insertData = [
				$team['id'], 
				$team['name'],
				$team['token'],
				$team['ownerId'],
				substr($team['createdAt'], 0, 10),
				$team['logoUrl'],
				$team['backgroundUrl']];

				$sql_query .= "(?, ?, ?, ?, ?, ?, ?), ";

			$allData = array_merge($allData, $insertData);
		}

		$sql_query = substr($sql_query, 0, -2)." ";
		$sql_query .= "ON DUPLICATE KEY UPDATE Name=VALUES(Name), Slug=VALUES(Slug), OwnerID=VALUES(OwnerID), LogoURL=VALUES(LogoURL), BackgroundURL=VALUES(BackgroundURL)";
		//echo $sql_query;
		$query = $this->db->query($sql_query, $allData);
		return $this->db->affected_rows();
	}


	public function getTeamMembers($teamId) {
		$query = $this->db
			->select('*')
			->from('Users')
			->join('UserTeams', 'UserTeams.MixerID = Users.ID')
			->where('UserTeams.TeamID', $teamId)
			->order_by('LastStreamStart', 'DESC')
			->get();
		return $query->result();
	}

	public function getTeamMembersFromMixer($teamId, $onlineOnly = false) {
		$url = "https://mixer.com/api/v1/teams/$teamId/users";
		$currentPage = 0;
		$foundAllMembers = false;
		$allTeamMembers = array();		
		
		$urlParameters = "?limit=100";
		//$urlParameters .="&where=numFollowers:gte:".$minFollows;
		//$urlParameters .="&order=viewersCurrent:DESC";
		//$urlParameters .="&fields=id,username";
		//$urlParameters .="&fields=id,userId,token,online,partnered,suspended,viewersTotal,numFollowers,costreamId,createdAt,user,type";

		while (!$foundAllMembers) {

			$content = file_get_contents($url.$urlParameters."&page=".$currentPage);
			$newList = json_decode($content, true);

			if ($onlineOnly) {
				foreach ($newList as $member) {
					if ($member['channel']['online']) {
						$allTeamMembers[] = $member; }
				}
			} else {
				$allTeamMembers = array_merge($allTeamMembers, $newList);
				$currentPage = $currentPage + 1;

				if ($currentPage % 20 == 0) {
					//sleep(5);
				}
			}

			

			if (count($newList) < 100) {
				// We've got all followed channels 
				$foundAllMembers = true;
			}
		}
		
		return $allTeamMembers;
	}

	public function syncTeamMembers($teamId) {
		$actualMembers = $this->getTeamMembersFromMixer($teamId);
		$actualMemberIDs = array();

		foreach ($actualMembers as $member) { $actualMemberIDs[] = $member['channel']['id']; }

		$storedMembers = $this->getTeamMembers($teamId);
		$storedMemberIDs = explode(",", getIdList($storedMembers));

		// If team is not in Mixer, but is in mingler: remove
		$removeMembers = array_diff($storedMemberIDs, $actualMemberIDs);
		foreach ($removeMembers as $member) {
			$this->db
				->where('TeamID', $teamId)
				->where('MixerID', $member)
				->delete('UserTeams');
		}

		// If team is in Mixer, but not mingler: add team
		$addMembers = array_diff($actualMemberIDs, $storedMemberIDs);

		foreach ($addMembers as $member) {
			$data = ['TeamID'=>$teamId, 'MixerID'=>$member];
			//$this->db->insert('UserTeams', $data);

			$insert_query = $this->db->insert_string('UserTeams', $data);
			$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
			$this->db->query($insert_query);
		}
	}


	
}?>