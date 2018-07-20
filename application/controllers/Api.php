<?php
class Api extends CI_Controller {

	public function index()
	{
		$data = $this->getBaseData();
		$data->error = "Not found";
		$data->message = "Invalid endpoint";
		echo json_encode($data);
	}

	public function user($param = null) {
		$data = $this->getBaseData();
		if ($param != null) {
			$data->requestedUser = $param;
			$data->message = "looking for user";
			$data->error = "function incomplete";
		} else {
			$data->requestedUser = $param;
			$data->message = "no user provided";
			$data->error = "failure";
		}

		echo json_encode($data);
	}

	public function alter($param = null) {
		$data = $this->getBaseData();
		if ($param != null) {
			$data->requestedAction = $param;
			$data->message = "looking for user";
			$data->error = "function incomplete";

			switch ($param) {
				case "followCommunity":
					break;

				case "unfollowCommunity":
					break;

				case "joinCommunity":
					break;
					
				case "leaveCommunity":
					break;
			}



		} else {
			$data->requestedAction = $param;
			$data->message = "no action provided";
			$data->error = "failure";
		}

		echo json_encode($data);
	}


	public function follows($userID) {
		$this->load->library('users');
		//308014
		$follows =  $this->users->getFollowedChannelsFromMixer($userID);
		echo json_encode($follows);
	}

	private function getBaseData() {
		$return = new stdClass();
		$return->error = "";
		$return->message = "";
		return $return;
	}

	private function getUserDetails() {
		$this->load->database();
		//$sql_query = "SELECT * FROM mixer_users WHERE name_token=?";
	}

}
?>