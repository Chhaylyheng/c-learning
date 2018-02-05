$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('table').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('table').find('input.Chk').prop('checked',false);
		}
	});

	$('.drill-dropdown-toggle').on('click',function() {
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

		if (mode[2] == 'edit' && $(this).attr('mode') > 0) {
			list.find('.DrillCopy,.DrillToCSV').parent('li').hide();
		} else {
			list.find('li').show();
		}

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$(document).on('click','.ans-dropdown-toggle', function() {
		pickListShow($(this));
	});

	$('.DrillQueryGroupEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/drill/groupedit/'+aObj[1];
		return false;
	});

	$('.DrillCateEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/drill/cateedit/'+aObj[1];
		return false;
	});

	$('.DrillCateDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_drill_DrillCateDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/drill/catedelete/'+aObj[1];
		});
		return false;
	});

	$('.DrillCateSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.DrillCateSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/drill/CateSort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"dc": aObj[1],
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
								var oB = $(TR).prev('tr').find('.DrillCateSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.DrillCateSort');
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

	$('.DQGadd').on('click', function() {
		var table = $('#DQGList');
		var nrow = table.find('tr:last').clone(true);

		var aObj = nrow.attr('obj').split('_');
		var no = parseInt(nrow.attr('no')) + 1;

		nrow.attr('no',no);
		nrow.attr('obj', aObj[0] + '_' + no);
		nrow.find('input[name=dg_name]').val('');
		nrow.find('input[name=dg_name]').removeClass('modify');
		nrow.find('.DQGNum').text('0');

		var delBtn = $('.DQGDelBtn:last').clone(true);
		nrow.find('.DQGDel').empty();
		nrow.find('.DQGDel').append(delBtn);
		delBtn.show();

		table.append(nrow);
		nrow.find('input[name=dg_name]').focus();
	});

	$('.DQGDelBtn').on('click', function() {
		var TR = $(this).parents('tr');
		var PREV = TR.prev('tr');
		var NEXT = TR.next('tr');
		var aObj = TR.attr('obj').split('_');

		if (aObj[1] == 0) {
			return false;
		}

		$.ajax({
			url: "/t/ajax/drill/GroupDelete.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"dc": aObj[0],
				"dg": aObj[1]
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
						console.log(NEXT);
						console.log(PREV);

						if (PREV.attr('no') == 0 && NEXT.length) {
							NEXT.find('.DQGSort[value=up]').attr('disabled','disabled');
						}
						if (!NEXT.length && PREV.attr('no') != 0) {
							PREV.find('.DQGSort[value=down]').attr('disabled','disabled');
						}
						addAlert(o.msg,'tmp');
						TR.remove();
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

	$('.DQGUpdate').on('click', function() {
		var TR = $(this).parents('tr');
		var PREV = TR.prev('tr');
		var aObj = TR.attr('obj').split('_');
		var DgName = TR.find('input[name=dg_name]').val();

		if (!DgName) {
			addAlert($.i18n.prop('cl_t_drill_DQGUpdate_1'),'alert');
			return false;
		}

		$.ajax({
			url: "/t/ajax/drill/GroupUpdate.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"dc": aObj[0],
				"dg": aObj[1],
				"dgname": DgName
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
						TR.attr('obj',res.obj);
						TR.attr('no',res.no);

						if (res.insert) {
							var sortUp = $('.DQGSort[value=up]:last').clone(true);
							var sortDown = $('.DQGSort[value=down]:last').clone(true);

							TR.find('.DQGDel').prepend(' ');
							TR.find('.DQGDel').prepend(sortDown);
							TR.find('.DQGDel').prepend(' ');
							TR.find('.DQGDel').prepend(sortUp);

							if (PREV.attr('no') != 0) {
								sortUp.removeAttr('disabled');
								PREV.find('.DQGSort[value=down]').removeAttr('disabled');
							}
							sortUp.show();
							sortDown.show();
						}

						TR.find('input[name=dg_name]').removeClass('modify');
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

	$('.DQGName input[name=dg_name]').on('change', function() {
		$(this).addClass('modify');
	});

	$('.DQGSort').click(function() {
		var TR = $(this).parents('tr');
		var aObj = TR.attr('obj').split("_");
		var m = $(this).val();
		var oA = TR.find('.DQGSort');
		TR.css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/drill/GroupSort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"dc": aObj[0],
				"dg": aObj[1],
				"m": m,
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
								var oB = $(TR).prev('tr').find('.DQGSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.DQGSort');
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
		TR.css({'background-color':'transparent'});
		return false;
	});




	$('.DrillPublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/drill/Public.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"dc": aObj[0],
				"db": aObj[1],
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

	$('.DrillEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/drill/edit/'+aObj[0]+'/'+aObj[1];
		return false;
	});

	$('.DrillCopy').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/drill/edit/'+aObj[0]+'/'+aObj[1];
		return false;
	});

	$('#DrillCopyExec').on('click', function() {
		var form = $('#ClassCheckForm');
		var input = form.find('input.Chk');
		var bChk = false;

		for (var i = 0; i < input.length; i++) {
			if (input.eq(i).prop('checked')) {
				bChk = true;
				break;
			}
		}

		if (!bChk) {
			addAlert($.i18n.prop('cl_t_quest_QuestCopyExec_1'),'alert');
			return false;
		}

		form.submit();
		return false;
	});

	$('.DrillQueryEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/drill/querylist/'+aObj[0]+'/'+aObj[1];
		return false;
	});

	$('.DrillToCSV').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/output/drill/'+aObj[0]+'/'+aObj[1]+"/drill_questions.csv";

		$('.dropdown-list').slideUp('fast');
		return false;
	});

	$('.Drill2Test').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/drill/tqselect/'+aObj[0]+'/'+aObj[1];
		return false;
	});

	$('.DrillPutReset').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");
		var oLead = $('#'+aObj[0]+'_'+aObj[1]).find('span.PutNum');
		var oAvg = $('#'+aObj[0]+'_'+aObj[1]).find('span.RightAvg');

		confirm($.i18n.prop('cl_t_quest_QuestPutReset_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			$.ajax({
				url: "/t/ajax/drill/PutReset.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"dc": aObj[0],
					"db": aObj[1],
				},
				success: function(o){
					switch (o.err)
					{
						case -3:
						case -2:
						case -1:
							addAlert(o.msg,'alert');
						break;
						case 0:
							$(oLead).text('0');
							$(oAvg).text('0%');
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
		return false;
	});

	$('.DrillDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_drill_DrillDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/drill/delete/'+aObj[0]+'/'+aObj[1];
		});
		return false;
	});

	$('.DrillSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.DrillSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/drill/Sort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"dc": aObj[0],
				"db": aObj[1],
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
								var oB = $(TR).prev('tr').find('.DrillSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.DrillSort');
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

	if ($('.QueryDefaultPanel').get(0)) {
		PanelDisp($('.QueryDefaultPanel').attr('obj').split("_"));
	}
	if ($('.QueryDefaultNewPanel').get(0)) {
		var qNO = $('.QueryDefaultNewPanel').attr('value');
		PanelDispNew(qNO);
	}

	$('.EditPanelOpen').on('click', function() {
		var eNO = $(this).attr('eNo');
		PanelDispNew(eNO);
		$("html,body").animate({scrollTop:"0px"});
		return false;
	});

	$('.EditPanelModify').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		PanelDisp(aObj);
		$("html,body").animate({scrollTop:"0px"});
		return false;
	});

	$('.choice-add').on('click',function(){
		$('div.choice-none:first').css('display','block');
		$('div.choice-none:first').removeClass('choice-none').addClass('choice-block');
	});

	$('.RightChoice').on('change',function(){
		if ($(this).prop('checked')) {
			$(this).parents('label').removeClass('default');
			$(this).parents('label').addClass('confirm');
		} else {
			$(this).parents('label').removeClass('confirm');
			$(this).parents('label').addClass('default');
		}
	});

	$('select[name=qType]').on('change',function() {
		if ($(this).val() == 2) {
			$('div.QueryTypeSelect').hide();
			$('div.QueryTypeText').show();
		} else {
			$('div.QueryTypeText').hide();
			$('div.QueryTypeSelect').show();
		}
	});

	$('.DrillQueryImageDelete').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");

		confirm($.i18n.prop('cl_t_quest_QuestQueryImageDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			$.ajax({
				url: "/t/ajax/drill/QueryImageDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"dc": aObj[0],
					"db": aObj[1],
					"qs": aObj[2],
					"fn": aObj[3],
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
							if (res.del == 'base') {
								$('#qImage').hide();
								$('input[name=dqImage]').val('');
								$('#qImage').attr('src','');
								$('#qImageDel').val('');
								$('#qImageDel').hide();
							} else if (res.del == 'explain') {
								$('#qExplainImage').hide();
								$('input[name=dqExplainImage]').val('');
								$('#qExplainImage').attr('src','');
								$('#qExplainImageDel').val('');
								$('#qExplainImageDel').hide();
							} else {
								$('#qChoiceImage'+res.del).hide();
								$('#choice'+res.del).find('input[name=dqChoiceImage'+res.del+']').val('');
								$('#qChoiceImage'+res.del).attr('src','');
								$('#qChoiceImageDel'+res.del).val('');
								$('#qChoiceImageDel'+res.del).hide();
							}
							addAlert($.i18n.prop('cl_t_quest_QuestQueryImageDelete_2'),'tmp');
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

	$('.DrillQuerySort').click(function() {
		var LI = $(this).parents('div.QPanel');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(LI).find('.DrillQuerySort');

		$.ajax({
			url: "/t/ajax/drill/QuerySort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"dc": aObj[0],
				"db": aObj[1],
				"qn": aObj[2],
				"m": aObj[3],
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
							var oPrev = $(LI).prevAll('div.QPanel')[0];
							if (oPrev) {
								var oB = $(oPrev).find('.DrillQuerySort');
								SortBtnDisabled(oA,oB);
								$(LI).insertBefore($(oPrev));
								$(oPrev).insertAfter($(oPrev).next());
								$(LI).find('.QQS').text(parseInt(res.qs)-1);
								$(oPrev).find('.QQS').text(res.qs);
								$(LI).find('.PreviewLink').attr('href','/t/drill/preview/'+aObj[0]+'#q'+(parseInt(res.qs)-1));
								$(oPrev).find('.PreviewLink').attr('href','/t/drill/preview/'+aObj[0]+'#q'+parseInt(res.qs));
							}
						} else {
							var oNext = $(LI).nextAll('div.QPanel')[0];
							if (oNext) {
								var oB = $(oNext).find('.DrillQuerySort');
								SortBtnDisabled(oA,oB);
								$(LI).insertAfter($(oNext));
								$(oNext).insertBefore($(oNext).prev());
								$(LI).find('.QQS').text(parseInt(res.qs)+1);
								$(oNext).find('.QQS').text(res.qs);
								$(LI).find('.PreviewLink').attr('href','/t/drill/preview/'+aObj[0]+'#q'+(parseInt(res.qs)+1));
								$(oNext).find('.PreviewLink').attr('href','/t/drill/preview/'+aObj[0]+'#q'+parseInt(res.qs));
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
	});

	$('.DrillQueryDelete').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var url = '/t/drill/querydelete/'+aObj[0]+'/'+aObj[1]+'/'+aObj[2];

		confirm($.i18n.prop('cl_t_drill_DrillQueryDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			window.location.href = url;
		});
		return;
	});

	$("#form_d_group").autocomplete({
		delay: 300,
		minLength: 2,
		highlight: true,
		source: function(req, resp){
			$.ajax({
				url: "/t/ajax/drill/QueryGroup.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					dc: $("#form_d_group").attr('obj'),
					gname: req.term
				},
				success: function(o){
					resp(o);
				},
				error: function(xhr, ts, err){
					if (xhr.status == 406) {
						var o = xhr.responseJSON;
						resp(o);
						return;
					}
					resp();
				}
			});
		},
		search: function(event, ui){
			if (event.keyCode == 229) return false;
				return true;
		},
		open: function() {
			$(this).removeClass("ui-corner-all");
		}
	})
	.keyup(function(event){
		if (event.keyCode == 13) {
			$(this).autocomplete("search");
		}
	});

	$('.QBTabMenu li').click(function() {
		var mode = $(this).attr('data');
		$('.QBTabMenu li').removeClass('QBTabActive');
		$('.QBTabContents').hide();

		$('#'+mode).show();
		$(this).addClass('QBTabActive');
	});

	$('select[name=t_select]').on('change', function() {

		if ($(this).val() == 'new') {
			$('#test-create').show();
		} else {
			$('#test-create').hide();
		}

	});

	$('input[name=t_auto_public]').on('change',function() {
		if ($(this).val() == 1) {
			$('.auto-datetime').show();
		} else {
			$('.auto-datetime').hide();
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

	$('input.dq-score').on('change blur',function() {
		var total = 0;
		$('input.dq-score').each(function() {
			total += parseInt($(this).val());
		});
		$('span.total-score').text(total);
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

function PanelDispNew(eNO) {
	$('p.error-box').hide();
	$('#eNo').text(eNO);
	$('.eLabelAdd').show();
	$('.eLabelEdit').hide();
	$('input[name=qSort]').val(eNO);
	$('input[name=qNo]').val('');
	$('input[name=qGroup]').val('');
	$('select[name=qType]').val(0);
	$('textarea[name=qText]').val('');
	$('textarea[name=qExplain]').val('');

	$('input[name=qImage]').replaceWith('<input type="file" value="" name="qImage">');
	$('#qImage').hide();
	$('input[name=dqImage]').val('');
	$('#qImage').attr('src','');
	$('#qImageDel').val('');
	$('#qImageDel').hide();

	$('input[name=qExplainImage]').replaceWith('<input type="file" value="" name="qExplainImage">');
	$('#qExplainImage').hide();
	$('input[name=dqExplainImage]').val('');
	$('#qExplainImage').attr('src','');
	$('#qExplainImageDel').val('');
	$('#qExplainImageDel').hide();

	$('div.choice-block').each(function(i) {
		$(this).hide();
		$(this).find('textarea,input[type=hidden]').val('');
		$(this).find('input[type=checkbox]').prop('checked',false);
		$(this).find('input[type=checkbox]').parents('label').removeClass('default');
		$(this).find('input[type=checkbox]').parents('label').removeClass('confirm');
		$(this).find('input[type=checkbox]').parents('label').addClass('default');
		var inputName = $(this).find('input[type=file]').attr('name');
		$(this).find('input[type=file]').replaceWith('<input type="file" value="" name="'+inputName+'">');
		$(this).find('img').attr('src','');
		$(this).find('img').hide();
		$(this).find('.DrillQueryImageDelete').val('');
		$(this).find('.DrillQueryImageDelete').hide();
		$(this).removeClass('choice-block').addClass('choice-none');
	});

	$('div.choice-none').each(function(i) {
		if (i < 5) {
			$(this).css('display','block');
			$(this).removeClass('choice-none').addClass('choice-block');
		}
	});

	$('input.DrillRightText').each(function(i) {
		$(this).val('');
	});

	$('div.QueryTypeSelect').show();
	$('div.QueryTypeText').hide();
	$('#QueryEditPanel').show();
	return false;
};

function PanelDisp(aObj) {
	$.ajax({
		url: "/t/ajax/drill/QueryLoad.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"dc": aObj[0],
			"db": aObj[1],
			"qn": aObj[2],
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
					$('p.error-box').hide();
					$('#eNo').text(res.dqSort);
					$('.eLabelAdd').hide();
					$('.eLabelEdit').show();
					$('input[name=qSort]').val(res.dqSort);
					$('input[name=qNo]').val(res.dqNO);
					$('input[name=qGroup]').val(res.dgName);

					$('select[name=qType]').val(res.dqStyle);
					var aType = $('p.qType span');
					aType.css({'display':'none'});
					aType.eq(res.dqStyle).css({'display':'inline'});

					$('textarea[name=qText]').val(res.dqText);
					$('input[name=qImage]').replaceWith('<input type="file" value="" name="qImage">');
					if (res.dqImage) {
						$('input[name=dqImage]').val(res.dqImage);
						$('#qImage').attr('src',o.path+res.dqImage+'?'+getRand(0,99999));
						$('#qImage').show();
						$('#qImageDel').val(res.dcID+'_'+res.dbNO+'_'+res.dqSort+'_'+res.dqImage);
						$('#qImageDel').show();
					} else {
						$('#qImage').hide();
						$('input[name=dqImage]').val('');
						$('#qImage').attr('src','');
						$('#qImageDel').val('');
						$('#qImageDel').hide();
					}
					$('textarea[name=qExplain]').val(res.dqExplain);
					$('input[name=qExplainImage]').replaceWith('<input type="file" value="" name="qExplainImage">');
					if (res.dqExplainImage) {
						$('input[name=dqExplainImage]').val(res.dqExplainImage);
						$('#qExplainImage').attr('src',o.path+res.dqExplainImage+'?'+getRand(0,99999));
						$('#qExplainImage').show();
						$('#qExplainImageDel').val(res.dcID+'_'+res.dbNO+'_'+res.dqSort+'_'+res.dqExplainImage);
						$('#qExplainImageDel').show();
					} else {
						$('#qExplainImage').hide();
						$('input[name=dqExplainImage]').val('');
						$('#qExplainImage').attr('src','');
						$('#qExplainImageDel').val('');
						$('#qExplainImageDel').hide();
					}
					$('div.choice-block').each(function(i) {
						$(this).hide();
						$(this).find('textarea,input[type=hidden]').val('');
						$(this).find('input[type=checkbox]').prop('checked',false);
						$(this).find('input[type=checkbox]').parents('label').removeClass('default');
						$(this).find('input[type=checkbox]').parents('label').removeClass('confirm');
						$(this).find('input[type=checkbox]').parents('label').addClass('default');
						var inputName = $(this).find('input[type=file]').attr('name');
						$(this).find('input[type=file]').replaceWith('<input type="file" value="" name="'+inputName+'">');
						$(this).find('img').attr('src','');
						$(this).find('img').hide();
						$(this).find('.DrillQueryImageDelete').val('');
						$(this).find('.DrillQueryImageDelete').hide();
						$(this).removeClass('choice-block').addClass('choice-none');
					});

					$('input.DrillRightText').each(function(i) {
						$(this).val('');
					});

					if (res.dqStyle != 2) {
						var dqRight = res.dqRight1.split("|");
						var check = false;
						var color = 'default';
						for (var i = 1; i <= res.dqChoiceNum; i++) {
							if ($.inArray(String(i),dqRight) >= 0) {
								check = true;
								color = 'confirm';
							} else {
								check = false;
								color = 'default';
							}
							$('#choice'+i).find('textarea').val(res['dqChoice'+i]);
							$('#choice'+i).find('input[type=checkbox]').prop('checked', check);
							$('#choice'+i).find('input[type=checkbox]').parents('label').removeClass('default');
							$('#choice'+i).find('input[type=checkbox]').parents('label').addClass(color);
							if (res['dqChoiceImg'+i]) {
								$('input[name=dqChoiceImage'+i+']').val(res['dqChoiceImg'+i]);
								$('#qChoiceImage'+i).attr('src',o.path+res['dqChoiceImg'+i]+'?'+getRand(0,99999));
								$('#qChoiceImage'+i).show();
								$('#qChoiceImageDel'+i).val(res.dcID+'_'+res.dbNO+'_'+res.dqSort+'_'+res['dqChoiceImg'+i]);
								$('#qChoiceImageDel'+i).show();
							}
							$('#choice'+i).css('display','block');
							$('#choice'+i).removeClass('choice-none').addClass('choice-block');
						}
						$('div.QueryTypeText').hide();
						$('div.QueryTypeSelect').show();
					} else {
						for (var i = 1; i <= 5; i++) {
							$('input[name=qRightText'+i+']').val(res['dqRight'+i]);
						}
						$('div.QueryTypeSelect').hide();
						$('div.QueryTypeText').show();
					}
					$('#QueryEditPanel').show();
				break;
			}
			return false;
		},
		error: function(xhr, ts, err){
			addAlert('Network Access Error','alert');
			return false;
		}
	});
};


function AggregationState() {
	var btn = $('#drill-analysis-btn');
	var sObj = btn.attr('obj');

	$.ajax({
		url: "/t/ajax/drill/AggregationState.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"dc" : sObj
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
					if (res == 0 || res == 2) {
						clearInterval(timerID);
						window.location.reload();
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

