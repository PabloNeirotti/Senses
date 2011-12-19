/**                                                                     **
 *                              S E N S E S                              *
 *                           multimedia player                           *
 *                                                                       *
 *                       version: "fertile grounds"                      *
 *                                                                       *
 *                                                                       *
 *                                                                       *
 *  HTML5 decentralized multimedia player.                               *
 *                                                                       *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                       *
 *  Senses is under Pixelsize Public License.                            *
 *  More information at http://pixelsize.net/artise/support/license/     *
 *                                                                       *
 *  Author: Pablo Neirotti                                               *
 *  More information at http://artpulse.me/                              *
 *                                                                       */


/*** Settings ***/

var default_document_title = 'Senses';


/*** Else ***/

// Timers
var progressBarTimer = false;

// Constants
var VideoType = 1;
var AudioType = 2;

// Host for several Senses objects.
var Navigation;
var Player;
var Input;
var KeyboardInterface;

var LoadingNotif, PlaybackNotif, LicenseNotif;

$(document).ready(function() {

	// Quit if we are not on WebKit.
	if(!$.browser.webkit) {
		alert('Senses currently only runs on WebKit. Open Senses with Apple Safari or Google Chrome.');
		return;
	}
	

	// Initialize Senses objects.
	Navigation = new NavigationObject();
	LoadingNotif = new PassiveNotificationObject($('#loading-notif'));
	PlaybackNotif = new ActiveNotificationObject($('#playback-notif'), 'active-notification');
	LicenseNotif = new ActiveNotificationObject($('#license'), 'license-animation');
	
	/* Initialize Player */
	
	// Initialize Player.
	Player = new PlayerObject();
	
	// Append listeners.
	Player.dom_video.addEventListener("progress", player_listenerProgress, false);
	Player.dom_video.addEventListener("canplaythrough", player_listenerCanPlayThrough);
	Player.dom_video.addEventListener("suspend", player_listenerSuspend);
	Player.dom_video.addEventListener("timeupdate", player_listenerTimeUpdate);
	Player.dom_video.addEventListener("play", player_listenerPlay);
	Player.dom_video.addEventListener("waiting", player_listenerWaiting);
	Player.dom_video.addEventListener("ended", player_listenerEnded);
	
	Player.dom_audio.addEventListener("progress", player_listenerProgress, false);
	Player.dom_audio.addEventListener("canplaythrough", player_listenerCanPlayThrough);
	Player.dom_audio.addEventListener("suspend", player_listenerSuspend);
	Player.dom_audio.addEventListener("timeupdate", player_listenerTimeUpdate);
	Player.dom_audio.addEventListener("play", player_listenerPlay);
	Player.dom_audio.addEventListener("waiting", player_listenerWaiting);
	Player.dom_audio.addEventListener("ended", player_listenerEnded);
	
	
	/* Initialize Inputs */
	
	// Initialize Input.
	Input = new InputObject();
	
	// Activate Keyboard interface.
	KeyboardInterface = new KeyboardInterfaceObject();
	
	
	/* Misc */
	
	// Display instructions
	$('#instructions').addAnimationClass('animate', function() {
		$('#instructions').remove();
	});
	
	
	//$('#playback-notif').center( { vertical: true, horizontal: true });
	$('#loading-notif').center( { vertical: true, horizontal: true });
	
	// Maximize window
	//window.moveTo(0,0);
	//window.resizeTo(screen.width,screen.height);
	
	
	$.preLoadImages('/graphics/loader.gif',
					'/graphics/playback-notif/pause.svg',
					'/graphics/playback-notif/play.svg');
	
	
	// Load Hash location.
	if(window.location.hash) {
		var hash = window.location.hash;
		
		// Get hash folders. That is, in example: library/music
		var hash_folders = hash.split('/');
		hash_folders.shift();
		
		Navigation.enterPage('/' + hash_folders.join('/'));
		/*
		
		if(hash_folders[1]) {
			// Passively load the Library categories navigation.
			Navigation.enterPage('/library', true);
			
			// Get hash_steps. That is, in example: music:artist:album
			var hash_steps = hash_folders[1].split(':');
			
			// The path we will be building and entering with each loop.
			var page_path = new Array();
			
			// Enter each hash step.
			for(var i = 0; i < hash_steps.length; i ++) {
				// Add this step to the path.
				page_path.push(hash_steps[i]);
				
				// Passively load the step, except the last one, which is loaded actively.
				Navigation.enterPage('/library/' + page_path.join(':'), i < (hash_steps.length - 1) ? true : false);
			}
		} else {
			// Load the Library categories navigation.
			Navigation.enterPage('/library');
		}*/
	} else {
		// Load the Library categories navigation.
		Navigation.enterPage('/library');
	}
	
	// Focus the document! =D
	$(document).focus();
});

/**
 * Called from console. Used to resize the window
 * to the Snapshot taking resolution.
 */
function snapshotWindowResize() {
	window.resizeTo(1306, 806);
}