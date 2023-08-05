/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
(function ($) {

	var $navbar_height = 49;   // Note: You will also need to change this variable in the "variable.less" file.

	// so far this is covering most hand held devices
	var isMobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));

	if (!isMobile) {
		// Desktop
		$('body').addClass('desktop-detected');
	} else {
		// Mobile
		$('body').addClass('mobile-detected');
		FastClick.attach(document.body);    		// Removes the tap delay in idevices - dependency: js/plugin/fastclick/fastclick.js
	}

	/**
	 * NAV or #LEFT-BAR resize detect
	 *
	 * Description: changes the page min-width of #CONTENT and NAV when navigation is resized.
	 *              This is to counter bugs for min page width on many desktop and mobile devices.
	 *
	 * Note:        This script uses JSthrottle technique so don't worry about memory/CPU usage
	 */
	function nav_page_height() {
		var setHeight = $('#main').height();
		// menuHeight = $left_panel.height();

		var windowHeight = $(window).height() - $navbar_height;

		// if content height exceedes actual window height and menuHeight
		var hMenu = (setHeight > windowHeight) ? setHeight : windowHeight;
		var hBody = (setHeight > windowHeight) ? setHeight + $navbar_height : windowHeight;

		$('#left-panel').css('min-height', hMenu + 'px');
		$('body').css('min-height', hBody + 'px');
	}

	/*
	 * FULL SCREEN FUNCTION
	 */

	// Find the right method, call on correct element
	function launchFullscreen(element) {
		$body = $('body');
		if (!$body.hasClass("full-screen")) {
			$body.addClass("full-screen");
			if (element.requestFullscreen) {
				element.requestFullscreen();
			} else if (element.mozRequestFullScreen) {
				element.mozRequestFullScreen();
			} else if (element.webkitRequestFullscreen) {
				element.webkitRequestFullscreen();
			} else if (element.msRequestFullscreen) {
				element.msRequestFullscreen();
			}
		} else {
			$body.removeClass("full-screen");
			if (document.exitFullscreen) {
				document.exitFullscreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			}
		}
	}

	/*
	 * END: FULL SCREEN FUNCTION
	 */

	$(document).ready(function () {

		nav_page_height();

		$('#main').resize(function () {
			nav_page_height();

			if ($(window).width() < 979) {
				$('body').addClass('mobile-view-activated')
					.removeClass('minified');
				$.cookie('collapsedmenu', 0);
			} else if ($('body').hasClass('mobile-view-activated')) {
				$('body').removeClass('mobile-view-activated');
			}
		});

		$('nav').resize(function () {
			nav_page_height();
		});

		// Collapse Left NAV
		$('.minifyme').click(function (e) {
			var c = $('body').toggleClass('minified').is('.minified');
			$.cookie('collapsedmenu', c ? 1 : 0);
			e.preventDefault();
		});

		$('[data-menu="hidemenu"]').click(function () {
			$('body').toggleClass('hidden-menu');
		});

		var $body = $('body');

		$body.on("click", '[data-action="launchFullscreen"]', function (b) {
			b.preventDefault();
			launchFullscreen(document.documentElement);
		});

		$body.on("click", '[data-action="sync-media"]', function (b) {
			b.preventDefault();
			var $this = $(this);
			$this.find('.fa').addClass('fa-spin');
			$.ajax({
				url: 'index.php?option=com_sellacious&task=media.syncAjax',
				type: 'POST',
				cache: false,
				dataType: 'json'
			}).done(function (response) {
				if (response.state == 1) {
					Joomla.renderMessages({success: [response.message]});
				} else {
					Joomla.renderMessages({error: [response.message]});
				}
			}).fail(function (xhr) {
				console.log(xhr.responseText);
				Joomla.renderMessages({error: ['Sync failed due to a system error.']});
			}).always(function () {
				$this.find('.fa').removeClass('fa-spin')
			});
		});


		// Keep only 1 active popover per trigger -
		// also check and hide active popover if user clicks on document
		$body.on('click', function (e) {
			$('[rel="popover"]').each(function () {
				// the 'is' for buttons that trigger popups
				// the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					$(this).popover('hide');
				}
			});
		});

		// css class workaround for sellacious template
		$('input.inputbox,.textarea').addClass('form-control');

		$('.premium-input').each(function () {
			var closest = $(this).closest('.input-container');
			if (closest.length == 0) closest = $(this).closest('div.controls');
			var e = $('<span class="premium-input-pre"></span>');
			closest.prepend(e);
		});

		if (typeof $.fn.select2 == 'function') {
			$('.hasSelect2').select2();
		}
	});

})(jQuery);

/**
 * Resizer with throttle
 * Source: http://benalman.com/code/projects/jquery-resize/examples/resize/
 */
(function ($, window, undefined) {

	var $throttle_delay = 350;  // Impacts the responce rate of some of the responsive elements (lower value affects CPU but improves speed)

	var elems = $([]), jq_resize = $.resize = $.extend($.resize, {}), timeout_id, str_setTimeout = 'setTimeout', str_resize = 'resize', str_data = str_resize + '-special-event', str_delay = 'delay', str_throttle = 'throttleWindow';

	jq_resize[str_delay] = $throttle_delay;

	jq_resize[str_throttle] = true;

	$.event.special[str_resize] = {

		setup: function () {
			if (!jq_resize[str_throttle] && this[str_setTimeout]) {
				return false;
			}

			var elem = $(this);
			elems = elems.add(elem);
			$.data(this, str_data, {
				w: elem.width(),
				h: elem.height()
			});
			if (elems.length === 1) {
				loopy();
			}
		},

		teardown: function () {
			if (!jq_resize[str_throttle] && this[str_setTimeout]) {
				return false;
			}

			var elem = $(this);
			elems = elems.not(elem);
			elem.removeData(str_data);
			if (!elems.length) {
				clearTimeout(timeout_id);
			}
		},

		add: function (handleObj) {
			if (!jq_resize[str_throttle] && this[str_setTimeout]) {
				return false;
			}
			var old_handler;

			function new_handler(e, w, h) {
				var elem = $(this), data = $.data(this, str_data);
				data.w = w !== undefined ? w : elem.width();
				data.h = h !== undefined ? h : elem.height();

				try {
					old_handler.apply(this, arguments);
				} catch (e) {
					// ignore the undefined tip error
				}
			}

			if ($.isFunction(handleObj)) {
				old_handler = handleObj;
				return new_handler;
			} else {
				old_handler = handleObj.handler;
				handleObj.handler = new_handler;
			}
		}
	};

	function loopy() {
		timeout_id = window[str_setTimeout](function () {
			elems.each(function () {
				var elem = $(this), width = elem.width(), height = elem.height(), data = $.data(this, str_data);
				if (width !== data.w || height !== data.h) {
					elem.trigger(str_resize, [data.w = width, data.h = height]);
				}

			});
			loopy();
		}, jq_resize[str_delay]);
	}
})(jQuery, window);
