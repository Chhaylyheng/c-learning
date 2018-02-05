$(function() {
	$('.ClassSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.ClassSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/class/Sort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"status": aObj[1],
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
								var oB = $(TR).prev('tr').find('.ClassSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.ClassSort');
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
});

$(window).load(function() {

	/**************************************************
	 * Assistant Dialog Show
	 **************************************************/
	$('.AssistantDialogShow').on('click', function() {
		shadowMask('on');
		var aObj = $(this).attr('id').split("_");
		var sTit = ' - ' + $(this).parents('tr').children('td:first-child').children('a').text();
		var sAss = $(this).text();
		var iAss = parseInt($(this).attr('state'));
		var iOpt = $('.AssistListBox').find('select option').length;

		var dg = $('#AssistantDialog');

		if (iAss) {
			sTit += '（' + sAss + '）';
			$('.AssistDeleteBox').show();
		} else {
			$('.AssistDeleteBox').hide();
		}
		if (iOpt > 1) {
			$('.AssistListBox').show();
		} else {
			$('.AssistListBox').hide();
		}

		dg.find('.cTitle').text(sTit);
		dg.find('input[name=ctid]').val(aObj[0]);
		dg.find('input[name=a_name]').val('');
		dg.find('input[name=a_mail]').val('');
		dg.find('.error-msg').text('');

		var mbTop = (($(window).height()-dg.outerHeight())/2 - FixedSize);
		var mbLeft = (($(window).width()-dg.outerWidth())/2);

		mbTop = (mbTop < 0)? 8:mbTop;
		mbLeft = (mbLeft < 0)? 0:mbLeft;

		dg.css({
			'top': mbTop+'px',
			'left': mbLeft+'px'
		});

		dg.find('.error-msg').hide();
		dg.show();

		$('#shadowMask').on('click', function() {
			$('#AssistantDialog').hide();
			shadowMask('off');
		});
	});

	/**************************************************
	 * Assistant New Registration
	 **************************************************/
	$('#AssistNewRegist').on('submit', function() {
		$('#AssistantDialog').find('input,button').attr('disabled','disabled');

		var ctid = $(this).find('input[name=ctid]').val();
		var a_name = $(this).find('input[name=a_name]').val();
		var a_mail = $(this).find('input[name=a_mail]').val();
		var Btn = $('#'+ctid+'_assist');

		$.ajax({
			url: "/t/ajax/assistant/regist.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": ctid,
				"a_name": a_name,
				"a_mail": a_mail,
			},
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -1:
					case -2:
						$('#AssistantDialog').hide();
						shadowMask('off');
						addAlert(o.msg,'alert');
					break;
					case -3:
						$('#AssistNewRegist .error-msg').html(o.msg).show();
						return false;
					break;
					case 0:

						Btn.text(res.name);
						Btn.attr('state',1);

						var bOpt = false;
						var option = $('<option>')
							.val(res.atid)
							.text(res.name+'（'+res.mail+'）')
						;

						$('#AssistantDialog .AssistListBox select option').each(function(index) {
							if ($(this).attr('value') == res.atid) {
								console.log('val:' + $(this).attr('value'));
								$(this).text(res.name+'（'+res.mail+'）');
								bOpt = true;
							}
						});
						if (!bOpt) {
							$('#AssistantDialog .AssistListBox select').append(option);
						}

						$('#AssistantDialog').hide();
						shadowMask('off');

						addAlert(o.msg,'tmp');
						return false;
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				$('#AssistantDialog').hide();
				shadowMask('off');

				addAlert('Network Access Error','alert');
				return false;
			},
			complete: function() {
				$('#AssistantDialog').find('input,button').removeAttr('disabled');
			}
		});

		return false;
	});

	/**************************************************
	 * Assistant Set from List
	 **************************************************/
	$('#AssistListSet').on('submit', function() {
		$('#AssistantDialog').find('input,button').attr('disabled','disabled');

		var ctid = $(this).find('input[name=ctid]').val();
		var aid = $(this).find('select[name=a_list]').val();
		var Btn = $('#'+ctid+'_assist');

		$.ajax({
			url: "/t/ajax/assistant/set.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": ctid,
				"at": aid,
			},
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -1:
					case -2:
						$('#AssistantDialog').hide();
						shadowMask('off');
						addAlert(o.msg,'alert');
					break;
					case -3:
						$('#AssistListSet .error-msg').text(o.msg).show();
						return false;
					break;
					case 0:

						Btn.text(res.name);
						Btn.attr('state',1);

						$('#AssistantDialog').hide();
						shadowMask('off');

						addAlert(o.msg,'tmp');
						return false;
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				$('#AssistantDialog').hide();
				shadowMask('off');

				addAlert('Network Access Error','alert');
				return false;
			},
			complete: function() {
				$('#AssistantDialog').find('input,button').removeAttr('disabled');
			}
		});

		return false;
	});

	/**************************************************
	 * Assistant Delete
	 **************************************************/
	$('#AssistDelete').on('submit', function() {
		$('#AssistantDialog').find('input,button').attr('disabled','disabled');

		var ctid = $(this).find('input[name=ctid]').val();
		var Btn = $('#'+ctid+'_assist');

		$('#AssistantDialog').css('opacity', 0);
		confirm($.i18n.prop('cl_t_index_AssistDelete_1'), function(bOK) {
			if (!bOK) {
				$('#AssistantDialog').css('opacity', 1);
				$('#AssistantDialog').find('input,button').removeAttr('disabled');
				return false;
			}

			$('#AssistantDialog').css('opacity', 1);
			$.ajax({
				url: "/t/ajax/assistant/remove.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"ct": ctid
				},
				success: function(o){
					var res = o.res;
					switch (o.err)
					{
						case -1:
						case -2:
							$('#AssistantDialog').hide();
							shadowMask('off');
							addAlert(o.msg,'alert');
						break;
						case -3:
							$('#AssistDelete .error-msg').text(o.msg).show();
							return false;
						break;
						case 0:

							Btn.text(res.name);
							Btn.attr('state',0);


							$('#AssistantDialog').hide();
							shadowMask('off');

							addAlert(o.msg,'tmp');
							return false;
						break;
					}
					return false;
				},
				error: function(xhr, ts, err){
					$('#AssistantDialog').hide();
					shadowMask('off');

					addAlert('Network Access Error','alert');
					return false;
				},
				complete: function() {
					$('#AssistantDialog').find('input,button').removeAttr('disabled');
				}
			});

			return false;
		});
		return false;
	});

	/**************************************************
	 * Assistant Dialog Close
	 **************************************************/
	$('.AssistDialogClose').on('click', function() {

		$('#AssistantDialog').hide();
		shadowMask('off');

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

