$(function() {

	$('#datepick1').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 1,
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
		numberOfMonths: 1,
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

	$('.AlogGoalSet').on('click', function() {
		var altID = $(this).attr('id');
		var formBox = $('#GoalEditBox');

		formBox.hide();
		formBox.find('#ALTheme').text('');
		formBox.find('#ALGoalLabel').text('');
		formBox.find('#ALGoalDesc').text('');
		formBox.find('textarea').val('');
		formBox.find('input[name=alt]').val('');

		$.ajax({
			url: "/s/ajax/alog/getGoal.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"alt": altID,
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
						formBox.find('#ALTheme').text(res.theme);
						formBox.find('#ALGoalLabel').text(res.goal_label);
						formBox.find('#ALGoalDesc').text(res.goal_desc);
						formBox.find('textarea').val(res.goal);
						formBox.find('input[name=alt]').val(altID);

						shadowMask('on');
						$('#shadowMask').on('click', function() {
							formBox.hide();
							shadowMask('off');
						});

						var mbTop = (($(window).height()-formBox.outerHeight())/2 - FixedSize);
						var mbLeft = (($(window).width()-formBox.outerWidth())/2);

						mbTop = (mbTop < 0)? 8:mbTop;
						mbLeft = (mbLeft < 0)? 0:mbLeft;

						formBox.css({
							'top': mbTop+'px',
							'left': mbLeft+'px'
						});

						formBox.show();
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

	$('.GoalEditClose').on('click', function() {
		var formBox = $('#GoalEditBox');
		formBox.hide();
		shadowMask('off');
	});

	$('#GoalEditBox').on('submit', function() {
		var formBox = $(this);
		var altID = formBox.find('input[name=alt]').val();
		var goal = formBox.find('textarea').val();

		$.ajax({
			url: "/s/ajax/alog/setGoal.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"alt": altID,
				"goal": goal
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
						formBox.hide();

						$('#GoalText').html(nl2br(goal));

						shadowMask('off');
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
		return false;
	});

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
		$fd.append("prefix", '_material_');

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

	$('.AlogDelete').click(function() {
		var url = $(this).attr('href');

		confirm($.i18n.prop('cl_s_alog_AlogDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = url;
		});
		return false;
	});

});

