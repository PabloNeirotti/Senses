/**                                                                     **
 *                              S E N S E S                              *
 *                           multimedia player                           *
 *                                                                       *
 *                       p l a y e r   o b j e c t                       *
 *                                                                       *
 *                                                                       *
 *                                                                       *
 *  This object handles video and audio playback.                        *
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



/*** Constants ***/

var Audio_Player = 'audio';
var Video_Player = 'video';



/*** Player Object ***/

function PlayerObject() {
	// Information about current media being played.
	this.current_media = {
		dom: null,				// DOM of the current media.
		media_type: null,		// Media type.  "music" | "videos" | "series" | "movies" | ...
		player_type: null,		// Player type.  Audio_Player | Video_Player
		is_playing: false,		// Wether reproduction is currently playing or paused.
		item_data: null			// Stores the item data.
	};
	
	// Progress bar hide timeout.
	var progress_bar_hide_timeout;
	
	// Main Audio and Video tags DOM selectors.
	this.dom_audio = document.getElementById("senses-audio");
	this.dom_video = document.getElementById("senses-video");
	this.jq_audio = $("#senses-audio");
	this.jq_video = $("#senses-video");
	
	
	/**
	 * [Media Playback] Play Media
	 *
	 * Makes a media active and plays it.
	 *
	 * @param item_data			The one that came from the cloud!
	 */
	this.playMedia = function(item_data) {
		// If we are playing a media, pause it.
		if(this.current_media.is_playing)
			this.pause();
		
		// Store the new item data.
		this.current_media.item_data = item_data;
		
		// Change the player type, if necessary
		this.setPlayerType(item_data.player_type);
		
		// Update the Audio artwork.
		if(item_data.player_type == Audio_Player)
			$('#audio-screen > img.artist-thumb').attr('src', '/library/' + item_data.artist_codename + '/thumb.jpg');
		
		// Reset license sign.
		$('#license').removeClass();
		
		// Does this media have any kind of license?
		if(item_data.license) {
			// Set the license type.
			if(item_data.license.substr(0, 16) == 'Creative Commons') {
				// Apply CC goodies =3
				$('#license').addClass('cc').html(item_data.license.substr(17));
			} else {
				$('#license').html(item_data.license)
			}
			
			// Display the license notification.
			LicenseNotif.show();
		}
		
		// Reset the current media player and data.
		this.resetProgress();
		
		// Set the resource address.
		this.current_media.dom.src = item_data.media_src;
		
		// Load the resource.
		this.current_media.dom.load();
		
		// Update the "Now playing..." title.
		$('#topBar .title').html(item_data.artist_name + ' - ' + item_data.title);
		
		// Hide the Ticket Box.
		Navigation.displayTicketBox(false);
		
		// Set this media as currently playing (regardless if it's not yet).
		this.current_media.is_playing = true;
		
		// Display the loading notification.
		LoadingNotif.show();
		
		// Reset buffer progress.
		this.updateBufferProgress(0, 1);
	}
	
	/**
	 * [Media Playback] Reset Progress
	 *
	 * Resets the progress of a player to the beginning.
	 */
	this.resetProgress = function() {
		this.updateBufferProgress(0, 1);
		this.updateProgressPosition(0, 1);
		
		// Reset DOM.
		this.current_media.dom.pause();
		this.current_media.dom.src = '';
	}
	
	
	/**
	 * [Media Playback] Play
	 *
	 * Plays media. This methods depends that a media is already loaded.
	 */
	this.play = function() {
		// Tell the player to play.
		Player.current_media.dom.play();
		
		// Summon play notification.
		PlaybackNotif.show('play');
		
		// Display the progress bar.
		Player.displayProgressBar(true);
	}
	
	
	/**
	 * [Media Playback] Pause
	 *
	 * Pauses media. This methods depends that a media is already loaded.
	 */
	this.pause = function() {
		// Tell the player to pause.
		Player.current_media.dom.pause();
		
		// Summon pause notification.
		PlaybackNotif.show('pause');
		
		// Display the progress bar.
		Player.displayProgressBar(true);
		
		// Update the document title.
		document.title = default_document_title;
	}
	
	
	/**
	 * [Media Playback] Play Pause
	 *
	 * Plays or Pauses media depending on the current state.
	 */
	this.playPause = function() {
		if(!Player.current_media.dom)
			return;
			
		// Plays or pauses depending on the current state.
		if(Player.current_media.dom.paused)
			this.play();
		else
			this.pause();
	}
	
	
	/**
	 * [GUI] Update Progress Position
	 *
	 * Updates the seeker position in the progress bar.
	 */
	this.updateProgressPosition = function(currentTime, duration) {
		var newPos = currentTime * 384 / duration;
		$('#progress-bar > .bar > .position').css({left: (2 + newPos) + 'px'});
	}
	
	/**
	 * [GUI] Update Buffer Progress
	 *
	 * Updates the buffer progress bar with the provided values.
	 */
	this.updateBufferProgress = function(loaded, total) {
		// Calculate the percentage of progress.
		var perc = loaded / total;
		
		// Update the DOM with the new width.
		$('#progress-bar > .bar > .buffer').css({width: Math.round(Math.min(100, 2.5 + perc * 100)) + '%'});
		
		// Done class.
		if(perc >= 1)
			$('#progress-bar > .bar > .buffer').addClass('done');
		else
			$('#progress-bar > .bar > .buffer').removeClass('done');
	}
	
	/**
	 * [Media Playback] Set Player Type
	 *
	 * Sets wether it's a Video or Audio player.
	 */
	this.setPlayerType = function(player_type) {
		// Temporarily store the previous player type.
		var previous_player_type = this.current_media.player_type;
		
		// Exit if the player type did not change.
		if(previous_player_type == player_type)
			return;
			
		// Fade the player out, then remove it's source.
		$(this.current_media.dom).animate({'opacity': 0}, 300, function() {
			$(this).attr('src', '');
		});
		
		// Update the current media's player type.
		this.current_media.player_type = player_type;
		switch (player_type) {
			case 'video':
				// Define the active media DOM.
				this.current_media.dom = this.dom_video;
				break;
				
			case 'audio':
				// Define the active media DOM.
				this.current_media.dom = this.dom_audio;
				break;
		}
		
		// Restore visibility of the player.
		$(this.current_media.dom).css({opacity: 1});
		
		// Define the "now playing" or "now listening".
		$('#surface').removeClass('action-playing-' + previous_player_type);
		$('#surface').addClass('action-playing-' + player_type);
	}
	
	this.setBackgroundPoster = function(imgPath) {
		imgPath = imgPath.replace('/thumbs/', '/artworks/');
		imgPath = imgPath.replace('.png', '.jpg');
		$('body').css({'background-image': imgPath});
	}
	
	/**
	 * [GUI] Display Progress Bar
	 *
	 * Used to display the progress bar.
	 * Examples: seeking through a video, or new video is loaded resetting the seeking position.
	 *
	 * @param hold				If true... I have no idea...
	 */
	this.displayProgressBar = function(show) {
		// Reset the timeout, if any.
		if (this.progress_bar_hide_timeout) {
			clearTimeout(this.progress_bar_hide_timeout);
		}
		
		if(show == false) {
			/* Hide the progress bar */
			
			$('#progress-bar').removeClass('show');
		} else {
			/* Show the progress bar */
			
			$('#progress-bar').addClass('show');
			
			// Make it hide after the timeout.
			this.progress_bar_hide_timeout = setTimeout(function() {
				// Reset the timeout.
				this.progress_bar_hide_timeout = false;
				
				// Hide the progress bar.
				$('#progress-bar').removeClass('show');
			}, 1600);
		}
	}
}

