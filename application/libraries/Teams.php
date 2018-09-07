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

	public function getTeamMembersFromMixer($teamId) {
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

			$allTeamMembers = array_merge($allTeamMembers, $newList);

			$currentPage = $currentPage + 1;

			if ($currentPage % 20 == 0) {
				//sleep(5);
			}

			if (count($newList) < 100) {
				// We've got all followed channels 
				$foundAllMembers = true;
			}
		}
		
		return $allTeamMembers;
	}


	
}?>