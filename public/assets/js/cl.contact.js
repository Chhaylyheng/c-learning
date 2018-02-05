$(function() {
	var pathinfo = window.location.pathname.split('/');
	var cMode = pathinfo[1];

	$(document).on('click', '.ContactShowThread', function() {
		var par = $(this).parents('div.co-anchor-block');
		var cNO = $(this).attr('obj');
		if (par.find('.ContactDisp:first').css('display') == 'none') {
			$.ajax({
				url: "/"+cMode+"/ajax/contact/ContactRead.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"cn": cNO,
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
						break;
					}
					return false;
				},
				error: function(xhr, ts, err){
					addAlert('Network Access Error','alert');
					return false;
				}
			});
		}

		par.find('.ContactDisp').toggle();
		return false;
	});

	$(document).on('click', 'a.ContactCreate', function() {
		var field = $('#content-inner > .res-field');
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=c_no]').val();

		resbox.find('.res-msg-box').text('');
		resbox.find('input[name=c_subject]').val('');
		resbox.find('input[name=mode]').val('pcreate');
		resbox.find('button[type=submit]').hide();
		resbox.find('button.ToContact').show();
		resbox.find('button.ContactReplyToQuote').hide();

		if (parseInt(cNO) != 0) {
			resbox.hide();
			resbox.find('input[name=c_no]').val(0);
			resbox.find('input[name=c_before]').val(0);
		}
		else if (resbox.css('display') == 'inline-block') {
			ResCancel();
			return false;
		}

		field.append(resbox);
		resbox.show();

		return false;
	});


	$(document).on('click', 'button.ContactReplyTo', function() {
		var aObj = $(this).val().split("_");
		var thread = $(this).parents('#c'+aObj[0]);
		var resbox = $('.res-box');
		if (aObj[0] != aObj[1]) {
			var before = thread.find('#c'+aObj[1]);
			var subject = before.find('.co-thread-title').text();
		} else {
			var before = thread;
			var subject = before.find('.co-thread-title span:first-child').text();
		}
		var cNO = resbox.find('input[name=c_no]').val();
		var cBefore = resbox.find('input[name=c_before]').val();
		resbox.find('.res-msg-box').text('');
		resbox.find('input[name=mode]').val('input');
		resbox.find('button[type=submit]').hide();
		resbox.find('button.ReplyTo').show();
		resbox.find('input[name=c_subject]').val('Re:'+subject);
		resbox.find('button.ContactReplyToQuote').show();

		if (parseInt(aObj[0]) != parseInt(cNO)) {
			resbox.hide();
			resbox.find('input[name=c_no]').val(aObj[0]);
			resbox.find('input[name=c_before]').val(aObj[1]);
		}
		else if (resbox.css('display') == 'inline-block') {
			ResCancel();
			return false;
		}

		before.find('div.co-thread-res:first').append(resbox);
		resbox.show();

		return false;
	});

	$(document).on('click', 'button.ContactReplyToQuote', function() {
		var resbox = $('.res-box');
		var cNO = resbox.find('input[name=c_no]').val();
		var cBefore = resbox.find('input[name=c_before]').val();
		var textval = resbox.find('textarea[name=c_text]').val();
		resbox.find('.res-msg-box').text('');

		if (parseInt(cNO) == 0)
		{
			return false;
		}

		var mode = resbox.find('input[name=mode]').val();
		var before = $('#c'+cBefore+' > .co-thread-box .co-thread-body p.co-thread-text-raw');

		var quote = before.text();
		quote = quote.replace(/^(.*?)$/mg,"> $1");
		quote = quote + "\n\n" + textval;
		resbox.find('textarea[name=c_text]').val(quote);
		return false;
	});

	$(document).on('click', 'button.ContactToCancel', ResCancel);

	$(document).on('click', 'button.ContactReplyToSubmit', function() {
		var resbox = $('.res-box');
		resbox.find('res-msg-box').text('');

		var sMsg = '';
		var mode = resbox.find('input[name=mode]').val();
		var subject = resbox.find('input[name=c_subject]').val();
		var textval = resbox.find('textarea[name=c_text]').val();

		if (textval == '') {
			sMsg += $.i18n.prop('cl_contact_ContactReplyToSubmit_1');
		}

		if (sMsg != '') {
			resbox.find('.res-msg-box').html(sMsg);
			return false;
		}

		resbox.find('.res-msg-box').text('');
		if (mode == 'pcreate') {
			return true;
		}

		var cNO = resbox.find('input[name=c_no]').val();
		var fData = {
			ct: resbox.find('input[name=ct]').val(),
			cn: cNO,
			m: mode,
			c_subject: subject,
			c_text: textval
		};

		$.ajax({
			url: "/"+cMode+"/ajax/contact/ContactRes.json",
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
					break;
					case 0:
						switch (mode) {
							case 'input':
								var par = $('#c'+cNO+' > div.co-thread-box');
								if (par.find('ul.co-comment-list').get(0)) {
									var comlist = par.find('ul.co-comment-list');
								} else {
									var comlist = $('<ul></ul>').addClass('co-comment-list');
								}
								var listitem = $('<li></li>').addClass('co-anchor-block').attr('id','c'+res.no);
								listitem.append('<span class="co-tree-line"></span>');
								var act = par.clone(true);
								act.css({'border-left':'5px solid #dddddd'});
								if (act.find('ul.co-comment-list').get(0)) {
									act.find('ul.co-comment-list').remove();
								}

								act.find('span.co-thread-writer').text(res.coName);
								var colorClass = (cMode == 't')? 'font-red':'font-green';

								act.find('.ContactShowThread').removeClass('ContactShowThread');
								if (act.find('span.co-thread-comnum').get(0)) {
									act.find('span.co-thread-comnum').remove();
								}
								if (act.find('.co-thread-title img').get(0)) {
									act.find('.co-thread-title img').remove();
								}

								act.find('.co-thread-title').text(res.coSubject);
								act.find('.co-thread-title').prepend('<i class="fa fa-envelope-open-o mr4"></i>');
								act.find('span.co-thread-writer').removeClass('font-gray font-red font-green').addClass(colorClass);
								act.find('span.thread-date').text(res.coDate);
								act.find('p.co-thread-text').html(res.coBody);
								act.find('p.co-thread-text-raw').text(textval);

								act.find('.co-thread-option button').val(cNO+'_'+res.no);
								act.find('.co-thread-option button').show();

								act.find('.co-thread-option .ContactReplyTo').text($.i18n.prop('cl_contact_ContactReplyToSubmit_2'));

								act.find('.co-thread-res .res-box').remove();
								act.find('.co-thread-option .co-thread-coms').remove();

								listitem.append(act);
								comlist.append(listitem);
								par.append(comlist);

								comlist.parents('.co-thread-box').find('.co-thread-comnum').each(function(i, val) {
									var i  = parseInt($(this).text());
									i++;
									$(this).text(i);
									$(this).show();
								});
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
				return false;
			}
		});
		return false;
	});

	$(document).on('click', '.ContactDelete',function() {
		var aObj = $(this).val().split("_");
		var thread = $(this).parents('#c'+aObj[0]);
		var mine = $(this).parents('#c'+aObj[1]);
		var field = $('#content-inner > .res-field');
		var resbox = $('.res-box');

		confirm($.i18n.prop('cl_contact_ContactDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			resbox.hide();
			field.append(resbox);

			$.ajax({
				url: "/"+cMode+"/ajax/contact/ContactDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
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
							var resMsg = $.i18n.prop('cl_contact_ContactDelete_2');
							$('#c'+aObj[1]).remove();
							if (res.childNum > 0) {
								resMsg = $.i18n.prop('cl_contact_ContactDelete_3') + '('+res.childNum+')';
							}

							thread.find('.co-thread-comnum').each(function(i, val) {
								var i  = parseInt($(this).text());
								i--;
								$(this).text(i);
								if (i > 0) {
									$(this).show();
								} else {
									$(this).hide();
								}
							});
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

	$(document).on('click', '.contact-dropdown-toggle',function() {
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
			left: parseInt(offset.left - list.width() + $(this).width() + 5)+'px',
		});
		list.slideDown('fast');
	});

	$('.ContactStatus').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var ct   = $(list).attr('ct');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/"+cMode+"/ajax/contact/ContactStatus.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": ct,
				"cn": aObj[0],
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
						$(Btn).removeClass('font-red');
						$(Btn).removeClass('font-blue');
						$(Btn).addClass(res.class);
						$(Btn).find('div').text(res.text);
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


});


function ResCancel() {
	var resbox = $('.res-box');

	resbox.find('.res-msg-box').text('');

	resbox.find('input[name=c_subject]').val('');
	resbox.find('button.ContactReplyToQuote').show();

	resbox.find('textarea[name=c_text]').val('');

	resbox.find('input[name=c_no]').val(0);
	resbox.find('input[name=c_before]').val(0);
	resbox.hide();
}
