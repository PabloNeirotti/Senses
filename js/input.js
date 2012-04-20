/**                                                                     **
 *                              S E N S E S                              *
 *                           multimedia player                           *
 *                                                                       *
 *                        i n p u t   o b j e c t                        *
 *                                                                       *
 *                                                                       *
 *                                                                       *
 *  This object listens the keyboard or other devices' inputs and        *
 *  redirects them to the correct object.                                *
 *  Each device is an interface to the actual input controller           *
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


/* Keyboard */

var Keyboard_Repeat_Delay = 150;



/*** Input Object ***/

function InputObject() {
	
	/**
	 * Forward gesture.
	 */
	this.forward = function() {
		if (Navigation.ticket_box_visible == true) {
			/* Go forward at navigation */
			Navigation.displayTicketBox(true);
			Navigation.moveCursor(1);
		} else {
			/* Go forward at player */
			Player.current_media.dom.currentTime += 5;
			Player.displayProgressBar(true);
		}
	}
	
	/**
	 * Previous gesture.
	 */
	this.previous = function() {
		if (Navigation.ticket_box_visible == true) {
			/* Go previous at navigation */
			Navigation.displayTicketBox(true);
			Navigation.moveCursor(3);
		} else {
			/* Go previous at player */
			if (Player.current_media.dom.currentTime >= 5)
				Player.current_media.dom.currentTime -= 5;
			else
				Player.current_media.dom.currentTime = 0;
			Player.displayProgressBar(true);
		}
	}
	
	/**
	 * Action gesture.
	 */
	this.action = function() {
		if(Navigation.ticket_box_visible) {
			/* We are at the ticket box. Talk to Navigation */
			
			// Tell Navigation to execute what the cursor is on.
			Navigation.executeCursor();
		} else {
			/* We are at the player. Talk to Player */
			
			Player.playPause();
		}
	}
	
	/**
	 * Back gesture.
	 */
	this.back = function() {
		// Do only go back if we are at the ticket box.
		if (Navigation.ticket_box_visible == true) {
			Navigation.closePage();
		}
	}
	
	/**
	 * View switch gesture.
	 */
	this.view = function() {
		Navigation.displayTicketBox();
	}
	
}

/*** Keyboard Interface Object ***/

function KeyboardInterfaceObject() {
	var _this = this;
	
	// Stores if the keyboard is enabled to execute again while the key is still being pressed.
	_this.input_enabled = true;
	
	// The Timeout ID for the repeat waiting.
	_this.repeat_timeout = null;
	
	// Append Key strokes listener.
	$(document).keydown(function(event) {
		// Check if we can already process the input.
		if (_this.input_enabled == true) {
			
			// Disables input for a period of time.
			_this.input_enabled = false;
			
			// Re-enable this key after the repeat delay.
			_this.repeat_timeout = setTimeout(function() {
				_this.input_enabled = true;
			}, Keyboard_Repeat_Delay);
			
			// Switch through the different possible key strokes.
			switch (event.keyCode) {
				case 39: // Right
					event.preventDefault();
					Input.forward();
					break;
				
				case 37: // Left
					event.preventDefault();
					Input.previous();
					break;
				
				case 13: // Enter
				case 40: // Down
					event.preventDefault();
					Input.action();
					break;
					
				case 27: // Escape
					event.preventDefault();
					Input.view();
					break;
				
				case 38: // Up
					event.preventDefault();
					Input.back();
					break;
			}
		}
	});
	
	// Append key release listener.
	$(window).keyup(function(event) {
		// Clears the repeating-key timeout, as the key was released.
		clearTimeout(_this.repeat_timeout);
		
		// Re-enables keys to be pressed again.
		_this.input_enabled = true;
	});
}