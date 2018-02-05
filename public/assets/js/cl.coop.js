$(function() {
	var pathinfo = window.location.pathname.split('/');
	var cMode = pathinfo[1];

	$('.upload-box-toggle').on('click',function() {
		$(this).parents('.formLabel').next().find('.file-uploader').toggle();
		$(this).parents('.formLabel').next().find('.file-uploader-memo').toggle();
		$(this).find('i').toggle();
		return false;
	});

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
		$fd.append("prefix", '_coop_');

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

	$(document).on('click', 'a.CoopThreadCreate', function() {
		var field = $('#content-inner > .res-field');
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=c_no]').val();

		resbox.find('button').removeAttr('disabled');
		resbox.find('.res-msg-box').text('');
		resbox.find('input[name=mode]').val('pcreate');
		resbox.find('input[name=c_title]').parents('.formGroup').show();
		resbox.find('button[type=submit]').hide();
		resbox.find('button.ThreadRegist').show();
		resbox.find('button.CoopReplyToQuote').hide();
		resbox.find('input[name=mail-reply]').prop('checked',false);
		resbox.find('input[name=mail-reply]').parents('label').hide();

		if (parseInt(cNO) != 0) {
			resbox.hide();
			resbox.find('input[name=c_no]').val(0);
		}
		else if (resbox.css('display') == 'inline-block') {
			ResCancel();
			return false;
		}

		field.append(resbox);
		resbox.show();

		return false;
	});


	$(document).on('click', 'button.CoopReplyTo', function() {
		var aObj = $(this).val().split("_");
		var thread = $(this).parents('#c'+aObj[1]);
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=c_no]').val();
		resbox.find('button').removeAttr('disabled');
		resbox.find('.res-msg-box').text('');
		resbox.find('input[name=mode]').val('input');
		resbox.find('button[type=submit]').hide();
		resbox.find('button.toComment').show();
		resbox.find('input[name=c_title]').parents('.formGroup').hide();
		resbox.find('button.CoopReplyToQuote').show();
		resbox.find('input[name=c_id]').val(aObj[0]);

		var writer = thread.find('.thread-writer:first');
		if (writer.hasClass('mine')) {
			resbox.find('input[name=mail-reply]').prop('checked',false);
			resbox.find('input[name=mail-reply]').parents('label').hide();
		} else {
			resbox.find('input[name=mail-reply]').parents('label').find('span.reply-name').text(writer.text());
			resbox.find('input[name=mail-reply]').parents('label').show();
		}

		if (parseInt(aObj[1]) != parseInt(cNO)) {
			resbox.hide();
			resbox.find('input[name=c_no]').val(aObj[1]);
		}
		else if (resbox.css('display') == 'inline-block') {
			ResCancel();
			return false;
		}

		thread.find('div.thread-res:first').append(resbox);
		resbox.show();

		return false;
	});

	$(document).on('click', 'button.CoopResEdit', function() {
		var aObj = $(this).val().split("_");
		var thread = $(this).parents('#c'+aObj[1]+' > .thread-box');
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=c_no]').val();
		resbox.find('button').removeAttr('disabled');
		resbox.find('.res-msg-box').text('');
		resbox.find('button[type=submit]').hide();
		resbox.find('button.toUpdate').show();
		resbox.find('input[name=mode]').val('edit');
		resbox.find('input[name=c_title]').parents('.formGroup').hide();
		resbox.find('input[name=c_id]').val(aObj[0]);
		resbox.find('button.CoopReplyToQuote').show();

		var pthread = $(this).parents('#c'+aObj[1]).parent().closest('.anchor-block');
		if (pthread) {
			var writer = pthread.find('.thread-writer:first');
			if (writer.hasClass('mine')) {
				resbox.find('input[name=mail-reply]').prop('checked',false);
				resbox.find('input[name=mail-reply]').parents('label').hide();
			} else {
				resbox.find('input[name=mail-reply]').parents('label').find('span.reply-name').text(writer.text());
				resbox.find('input[name=mail-reply]').parents('label').show();
			}
		} else {
			resbox.find('input[name=mail-reply]').prop('checked',false);
			resbox.find('input[name=mail-reply]').parents('label').hide();
		}

		if (parseInt(aObj[1]) != parseInt(cNO)) {
			resbox.hide();
			resbox.find('input[name=c_no]').val(aObj[1]);
		}
		else if (resbox.css('display') == 'inline-block') {
			ResCancel();
			return false;
		}

		resbox.find('textarea[name=c_text]').val(thread.children('.thread-body').children('p.thread-text-raw').text());

		thread.children('.thread-body').find('ul.files li').each(function(i) {
			var id = $(this).attr('obj').split('_');
			var upbox = resbox.find('input[name=c_file'+id[0]+']').parent('li.file-box');

			if ($(this).find('img').get(0)) {
				var path = $(this).find('span.f-path').text();
				upbox.find('.input-cover').css('background-image','url('+path+')');
			}

			upbox.find('input[type=hidden]').val(id[1]);
			upbox.find('div.uploaded-file a.file').attr('href',$(this).find('span.f-path').text());
			upbox.find('div.uploaded-file span.name').text($(this).find('span.f-name').text());
			upbox.find('div.uploaded-file span.size').text($(this).find('span.f-size').text());
			upbox.find('div.uploaded-file').show();
		});

		thread.find('div.thread-res:first').append(resbox);
		resbox.show();

		return false;
	});

	$(document).on('click', 'button.CoopThreadEdit', function() {
		var aObj = $(this).val().split("_");
		var thread = $(this).parents('#c'+aObj[1]+' > .thread-box');
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=c_no]').val();
		resbox.find('button').removeAttr('disabled');
		resbox.find('.res-msg-box').text('');
		resbox.find('input[name=mode]').val('pedit');
		resbox.find('button[type=submit]').hide()
		resbox.find('button.toUpdate').show()
		resbox.find('input[name=c_title]').parents('.formGroup').show();
		resbox.find('button.CoopReplyToQuote').hide();
		resbox.find('input[name=c_id]').val(aObj[0]);

		resbox.find('input[name=mail-reply]').prop('checked',false);
		resbox.find('input[name=mail-reply]').parents('label').hide();

		if (parseInt(aObj[1]) != parseInt(cNO)) {
			resbox.hide();
			resbox.find('input[name=c_no]').val(aObj[1]);
		}
		else if (resbox.css('display') == 'inline-block') {
			ResCancel();
			return false;
		}

		resbox.find('input[name=c_title]').val(thread.children('.thread-title').children('a:first').text());
		resbox.find('textarea[name=c_text]').val(thread.children('.thread-body').children('p.thread-text-raw').text());

		thread.children('.thread-body').find('ul.files li').each(function(i) {
			var id = $(this).attr('obj').split('_');
			var upbox = resbox.find('input[name=c_file'+id[0]+']').parent('li.file-box');

			if ($(this).find('img').get(0)) {
				var path = $(this).find('span.f-path').text();
				upbox.find('.input-cover').css('background-image','url('+path+')');
			}

			upbox.find('input[type=hidden]').val(id[1]);
			upbox.find('div.uploaded-file a.file').attr('href',$(this).find('span.f-path').text());
			upbox.find('div.uploaded-file span.name').text($(this).find('span.f-name').text());
			upbox.find('div.uploaded-file span.size').text($(this).find('span.f-size').text());
			upbox.find('div.uploaded-file').show();
		});

		thread.find('div.thread-res:first').append(resbox);
		resbox.show();

		return false;
	});

	$(document).on('click', 'button.CoopReplyToQuote', function() {
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=c_no]').val();
		var textval = resbox.find('textarea[name=c_text]').val();
		resbox.find('.res-msg-box').text('');

		if (parseInt(cNO) == 0)
		{
			return false;
		}

		var mode = resbox.find('input[name=mode]').val();
		if (mode == 'edit') {
			var thread = $(this).parents('#c'+cNO).closest('.anchor-block > .thread-box');
		}
		else{
			var thread = $(this).parents('#c'+cNO+' > .thread-box');
		}

		var quote = thread.children('.thread-body').children('p.thread-text-raw').text();
		quote = quote.replace(/^(.*?)$/mg,"> $1");
		quote = quote + "\n\n" + textval;
		resbox.find('textarea[name=c_text]').val(quote);
		return false;
	});

	$(document).on('click', 'button.CoopReplyToCancel', ResCancel);

	$(document).on('click', 'button.CoopReplyToSubmit', function() {
		var resbox = $('.res-box');
		resbox.find('res-msg-box').text('');
		resbox.find('button').attr('disabled','disabled');

		var title = '';
		var sMsg = '';
		var mode = resbox.find('input[name=mode]').val();
		if (mode == 'pedit' || mode == 'pcreate') {
			title = resbox.find('input[name=c_title]').val();
			if (title == '') {
				sMsg += $.i18n.prop('cl_coop_CoopReplyToSubmit_1')+'<br>';
			}
		}
		var textval = resbox.find('textarea[name=c_text]').val();
		var file1 = resbox.find('.file-uploader input[name=c_file1]').val();
		var file2 = resbox.find('.file-uploader input[name=c_file2]').val();
		var file3 = resbox.find('.file-uploader input[name=c_file3]').val();

		if (textval == '' && file1 == '' && file2 == '' && file3 == '') {
			sMsg += $.i18n.prop('cl_coop_CoopReplyToSubmit_2');
		}

		if (sMsg != '') {
			resbox.find('.res-msg-box').html(sMsg);
			resbox.find('button').removeAttr('disabled');
			return false;
		}

		resbox.find('.res-msg-box').text('');
		if (mode == 'pcreate') {
			resbox.find('button').removeAttr('disabled');
			$(this).form.submit();
			return false;
		}

		var mailr = resbox.find('input[name=mail-reply]:checked').val() ? 1:0;
		var mailt = resbox.find('input[name=mail-teacher]:checked').val() ? 1:0;
		var mails = resbox.find('input[name=mail-student]:checked').val() ? 1:0;

		var cID = resbox.find('input[name=c_id]').val();
		var cNO = resbox.find('input[name=c_no]').val();
		var fData = {
			ct: resbox.find('input[name=ct]').val(),
			cc: cID,
			cn: cNO,
			m: mode,
			c_title: title,
			c_text: textval,
			c_file1: file1,
			c_file2: file2,
			c_file3: file3,
			mail_reply: mailr,
			mail_teacher: mailt,
			mail_student: mails,
		};

		$.ajax({
			url: "/"+cMode+"/ajax/coop/CoopRes.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: fData,
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -3:
					case -2:
					case -1:
						addAlert(o.msg,'alert');
						resbox.find('button').removeAttr('disabled');
					break;
					case 0:
						switch (mode) {
							case 'pedit':
							case 'edit':
								var act = $('#c'+cNO+' > div.thread-box');
								act.children('.thread-details').children('.thread-date').text(res.cDate);
								act.children('.thread-body').children('.thread-text').html(res.cText);
								act.children('.thread-body').children('.thread-text-raw').text(textval);

								act.children('.thread-body').children('.files').remove();

								if (Object.keys(res.cFiles).length > 0)
								{
									var files = $('<ul></ul>');
									files.addClass('files');

									$.each(res.cFiles, function(i, val) {
										var li = $('<li></li>').addClass('width-30 inline-block mobi-100 mr8').attr('obj',val.obj);
										li.append(val.tag);
										li.append('<span class="f-name" style="display: none;">'+val.name+'</span>');
										li.append('<span class="f-size" style="display: none;">'+val.size+'</span>');
										li.append('<span class="f-path" style="display: none;">'+val.path+'</span>');

										files.append(li);
									});
									act.children('.thread-body').prepend(files);
								}

								if (mode == 'pedit')
								{
									act.children('.thread-title').children('a:first').text(title);
								}
							break;
							case 'input':
								var par = $('#c'+cNO+' > div.thread-box');
								if (par.children('ul.comment-list').get(0)) {
									var comlist = par.children('ul.comment-list');
								} else {
									var comlist = $('<ul></ul>').addClass('comment-list');
								}
								var listitem = $('<li></li>').addClass('anchor-block').attr('id','c'+res.cNO);
								listitem.append('<span class="tree-line"></span>');
								var act = par.clone(true);
								if (act.children('ul.comment-list').get(0)) {
									act.children('ul.comment-list').remove();
								}

								act.children('.thread-details').children('.thread-writer').text(res.cName);
								var colorClass = (cMode == 't')? 'font-red':'font-green';
								act.children('.thread-details').children('.thread-writer').removeClass('font-gray font-red font-green').addClass(colorClass);
								act.children('.thread-details').children('.thread-writer').addClass('mine');
								act.children('.thread-details').children('.thread-date').text(res.cDate);
								act.children('.thread-body').children('.thread-text').html(res.cText);
								act.children('.thread-body').children('.thread-text-raw').text(textval);

								act.children('.thread-body').children('.files').remove();

								if (Object.keys(res.cFiles).length > 0)
								{
									var files = $('<ul></ul>');
									files.addClass('files');

									$.each(res.cFiles, function(i, val) {
										var li = $('<li></li>').addClass('width-30 inline-block mobi-100 mr8').attr('obj',val.obj);
										li.append(val.tag);
										li.append('<span class="f-name" style="display: none;">'+val.name+'</span>');
										li.append('<span class="f-size" style="display: none;">'+val.size+'</span>');
										li.append('<span class="f-path" style="display: none;">'+val.path+'</span>');

										files.append(li);
									});
									act.children('.thread-body').prepend(files);
								}

								act.children('.thread-option').find('button').val(cID+'_'+res.cNO);
								act.children('.thread-option').find('button').show();

								if (res.cBranch > 0) {
									act.children('.thread-option').find('.CoopReplyTo').hide();
									act.children('.thread-option').find('.thread-coms').hide();
								} else {
									act.children('.thread-option').find('.CoopReplyTo').text($.i18n.prop('cl_coop_CoopReplyToSubmit_3'));
									act.children('.thread-option').find('.thread-comnum').text('0');
								}

								act.children('.thread-option').find('.CoopThreadEdit').removeClass('CoopThreadEdit').addClass('CoopResEdit');
								act.children('.thread-option').find('.CoopPDelete').removeClass('CoopPDelete').addClass('CoopDelete');

								if (cMode == 't') {
									act.children('.thread-option').find('.CoopResEdit').show();
									act.children('.thread-option').find('.CoopDelete').show();
									act.children('.thread-option').find('.CoopAlreadyShow').show();
									act.children('.thread-option').find('.CoopAlreadyShow .thread-alrnum').text('0');
								}

								act.children('.thread-title').remove();
								act.children('.thread-res').children('.res-box').remove();
								act.find('span.attention').remove();

								listitem.append(act);
								comlist.append(listitem);
								par.append(comlist);

								comlist.parents('.thread-box').children('.thread-option').find('.thread-comnum').each(function(i, val) {
									var i  = parseInt($(this).text());
									i++;
									$(this).text(i);
								});

								if (cMode != 't') {
									comlist.parents('.thread-box:first').children('.thread-option').find('.CoopPDelete').hide();
									comlist.parents('.thread-box:first').children('.thread-option').find('.CoopDelete').hide();
								}
							break;
						}
						addAlert(o.msg,'tmp');
						ResCancel();
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				resbox.find('button').removeAttr('disabled');
				return false;
			}
		});

		return false;
	});

	$(document).on('click', 'button.CoopPDelete,a.CoopPDelete', function() {

		if ($(this).val()) {
			var id   = $(this).val();
		} else {
			var list = $(this).parents('ul');
			var id   = $(list).attr('obj');
			list.slideUp('fast');
		}
		var aObj = id.split("_");
		var field = $('#content-inner > .res-field');
		var resbox = $('.res-box');

		confirm($.i18n.prop('cl_coop_CoopPDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			resbox.hide();
			field.append(resbox);

			$.ajax({
				url: "/"+cMode+"/ajax/coop/CoopDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"cc": aObj[0],
					"cn": aObj[1],
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
							var resMsg = $.i18n.prop('cl_coop_CoopPDelete_2');
							if (aObj[3] == "list") {
								$('.c'+aObj[1]).remove();
							} else {
								$('#c'+aObj[1]).remove();
							}
							if (res.childNum > 0) {
								resMsg = $.i18n.prop('cl_coop_CoopPDelete_3') + '('+res.childNum+')';
							}
							addAlert(resMsg,'tmp');
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
		return false;
	});

	$(document).on('click', 'button.CoopDelete,a.CoopDelete', function() {
		if ($(this).val()) {
			var id   = $(this).val();
		} else {
			var list = $(this).parents('ul');
			var id   = $(list).attr('obj');
			list.slideUp('fast');
		}
		var aObj = id.split("_");
		var field = $('#content-inner > .res-field');
		var resbox = $('.res-box');

		confirm($.i18n.prop('cl_coop_CoopDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			resbox.hide();
			field.append(resbox);

			$.ajax({
				url: "/"+cMode+"/ajax/coop/CoopDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"cc": aObj[0],
					"cn": aObj[1],
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
							var resMsg = $.i18n.prop('cl_coop_CoopDelete_2');
							if (aObj[3] == "list") {
								$('.c'+aObj[1]).remove();
							} else {
								$('#c'+aObj[1]).remove();
							}
							if (res.childNum > 0) {
								resMsg = $.i18n.prop('cl_coop_CoopDelete_3') + '('+res.childNum+')';
							}
							addAlert(resMsg,'tmp');

							var num = parseInt(res.childNum) + 1;
							$.each(res.parentNo, function(i, val) {
								if (val > 0) {
									var ComNum = $('#c'+val).find('.thread-comnum:first');
									var j = parseInt(ComNum.text());
									j = j - num;
									ComNum.text(j);

									if (cMode != 't') {
										if (j == 0) {
											var Option = $('#c'+val).find('.thread-option:first');
											if (Option.find('.CoopThreadEdit').css('display') != 'none') {
												Option.find('.CoopPDelete').show();
											}
											if (Option.find('.CoopResEdit').css('display') != 'none') {
												Option.find('.CoopDelete').show();
											}
										}
									}
								}
							});
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
		return false;
	});
});

$(window).on('load', function(){
	var intVal = 70;
	var LB = $('div.anchor-block');
	var index = 0;
	var timeID = 0;

	if (LB) {
		var lu = $('div#thread-group').attr('data');
		var sw = $('div#thread-group').attr('search');
		timeID = setInterval(function() {
			var no = $(LB[index]).attr('id').substr(1);
			$(LB[index]).load(lu + no + '?w=' + encodeURIComponent(sw));
			index++;
			if (!LB[index]) {
				clearInterval(timeID);
			}
		}, intVal);
	}



});


function ResCancel() {
	var resbox = $('.res-box');

	resbox.find('.res-msg-box').text('');

	resbox.find('.input-cover').css({'background-image':'none'});
	resbox.find('.uploaded-file a.file').attr('href','');
	resbox.find('.uploaded-file span.name').text('');
	resbox.find('.uploaded-file span.size').text('');

	resbox.find('input[name=c_title]').val('');
	resbox.find('input[name=c_title]').parents('.formGroup').hide();
	resbox.find('button.CoopReplyToQuote').show();

	resbox.find('.uploaded-file').hide();
	resbox.find('.file-uploader input[type=hidden]').val('');

	resbox.find('#dburl').val('');
	resbox.find('textarea[name=c_text]').val('');

	resbox.find('input[name=c_no]').val(0);
	resbox.hide();
}
