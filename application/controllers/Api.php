<?php
class Api extends CI_Controller {
	public function __construct() {
		parent::__construct();
		// Your own constructor code
		$this->load->database();
		$this->load->library('news');
		$this->load->library('users');
		$this->load->library('types');
		$this->load->library('communities');
		$this->load->library('tools');

		$this->data = new stdClass();
	}
	private function returnData() {
		echo json_encode($this->data);
	}

	public function index()
	{
		//$data = $this->getBaseData();
		$this->data->error = "Not found";
		$this->data->message = "Invalid endpoint";
		
		$this->returnData();
	}

	public function user($param = null) {
		// param accepts either mixer_id OR name_token

		if ($param != null) {

			$this->data->requestedUser = $param;
			if (is_numeric($param)) {
				$searchParam = "mixer_id";
				$sql_query = "SELECT * FROM mixer_users WHERE mixer_id=?";
			} else {
				$searchParam = "name_token";
				$sql_query = "SELECT * FROM mixer_users WHERE name_token=?";
			}			


			$this->data->message = "looking for user";
			$this->data->error = "function incomplete";

			$criteria = array($param);
			$query = $this->db->query($sql_query, $criteria);
			$this->data->user = $query->result()[0];

			$this->data->message = "collected user";
			$this->data->error = "no error";



		} else {
			$this->data->requestedUser = $param;
			$this->data->message = "no user provided";
			$this->data->error = "failure";
		}

		$this->returnData();
	}

	public function recent($param = null) {
		if ($param != null) {



			$this->data->requestedUser = $param;
			if (!is_numeric($param)) {
				$query = $this->db->select('ID')->from("Users")->where('Username', $param)->get();
				$mixer_id = $query->result()[0]->ID;
				$username = $param;
			} else {
				$query = $this->db->select('Username')->from("Users")->where('ID', $param)->get();
				$username = $query->result()[0]->Username;
				$mixer_id = $param;
			}	

			$recents = $this->users->getUsersRecentStreamTypes($mixer_id);

			$this->data->message = "looking for user";
			$this->data->error = "function incomplete";

			$this->data->user = $query->result()[0];

			$this->data->message = "collected user";
			$this->data->error = "no error";

			$str = $username. "'s most streamed games this month are: ";
			$count = 0;
			foreach ($recents as $type) {
				if ($count >= 1) {$str.=", "; }
				if ($count >= 2) {$str.="and "; }
				$str .= $type->Name." ".$type->StreamCount." times";
				$count++;
				if ($count >= 3) {
					$str .= ".";
					break;
				}
			}

			echo $str;// json_encode($recents);
		} else {
			$this->data->requestedUser = $param;
			$this->data->message = "no user provided";
			$this->data->error = "failure";

			//$this->returnData();
			return null;
		}

	}
}
?>