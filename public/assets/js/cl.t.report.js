$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('table').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('table').find('input.Chk').prop('checked',false);
		}
	});

	$('.CheckNotYet').on('click', function() {
		var bChk;
		if ($(this).find('input[type=checkbox]').prop('checked')) {
			$(this).find('input[type=checkbox]').prop('checked',false);
			bChk = false;
		} else {
			$(this).find('input[type=checkbox]').prop('checked',true);
			bChk = true;
		}

		var table = $('#StudentCheckForm');

		table.find('tbody tr').each(function(i,elm) {
			var chk = $(this).find('input.Chk');
			if ($(this).find('a.ReportSubmit').get(0)) {
				chk.prop('checked',bChk);
			}
		});
		return false;
	});
	$('.CheckSbmted').on('click', function() {
		var bChk;
		if ($(this).find('input[type=checkbox]').prop('checked')) {
			$(this).find('input[type=checkbox]').prop('checked',false);
			bChk = false;
		} else {
			$(this).find('input[type=checkbox]').prop('checked',true);
			bChk = true;
		}

		var table = $('#StudentCheckForm');
		table.find('tbody tr').each(function(i,elm) {
			var chk = $(this).find('input.Chk');
			if (!$(this).find('a.ReportSubmit').get(0)) {
				chk.prop('checked',bChk);
			}
		});
		return false;
	});

	$('.checkStudentMail').on('click', function() {
		var form = $('#StudentCheckForm');
		var input = form.find('input.Chk');
		var bChk = false;

		for (var i = 0; i < input.length; i++) {
			if (input.eq(i).prop('checked')) {
				bChk = true;
				break;
			}
		}

		if (!bChk) {
			addAlert($.i18n.prop('cl_t_student_StudentMailSend_1'),'alert');
			return false;
		}

		form.submit();
		return false;
	});



	$('.report-dropdown-toggle').on('click',function() {
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

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.report-detail-show').on('click', function() {
		var id = $(this).parents('tr').attr('id');
		var data = $(this).attr('data');

		if (data == '1') {
			$('#' + id + '_detail').hide();
			$(this).find('i').removeClass('fa-minus-square-o');
			$(this).find('i').addClass('fa-plus-square-o');
			$(this).attr('data',0);
		} else {
			$('#' + id + '_detail').show();
			$(this).find('i').removeClass('fa-plus-square-o');
			$(this).find('i').addClass('fa-minus-square-o');
			$(this).attr('data',1);
		}
		return false;
	});

	$('.ReportPublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/report/Public.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"rb": aObj[1],
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

						if (res.timer) {
							$(Btn).find('div').append('<br><span class="font-size-80">'+res.timer+'</span>');
						}
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

	$('.ShareOpen').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/report/ShareOpen.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"rb": aObj[1],
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
						$(Btn).removeClass('font-blue');
						$(Btn).removeClass('font-default');
						$(Btn).addClass(res.class);
						$(Btn).find('div').text(res.text);

						if (res.anony) {
							$(Btn).next('button').show();
						} else {
							$(Btn).next('button').hide();
						}

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

	$('.ShareAnony').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/report/ShareAnony.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"rb": aObj[1],
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
						$(Btn).removeClass('font-blue');
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

	$('.ReportResultPublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/report/ResultPublic.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"rb": aObj[1],
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
						$(Btn).removeClass('font-blue');
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

	$('.ReportEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/report/edit/'+aObj[1];
		return false;
	});

	$('.ReportDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_report_ReportDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/report/delete/'+aObj[1];
		});
		return false;
	});

	$('.ReportSort').click(function() {
		var TR = $(this).parents('tr');
		var TBODY = $(this).parents('tbody');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.ReportSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/report/Sort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"rb": aObj[1],
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
							if ($(TBODY).prev('tbody')) {
								var oB = $(TBODY).prev('tbody').find('.ReportSort');
								SortBtnDisabled(oA,oB);
								$(TBODY).insertBefore($(TBODY).prev('tbody')[0]);
							}
						} else {
							if ($(TBODY).next('tbody')) {
								var oB = $(TBODY).next('tbody').find('.ReportSort');
								SortBtnDisabled(oA,oB);
								$(TBODY).insertAfter($(TBODY).next("tbody")[0]);
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

	$(document).on('click','.ReportSubmit', function() {
		var url = $(this).attr('href');
		confirm($.i18n.prop('cl_t_report_submit_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = url;
			return true;
		});
		return false;
	});
	$(document).on('click','.ReportSubmitCancel', function() {
		var url = $(this).attr('href');
		confirm($.i18n.prop('cl_t_report_submitcancel_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = url;
			return true;
		});
		return false;
	});


	$('input[name=r_auto_public]').on('change',function() {
		if ($(this).val() == 1) {
			$('.auto-datetime').show();
		} else {
			$('.auto-datetime').hide();
		}
	});

	$('input[name=r_share]').on('change',function() {
		if ($(this).val() >= 1) {
			$('.rAnonymous').show();
		} else {
			$('.rAnonymous').hide();
		}
	});

	$('#datepick1').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		minDate: 'today',
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});
	$('#datepick2').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		minDate: 'today',
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});
	if ($('.timepick1').get(0)) {
		$('.timepick1').timepicker({
			'timeFormat': 'H:i',
			'minTime': '0:00',
			'maxTime': '23:55',
			'forceRoundTime': true,
			'step': 5,
		});
		$('.timepick2').timepicker({
			'timeFormat': 'H:i',
			'minTime': '0:00',
			'maxTime': '23:55',
			'forceRoundTime': true,
			'step': 5,
		});
	}


	/* ファイルリンク */
	$('.file-uploader .input-cover a').on('click', function(e) {
		e.stopPropagation();
		return true;
	});
	/* ファイル削除 */
	$('.file-uploader .uploaded-file .remove').on('click', function(e) {
		var $ufile  = $(this).parents('li').find('.uploaded-file');
		var $hidden = $(this).parents('li').find('input[type=hidden]');
		var $cover  = $(this).parents('li').find('.input-cover');

		$hidden.val('');
		$ufile.find('.file').attr('href','');
		$ufile.find('.name').text('');
		$ufile.find('.size').text('');
		$ufile.hide();
		$cover.css({
			'background-image':'none',
		});

		e.stopPropagation();
		return false;
	});

	/* ファイルを選択 */
	$('.file-uploader .input-cover').on('click', function() {
		$(this).parents('li').find('input[type=file]').click();
	});
	/* ファイルアップロード */
	$(document).on('change','.file-uploader input[type=file]',function() {
		var $this   = $(this);
		var $p_bar   = $(this).parents('li').find('.upload-progress-bar');
		var $input  = $(this).parents('span');
		var $ufile  = $(this).parents('li').find('.uploaded-file');
		var $hidden = $(this).parents('li').find('input[type=hidden]');
		var $cover  = $(this).parents('li').find('.input-cover');

		var $fd = new FormData();
		if ($(this).val() !== '') {
			$fd.append("file", $(this).prop("files")[0]);
		}
		$fd.append("prefix", '_report_');

		$.ajax({
			async: true,
			xhr: function() {
				var $XHR = $.ajaxSettings.xhr();
				if ($XHR.upload) {
					$XHR.upload.addEventListener('progress',function($e) {
						var $progre = parseInt($e.loaded/$e.total*10000)/100;
						$p_bar.width(parseInt($progre)+'%');
					});
				}
				return $XHR;
			},
			url: "/uploadfile.json",
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: $fd,
			processData: false,
			contentType: false,
			success: function($o) {
				console.log($o);
				if ($o.error) {
					addAlert($o.error,'alert');
					return false;
				}
				$hidden.val($o.hval);
				$ufile.find('.file').attr('href',$o.file);
				$ufile.find('.name').text($o.name);
				$ufile.find('.size').text($o.size);
				if ($o.isimg) {
					$cover.css({
						'background-image':'url("'+$o.file+'")',
						'background-size':'cover',
					});
				} else {
					$cover.css({
						'background-image':'none',
					});
				}
				$ufile.show();
				return false;
			},
			error: function(xhr, ts, err) {
				addAlert('Network Access Error','alert');
				return false;
			},
			complete: function() {
				var $tmp = $input.html();
				$input.html($tmp);
				$p_bar.width('0%');
				return false;
			}
		});
	});

	$('.QBTabMenu li').click(function() {
		var mode = $(this).attr('data');
		$('.QBTabMenu li').removeClass('QBTabActive');
		$('.QBTabContents').hide();

		$('#'+mode).show();
		$(this).addClass('QBTabActive');
	});
});

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

function ArchiveDownload() {
	var btn = $('#archive-download-btn');
	var sObj = btn.attr('obj');

	$.ajax({
		url: "/t/ajax/report/ArchiveDownloadBtn.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"rb" : sObj
		},
		success: function(o){
			var res = o.res;
			switch (o.err)
			{
				case -3:
				case -2:
				case -1:
					console.log(o.msg);
				break;
				case 0:
					if (res.status == 0) {
						btn.attr('href',res.href);
						btn.removeAttr('disabled');
						btn.text(res.text);
						btn.prepend('<i class="fa fa-download mr0"></i>');
						btn.off('click');
						clearInterval(timerID);
					} else if (res.status == 2) {
						btn.text(res.text);
						btn.prepend('<i class="fa fa-exclamation-triangle mr0"></i>');
						clearInterval(timerID);
					}
				break;
			}
			return false;
		},
		error: function(res,status){
			console.log(status+'/'+res.responseText);
			return false;
		}
	});
	return;
}
