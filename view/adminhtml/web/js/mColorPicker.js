/*
  mColorPicker
  Version: 1.0 r39
  
  Copyright (c) 2010 Meta100 LLC.
  http://www.meta100.com/
  
  Licensed under the MIT license 
  http://www.opensource.org/licenses/mit-license.php 
*/

// After this script loads set:
// $.fn.mColorPicker.init.replace = '.myclass'
// to have this script apply to input.myclass,
// instead of the default input[type=color]
// To turn of automatic operation and run manually set:
// $.fn.mColorPicker.init.replace = false
// To use manually call like any other jQuery plugin
// $('input.foo').mColorPicker({options})
// options:
// imageFolder - Change to move image location.
// swatches - Initial colors in the swatch, must an array of 10 colors.
// init:
// $.fn.mColorPicker.init.enhancedSwatches - Turn of saving and loading of swatch to cookies.
// $.fn.mColorPicker.init.allowTransparency - Turn off transperancy as a color option.
// $.fn.mColorPicker.init.showLogo - Turn on/off the meta100 logo (You don't really want to turn it off, do you?).

require([
	'jquery'
], function(jQuery){
	(function(mgsjQuery){

	  var $o, $i, i, $b,
		  div = '<div>',
		  img = '<img>',
		  span = '<span>',
		  $document = mgsjQuery(document),
		  $mColorPicker = mgsjQuery(div),
		  $mColorPickerBg = mgsjQuery(div),
		  $mColorPickerTest = mgsjQuery(div),
		  $mColorPickerInput = mgsjQuery('<input>'),
		  rRGB = /^rgb[a]?\((\d+),\s*(\d+),\s*(\d+)(,\s*(\d+\.\d+)*)?\)/,
		  rHEX = /([a-f0-9])([a-f0-9])([a-f0-9])/,
		  rHEX3 = /#[a-f0-9]{3}/,
		  rHEX6 = /#[a-f0-9]{6}/;

	  mgsjQuery.fn.mColorPicker = function(options) {

		var swatches = mgsjQuery.fn.mColorPicker.getCookie('swatches');

		$o = mgsjQuery.extend(mgsjQuery.fn.mColorPicker.defaults, options);
		mgsjQuery.fn.mColorPicker.defaults.swatches.concat($o.swatches).slice(-10);

		if ($i.enhancedSwatches && swatches) $o.swatches = swatches.split('||').concat($o.swatches).slice(0, 10) || $o.swatches;

		if (!mgsjQuery("div#mColorPicker").length) mgsjQuery.fn.mColorPicker.drawPicker();
		if (!mgsjQuery('#css_disabled_color_picker').length) mgsjQuery('head').prepend('<meta data-remove-me="true"/><style id="css_disabled_color_picker" type="text/css">.mColorPicker[disabled] + span, .mColorPicker[disabled="disabled"] + span, .mColorPicker[disabled="true"] + span {filter:alpha(opacity=50);-moz-opacity:0.5;-webkit-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5;cursor:default;}</style>');

		mgsjQuery('meta[data-remove-me=true]').remove();

		this.each(mgsjQuery.fn.mColorPicker.drawPickerTriggers);

		return this;
	  };

	  mgsjQuery.fn.mColorPicker.init = {
		replace: 'input.color',
		index: 0,
		enhancedSwatches: true,
		allowTransparency: true,
		slogan: 'Meta100 - Designing Fun',
		showLogo: true
	  };

	  mgsjQuery.fn.mColorPicker.defaults = {
		currentId: false,
		currentInput: false,
		currentColor: false,
		changeColor: false,
		color: false,
		imageFolder: 'images/',
		swatches: [
		  "#ffffff",
		  "#ffff00",
		  "#00ff00",
		  "#00ffff",
		  "#0000ff",
		  "#ff00ff",
		  "#ff0000",
		  "#4c2b11",
		  "#3b3b3b",
		  "#000000"
		]
	  };

	  mgsjQuery.fn.mColorPicker.start = function() {

		mgsjQuery('input[data-mcolorpicker!="true"]').filter(function() {
	  
		  return ($i.replace == '[type=color]')? this.getAttribute("type") == 'color': mgsjQuery(this).is($i.replace);
		}).mColorPicker();
	  };

	  mgsjQuery.fn.mColorPicker.events = function() {

		//mgsjQuery("#mColorPickerBg").live('click', mgsjQuery.fn.mColorPicker.closePicker);
		mgsjQuery(document).on("click", "#mColorPickerBg", mgsjQuery.fn.mColorPicker.closePicker);

		
		mgsjQuery(document).on("keyup", "mColorPicker",function(){
			try {
	  
				mgsjQuery(this).css({
				  'background-color': mgsjQuery(this).val()
				}).css({
				  'color': mgsjQuery.fn.mColorPicker.textColor(mgsjQuery(this).css('background-color'))
				}).trigger('change');
			  } catch (r) {}
		});


		//mgsjQuery('.mColorPickerTrigger').live('click', mgsjQuery.fn.mColorPicker.colorShow);
		mgsjQuery(document).on("click", ".mColorPickerTrigger", mgsjQuery.fn.mColorPicker.colorShow);
		
		mgsjQuery(document).on("mousemove", ".mColor, .mPastColor", function(e){
			if (!$o.changeColor) return false;
	  
		  var $t = mgsjQuery(this),
			  offset = $t.offset(),
			  $e = $o.currentInput,
			  hex = $e.attr('data-hex') || $e.attr('hex');

			  $o.color = $t.css("background-color");

			  if ($t.hasClass('mPastColor')) $o.color = mgsjQuery.fn.mColorPicker.setColor($o.color, hex);
			  else if ($t.hasClass('mColorTransparent')) $o.color = 'transparent';
			  else if (!$t.hasClass('mPastColor')) $o.color = mgsjQuery.fn.mColorPicker.whichColor(e.pageX - offset.left, e.pageY - offset.top, hex);

			  $o.currentInput.mSetInputColor($o.color);
		}).on("click", ".mColor, .mPastColor", mgsjQuery.fn.mColorPicker.colorPicked);
	  
		/* mgsjQuery('.mColor, .mPastColor').live('mousemove', function(e) {

		  
		}).live('click', mgsjQuery.fn.mColorPicker.colorPicked); */
		
		mgsjQuery(document).on("keyup", "#mColorPickerInput", function(e){
			try {
	  
				$o.color = $(this).val();
				$o.currentInput.mSetInputColor($o.color);
			
				if (e.which == 13) mgsjQuery.fn.mColorPicker.colorPicked();
			  } catch (r) {}
		}).on("blur", "#mColorPickerInput", function(){
			$o.currentInput.mSetInputColor($o.color);
		});
		
		mgsjQuery(document).on("mouseleave", "#mColorPickerWrapper", function(){
			if (!$o.changeColor) return false;

			  var $e = $o.currentInput; 

			  $o.currentInput.mSetInputColor(mgsjQuery.fn.mColorPicker.setColor($o.currentColor, ($e.attr('data-hex') || $e.attr('hex'))));
		});
	  };

	  mgsjQuery.fn.mColorPicker.drawPickerTriggers = function () {

		var $t = mgsjQuery(this),
			id = $t.attr('id') || 'color_' + $i.index++,
			hidden = $t.attr('text') == 'hidden' || $t.attr('data-text') == 'hidden'? true: false,
			color = mgsjQuery.fn.mColorPicker.setColor($t.val(), ($t.attr('data-hex') || $t.attr('hex'))),
			width = $t.width(),
			height = $t.height(),
			flt = $t.css('float'),
			$c = mgsjQuery(span),
			$trigger = mgsjQuery(span),
			colorPicker = '',
			$e;

		$c.attr({
		  'id': 'color_work_area',
		  'class': 'mColorPickerInput'
		}).appendTo($b)

		$trigger.attr({
		  'id': 'mcp_' + id,
		  'class': 'mColorPickerTrigger'
		}).css({
		  'display': 'inline-block',
		  'cursor': 'pointer'
		}).insertAfter($t)
		
		mgsjQuery(img).attr({
		  'src': $o.imageFolder + 'color.png'
		}).css({
		  'border': 0,
		  'margin': '0 0 0 3px',
		  'vertical-align': 'text-bottom'
		}).appendTo($trigger);

		$c.append($t);
		colorPicker = $c.html().replace(/type=[^a-z ]*color[^a-z //>]*/gi, 'type="' + (hidden? 'hidden': 'text') + '"');
		$c.html('').remove();
		$e = mgsjQuery(colorPicker).attr('id', id).addClass('mColorPicker').val(color).insertBefore($trigger);

		if (hidden) $trigger.css({
		  'border': '1px solid black',
		  'float': flt,
		  'width': width,
		  'height': height
		}).addClass($e.attr('class')).html('&nbsp;');

		$e.mSetInputColor(color);

		return $e;
	  };

	  mgsjQuery.fn.mColorPicker.drawPicker = function () {
	  
		var $s = mgsjQuery(div),
			$l = mgsjQuery('<a>'),
			$f = mgsjQuery(div),
			$w = mgsjQuery(div);

		$mColorPickerBg.attr({
		  'id': 'mColorPickerBg'
		}).css({
		  'display': 'none',
		  'background':'black',
		  'opacity': .01,
		  'position':'absolute',
		  'top':0,
		  'right':0,
		  'bottom':0,
		  'left':0,
		  'z-index': 999999
		}).appendTo($b);

		$mColorPicker.attr({
		  'id': 'mColorPicker',
		  'data-mcolorpicker': true
		}).css({
		  'position':'absolute',
		  'border':'1px solid #ccc',
		  'color':'#fff',
		  'width':'194px',
		  'height':'184px',
		  'font-size':'12px',
		  'font-family':'times',
		  'display': 'none'
		}).appendTo($b);

		$mColorPickerTest.attr({
		  'id': 'mColorPickerTest'
		}).css({
		  'display': 'none'
		}).appendTo($b);

		$w.attr({
		  'id': 'mColorPickerWrapper'
		}).css({
		  'position':'relative',
		  'border':'solid 1px gray'
		}).appendTo($mColorPicker);

		mgsjQuery(div).attr({
		  'id': 'mColorPickerImg',
		  'class': 'mColor'
		}).css({
		  'height': '136px',
		  'width': '192px',
		  'border': 0,
		  'cursor': 'crosshair',
		  'background-image': 'url(' + $o.imageFolder + 'picker.png)'
		}).appendTo($w);

		$s.attr({
		  'id': 'mColorPickerSwatches'
		}).css({
		  'border-right':'1px solid #000'
		}).appendTo($w);

		mgsjQuery(div).addClass(
		  'mClear'
		).css({
		  'clear': 'both'
		}).appendTo($s);

		for (i = 9; i > -1; i--) {

		  mgsjQuery(div).attr({
			'id': 'cell' + i,
			'class': "mPastColor" + ((i > 0)? ' mNoLeftBorder': '')
		  }).css({
			'background-color': $o.swatches[i].toLowerCase(),
			'height':'18px',
			'width':'18px',
			'border':'1px solid #000',
			'float':'left'
		  }).html(
			'&nbsp;'
		  ).prependTo($s);
		}

		$f.attr({
		  'id': 'mColorPickerFooter'
		}).css({
		  'background-image': 'url(' + $o.imageFolder + 'grid.gif)',
		  'position': 'relative',
		  'height': '26px'
		}).appendTo($w);

		$mColorPickerInput.attr({
		  'id': 'mColorPickerInput',
		  'type': 'text'
		}).css({
		  'border': 'solid 1px gray',
		  'font-size': '10pt',
		  'margin': '3px',
		  'width': '80px'
		}).appendTo($f);

		if ($i.allowTransparency) mgsjQuery(span).attr({
		  'id': 'mColorPickerTransparent',
		  'class': 'mColor mColorTransparent'
		}).css({
		  'font-size': '16px',
		  'color': '#000',
		  'padding-right': '30px',
		  'padding-top': '3px',
		  'cursor': 'pointer',
		  'overflow': 'hidden',
		  'float': 'right'
		}).text(
		  'transparent'
		).appendTo($f);

		if ($i.showLogo) $l.attr({
		  'href': 'http://meta100.com/',
		  'title': $i.slogan,
		  'alt': $i.slogan,
		  'target': '_blank'
		}).css({
		  'float': 'right'
		}).appendTo($f);
		
		mgsjQuery(img).attr({
		  'title': $i.slogan,
		  'alt': $i.slogan
		}).css({
		  'border': 0,
		  'border-left': '1px solid #aaa',
		  'right': 0,
		  'position': 'absolute'
		}).appendTo($l);

		mgsjQuery('.mNoLeftBorder').css({
		  'border-left':0
		});
	  };

	  mgsjQuery.fn.mColorPicker.closePicker = function () {

		$mColorPickerBg.hide();
		$mColorPicker.fadeOut()
	  };

	  mgsjQuery.fn.mColorPicker.colorShow = function () {

		var $t = mgsjQuery(this),
			id = $t.attr('id').replace('mcp_', ''),
			pos = $t.offset(),
			$i = mgsjQuery("#" + id),
			pickerTop = pos.top + $t.outerHeight(),
			pickerLeft = pos.left;

		if ($i.attr('disabled')) return false;

		$o.currentColor = $i.css('background-color')
		$o.changeColor = true;
		$o.currentInput = $i;
		$o.currentId = id;

		// KEEP COLOR PICKER IN VIEWPORT
		if (pickerTop + $mColorPicker.height() > $document.height()) pickerTop = pos.top - $mColorPicker.height();
		if (pickerLeft + $mColorPicker.width() > $document.width()) pickerLeft = pos.left - $mColorPicker.width() + $t.outerWidth();
	  
		$mColorPicker.css({
		  'top':(pickerTop) + "px",
		  'left':(pickerLeft) + "px",
		  'z-index': 999999
		}).fadeIn("fast");
	  
		$mColorPickerBg.show();
	  
		if (mgsjQuery('#' + id).attr('data-text')) $o.color = $t.css('background-color');
		else $o.color = $i.css('background-color');

		$o.color = mgsjQuery.fn.mColorPicker.setColor($o.color, $i.attr('data-hex') || $i.attr('hex'));

		$mColorPickerInput.val($o.color);
	  };

	  mgsjQuery.fn.mColorPicker.setInputColor = function (id, color) {

		mgsjQuery('#' + id).mSetInputColor(color);
	  };

	  mgsjQuery.fn.mSetInputColor = function (color) {
	  
		var $t = mgsjQuery(this),
			css = {
			  'background-color': color,
			  'background-image': (color == 'transparent')? "url('" + $o.imageFolder + "grid.gif')": '',
			  'color': mgsjQuery.fn.mColorPicker.textColor(color)
			};
	  
		if ($t.attr('data-text') || $t.attr('text')) $t.next().css(css);

		$t.val(color).css(css).trigger('change');

		$mColorPickerInput.val(color);
	  };

	  mgsjQuery.fn.mColorPicker.textColor = function (val) {

		val = mgsjQuery.fn.mColorPicker.RGBtoHex(val);

		if (typeof val == 'undefined' || val == 'transparent') return "black";

		return (parseInt(val.substr(1, 2), 16) + parseInt(val.substr(3, 2), 16) + parseInt(val.substr(5, 2), 16) < 400)? 'white': 'black';
	  };

	  mgsjQuery.fn.mColorPicker.setCookie = function (name, value, days) {
	  
		var cookie_string = name + "=" + escape(value),
		  expires = new Date();
		  expires.setDate(expires.getDate() + days);
		cookie_string += "; expires=" + expires.toGMTString();
	   
		document.cookie = cookie_string;
	  };

	  mgsjQuery.fn.mColorPicker.getCookie = function (name) {
	  
		var results = document.cookie.match ( '(^|;) ?' + name + '=([^;]*)(;|$)' );
	  
		if (results) return (unescape(results[2]));
		else return null;
	  };

	  mgsjQuery.fn.mColorPicker.colorPicked = function () {

		$o.changeColor = false;
	  
		mgsjQuery.fn.mColorPicker.closePicker();
		mgsjQuery.fn.mColorPicker.addToSwatch();
	  
		$o.currentInput.trigger('colorpicked');
	  };

	  mgsjQuery.fn.mColorPicker.addToSwatch = function (color) {
	  
		if (!$i.enhancedSwatches) return false;

		var swatch = []
			i = 0;
	 
		if (typeof color == 'string') $o.color = color;
		if ($o.color != 'transparent') swatch[0] = mgsjQuery.fn.mColorPicker.hexToRGB($o.color);
	  
		mgsjQuery('.mPastColor').each(function() {
	  
		  var $t = mgsjQuery(this);

		  $o.color = mgsjQuery.fn.mColorPicker.hexToRGB($t.css('background-color'));

		  if ($o.color != swatch[0] && swatch.length < 10) swatch[swatch.length] = $o.color;
	  
		  $t.css('background-color', swatch[i++])
		});

		if ($i.enhancedSwatches) mgsjQuery.fn.mColorPicker.setCookie('swatches', swatch.join('||'), 365);
	  };

	  mgsjQuery.fn.mColorPicker.whichColor = function (x, y, hex) {

		var color = [255, 255, 255];

		if (x < 32) {
	  
		  color[1] = x * 8;
		  color[2] = 0;
		} else if (x < 64) {
	  
		  color[0] = 256 - (x - 32 ) * 8;
		  color[2] = 0;
		} else if (x < 96) {
	  
		  color[0] = 0;
		  color[2] = (x - 64) * 8;
		} else if (x < 128) {
	  
		  color[0] = 0;
		  color[1] = 256 - (x - 96) * 8;
		} else if (x < 160) {
	  
		  color[0] = (x - 128) * 8;
		  color[1] = 0;
		} else {
	  
		  color[1] = 0;
		  color[2] = 256 - (x - 160) * 8;
		}

		for (var n = 0; n < 3; n++) {

		  if (y < 64) color[n] += (256 - color[n]) * (64 - y) / 64;
		  else if (y <= 128) color[n] -= color[n] * (y - 64) / 64;
		  else if (y > 128) color[n] = 256 - ( x / 192 * 256 );
	  
		  color[n] = Math.round(Math.min(color[n], 255));

		  if (hex == 'true') color[n] = mgsjQuery.fn.mColorPicker.decToHex(color[n]);
		}

		if (hex == 'true') return "#" + color.join('');
		
		return "rgb(" + color.join(', ') + ')';
	  };

	  mgsjQuery.fn.mColorPicker.setColor = function (color, hex) {

		if (hex == 'true') return mgsjQuery.fn.mColorPicker.RGBtoHex(color);

		return mgsjQuery.fn.mColorPicker.hexToRGB(color);
	  }

	  mgsjQuery.fn.mColorPicker.colorTest = function (color) {

		$mColorPickerTest.css('background-color', color);

		return $mColorPickerTest.css('background-color');
	  }

	  mgsjQuery.fn.mColorPicker.decToHex = function (color) {

		var hex_char = "0123456789ABCDEF";

		color = parseInt(color);

		return String(hex_char.charAt(Math.floor(color / 16))) + String(hex_char.charAt(color - (Math.floor(color / 16) * 16)));
	  }

	  mgsjQuery.fn.mColorPicker.RGBtoHex = function (color) {

		var decToHex = "#",
			rgb;

		color = color? color.toLowerCase(): false;

		if (!color) return '';
		if (rHEX6.test(color)) return color.substr(0, 7);
		if (rHEX3.test(color)) return color.replace(rHEX, "$1$1$2$2$3$3").substr(0, 7);

		if (rgb = color.match(rRGB)) {

		  for (var n = 1; n < 4; n++) decToHex += mgsjQuery.fn.mColorPicker.decToHex(rgb[n]);
		
		  return decToHex;
		}

		return mgsjQuery.fn.mColorPicker.colorTest(color);
	  };

	  mgsjQuery.fn.mColorPicker.hexToRGB = function (color) {

		color = color? color.toLowerCase(): false;

		if (!color) return '';
		if (rRGB.test(color)) return color;

		if (rHEX3.test(color)) {

		  if (!rHEX6.test(color)) color = color.replace(rHEX, "$1$1$2$2$3$3");
	  
		  return 'rgb(' + parseInt(color.substr(1, 2), 16) + ', ' + parseInt(color.substr(3, 2), 16) + ', ' + parseInt(color.substr(5, 2), 16) + ')';
		}

		return mgsjQuery.fn.mColorPicker.colorTest(color);
	  };

	  $i = mgsjQuery.fn.mColorPicker.init;

	  $document.ready(function () {

		$b = mgsjQuery('body');

		mgsjQuery.fn.mColorPicker.events();

		if ($i.replace) {

		  if (typeof mgsjQuery.fn.mDOMupdate == "function") {
	  
			mgsjQuery('input').mDOMupdate(mgsjQuery.fn.mColorPicker.start);
		  } else if (typeof mgsjQuery.fn.livequery == "function") {
	  
			mgsjQuery('input').livequery(mgsjQuery.fn.mColorPicker.start);
		  } else {
	  
			mgsjQuery.fn.mColorPicker.start();
			//$document.live('ajaxSuccess.mColorPicker', mgsjQuery.fn.mColorPicker.start);
			
			mgsjQuery(document).on("ajaxSuccess.mColorPicker", mgsjQuery(document), mgsjQuery.fn.mColorPicker.start);
			
		  }
		}
	  });
	})(jQuery);
});