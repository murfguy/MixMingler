<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Version {
	protected $CI;
	private $major;
	private $minor;
	private $revision;
	private $build;
	private $stage;

	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();

		$this->major = 0;
		$this->minor = 2;
		$this->revision = 1;
		$this->stage = "Types";

		// Load Database
		//$this->CI->load->database();
	}

	public function getStageName($major, $minor) {
		switch ($major.".".$minor) {
			case "0.1":
				$stage = "Groundworks";
				break;
			case "0.2":
				$stage = "Types";
				break;
			case "0.3":
				$stage = "Communities";
				break;
			case "0.4":
				$stage = "Streamers";
				break;
		}
	}

	public function getVersion() {
		$versionData = new stdClass();
		$versionData->major = $this->major; // Moves up on major release
		$versionData->minor = $this->minor; // Moves up when new core feature is made live/completed
		$versionData->revision = $this->revision; // Moves up when new update is made
		$versionData->stage = $this->stage;
		$versionData->version = "$this->major.$this->minor.$this->revision";

		return $versionData;
	}

	public function getVersionHistory() {
		$patchNotes = array();

		$currentPatch = $this->getVersion()->version;
		$date_string = 'd M Y';

		/* -- Template ---------
		$patch = new stdClass();
		$patch->version = "0.1.x";
		$patch->date = date($date_string, strtotime('2017-12-21'));
		$patch->notes = array();
		$patch->notes[] = "note";
		$patchNotes[] = $patch;
		----- Template --------- */
		$patch = new stdClass();
		$patch->version = "0.2.1";
		$patch->date = date($date_string, strtotime('2018-08-01'));
		$patch->notes = array();
		$patch->notes[] = "Admin Panel: user roles assignable";
		$patch->notes[] = "Home page: news feeds load asynchronously.";
		$patch->notes[] = "New communities can be requested.";
		$patch->notes[] = "Admin Panel: approve/reject community requests";
		$patch->notes[] = "Communities can now be founded once approved.";
		//$patch->notes[] = "Community moderation page for managing communities.";

		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.2.0";
		$patch->date = date($date_string, strtotime('2018-07-24'));
		$patch->notes = array();
		$patch->notes[] = "Stable Release of v0.2-Types!";
		$patch->notes[] = "Login/Logout buttons now in header.";
		$patch->notes[] = "Improvements to alpha/version info page.";
		$patch->notes[] = "Icons added to navigation.";
		$patch->notes[] = "Alpha Info added to navigation.";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.1.4";
		$patch->date = date($date_string, strtotime('2018-07-21'));
		$patch->notes = array();
		$patch->notes[] = "Fixed a backend issue that was causing bad database insertions on types. Required adjustments to type URLs.";
		$patch->notes[] = "Added basic status of streamer/viewer counts and 'view on mixer' link on stream detail page.";
		$patch->notes[] = "Recent streamers on stream type page are now consolidated down to one listing per streamer.";
		$patch->notes[] = "Now able to manage followed/ignored types from account management page.";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.1.3";
		$patch->date = date($date_string, strtotime('2018-07-20'));
		$patch->notes = array();
		$patch->notes[] = "Streamlined design on 'common games' on type detail page.";
		$patch->notes[] = "Added note for when there are no active streams on a type's detail page.";
		$patch->notes[] = "Followed games is now listed in streamer profile.";
		$patch->notes[] = "Common games, and time notices hidden for streamers with under 25 followers.";
		$patch->notes[] = "Types list now can now toggle between 'followed' types and 'active' types.";
		$patch->notes[] = "Time of user registration to MixMingler is now being tracked.";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.1.2";
		$patch->date = date($date_string, strtotime('2018-07-19'));
		$patch->notes = array();
		$patch->notes[] = "Followed games are the focus of the Stream Types page if user is logged in.";
		$patch->notes[] = "Ignored games are hidden on the Stream Types page.";
		$patch->notes[] = "[Type List] Style adjustments for type listing panels, and using iconograpy for stream and viewer count.";
		$patch->notes[] = "Tweaked time displays on streamer profiles";
		$patch->notes[] = "Login page now has a proper button.";
		$patchNotes[] = $patch;


		$patch = new stdClass();
		$patch->version = "0.1.1";
		$patch->date = date($date_string, strtotime('2018-07-16'));
		$patch->notes = array();
		$patch->notes[] = "Build Code Name: \"TYPES\"";
		$patch->notes[] = "Streamer Auto-Scan: Syncs/adds currently online streamers with 25+ followers every 10 minutes.";
		$patch->notes[] = "Type Auto-scan: Syncs/adds any currently online stream types every 30 minutes";
		$patch->notes[] = "Stream Types: It is now possible to look at info for specific stream types. Info includes: current number of streams, list of active streams by popularity, list of streams by start time, frequent streamers by count of stream type in the last 30 days.";
		$patch->notes[] = "Follow/Unfollow Stream Types: Users can choose which games they want to prioritize. These games are selectable on thier home page where they can see recent events related to that type, as well as the top 6 current streams of that type.";
		$patch->notes[] = "General design overhauls to home page and profile page.";
		$patch->notes[] = "FAQ updates.";
		$patch->notes[] = "Versions reset AGAIN properly align with new development roadmap.";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.1.0";
		$patch->date = date($date_string, strtotime('2017-12-21'));
		$patch->notes = array();
		$patch->notes[] = "'New Communities' list on home page now shows any communities that are new since you last logged in OR that were created within the last week.";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.0.6";
		$patch->date = date($date_string);
		$patch->notes = array();
		$patch->notes[] = "Newly added communities since your last login are now listed on your home page.";
		$patch->notes[] = "Admin Panel: See most recent logins";
		$patch->notes[] = "Added 'alpha' page that showcases version history and development plans (accesible by clicking version # in footer)";
		$patch->notes[] = "Versions reset to make more sense (0.0.1.x) where 'x' increments with each alpha update.";
		$patch->notes[] = "Added graceful failing on single community pages if no members have joined.";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.0.5";
		$patch->date = date($date_string, strtotime('2017-12-21'));
		$patch->notes = array();
		$patch->notes[] = "Now tracking last login time.";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.0.4";
		$patch->date = date($date_string, strtotime('2017-12-21'));
		$patch->notes = array();
		$patch->notes[] = "Added account management page.";
		$patch->notes[] = "Can modify core communities from Account page.";
		$patchNotes[] = $patch;


		$patch = new stdClass();
		$patch->version = "0.0.3";
		$patch->date = date($date_string, strtotime('2017-12-20'));
		$patch->notes = array();
		$patch->notes[] = "Added version number to footer.";
		$patch->notes[] = "CSS and content adjustments for communities listing page.";
		$patch->notes[] = "Can now toggle community categories.";
		$patch->notes[] = "General refactoring of code relating to creating community pages";
		$patch->notes[] = "First tracking of patch notes by version";
		$patchNotes[] = $patch;


		$patch = new stdClass();
		$patch->version = "0.0.2";
		$patch->date = date($date_string, strtotime('2017-12-15'));
		$patch->notes = array();
		$patch->notes[] = "Placeholder Admin Area. Only accesible by 'owner', 'admin', and 'dev' roles. Link to Admin area should appear in navbar if role is valid.";
		$patch->notes[] = "Intial home page with listing of your communities AND news feed for communities you follow.";
		$patch->notes[] = "Some refactoring of loading of single community pages. Should be no experience changes";
		$patch->notes[] = "Only 'joining' a community now adds a news item into database";
		$patch->notes[] = "Begun developing 'Core Communities' feature, only lists on profiles and home page. Setting core communities not implemented";
		$patchNotes[] = $patch;

		$patch = new stdClass();
		$patch->version = "0.0.1";;
		$patch->date = date($date_string, strtotime('2017-12-13'));
		$patch->notes = array();
		$patch->notes[] = "User registration now occurs as part of the authentication process";
		$patch->notes[] = "Refactored data controller for single user display page";
		$patch->notes[] = "Users now sync if their last sync with Mixer was over 30 minutes ago. This is to prevent over-pinging the Mixer API";
		$patch->notes[] = "News items now have human-readable timestamps";
		$patch->notes[] = "'Streaming Now!' appears if stream has been detected online in that last 30 minutes.";
		$patch->notes[] = "Joining/Following/Leaving/Unfollowing a community is now marked in your timeline";
		$patch->notes[] = "mixmingler.com domain registered, and development/alpha content has been migrated to this server";

		$patchNotes[] = $patch;		

		$patch = new stdClass();
		$patch->version = "0.0.0";;
		$patch->date = date($date_string, strtotime('2017-12-12'));
		$patch->notes = array();
		$patch->notes[] = "Authenticate with Mixer added";
		$patchNotes[] = $patch;

		return $patchNotes;
	}
}?>