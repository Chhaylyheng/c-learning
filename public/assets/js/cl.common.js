var FixedSize = 49;
var LangChk;
var i18nSet = false;

$(function() {
// $(window).on('load', function() {
	$.clLanguageDetect = function(callback) {
		var lang;
		$.ajax({
			async: true,
			url: "/restbase/languageDetect.json",
			dataType: 'json',
			success: function(lang) {
				callback(lang);
			}
		});
	}

	LangChk = $.when(
		$.clLanguageDetect(function(res) {
			var lang = res.lang;
			var clang = GetCookie('CL_LANG');
			if (clang) {
				lang = clang
			}
			jQuery.i18n.properties({
				async: true,
				name:'clJsMessages',
				path:'/assets/js/i18n/',
				mode:'both',
				language: lang,
				encoding: 'UTF-8',
				callback: function(){}
			});
			i18nSet = true;
		})
	);

	if ($('input[name=ltzone]').get(0)) {
		var tzone = moment.tz.guess();
		$('input[name=ltzone]').val(tzone);
	}
	if ($('.social-service').get(0)) {
		var tzone = moment.tz.guess();
		var hreft = '';
		$('.social-service').each(function() {
			hreft = $(this).attr('href');
			$(this).attr('href',hreft+'?tz='+encodeURIComponent(tzone));
		});
	}
	if ($('#tz-init').get(0)) {
		var def = $('#tz-init').attr('default');
		if (!def) {
			def = moment.tz.guess();
		}
		var region = def.split('/');

		$('#tz-region').val(region[0]);
		$('#tz-timezone optgroup[label='+region[0]+']').show();
		$('#tz-timezone').val(def);
	}

});


