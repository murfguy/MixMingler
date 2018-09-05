<?php
class Search extends CI_Controller {
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

	public function getStreamers() {
		$criteria = array();

		// Search criteria:
			// Stream Age range
			// Mixer Age range

			// Follower count range
			// View count range
			// By current view count range

			// Registered to mingler

			// Only followed types
			// Only partners/non-partners

			// Streamed my games

			// Is online (on by default)
		$onlineOnly = TRUE;

		//$criteria['registered'] = 1;
		//$criteria['streamage'] = 60 * 23;

		//$criteria['orderby'] = 'streamage';

		//$criteria['followers'] = '1000,1100';
		


		$this->db->select('*')->from('Users');

		if (!empty($criteria)) {
			if (isset($criteria['registered'])) { $this->db->where('isRegistered', $criteria['registered']); }
			
			if (isset($criteria['streamage'])) {

				if ($criteria['streamage'] <= (60 * 24)) {
						$this->db->where('LastStreamStart > DATE_SUB(NOW(), INTERVAL '.$criteria['streamage'].' MINUTE)'); 
				} else {
					$this->db->where('LastStreamStart < DATE_SUB(NOW(), INTERVAL 24 HOUR)');
				}
				if ($criteria['orderby'] = 'streamage') { $this->db->order_by('LastSeenOnline', 'DESC'); }
			}

			if (isset($criteria['followers'])) {
				$followers = explode(",", $criteria['followers']);
				$this->db->where('NumFollowers BETWEEN '.$followers[0].' AND '.$followers[1]);
			}

			if (isset($criteria['isOnline'])) { $onlineOnly = $criteria['isOnline']; }

		}
		if ($onlineOnly) {
			$this->db->where('LastSeenOnline > DATE_SUB(NOW(), INTERVAL 10 MINUTE)');
		}
		
		$this->db->limit(100);
		$query = $this->db->get();

		//echo json_encode($_REQUEST);
		echo json_encode($query->result());
	}
} ?>