$(function() {

	/* ファイルリンク */
	$('.file-uploader .input-cover a').on('click', function(e) {
		e.stopPropagation();
		return true;
	});
	/* ファイル削除 */
	$('.file-uploader .uploaded-file .remove').on('click', function(e) {
		var $ufile  = $(this).closest('li').find('.uploaded-file');
		var $hidden = $(this).closest('li').find('input[type=hidden]');
		var $cover  = $(this).closest('li').find('.input-cover');

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
		$(this).parents('li.file-box').find('input[type=file]').click();
	});
	/* ファイルアップロード */
	$(document).on('change','.file-uploader input[type=file]',function() {
		var $this   = $(this);
		var $p_bar  = $(this).parents('li.file-box').find('.upload-progress-bar');
		var $input  = $(this).parents('span');
		var $ufile  = $(this).parents('li.file-box').find('.uploaded-file');
		var $hidden = $(this).parents('li.file-box').find('input[type=hidden]');
		var $cover  = $(this).parents('li.file-box').find('.input-cover');

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
				addAlert('Network Access Error ' + err,'alert');
				return false;
			},
			complete: function() {
				var $tmp = $input.html();
				$input.html($tmp);
				$p_bar.width('0%');
				return false;
			}
		});
		return false;
	});

	$('.QBTabMenu li').click(function() {
		var mode = $(this).attr('data');
		$('.QBTabMenu li').removeClass('QBTabActive');
		$('.QBTabContents').hide();

		$('#'+mode).show();
		$(this).addClass('QBTabActive');
	});

	$('.ReportRateStart').on('click', function() {
		return false;
	});
	$('.RRateStars').hover(
		function () {
		},
		function () {
			$.each($('.RRStarBtn'), function() {
				$(this).find('i').attr('class', StarMem[$(this).val()]);
			});
		}
	);
	$('.RRStarBtn').hover(
		function () {
			$(this).find('i').removeClass('fa-star-o').addClass('fa-star font-red');
			$(this).prevAll().find('i').removeClass('fa-star-o').addClass('fa-star font-red');
			$(this).nextAll().find('i').removeClass('fa-star font-red').addClass('fa-star-o');
		},
		function () {
			$(this).find('i').removeClass('fa-star font-red').addClass('fa-star-o');
			$(this).prevAll().find('i').removeClass('fa-star font-red').addClass('fa-star-o');
		}
	);
	$('.RRStarBtn').on('click', function() {
		var Obj = $('.RRateStars').attr('data').split('_');
		var Val = $(this).val();

		$.ajax({
			url: "/s/ajax/report/ReportRate.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"rb": Obj[0],
				"st": Obj[1],
				"ct": Obj[2],
				"r":  Val
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
						StarMem = {
							1: res.mem.r1,
							2: res.mem.r2,
							3: res.mem.r3,
							4: res.mem.r4,
							5: res.mem.r5
						};

						$.each($('.RRStarBtn'), function() {
							$(this).find('i').attr('class', StarMem[$(this).val()]);
						});

						$('.rpAvgScore').text(res.avg);

						var gr;
						for (i = 1; i <= 5; i++) {
							gr = $('#gr'+i);
							gr.find('.RPGraph').css({ 'width': res.gr['gr'+i].avg+'%' });
							gr.find('.RPAvg').text(res.gr['gr'+i].avg+'%');
							gr.find('.RPNum').text(res.gr['gr'+i].num);
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


		return false;
	});

});