$(function() {
	$(document).on("keypress", "input:not(.allow_submit)", function(event) {
		return event.which !== 13;
	});

	$(document).on("click", function() {
		$('.dropdown-list:not(:animated)').slideUp('fast');
	});

	$('a[disabled=disabled]').on('click', function() {
		return false;
	});


	$('.deleteBtn').on('click', function(e) {
		var url = $(this).attr('href');
		var sM = $(this).attr('data');
		var code = null;
		switch (sM) {
			case 't-stu':
				code = "cl_common_deleteBtn_1";
			break;
			case 't-class':
				code = "cl_common_deleteBtn_2";
			break;
			case 't-attendreserve':
				code = "cl_common_deleteBtn_3";
			break;
			case 't-attenddelete':
				code = "cl_common_deleteBtn_4";
			break;
		}
		confirm($.i18n.prop(code), function(bOK) {
			if (bOK) {
				location.href = url;
			}
			return false;
		});
		return false;
	});
/*
	$('.formSubmit').on('click',function(){
		$(this).parents('form').submit();
	});
*/
	$('.window-close').on('click', function(e) {
		window.close();
	});
	$('.window-print').on('click', function(e) {
		window.print();
	});

	$('.scrollBottom').on('click', function(e) {
		scrollBottom();
		console.log( $(document).height());
		return false;
	});

	$('.custom_menu').on('click',function(e) {
		$('#CustomMenu').css({top: (e.pageY-45)+'px', left: (e.pageX-$('#CustomMenu').width()+50)+'px'});
		$('#CustomMenu').show();
		return false;
	});
	$('#CustomMenu .close a').on('click', function() {
		$('#CustomMenu').hide();
		return false;
	});

	$('button.dropdown-toggle').on('click', function() {
		if ($(this).next("ul.dropdown-list").css('display') == 'none') {
			$(this).next("ul.dropdown-list").slideDown('fast');
		} else {
			$(this).next("ul.dropdown-list").slideUp('fast');
		}
	});

	$('button.custommenu-dropdown-toggle').on('click', function() {
		var offset = $(this).offset();
		var height = $(this).outerHeight();
		var list = $('.dropdown-list-custommenu');
		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});

		if ($(list).css('display') == 'none') {
			$(list).slideDown('fast');
		} else {
			$(list).slideUp('fast');
		}
	});

	$(".ShowToggle").on('click', function() {
		var id = $(this).attr('data');
		$('#'+id).toggle();
	});

	$(".ShowToggleClass").on('click', function() {
		var id = $(this).attr('data');
		$('.'+id).toggle();
	});

	$(".VisibleToggle").on('click', function() {
		var id = $(this).attr('data');
		var target = $('#'+id);

		if (target.css('visibility') == 'hidden') {
			target.css('visibility','visible');
		} else {
			target.css('visibility','hidden');
		}
	});

	$('div.ajaxErrClose').on('click', function() {
		$('#ajaxErr').slideUp('fast');
	});


	$('.news-toggle').on('click', function() {
		var det = $(this).next();

		$('.marquee').show();
		$('.news-detail').hide();

		det.show();
		$(this).hide();
	});
	$('.news-detail').on('click', function() {
		$('.marquee').show();
		$('.news-detail').hide();
	});

	$('button[type=submit]').on('click', function() {
		if ($(this).hasClass('CoopReplyToSubmit')) {
			return true;
		}
		var form = $(this).parents('form');
		var hdn = $('<input>')
			.attr('type', 'hidden')
			.attr('name', $(this).attr('name'))
			.val($(this).val())
		;
		form.append(hdn);
		return true;
	});

	$('button[type=submit]').parents('form').on('submit', function(event) {
		var mode = $(this).find('input[name=mode]').val();
		if ($(this).find('button').hasClass('CoopReplyToSubmit') && mode != 'pcreate') {
			return true;
		}
		$(this).find('button').attr('disabled','disabled');
		return true;
	});

	$('select#tz-region').on('change', function() {
		$('select#tz-timezone optgroup').hide();
		$('select#tz-timezone option:selected').prop("selected",false);
		$('select#tz-timezone optgroup[label='+$(this).val()+']').show();
		$('select#tz-timezone optgroup[label='+$(this).val()+'] option:first').prop('selected',true);
	});



});
function addAlert(message,status) {
	var errBox = $("#ajaxErr");
	var errTxt = $("#ajaxErr p");

	errBox.slideUp('fast');

	errBox.removeClass('back-tmp');
	errBox.removeClass('back-info');
	errBox.removeClass('back-alert');
	errBox.addClass('back-'+status);

	errTxt.empty();
	errTxt.text(message);

	errBox.css({'left' : $(window).width()/2-errBox.width()/2+'px'});
	errBox.slideDown('fast');
}

function scrollTop() {
	$('html,body').animate({
		scrollTop: 0
	},200);
}
function scrollBottom() {
	$('html,body').animate({
		scrollTop: $(document).height()
	},200);
}

function getRand(min, max) {
	return Math.floor( Math.random() * (max - min + 1) ) + min;
}
function number_format(num) {
	return num.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,')
}
function nl2br(str) {
  return str.replace(/[\n\r]/g, "<br />");
}

function convertAlpha(num) {
	var val = num;
	var han = val.replace(/[Ａ-Ｚａ-ｚ０-９]/g,function(s){return String.fromCharCode(s.charCodeAt(0)-0xFEE0)});
	var han = han.replace(/,/g, '');
	return han;
}

function GetCookie(name) {
	var result = null;
	var cookieName = name + '=';
	var allcookies = document.cookie;

	var position = allcookies.indexOf( cookieName );
	if( position != -1 ) {
		var startIndex = position + cookieName.length;
		var endIndex = allcookies.indexOf( ';', startIndex );
		if( endIndex == -1 ) {
			endIndex = allcookies.length;
		}
		result = decodeURIComponent(
		allcookies.substring( startIndex, endIndex ));
	}
	return result;
}

function setSessionStorage(key, value) {
	if (!window.sessionStorage) return;
	try {
		sessionStorage.setItem(key, value);
	} catch (err) {
		console.error(err);
	}
}

