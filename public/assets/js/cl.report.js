$(function() {
	var pathinfo = window.location.pathname.split('/');
	var cMode = pathinfo[1];


	$(document).on('click', 'button.ReportCommentCreate', function() {
		var field = $(this).parent('div').next('.res-field');
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=no]').val();

		resbox.find('button').removeAttr('disabled');
		resbox.find('.res-msg-box').text('');
		resbox.find('input[name=mode]').val('cstart');
		resbox.find('button[type=submit]').hide();
		resbox.find('button.toComment').show();
		resbox.find('button.CoopReplyToQuote').hide();

		if (parseInt(cNO) != 0) {
			resbox.hide();
			resbox.find('input[name=no]').val(0);
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
		var thread = $(this).parents('#c'+aObj[2]);
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=no]').val();
		resbox.find('button').removeAttr('disabled');
		resbox.find('.res-msg-box').text('');
		resbox.find('input[name=mode]').val('input');
		resbox.find('button[type=submit]').hide();
		resbox.find('button.toComment').show();
		resbox.find('button.CoopReplyToQuote').show();

		if (parseInt(aObj[2]) != parseInt(cNO)) {
			resbox.hide();
			resbox.find('input[name=no]').val(aObj[2]);
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
		var thread = $(this).parents('#c'+aObj[2]+' > .thread-box');
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=no]').val();
		resbox.find('button').removeAttr('disabled');
		resbox.find('.res-msg-box').text('');
		resbox.find('button[type=submit]').hide();
		resbox.find('button.toUpdate').show();
		resbox.find('input[name=mode]').val('edit');
		resbox.find('button.CoopReplyToQuote').show();

		if (parseInt(aObj[2]) != parseInt(cNO)) {
			resbox.hide();
			resbox.find('input[name=no]').val(aObj[2]);
		}
		else if (resbox.css('display') == 'inline-block') {
			ResCancel();
			return false;
		}

		resbox.find('textarea[name=c_text]').val(thread.children('.thread-body').children('p.thread-text-raw').text());
		thread.find('div.thread-res:first').append(resbox);
		resbox.show();

		return false;
	});

	$(document).on('click', 'button.CoopReplyToQuote', function() {
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=no]').val();
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
		var textval = resbox.find('textarea[name=c_text]').val();

		if (textval == '') {
			sMsg += $.i18n.prop('cl_report_CoopReplyToSubmit_1');
		}

		if (sMsg != '') {
			resbox.find('.res-msg-box').html(sMsg);
			return false;
		}

		resbox.find('.res-msg-box').text('');

		var rb = resbox.find('input[name=rb]').val();
		var st = resbox.find('input[name=st]').val();
		var cNO = resbox.find('input[name=no]').val();
		var fData = {
			ct: resbox.find('input[name=ct]').val(),
			rb: rb,
			st: st,
			no: cNO,
			m: mode,
			c_text: textval,
		};

		$.ajax({
			url: "/"+cMode+"/ajax/report/ReportCommentRes.json",
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
							case 'edit':
								var act = $('#c'+cNO+' > div.thread-box');
								act.children('.thread-details').children('.thread-date').text(res.rcDate);
								act.children('.thread-body').children('.thread-text').html(res.rcComment);
								act.children('.thread-body').children('.thread-text-raw').text(textval);
							break;
							case 'cstart':
							case 'input':
								var par = $('#c'+cNO+' > div.thread-box');
								if (par.children('ul.comment-list').get(0)) {
									var comlist = par.children('ul.comment-list');
								} else {
									var comlist = $('<ul></ul>').addClass('comment-list');
								}
								var listitem = $('<li></li>').addClass('anchor-block').attr('id','c'+res.no);
								listitem.append('<span class="tree-line"></span>');
								if (mode == 'cstart') {
									var act = $('#s > div.thread-box').clone(true);
								} else {
									var act = par.clone(true);
								}
								if (act.children('ul.comment-list').get(0)) {
									act.children('ul.comment-list').remove();
								}

								act.children('.thread-details').children('.thread-writer').text(res.rcName);
								var colorClass = (cMode == 't')? 'font-red':'font-green';
								act.children('.thread-details').children('.thread-writer').removeClass('font-gray font-red font-green').addClass(colorClass);
								act.children('.thread-details').children('.thread-date').text(res.rcDate);
								act.children('.thread-body').children('.thread-text').html(res.rcComment);
								act.children('.thread-body').children('.thread-text-raw').text(textval);

								act.children('.thread-option').find('button').val(rb+'_'+st+'_'+res.no);
								act.children('.thread-option').find('button').show();

								if (res.rcBranch > 0) {
									act.children('.thread-option').find('.CoopReplyTo').hide();
									act.children('.thread-option').find('.thread-coms').hide();
								} else {
									act.children('.thread-option').find('.CoopReplyTo').text($.i18n.prop('cl_report_CoopReplyToSubmit_2'));
									act.children('.thread-option').find('.thread-comnum').text('0');
								}

								act.children('.thread-option').find('.CoopThreadEdit').removeClass('CoopThreadEdit').addClass('CoopResEdit');
								act.children('.thread-option').find('.CoopPDelete').removeClass('CoopPDelete').addClass('CoopDelete');

								if (cMode == 't') {
									act.children('.thread-option').find('.CoopResEdit').show();
									act.children('.thread-option').find('.CoopDelete').show();
								}

								act.children('.thread-title').remove();
								act.children('.thread-res').children('.res-box').remove();

								listitem.append(act);
								if (mode == 'cstart') {
									comlist.prepend(listitem);
								} else {
									comlist.append(listitem);
								}
								par.append(comlist);

								act.show();

								comlist.parents('.thread-box').children('.thread-option').find('.thread-comnum').each(function(i, val) {
									var i  = parseInt($(this).text());
									i++;
									$(this).text(i);
								});

								if (cMode != 't') {
									comlist.parents('.thread-box:first').children('.thread-option').find('.CoopDelete').hide();
								}

								var comnum = $('.rpComNum:first-child').text();
								comnum++;
								$('.rpComNum').text(comnum);
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

		confirm($.i18n.prop('cl_report_CoopDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			resbox.hide();
			field.append(resbox);

			$.ajax({
				url: "/"+cMode+"/ajax/report/ReportCommentDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"rb": aObj[0],
					"st": aObj[1],
					"no": aObj[2],
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
							var resMsg = $.i18n.prop('cl_report_CoopDelete_2');
							$('#c'+aObj[2]).remove();
							if (res.childNum > 0) {
								resMsg = $.i18n.prop('cl_report_CoopDelete_3') + '('+res.childNum+')';
							}
							addAlert(resMsg,'tmp');

							var num = parseInt(res.childNum) + 1;

							var comnum = $('.rpComNum:first-child').text();
							comnum -= num;
							$('.rpComNum').text(comnum);

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
