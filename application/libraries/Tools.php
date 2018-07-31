<?
defined('BASEPATH') OR exit('No direct script access allowed');

class Tools {
	protected $CI;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		// Load Database
		$this->CI->load->database();
	}

	public function removeValueFromList($value, $list) {
		//Convert list list to PHP array
		$array = explode(",", $list);
		
		// Remove the value from the array
		if (($key = array_search($value, $array)) !== false) { unset($array[$key]); }
		
		// Restore to string.list and return
		return implode(',', $array);
	}

	public function valueIsInList($value, $list) {
		$array = explode(",", $list);
		if (($key = array_search($value, $array)) !== false) { 
			return true;
		}
		return false;
	}

	public function getElapsedTimeString($timestamp) {
		$elapsedTime = time() - $timestamp;

		// If under one minute
		if ($elapsedTime < 60) {
			return $elapsedTime." seconds ago";
		}

		// If under one hour
		if ($elapsedTime < 60 * 60) {
			return ceil($elapsedTime/60)." minutes ago";
		}

		// If under one day ago
		if ($elapsedTime < 60 * 60 * 24) {
			return ceil($elapsedTime/(60*60))." hours ago";
		}

		// If over 24 hours
		if ($elapsedTime >= 60 * 60 * 24) {
			return number_format(ceil($elapsedTime/(60*60*24)))." days ago";
		}
	}

	public function formatNumber($number, $dec = 0) {
		return number_format($number, $dec, ".", ",");
	}
}
?>