function shadowMask(onoff,msg) {
	if (onoff == 'off') {
		$('#shadowMask').hide();
		$('#shadowMask').remove();
		return;
	}

	$('#shadowMask').hide();
	$('#shadowMask').remove();
	$('body')
	.append(
		$('<div>')
		.attr('id','shadowMask')
		.css({
			'display': 'block',
			'position': 'fixed',
			'top': '0',
			'width': '100vw',
			'height': '100vh',
			'background': 'rgba(0,0,0,0.4)',
			'zIndex': '150'
		})
		.click(function() {
			return false;
		})
	);
	if (msg) {
		$('#shadowMask')
		.append(
			$('<div>')
			.css({
				'position': 'absolute',
				'top': '50%',
				'text-align': 'center',
				'color': 'white',
				'width': '100%'
			})
			.append(
				$('<i>')
				.addClass('fa fa-spinner fa-pulse fa-fw fa-3x')
			)
			.append(
				$('<p>')
				.css({
					'margin-top': '14px',
					'text-shadow': '1px 1px 1px #333'
				})
				.text(msg)
			)
		);
	}
	return;
}

function shadowMaskEx(onoff, Element) {
	if (onoff == 'off') {
		$('.shadowMaskEx').hide();
		$('.shadowMaskEx').remove();
		return;
	}

	var tr = {'x': Element.offset().left + Element.innerWidth() + 1, 'y': Element.offset().top - 1};
	var bl = {'x': Element.offset().left - 1, 'y': Element.offset().top + Element.innerHeight() + 1};

	$('.shadowMaskEx').hide();
	$('.shadowMaskEx').remove();
	var tb = $('<div>')
		.addClass('shadowMaskEx')
		.attr('id', 'sM0')
		.css({
			'background-color': 'rgba(128,0,0,0.7)',
			'top': '0px',
			'left': '0px',
			'width': tr.x + 'px',
			'height': tr.y + 'px',
			'z-index': '150'
		})
		.click(function() {
			return false;
		})
	;
	var rb = $('<div>')
		.addClass('shadowMaskEx')
		.attr('id', 'sM1')
		.css({
			'background-color': 'rgba(0,128,0,0.7)',
			'top': '0px',
			'right': '0px',
			'width': $(window).width() - tr.x + 'px',
			'height': bl.y + 'px',
			'z-index': '151'
		})
		.click(function() {
			return false;
		})
	;
	var bb = $('<div>')
		.addClass('shadowMaskEx')
		.attr('id', 'sM2')
		.css({
			'background-color': 'rgba(0,0,128,0.7)',
			'bottom': '0px',
			'right': '0px',
			'width': $(window).width() - bl.x + 'px',
			'height': $(window).height() - bl.y + 'px',
			'z-index': '152'
		})
		.click(function() {
			return false;
		})
	;
	var lb = $('<div>')
		.addClass('shadowMaskEx')
		.attr('id', 'sM3')
		.css({
			'background-color': 'rgba(128,0,128,0.7)',
			'bottom': '0px',
			'left': '0px',
			'width': bl.x + 'px',
			'height': $(window).height() - tr.y + 'px',
			'z-index': '153'
		})
		.click(function() {
			return false;
		})
	;

	$('body').append(tb);
	$('body').append(rb);
	$('body').append(bb);
	$('body').append(lb);

}



/****************************************************************************
 * confirm dialog の オーバーライド
 *
 * @param string msg        // 確認メッセージ（改行は<br>に変換される）
 * @param function callback // コールバック関数
 * @param string btnOK      // OKボタンのテキスト（デフォルト 'OK'）
 * @param string btnCancel  // Cancelボタンのテキスト（デフォルト 'Cancel'）
 *
 * @usage
 *
 * confirm('Messages', function(ok) {
 *   if (ok) { // 結果が ture,false で返ります
 *     // OKボタンの処理
 *   } else {
 *     // Cancelボタンの処理
 *   }
 * }, 'OK', 'Cancel');
 *
 ****************************************************************************/
