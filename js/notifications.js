/**                                                                     **
 *                              S E N S E S                              *
 *                           multimedia player                           *
 *                                                                       *
 *                n o t i f i c a t i o n s   o b j e c t                *
 *                                                                       *
 *                                                                       *
 *                                                                       *
 *  Loading, Pause/Play notifications and such.                          *
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



/*** Navigation Objects ***/

/**
 * Passive Notification
 *
 * It can have many show levels, and is only gone when all tasks are
 * completed.
 */

function PassiveNotificationObject(jq_notification) {
	// Counter for show levels.
	this.show_level = 0;
	
	// Define the jQuery DOM selector.
	this.jq_notification = jq_notification;
	
	/**
	 * Show
	 *
	 * Adds one show level.
	 */
	this.show = function() {
		// Add a show level.
		this.show_level ++;
		
		// Make sure to apply the 'show' class to it.
		$(this.jq_notification).addClass('show');
	}
	
	/**
	 * Hide
	 *
	 * Removes one show level. If it reaches zero, the notification
	 * will be hidden.
	 */
	this.hide = function() {
		// Remove a show level.
		this.show_level --;
		
		// This shouldn't happen. Ever. But just in case...
		if(this.show_level < 0)
			this.show_level = 0;
		
		// Remove the 'show' class from it.
		$(this.jq_notification).removeClass('show');
	}
}

/**
 * Active Notification
 *
 * It shows up, then goes away.
 */
function ActiveNotificationObject(jq_notification, animation_name) {
	// Define the jQuery DOM selector.
	this.jq_notification = jq_notification;
	
	// Store the animation name
	this.animation_name = animation_name;
	
	/**
	 * Show
	 *
	 * Displays the object, then makes it fade off.
	 *
	 * @param icon			Icon to display in the notification.
	 */
	this.show = function(icon) {
		// Make sure to apply the 'show' class to it.
		//$(this.jq_notification).addClass('show');
		
		var selector = this.jq_notification;
		var animation_name = this.animation_name;
		
		if(icon) {
			$(selector).removeClass().addClass(icon)
		}
		$(selector).css({'-webkit-animation-name': ''});
		setTimeout(function() {
			$(selector).css({'-webkit-animation-name': animation_name});
		}, 0);
	}
}