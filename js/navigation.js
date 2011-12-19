/**                                                                     **
 *                              S E N S E S                              *
 *                           multimedia player                           *
 *                                                                       *
 *                   n a v i g a t i o n   o b j e c t                   *
 *                                                                       *
 *                                                                       *
 *                                                                       *
 *  This object handles listings, navigation and selector.               *
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

var Horizontal_Thumbed_Listing_Min_Width = 900;		// Minimum width of non-fixed items.
var Horizontal_Thumbed_Listing_Initial_X = 440;		// The initial TranslateX position of horizontally scrolled thumb listing.
var Horizontal_Thumbed_Listing_Min_Steady_Item = 2;	// How many items to go past in the listing for it to scroll.

var Vertical_Listing_Y_Offset_Perc = .25;			// The vertical offset of the listing, in screen percentage.
var Vertical_Listing_Initial_Y = 80;				// The initial TranslateY position of vertically scrolled listing.
var Vertical_Listing_Min_Steady_Items = 3;			// How many items to go past in the listing for it to scroll.


/*** Navigation Object ***/

function NavigationObject() {
	// Cursor.
	this.cursor = {
		jq_target: null,			// Target jQuery DOM element where the cursor is at.
		position: -1				// Position of cursor in the current listing.	(-1 means hidden)
	};
	
	// Page level.
	this.page_level = 0;			// 0 = nothing.
	
	// Level data, which stores information of every folder/media on each level.
	this.level_data = new Array(null);		// Each item has the properties: cursor_position, item_list
	
	// Address.
	this.address = '/';				// Current navigation address.
	
	// Ticket Box visibility.
	this.ticket_box_visible = true;	// 0 = hidden. 1 = visible.
	
	
	/**
	 * Reset Cursor
	 *
	 * Resets cursor information. Used mostly when opening a new page.
	 **/
	this.resetCursor = function() {
		this.cursor.jq_target = null;
		this.cursor.position = -1;
	}
	
	this.moveCursor = function(direction) {
		
		// Define the direct parent of the items we move through.
		if($('.focus').hasClass('scrollable')) {
			var jq_selector = $('.focus > div');
		} else {
			var jq_selector = $('.focus');
		}
		
		// Get the total item count.
		var item_count = $(jq_selector).children().length;
		
		switch(direction) {
			case 1: // Right
				if ($(jq_selector).children('.sel').length > 0) {
				
					// Make the previous target loose selection.
					$(this.cursor.jq_target).removeClass('sel');
					
					// Move the cursor forward, and reset if it reaches the limit.
					this.cursor.position ++;
					if (this.cursor.position > item_count)
						this.cursor.position = 1;
				} else if ($(jq_selector).children('.sel').length == 0) {
					/* Default behavior when a key is pressed with nothing selected. */
					
					// Update the cursor position.
					this.cursor.position = 1;
				}
				break;
				
			case 3: // Left
				if ($(jq_selector).children('.sel').length > 0) {
					
					// Make the previous target loose selection.
					$(this.cursor.jq_target).removeClass('sel');
					
					// Move the cursor backwards, and go to the end of the line if it reaches the beginning.
					this.cursor.position --;
					if (this.cursor.position <= 0)
						this.cursor.position = item_count;
				} else if ($(jq_selector).children('.sel').length == 0) {
					/* Default behavior when a key is pressed with nothing selected. */
					
					// Update the cursor position.
					this.cursor.position = $(jq_selector).children('*').size();
				}
				break;
		}
		
		// Apply cursor position change on the DOM.
		this.setCursor();
		
		// Update the list slider. Scrolling. Yeah, that stuff.
		this.updateListSlide();
	}
	
	/**
	 * [Internal] Update List Slide
	 *
	 * Update list slider, if this navigation is scrollable.
	 *
	 * [WARNING] This function is not DRY. It's definitely wet.
	 */
	this.updateListSlide = function() {
		// If the slider is not scrollable, then just exit.
		if(!$('.focus').hasClass('scrollable'))
			return;
		
		// Get dependencies.
		var jq_selector = $('.focus > div');
		var scroll_vertical = $('.focus').hasClass('vertical') ? true : false;
		
		// Get the total item count.
		var item_count = $(jq_selector).children().length;
		
		if(scroll_vertical) {
			/* Vertical scrolling */
			
			var item_height = $(jq_selector).children('*:last-child').outerHeight(true);
			
			var end_of_road_offset = Math.ceil(($(window).height() * Vertical_Listing_Y_Offset_Perc) / item_height);
			
			if(this.cursor.position > item_count - end_of_road_offset && item_count > end_of_road_offset) {
				// Do not scroll on last two elements.
				// Visual feedback of "end of road".
				var newPosition = (Vertical_Listing_Initial_Y - (item_count - 2 - end_of_road_offset) * item_height);
			} else {
				// Scroll to the current Selector Position.
				var newPosition = (Vertical_Listing_Initial_Y - (Math.max(Vertical_Listing_Min_Steady_Items, this.cursor.position) - Vertical_Listing_Min_Steady_Items) * item_height);
			}
				
			$(jq_selector).css({ '-webkit-transform': 'translate3d(0, ' + newPosition + 'px, 0)'});
		} else {
			/* Horizontal scrolling */
			
			var item_width = $(jq_selector).children('*:last-child').outerWidth(true);
			
			var end_of_road_offset = Math.ceil(($(window).width() - Horizontal_Thumbed_Listing_Min_Width) / item_width);
			
			if(this.cursor.position > item_count - end_of_road_offset && item_count > end_of_road_offset) {
				// Do not scroll on last two elements.
				// Visual feedback of "end of road".
				var newPosition = (Horizontal_Thumbed_Listing_Initial_X - (item_count - 1 - end_of_road_offset) * item_width)
			} else {
				// Scroll to the current Selector Position.
				var newPosition = Horizontal_Thumbed_Listing_Initial_X
									- (Math.max(Horizontal_Thumbed_Listing_Min_Steady_Item, this.cursor.position - 1) - Horizontal_Thumbed_Listing_Min_Steady_Item)
									* item_width;
			}
				
			$(jq_selector).css({ '-webkit-transform': 'translate3d(' + newPosition + 'px, 0, 0)'});
		}
	}
	
	/**
	 * Set Cursor
	 *
	 * Sets the cursor on a position. If none is provided, the current position is otherwise used.
	 *
	 * @param position		Int.
	 */
	this.setCursor = function(position) {
		// Update the cursor position, if one was provided.
		if(position)
			this.cursor.position = position;
		
		// Update this level's information.
		this.level_data[this.page_level].cursor_position = this.cursor.position;
		
		// Make previous targets look deselected.
		$('#navigation > .focus > .sel').removeClass('sel');
		
		// Define the direct parent of the items we move through.
		if($('.focus').hasClass('scrollable')) {
			var jq_selector = $('.focus > div');
			var scrollable = true;
		} else {
			var jq_selector = $('.focus');
			var scrollable = false;
		}
		
		// Update the cursor's target.
		this.cursor.jq_target = $(jq_selector).children(':nth-child(' + this.cursor.position + ')');
		
		// Make the new target look selected.
		$(this.cursor.jq_target).addClass('sel');
	}
	
	/**
	 * Execute cursor
	 *
	 * It executes the item the cursor is on.
	 * That will lead to opening a new page, or initiating a media playback.
	 */
	this.executeCursor = function() {
		// Exit if the cursor is not on anything.
		if(this.cursor.position <= 0)
			return;
		
		// Fetch item's data.
		var item_data = this.level_data[this.page_level].item_list[this.cursor.position - 1];
		
		// Act upon the link type.
		switch(item_data.link_type) {
			case 'folder':
				// Enter a page using that link.
				this.enterPage(item_data.link);
				break;
			
			case 'media':
				/* Tell the player to play this media */
				
				// Does this media have auto-playlist?
				if(item_data.auto_playlist) {
					// Play this playlist, at the current position.
					Player.playPlaylist(this.level_data[this.page_level].item_list, this.cursor.position - 1);
				} else {
					// Play this media item, and clear current playlist.
					Player.clearPlaylist();
					Player.playMedia(item_data);
				}
				break;
		}
	}
	
	
	/**
	 * Enter page
	 *
	 * Enters a page, by appending it to the navigation, focusing it,
	 * and entering a new navigation level.
	 *
	 * @param address		String. Must begin with a slash, and NOT end with a slash.
	 *								ie: "/library:videos"
	 * @param active		Bool.	Wether it is the final address or not. That will
	 *								mean the Hash is changed and so on.
	 */
	this.enterPage = function(address, passive) {
		// Show loading notification.
		LoadingNotif.show();
		
		// Scope stuff.
		var page_passive_load = passive;
		
		// Request page.
		$.ajax({
			url: address + '/',
			context: this,
			success: function(json){
				// We are waiting. Hide the loading notification.
				LoadingNotif.hide();
				
				// Cut off Artise Procedure's JSON to the last Action.
				json = json.pop();
				
				// Get the page type and the listing.
				var page_info = json[0];
				var list = json[1];
				
				// Create a new Slate, with the corresponding palette.
				var slate = new Slate('page_' + page_info.type);
				
				// Sidegroup stuff.
				if(page_info.type == 'items_list_and_sidegroup') {
					slate.setvar('sidegroup', page_info.thumbnail);
				}
				
				// Loop through each item.
				for(i in list) {
					// Create a new 'item' block in the 'items' zone.
					if(list[i].feat) {
						block = slate.block('items', 'item_wfeat');
						block.setvar('feat', 'Feat. ' + list[i].feat);
					} else {
						block = slate.block('items', 'item');
					}
					
					// Set variable tokens.
					switch(page_info.type) {
						case 'media_types':		// Media types. i.e Music, Videos, Series, Movies.
							block.setvar('caption', list[i].caption);
							break;
							
						case 'items_list':		// Vertical list. Used mostly for Music.
						case 'items_list_and_sidegroup':
							block.setvar('caption', (list[i].caption ? list[i].caption : list[i].title));
							break;
							
						case 'items_thumbs_square':		// Box thumbnails listing.
						case 'items_thumbs_poster':		// Rectangle thumbnails listing.
							block.setvar('caption', (list[i].caption ? list[i].caption : list[i].title));
							block.setvar('thumb', list[i].thumbnail);
							break;
					}
				}
				
				// Make any other element loose focus.
				$('.focus').removeClass('focus');
				
				// Append page to the navigation.
				$('#navigation').append(slate.toString());
				
				var stylesheet = null;
				
				// Insert Stylesheet if one was provided.
				if(page_info.stylesheet) {
					stylesheet = $('<link rel="stylesheet" type="text/css" href="' + page_info.stylesheet + '">');
					$('head').append(stylesheet);
				}
				
				// Update current address.
				this.address = address;
				window.location.hash = address;
				
				// Go one step forward in page level.
				this.page_level ++;
				
				// Add this page to breadcrumbs, if it has a title.
				if(page_info.title)
					$('#breadcrumbs').append('<span>' + page_info.title + '</span>');
				
				// Store the list as this new page level's data (naturally, at the end of the array).
				this.level_data.push({	cursor_position: -1,
										page_info: page_info,
										item_list: list,
										address: address,
										stylesheet: stylesheet
										});
				
				// Reset the cursor.
				this.resetCursor();
			}
		});
	}
	
	
	/**
	 * Close page
	 *
	 * Closes the current page, going back one level in the navigation.
	 */
	this.closePage = function() {
		// Do not execute if we are already at root level.
		if(this.page_level <= 1)
			return;
		
		// Removes the focus class, and adds a close one.
		$('#navigation > div:last-child').removeClass('focus').addAnimationClass('close', function() {
			// Once the closing animation is over, remove this page.
			$('#navigation > div:last-child').remove();
		});
		
		// Remove this page from breadcrumbs, if it was put there before.
		if(this.level_data[this.page_level].page_info.title) {
			$('#breadcrumbs > span').last().remove();
		}
		
		// Remove this page's Stylesheet, if any.
		if(this.level_data[this.page_level].stylesheet) {
			$(this.level_data[this.page_level].stylesheet).remove();
		}
		
		// Go one step back in page level.
		this.page_level --;
		
		// Update current address.
		this.address = this.level_data[this.page_level].address;
		window.location.hash = this.address;
		
		// Focus on the previous page level.
		$('#navigation').children().eq(this.page_level - 1).addClass('focus');
		
		// Restore the cursor position at the last page.
		this.setCursor(this.level_data[this.page_level].cursor_position);
		
		// Remove current page level's data (naturally, the last one).
		this.level_data.pop();
	}
	
	
	/**
	 * Display Ticket Box
	 *
	 * Displays or hides the ticket box.
	 *
	 * @param visible		Bool.	Wether to make it visible or not.
	 *								If nothing is provided, it switches status.
	 */
	this.displayTicketBox = function(visible) {
		// Exit if there will be no status change.
		if (this.ticket_box_visible == visible)
			return false;
		
		// Define visible as the opposite of the current status, if nothing was provided.
		if(!visible)
			visible = !this.ticket_box_visible;
			
		if (visible == true) {
			/* Display it */
			
			// Remove old class and make it zoom in.
			$('#ticket-box').removeClass('fade-out').addClass('zoom-in');
		} else {
			/* Hide it */
			
			// Remove old class and make it fade out.
			$('#ticket-box').removeClass('zoom-in').addClass('fade-out');
		}
		
		// Save new status.
		Navigation.ticket_box_visible = visible;
		
		// Add CSS indicators.
		if(Navigation.ticket_box_visible == true) {
			$('#surface').addClass('action-ticket-box');
		} else {
			$('#surface').removeClass('action-ticket-box');
		}
	}
}