<?php
class Auth extends CI_Controller {
	public function index()
	{
		//echo 'Hello World!';
		header('Location: /auth/session');
	}

	public function session()
	{
		$this->load->library('authdata');

		$provider = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId'				=> $this->authdata->getClientId(),	// The client ID assigned to you by the provider
			'clientSecret'			=> $this->authdata->getClientSecret(),   // The client password assigned to you by the provider
			'redirectUri'			 => base_url().'/auth/session',
			'urlAuthorize'			=> 'https://mixer.com/oauth/authorize',
			'urlAccessToken'		  => 'https://mixer.com/api/v1/oauth/token',
			'urlResourceOwnerDetails' => 'https://mixer.com/api/v1/users/current'
		]);

		$options = [
			'scope' => ['user:details:self']
		];

		// If we don't have an authorization code then get one
		if (!isset($_GET['code'])) {

			// Fetch the authorization URL from the provider; this returns the
			// urlAuthorize option and generates and applies any necessary parameters
			// (e.g. state).
			$authorizationUrl = $provider->getAuthorizationUrl($options);

			// Get the state generated for you and store it to the session.
			$_SESSION['oauth2state'] = $provider->getState();
			$_SESSION['returnUrl'] = $_SERVER['HTTP_REFERER'];

			// Redirect the user to the authorization URL.
			header('Location: ' . $authorizationUrl);
			exit;

		// Check given state against previously stored one to mitigate CSRF attack
		} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

			if (isset($_SESSION['oauth2state'])) {
				unset($_SESSION['oauth2state']);
			}
			
			exit('Invalid state');

		} else {

			try {
				// Load Libraries
				$this->load->library('users');
				$this->load->library('news');
				$this->load->library('communications');

				// Try to get an access token using the authorization code grant.
				$accessToken = $provider->getAccessToken('authorization_code', [
					'code' => $_GET['code']
				]);

				// Using the access token, we may look up details about the
				// resource owner.
				$resourceOwner = $provider->getResourceOwner($accessToken);
				$owner = $resourceOwner->toArray();
				
				// Adjust array to include avatarUrl in an expected position.
				// We do this since the library expects an input for mixer api /channels/ endpoint,
				// but we are sending in /users/current which has a different data structure.
				//$owner['channel']['user']['avatarUrl'] = $owner['avatarUrl'];
				$channelData = $this->users->getUserFromMixer($owner['username']);
				$user = $this->users->syncUser($channelData, true);

				$emailSynced = $this->users->syncEmailAddress($owner['email'], $owner['channel']['id']);

				$minglerData = $this->users->getUserFromMingler($owner['channel']['id']);

				$this->users->syncFollows($owner['channel']['id'], $owner['id']);


				$this->users->loggedIn($owner['channel']['id']);

				$_SESSION['mixer_user'] = $owner['username'];
				$_SESSION['mixer_id'] = $owner['channel']['id'];
				$_SESSION['mixer_userId'] = $owner['id'];
				$_SESSION['site_role'] = $minglerData->SiteRole;


				if (is_null($minglerData->Settings_Communications)) {
					$this->users->applyUserSettings('communications', $this->communications->getFreshCommunicationSettings());}				
				

				if (strpos(parse_url($_SESSION['returnUrl'], PHP_URL_HOST), 'mixmingler') !== false) {
					$returnLocation = $_SESSION['returnUrl'];
					unset($_SESSION['returnUrl']);

					header('Location: '.$returnLocation);
				} else {
					header('Location: /');					
				}

				//var_export($resourceOwner->toArray());

			} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

				// Failed to get the access token or user details.
				exit($e->getMessage());

			}
		}
	}


	public function logout() {
		session_unset();
		session_destroy();

		$response = new stdClass();
		$response->action = "logout";
		$response->success = true;
		echo json_encode($response);
		//header('Location:'.base_url());
	}
}
?>