window.confirm = function (msg, callback, btnOK, btnCancel) {
	var bM = true;
	if ($('#shadowMask').get(0)) {
		bM = false;
	}

	if (bM) shadowMask('on');
	$('div.alertBox').remove();

	btnOK = (btnOK === undefined)? 'OK':btnOK;
	btnCancel = (btnCancel === undefined)? 'Cancel':btnCancel;

	var bOK = $('<button>')
		.addClass('button na default width-auto')
		.text(btnOK)
		.css({
			'padding': '4px 8px',
			'margin-right': '12px'
		})
		.on('click', function() {
			if (bM) shadowMask('off');
			$('div.alertBox').hide();
			callback(true);
			return true;
		})
	;
	var bCC = $('<button>')
		.addClass('button na default width-auto')
		.text(btnCancel)
		.css({
			'padding': '4px 8px',
		})
		.on('click', function() {
			if (bM) shadowMask('off');
			$('div.alertBox').hide();
			callback(false);
			return false;
		})
	;

	var mb = $('<div>')
		.addClass('alertBox')
		.css({
			'display': 'none'
		})
		.append(
			$('<div>')
				.addClass('msg-area')
				.html(nl2br(msg))
		)
		.append(
			$('<div>')
				.addClass('button-area')
				.append(bOK)
				.append(bCC)
		)
	;

	$('body').append(mb);

	var mbTop = (($(window).height()-mb.outerHeight())/2 - FixedSize);
	var mbLeft = (($(window).width()-mb.outerWidth())/2);

	mbTop = (mbTop < 0)? 8:mbTop;
	mbLeft = (mbLeft < 0)? 0:mbLeft;

	mb.css({
		'top': mbTop+'px',
		'left': mbLeft+'px'
	});

	mb.show();
}


/****************************************************************************
 * alert dialog の オーバーライド
 *
 * @param string msg        // 確認メッセージ（改行は<br>に変換される）
 * @param function callback // コールバック関数（省略可）
 * @param string btnOK      // OKボタンのテキスト（デフォルト 'OK'）
 *
 * @usage
 *
 * alert('Messages', function() {
 *   // OKボタンの処理
 * }, 'OK');
 *
 * alert('Messages');
 *
 ****************************************************************************/
window.alert = function (msg, callback, btnOK) {
	var bM = true;
	if ($('#shadowMask').get(0)) {
		bM = false;
	}

	if (bM) shadowMask('on');
	$('div.alertBox').remove();

	btnOK = (btnOK === undefined)? 'OK':btnOK;

	var bOK = $('<button>')
		.addClass('button na default width-auto')
		.text(btnOK)
		.css({
			'padding': '4px 8px',
		})
		.on('click', function() {
			if (bM) shadowMask('off');
			$('div.alertBox').hide();
			if (callback !== undefined) {
				callback();
			}
			return true;
		})
	;

	var mb = $('<div>')
		.addClass('alertBox')
		.css({
			'display': 'none'
		})
		.append(
			$('<div>')
				.addClass('msg-area')
				.html(nl2br(msg))
		)
		.append(
			$('<div>')
				.addClass('button-area')
				.append(bOK)
		)
	;

	$('body').append(mb);

	var mbTop = (($(window).height()-mb.outerHeight())/2 - FixedSize);
	var mbLeft = (($(window).width()-mb.outerWidth())/2);

	mbTop = (mbTop < 0)? 8:mbTop;
	mbLeft = (mbLeft < 0)? 0:mbLeft;

	mb.css({
		'top': mbTop+'px',
		'left': mbLeft+'px'
	});

	mb.show();
}

// アプリから言語変更
function jumpLang($lang) {
	location.href = '/language/select/'+$lang;
}
