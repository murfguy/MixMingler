<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Host extends CI_Controller {

	public function index()
	{
		//echo 'Hello World!';
		$this->load->database();
		$this->load->library('users');
		$this->load->library('news');

		//echo count($onlineData);
		//echo "<hr>";

		$url = "https://mixer.com/api/v1/channels/273268/hostee";;
		 
		//Initiate cURL
		$ch = curl_init($url);
		 
		//Use the CURLOPT_PUT option to tell cURL that
		//this is a PUT request.
		curl_setopt($ch, CURLOPT_PUT, true);
		 
		//We want the result / output returned.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		//Our fields.
		$fields = array("id" => 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		 
		//Execute the request.
		$response = curl_exec($ch);
		 
		echo $response;

	}



	private function displayOnline($onlineInfo) {
		
	}
}
?>