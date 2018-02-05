// 初期設定値
var TimerMSec = 10000;
var GetCom = 10;
var bH;

$(function() {
	var breakpoint = 768;
	var windowWidth = $(window).width();

	$('.KRLikeUP').click(function() {
		$('#krErr').hide();

		var iUP = $(this).children('i');
		var numSpan = $(this).nextAll('span');
		var num = Number(numSpan.text());

		var tt = $(this).parents('tr').attr('data');
		var sub = $(this).attr('data');
		var data = $(this).parents('table').attr('data').split('|');

		$.ajax({
			url: "/t/ajax/KReportLikeUP.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"y":data[0],
				"p":data[1],
				"tt":tt,
				"sub":sub,
				"ta":data[3],
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					$('#krErr').text(o.msg);
					$('#krErr').show();
					return false;
				}
				numSpan.text(res.num);
				iUP.parent('a').replaceWith(iUP);
				iUP.removeClass('fa-thumbs-o-up');
				iUP.addClass('fa-check');
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		return false;
	});

	$('.KRLikeUP2').on('click', function() {
		var btn = $(this);
		var data = btn.attr('data').split('|');

		$.ajax({
			url: "/t/ajax/KReportLikeUP.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"y":data[0],
				"p":data[1],
				"tt":data[2],
				"sub":data[3],
				"ta":data[4],
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					$('#krErr').text(o.msg);
					$('#krErr').show();
					return false;
				}
				btn.attr('disabled','disabled');
				btn.removeClass('do');
				btn.addClass('dis');
				btn.children('i').removeClass('fa-thumbs-o-up');
				btn.children('i').addClass('fa-check');
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		return false;
	});

	if ($('.comment-write-text').get(0)) {
		bH = $('.comment-write-text').get(0).offsetHeight;
		writeBtnChk();
	}
	$('.comment-write-text').on('input',function() {
		writeBtnChk();
	});

	$('.comment-write-button').on('click', function() {
		if (!writeBtnChk()) {
			alert($.i18n.prop('cl_t_kreport_comment_write_button_1'));
			return false;
		}
		var v = $('.comment-write-text').val();
		var data = $('.kreport-data').attr('data').split('|');

		if (v.length > 2000) {
			alert($.i18n.prop('cl_t_kreport_comment_write_button_2'));
			return false;
		}

		$.ajax({
			url: "/t/ajax/KReportCommentSet.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"y":data[0],
				"p":data[1],
				"put":data[2],
				"sub":data[4],
				"tt":data[3],
				"txt":v
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					alert(o.msg);
					return false;
				}
				KRCommentChecker(0);
				$('.comment-write-text').val('');
				writeBtnChk();
				scrollBottom();
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		return false;
	});

	if ($('.comment-item-template').get(0)) {
		writeBtnChk();
		// リアルタイムチェック
		setInterval(function() {
			KRCommentChecker(0);
		},TimerMSec);
		// 初回呼び出し
		KRCommentChecker(0);
	}

	$("p.comment-more-show span").on('click', function() {
		var cnt = Number($('ul.comment-show-box').attr('cnt'));
		KRCommentChecker(cnt);
		return;
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

		$hidden.val('');
		$ufile.find('.file').attr('href','');
		$ufile.find('.name').text('');
		$ufile.find('.size').text('');
		$ufile.hide();

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

		var $fd = new FormData();
		if ($(this).val() !== '') {
			$fd.append("file", $(this).prop("files")[0]);
		}
		$fd.append("prefix", '_kreport_');

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
});



function writeBtnChk() {
	if ($('.comment-write-text').get(0)) {
		$('.comment-write-text').css({height:bH+'px'});
		var sH = $('.comment-write-text').get(0).scrollHeight;
		$('.comment-write-text').css({height:sH+'px'});

		var pB = parseInt($('#content').css('padding-bottom'));
		if (pB < sH) {
			$('#content').css('padding-bottom',sH + 'px');
		}

		var v = $('.comment-write-text').val();
		v = $.trim(v);
		if (v.length > 0) {
			$('.comment-write-button').removeAttr('disabled');
			$('.comment-write-button').removeClass('cancel');
			$('.comment-write-button').addClass('do');
			return true;
		} else {
			$('.comment-write-button').attr('disabled','disabled');
			$('.comment-write-button').addClass('cancel');
			$('.comment-write-button').removeClass('do');
			return false;
		}
	} else {
		return;
	}
}

/* チェック処理 */
function KRCommentChecker(cnt) {
	var base  = $(".kreport-data").attr("data").split("|");
	var sData = new Object;

	sData.y     = base[0];
	sData.p     = base[1];
	sData.put   = base[2];
	sData.sub   = base[4];
	sData.tt    = base[3];
	sData.s     = cnt;
	sData.limit = GetCom;

	var aData = new Object;
	$.each($("ul.comment-show-box li.comment-item"), function(i) {
		var Item = $("li.comment-item:eq("+i+")");
		aData[i] = decodeURIComponent(Item.attr("no"));
	});
	sData.lists = aData;
	$.ajax({
		url: "/t/ajax/KReportCommentGet.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: sData,
		timeout: 30000,
		success: function(o,state) {
			var res = o.res;
			if (o.err != 0) {
				// システム上のエラーの場合
				console.log(res.msg);
				return false;
			} else {
				// 正常処理
				var rData = res.data;
				if (rData != 0) {
					$.each(rData, function(i,oData) {
						if (oData.n == 0) {
							//
							return;
						} else {
							var mine = (oData.mine)? '-mine':'-other';
							var oI = $('ul.comment-item-template li.comment-item'+mine).clone(true);
							oI.attr('no',oData.no);
							oI.children('.time').html(oData.kcDate);
							oI.children('.comment').html(oData.kcComment);
							if (!oData.mine) {
								oI.children('img').attr('src',oData.ttImage);
								oI.children('.name').children('span:first').text(oData.cmName+' '+oData.ttName);
								if (oData.ptName != null) {
									oI.children('.name').children('span:last').append(oData.pcName+' '+oData.ptName);
									oI.children('.name').children('span:last').show();
								}
							} else {
								oI.children('.name').hide();
								if (oData.ptName != null) {
									oI.children('.name').children('span:last').prepend(oData.pcName+' '+oData.ptName);
									oI.children('.name').children('span:last').show();
									oI.children('.name').show();
								}
							}
						}
						if ($('ul.comment-item-template li[no='+oData.no+']').get(0)) {
							return;
						}
						if (oData.n == 1) {
							$("ul.comment-show-box").append(oI);
						} else {
							$("ul.comment-show-box").prepend(oI);
						}
						oI.slideDown('fast');
						return;
					});
				}
				var cnt = $("ul.comment-show-box").attr("cnt");
				if (cnt == 0 || res.cnt < cnt) {
					$("ul.comment-show-box").attr("cnt",res.cnt);
				}
				if (res.more) {
					$("p.comment-more-show").show();
				} else {
					$("p.comment-more-show").hide();
				}
				return false;
			}
			return false;
		},
		error: function(res,status) {
			console.log(status+'/'+res.responseText);
			return false;
		},
		complete: function() {
		},
	});
}
