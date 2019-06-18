function closeColorTable(el){
	require([
		"jquery"
	], function($){
		$(el).slideUp('normal');
	});
}

function openColorTable(el){
	require([
		"jquery"
	], function($){
		$(el).slideToggle('normal');
	});
}

function changeInputColor(name, input, el, wrapper){
	require([
		"jquery"
	], function($){
		$('#'+input).val(name);
		$('#'+wrapper+' ul li a').removeClass('active');
		$(el).addClass('active');
		divwrapper = wrapper.replace('colour-content','color');
		$('.'+divwrapper+' .remove-color').show();
	});
	
}

function removeColor(input, el){
	require([
		"jquery"
	], function($){
		$('#'+input).val('');
		$(el).hide();
	});
	
}

function setLocation(url) {
	require([
		'jquery'
	], function (jQuery) {
		(function () {
			window.location.href = url;
		})(jQuery);
	});
}