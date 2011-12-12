/**                                                                     **
 *                              S E N S E S                              *
 *                           multimedia player                           *
 *                                                                       *
 *                        version: "good grounds"                        *
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
	
	Player.dom_audio.addEventListener("progress", player_listenerProgress, false);
	Player.dom_audio.addEventListener("canplaythrough", player_listenerCanPlayThrough);
	Player.dom_audio.addEventListener("suspend", player_listenerSuspend);
	Player.dom_audio.addEventListener("timeupdate", player_listenerTimeUpdate);
	Player.dom_audio.addEventListener("play", player_listenerPlay);
	Player.dom_audio.addEventListener("waiting", player_listenerWaiting);
	
	
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
	
	// Load the Library categories navigation.
	Navigation.enterPage('/library');
});

function centerNotif(icon) {
	$('#centerNotif').removeClass().addClass(icon).css({'-webkit-animation-name': ''});
	setTimeout(function() {
		$('#centerNotif').css({'-webkit-animation-name': 'centerNotif'});
	}, 1);
}

function cssAnimate(element, animation) {
	$(element).css({'-webkit-animation-name': ''});
	setTimeout(function() {
		$(element).css({'-webkit-animation-name': animation});
	}, 0);
	
	var duration = $(element).css('-webkit-animation-duration');
	duration = parseFloat(duration.substr(0, duration.length - 1)) * 1000;
	
	setTimeout(function() {
		switch (animation) {
			case 'zoom-in':
			case 'fade-in':
				$(element).css({opacity: 1});
				break;
				
			case 'fadeOutFromTransp':
			case 'zoom-out':
			case 'fade-out':
				$(element).css({opacity: 0});
				break;
				
			case 'fadeInToTransp':
				$(element).css({opacity: 0.7});
				break;
		}
	}, duration);
}