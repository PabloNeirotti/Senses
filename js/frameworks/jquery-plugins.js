/*
	jQuery addEventListener
*/

(function($){
  $.fn.addEventListener = function(eventType, handler, scope, eventData){
    if (arguments.length < 2) 
      return;
      
    var fn = handler;
    if (arguments.length > 2) {
      fn = $.proxy(handler, scope);
    }
    return this.each(function(){
      $(this).bind(eventType, eventData, fn);
    });
    
  };
})(jQuery);



/*
	jQuery addAnimationClass
*/

(function($){
	$.fn.addAnimationClass = function(className, callback) {
	// Add class.
	$(this).addClass(className);
	
	$(this).addEventListener('webkitAnimationEnd', function() {
		$(this).removeClass(className);
		if (callback)
			callback();
	}, this, [className, callback]);
}
})(jQuery);



/*
	jQuery Preload
*/

(function($) {
  var cache = [];
  // Arguments are image paths relative to the current page.
  $.preLoadImages = function() {
    var args_len = arguments.length;
    for (var i = args_len; i--;) {
      var cacheImage = document.createElement('img');
      cacheImage.src = arguments[i];
      cache.push(cacheImage);
    }
  }
})(jQuery)