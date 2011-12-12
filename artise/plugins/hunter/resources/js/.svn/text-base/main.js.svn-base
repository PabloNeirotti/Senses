$(document).ready(function() {
	$('.tip > button').click(function(){
		displayTip($(this).parent().find('.content').html());
	});
	
	$('#hunterTip > .close').click(function(){
		displayTip();
	});
	
	$('#logView > .bright').click(function(){
		displayTip();
	});
	
	$('.filter-bar > li').click(function() {
		$('.filter-bar > li.active').removeClass('active');
		$(this).addClass('active');
		
		if($(this).attr('id') == 'all') {
			$('.logList > div').slideDown(200);
			$('.logList > div').removeClass('nth-child-even').removeClass('nth-child-odd nth-child-even');
		} else {
			$('.logList > div').not('.' + $(this).attr('id')).removeClass('visible').slideUp(200);
			$('.logList > div.' + $(this).attr('id')).addClass('visible').slideDown(250, function() {
				$('.logList > div').filter('.visible').filter(':even').removeClass('nth-child-even').addClass('nth-child-odd');
				$('.logList > div').filter('.visible').filter(':odd').removeClass('nth-child-odd').addClass('nth-child-even');
			});
		}
	});
	
});

function displayTip(content) {
	
	if(content) {
		$('#hunterTip .bubble > div').html(content);
		$('body').addClass('hunterView');
		$('#logView > .bright').fadeIn(500);
	} else {
		$('body').removeClass('hunterView');
		$('#logView > .bright').fadeOut(500);
	}
}