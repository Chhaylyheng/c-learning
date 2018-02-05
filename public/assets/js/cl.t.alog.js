$(function() {
	$('#datepick1').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px', 'z-index': 140});
		},
	});

	$('#datepick2').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px', 'z-index': 141});
		},
	});

	$('#datepick3').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});

	$('#datepick4').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});

	if ($('.timepick1').get(0)) {
		$('.timepick1').timepicker({
			'timeFormat': 'H:i',
			'minTime': '0:00',
			'maxTime': '23:45',
			'forceRoundTime': true,
			'step': 15,
		});
		$('.timepick2').timepicker({
			'timeFormat': 'H:i',
			'minTime': '0:00',
			'maxTime': '23:45',
			'forceRoundTime': true,
			'step': 15,
		});
	}

	$('.alog-dropdown-toggle').on('click',function() {
		var id = $(this).attr('id');
		var mode = id.split('_');
		var list = $('.dropdown-list-'+mode[2]);
		var obj = list.attr('obj');

		if (id == obj && list.css('display') == 'block') {
			list.slideUp('fast');
			return;
		}

		list.hide();
		list.attr('obj',id);

		var offset = $(this).offset();
		var height = $(this).outerHeight();

		list.find('li').show();

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.AltThemePublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/alog/ThemePublic.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"alt": aObj[1],
				"m":  m,
			},
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -3:
					case -2:
					case -1:
						addAlert(o.msg,'alert');
					break;
					case 0:
						$(Btn).html('<div></div>');
						$(Btn).removeClass('font-blue');
						$(Btn).removeClass('font-red');
						$(Btn).removeClass('font-default');
						$(Btn).addClass(res.class);
						$(Btn).find('div').text(res.text);
						addAlert(o.msg,'tmp');
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		$(list).slideUp('fast');
		return false;
	});

	$('.AltThemeEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/alog/edit/'+aObj[1];
		return false;
	});

	$('.AltPreview').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/alog/preview/'+aObj[1];
		return false;
	});

	$('.AltToCSV').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/output/alog/'+aObj[1]+"/ActivityLogRecords"+".csv";

		$('.dropdown-list').slideUp('fast');
		return false;
	});

	$('.AltThemeDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_alog_AltThemeDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/alog/delete/'+aObj[1];
		});
		return false;
	});

	$('.AltThemeSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.AltThemeSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/alog/ThemeSort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"alt": aObj[1],
				"m": aObj[2],
			},
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -3:
					case -2:
					case -1:
						addAlert(o.msg,'alert');
					break;
					case 0:
						if (res.m == 'up') {
							if ($(TR).prev('tr')) {
								var oB = $(TR).prev('tr').find('.AltThemeSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.AltThemeSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertAfter($(TR).next("tr")[0]);
							}
						}
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		$(TR).css({'background-color':'transparent'});
		return false;
	});

	$('#AlogThemeEdit textarea').on('change forcus keyup', StrLen);

	$('.TeachCommentUpdate').on('click', function() {
		var textarea = $(this).parents('#TeachComment').find('textarea');
		var text = textarea.val();
		var sObj = $(this).attr('data');
		var aObj = sObj.split("_");

		$.ajax({
			url: "/t/ajax/alog/TeachComment.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"alt": aObj[0],
				"no": aObj[1],
				"com": text
			},
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -3:
					case -2:
					case -1:
						addAlert(o.msg,'alert');
					break;
					case 0:
						if (res != 0) {
							textarea.val(text);
							addAlert(o.msg,'tmp');
						}
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		return false;
	});

});

function StrLen(e) {
	var leng = $(e.target).val().length;
	var txt = $(e.target).parent('div').find('.ChrNum');
	txt.text(leng);
}

function SortBtnDisabled(oA,oB) {
	var A0 = $(oA[0]).attr('disabled');
	var A1 = $(oA[1]).attr('disabled');
	var B0 = $(oB[0]).attr('disabled');
	var B1 = $(oB[1]).attr('disabled');
	if (A0 == null) { $(oB[0]).removeAttr('disabled'); } else { $(oB[0]).attr('disabled',A0); }
	if (A1 == null) { $(oB[1]).removeAttr('disabled'); } else { $(oB[1]).attr('disabled',A1); }
	if (B0 == null) { $(oA[0]).removeAttr('disabled'); } else { $(oA[0]).attr('disabled',B0); }
	if (B1 == null) { $(oA[1]).removeAttr('disabled'); } else { $(oA[1]).attr('disabled',B1); }
}



$(window).load(function() {

	var ol = $('.tr-overlay');

	ol.each(function( index ) {
		var parTD = $(this).parents('td');
		var parTR = $(this).parents('tr');
		parTD.css({'position': 'relative'});
		var olw = parTR.outerWidth() - parTR.find('td:first-child').outerWidth();
		$(this).css({
			'position': 'absolute',
			'top': '-1px',
			'left':  '-1px',
			'width': olw+'px',
			'height': parTR.outerHeight()+'px',
			'background-color': 'rgba(0,0,0,0.3)'
		})
		parTR.find('td:first-child').css({'position': 'relative'});
		parTR.find('td:first-child').find('label').css({
			'position': 'absolute',
			'top': '-1px',
			'left': '-1px',
			'width': parTR.find('td:first-child').outerWidth()+'px',
			'height': parTR.find('td:first-child').outerHeight()+'px',
			'background-color': 'transparent',
			'border-bottom': '1px solid #C3CCD3',
			'border-right': '1px solid #C3CCD3'
		});

		if (parTR.find('.modify-check').prop('checked')) {
			$(this).hide();
		} else {
			$(this).css({'height':parTR.outerHeight()+'px'});
			$(this).show();
		}

	});

	$('.modify-check').change(function() {
		var parTR = $(this).parents('tr');
		if ($(this).prop('checked')) {
			parTR.find('.tr-overlay').hide();
		} else {
			parTR.find('.tr-overlay').css({'height':parTR.outerHeight()+'px'});
			parTR.find('.tr-overlay').show();
		}
	});

	$('#AlogThemeEdit textarea').each(function() {
		var leng = $(this).val().length;
		var txt = $(this).parent('div').find('.ChrNum');
		txt.text(leng);
	});

});


