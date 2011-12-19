/**                                                                     **
 *  This is an experimental release of Artise for JavaScript. This is    *
 *  not an official release and is only used for testing purposes.       *
 *                                                                       *
 *  For more information about Artise, please go to:                     *
 *  http://pixelsize.net/artise/                                         *
 *                                                                       */
 
 function Slate(palette_name) {
	var _this = this;
	
	// Stores the Palette source.
	_this.palette = Palettes.get(palette_name);
	
	// Stores the defined blocks.
	_this.blocks = new Array();
	
	// Pointer for blocks.
	_this.block_pointer = -1;
	
	// Where the variables will be hosted for replace later on.
	_this.variables = new Array();
	
	/**
	 * Sets a variable token.
	 */
	_this.setvar = function(variable, value) {
		_this.variables.push(Array(variable, value));
	}
	
	_this.block = function(block_zone, block_name) {
		_this.block_pointer ++;
		
		// Each block is stored with a Target Block Zone and a Block Object.
		_this.blocks[_this.block_pointer] = new Array(	block_zone,
														new Block(_this.palette['blocks'][block_name])
														);
		
		// Returns only the Block Object, for manipulation.
		return _this.blocks[_this.block_pointer][1];
	}
	
	/**
	 * Replaces all block zones with finished blocks.
	 */
	_this.toString = function() {
		// Copy the Palette's main html source.
		var palette_main_html = _this.palette['main'];
		
		// Where each block is printed, separated on different Block Zones.
		var blocks_by_zones = {};
		
		for(var i = 0; i < _this.blocks.length; i ++) {
			
			// If the block zone was not used yet, define it.
			if(!blocks_by_zones[_this.blocks[i][0]])
				blocks_by_zones[_this.blocks[i][0]] = '';
			
			// Paste the HTML code to the corresponding Block Zone.
			blocks_by_zones[_this.blocks[i][0]] += _this.blocks[i][1];
		}
		
		// Replace each Block Zone in the HTML.
		for(zone in blocks_by_zones) {
			palette_main_html = palette_main_html.replace('<!--zone:' + zone + '-->', blocks_by_zones[zone]);
		}
		
		// Replace each variable in the HTML.
		for(var i = 0; i < _this.variables.length; i ++) {
			palette_main_html = palette_main_html.replace('%' + _this.variables[i][0], _this.variables[i][1]);
		}
		
		// Return the painted Slate.
		return palette_main_html;
	}
}

function Block(block_html) {
	var _this = this;
	
	// Store this block's HTML.
	_this.block_html = block_html;
	
	// Where the variables will be hosted for replace later on.
	_this.variables = new Array();
	
	/**
	 * Sets a variable token.
	 */
	_this.setvar = function(variable, value) {
		_this.variables.push(Array(variable, value));
	}
	
	/**
	 * Replaces all variables and return the finished block.
	 */
	_this.toString = function() {
		// Copy the block html source.
		var block_html = _this.block_html;
		
		// Replace each variable in the HTML.
		for(var i = 0; i < _this.variables.length; i ++) {
			block_html = block_html.replace('%' + _this.variables[i][0], _this.variables[i][1]);
		}
			
		// Return the finished block.
		return block_html;
	}
}