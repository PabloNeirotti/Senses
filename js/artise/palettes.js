/**                                                                     **
 *  This is an experimental release of Artise for JavaScript. This is    *
 *  not an official release and is only used for testing purposes.       *
 *                                                                       *
 *  For more information about Artise, please go to:                     *
 *  http://pixelsize.net/artise/                                         *
 *                                                                       */

function PalettesStore() {
	this.palettes_store = {
		'page_items_list' : {
			'main' : '<div class="browser list scrollable vertical focus"><div><!--zone:items--></div></div>',
			'blocks' : {
				'item' : '<div>%caption</div>',
				'item_wfeat' : '<div class="wfeat"><div>%caption</div><div>%feat</div></div>'
			}
		},
		
		'page_items_list_and_sidegroup' : {
			'main' : '<div class="list-and-sidegroup"><div class="sidegroup"><img src="%sidegroup" /><div></div></div><div class="browser list scrollable vertical focus"><div><!--zone:items--></div></div></div>',
			'blocks' : {
				'item' : '<div>%caption</div>',
				'item_wfeat' : '<div class="wfeat"><div>%caption</div><div>%feat</div></div>'
			}
		},
		
		'page_items_thumbs_square' : {
			'main' : '<div class="browser sliding-media scrollable horizontal focus"><div><!--zone:items--></div></div>',
			'blocks' : {
				'item' : '<div><img src="%thumb" /><span class="caption">%caption</span></div>'
			}
		},
		
		'page_items_thumbs_poster' : {
			'main' : '<div class="browser sliding-media poster scrollable horizontal focus"><div><!--zone:items--></div></div>',
			'blocks' : {
				'item' : '<div><img src="%thumb" /><span class="caption">%caption</span></div>'
			}
		},
		
		'page_media_types' : {
			'main' : '<nav class="categories focus"><!--zone:items--></nav>',
			'blocks' : {
				'item' : '<a href="#">%caption</a>'
			}
		}
	};
	
	this.get = function(palette_name) {
		return this.palettes_store[palette_name];
	}
}

var Palettes = new PalettesStore();