/**
 * [Listener] Progress
 *
 * Called when the loading progress changes.
 */
function player_listenerProgress(event) {
	var loaded = $(Player.current_media.dom).attr("buffered").end();
	var length = $(Player.current_media.dom).attr("duration");
	
	Player.updateBufferProgress(loaded, length);
}




/**
 * [Listener] Can Play Through
 *
 * Called when there is enough buffer to play the whole media non-stop.
 */
function player_listenerCanPlayThrough(event) {
	// Play the media.
	Player.current_media.dom.play();
	
	Player.current_media.is_playing = true;
}

/**
 * [Listener] Suspend
 *
 * Called when the browser suspends loading the media data and does not have the entire media resource downloaded.
 */
function player_listenerSuspend(event) {
	// [TODO] Missing proper handling of this event.
	console.log('WARNING. Listener Suspend called, and not handled');
}


/**
 * [Listener] Waiting
 *
 * Called when the playback stopped due to not having enough buffer.
 */
function player_listenerWaiting(event) {
	// Shows the loading notification.
	LoadingNotif.show();
}

/**
 * [Listener] Time Update
 *
 * Called when the current time (that is, position in video) was updated. Normally caused by playback itself.
 */
function player_listenerTimeUpdate(event) {
	Player.updateProgressPosition(Player.current_media.dom.currentTime, Player.current_media.dom.duration);
}

/**
 * [Listener] Play
 *
 * Called when the media starts playing. Either from the beginning or leaving pause.
 */
function player_listenerPlay(event) {
	// Display the progress bar.
	Player.displayProgressBar(true);
	
	// Hide the loading notification.
	LoadingNotif.hide();
	
	// Update the document title.
	document.title = '▶ ' + Player.current_media.item_data.artist_name + ' - ' + Player.current_media.item_data.title;
}