$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('table').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('table').find('input.Chk').prop('checked',false);
		}
	});

	$('.test-dropdown-toggle').on('click',function() {
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
			list.find('.TestCopy,.TestToCSV').parent('li').hide();
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

	$('.TestPublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/test/Public.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"tb": aObj[1],
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

	$('.TestScorePublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/test/ScorePublic.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"tb": aObj[1],
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
						$(Btn).removeClass('font-green');
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

	$('.TestEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/test/edit/'+aObj[1];
		return false;
	});

	$('.TestCopy').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/test/copy/'+aObj[1];
		return false;
	});

	$('#TestCopyExec').on('click', function() {
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

	$('.TestQueryEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/test/querylist/'+aObj[1];
		return false;
	});

	$('.TestResultToCSV').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/output/testresult/'+aObj[1]+".csv";

		$('.dropdown-list').slideUp('fast');
		return false;
	});

	$('.TestToCSV').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/output/test/'+aObj[1]+".csv";

		$('.dropdown-list').slideUp('fast');
		return false;
	});

	$('.Test2Drill').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/test/dqselect/'+aObj[1];
		return false;
	});

	$('.TestPutReset').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");
		var oLead = $('#'+aObj[1]).find('span.PutNum');
		var oQualify = $('#'+aObj[1]).find('span.QualifyNum');
		var oAvg = $('#'+aObj[1]).find('span.AvgScore');

		confirm($.i18n.prop('cl_t_quest_QuestPutReset_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			$.ajax({
				url: "/t/ajax/test/PutReset.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"ct": aObj[0],
					"tb": aObj[1],
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
							$(oQualify).text('0');
							$(oAvg).text('0');
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

	$('.TestDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_test_TestDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/test/delete/'+aObj[1];
		});
		return false;
	});

	$('.TestSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.TestSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/test/Sort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"tb": aObj[1],
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
								var oB = $(TR).prev('tr').find('.TestSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.TestSort');
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

	$('.TestBaseImageDelete').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");

		confirm($.i18n.prop('cl_t_quest_QuestQueryImageDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			$.ajax({
				url: "/t/ajax/test/BaseImageDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"tb": aObj[0],
					"fn": aObj[1],
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
							$('#bImage').hide();
							$('input[name=tbImage]').val('');
							$('#bImage').attr('src','');
							$('#bImageDel').val('');
							$('#bImageDel').hide();
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

	$('.TestQueryImageDelete').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");

		confirm($.i18n.prop('cl_t_quest_QuestQueryImageDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			$.ajax({
				url: "/t/ajax/test/QueryImageDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"tb": aObj[0],
					"qs": aObj[1],
					"fn": aObj[2],
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
								$('input[name=tqImage]').val('');
								$('#qImage').attr('src','');
								$('#qImageDel').val('');
								$('#qImageDel').hide();
							} else if (res.del == 'explain') {
								$('#qExplainImage').hide();
								$('input[name=tqExplainImage]').val('');
								$('#qExplainImage').attr('src','');
								$('#qExplainImageDel').val('');
								$('#qExplainImageDel').hide();
							} else {
								$('#qChoiceImage'+res.del).hide();
								$('#choice'+res.del).find('input[name=tqChoiceImage'+res.del+']').val('');
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

	$('.TestQuerySort').click(function() {
		var LI = $(this).parents('div.QPanel');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(LI).find('.TestQuerySort');

		$.ajax({
			url: "/t/ajax/test/QuerySort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"tb": aObj[0],
				"qn": aObj[1],
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
							var oPrev = $(LI).prevAll('div.QPanel')[0];
							if (oPrev) {
								var oB = $(oPrev).find('.TestQuerySort');
								SortBtnDisabled(oA,oB);
								$(LI).insertBefore($(oPrev));
								$(oPrev).insertAfter($(oPrev).next());
								$(LI).find('.QQS').text(parseInt(res.qs)-1);
								$(oPrev).find('.QQS').text(res.qs);
								$(LI).find('.PreviewLink').attr('href','/t/test/preview/'+aObj[0]+'#q'+(parseInt(res.qs)-1));
								$(oPrev).find('.PreviewLink').attr('href','/t/test/preview/'+aObj[0]+'#q'+parseInt(res.qs));
							}
						} else {
							var oNext = $(LI).nextAll('div.QPanel')[0];
							if (oNext) {
								var oB = $(oNext).find('.TestQuerySort');
								SortBtnDisabled(oA,oB);
								$(LI).insertAfter($(oNext));
								$(oNext).insertBefore($(oNext).prev());
								$(LI).find('.QQS').text(parseInt(res.qs)+1);
								$(oNext).find('.QQS').text(res.qs);
								$(LI).find('.PreviewLink').attr('href','/t/test/preview/'+aObj[0]+'#q'+(parseInt(res.qs)+1));
								$(oNext).find('.PreviewLink').attr('href','/t/test/preview/'+aObj[0]+'#q'+parseInt(res.qs));
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

	$('.TestQueryDelete').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var url = '/t/test/querydelete/'+aObj[0]+'/'+aObj[1];

		confirm($.i18n.prop('cl_t_test_TestQueryDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			window.location.href = url;
		});
		return;
	});

	$(document).on({
		'click':function() {
			shadowMask('on');
			var data = $(this).attr('data');
			var text = $(this).nextAll('span').html();
			var choice = $(this).text();

			var CloseBtn = $('<div></div>')
				.addClass('BTClose')
				.append('<i class="fa fa-times"></i>')
				.on('click', function() {
					$('.BigText').hide();
					shadowMask('off');
					$('.BigText').remove();
				});

			var btnID = pickBtn.find('button').attr('id');
			var src = pickBtn.find('img').attr('src');
			var pick = pickBtn.find('img').attr('pick');

			pickBtn.find('button').attr('id',btnID+'_big');
			pickBtn.find('button').on('click', function() {
				pickListShow($(this));
			});

			var BigText = $('<div></div>')
				.attr('id','BigText')
				.append('<span class="choice'+data+'">'+choice+'</span>')
				.append(pickBtn)
				.append(text)
				.append(CloseBtn)
				.css({
					'width':'90%',
					'height':'90vh',
				})
				.on('click',function(){
					return false;
				})
				.on('scroll', function(){
					var sc = $(this).scrollTop();
					$('.BTClose').css({
						'top': 3+sc+'px'
					});

				});

			$('#shadowMask').on('click', function() {
				$('.BigText').hide();
				shadowMask('off');
				$('.BigText').remove();
			});
			$('#shadowMask').append(BigText);

			BigText.css({
				'top': (($(window).height()-BigText.outerHeight())/2)+'px',
				'left': (($(window).width()-BigText.outerWidth())/2)+'px'
			});
		}
	},'.QBCommentList li span.largeShow');

	$(document).on({
		'click':function() {
			shadowMask('on');
			var data = $(this).attr('data');
			var text = $(this).parents('tr').find('.QBAnsText span:last-child').html();
			var choice = $(this).text();

			var CloseBtn = $('<div></div>')
				.addClass('BTClose')
				.append('<i class="fa fa-times"></i>')
				.on('click', function() {
					$('.BigText').hide();
					shadowMask('off');
					$('.BigText').remove();
				});

			var BigText = $('<div></div>')
				.attr('id','BigText')
				.append('<span class="choice'+data+'">'+choice+'</span>')
				.append(text)
				.append(CloseBtn)
				.css({
					'width':'90%',
					'height':'90vh',
				})
				.on('click',function(){
					return false;
				})
				.on('scroll', function(){
					var sc = $(this).scrollTop();
					$('.BTClose').css({
						'top': 3+sc+'px'
					});

				});
			$('#shadowMask').on('click', function() {
				$('.BigText').hide();
				shadowMask('off');
				$('.BigText').remove();
			});
			$('#shadowMask').append(BigText);

			BigText.css({
				'top': (($(window).height()-BigText.outerHeight())/2)+'px',
				'left': (($(window).width()-BigText.outerWidth())/2)+'px'
			});
		}
	},'.QBBentAnsList span.largeShow');
	$('.QBCommentFilter span').on('click',function() {
		var cls = $(this).attr('class');
		if (cls == 'choiceAll') {
			$(this).parents('div').next('ul').attr('mode','all');
			$(this).parents('div').next('ul').find('li').removeClass('hideLi');
		} else {
			$(this).parents('div').next('ul').attr('mode',cls);
			$(this).parents('div').next('ul').find('li').addClass('hideLi');
			$(this).parents('div').next('ul').find('.'+cls).parent('li').removeClass('hideLi');
		}
	});

	$('.TeachCommentUpdate').on('click', function() {
		var textarea = $(this).parents('#TeachComment').find('textarea');
		var text = textarea.val();
		var sObj = $(this).attr('data');
		var aObj = sObj.split("_");

		$.ajax({
			url: "/t/ajax/test/TeachComment.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"tb": aObj[0],
				"st": aObj[1],
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

	$('.TBNumMember').on('click', function(e) {
		$('.QBFloatBox').hide();
		$('.QBFloatBox .QBFloatMember').text('');

		var sObj = $(this).attr('data');
		var aObj = sObj.split("_");

		console.log(aObj);

		$.ajax({
			url: "/t/ajax/test/BentMember.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"tb": aObj[0],
				"tq": aObj[1],
				"ch": aObj[2],
				"txt": aObj[3],
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
						var member;
						if (res) {
							member = res;
						} else {
							member = '<span class="font-red">'+$.i18n.prop('cl_t_quest_QBNumMember_1')+'</span>';
						}

						$('.QBFloatBox .QBFloatMember').html(member);
						$('.QBFloatBox').show();

						$('.QBFloatBox').css({
							'top': (e.pageY + 12)+'px',
							'right': '1%',
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

	$('.QBTabMenu li').click(function() {
		var mode = $(this).attr('data');
		$('.QBTabMenu li').removeClass('QBTabActive');
		$('.QBTabContents').hide();

		$('#'+mode).show();
		$(this).addClass('QBTabActive');
	});

	$('.QBFloatBox .QBFloatClose').on('click', function() {
		$('.QBFloatBox').hide();
		$('.QBFloatBox .QBFloatMember').text('');
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

	$('#ChkShow .SelShow').on('click', function() {
		var body = $(this).parents('form').find('tbody');

		body.find('input[type=checkbox]').each(function () {
			if ($(this).prop('checked')) {
				$(this).parents('tr').css({'display':'table-row'});
			} else {
				$(this).parents('tr').css({'display':'none'});
			}
		});

	});
	$('#ChkShow .AllShow').on('click', function() {
		var body = $(this).parents('form').find('tbody');

		body.find('input[type=checkbox]').each(function () {
			$(this).parents('tr').css({'display':'table-row'});
		});

	});

	$('select[name=d_category]').on('change', function() {

		var cat = $(this).val();
		var sel = $('select[name=d_select]');
		var selv = sel.val();
		var optdef = sel.find('option[value=0]');
		var optnew = sel.find('option[value=new]');

		$.ajax({
			url: "/t/ajax/test/drill.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"dc": cat,
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
						sel.empty();
						sel.append(optdef);

						for (var i = 0; i < res.length; i++) {
							sel.append(
								$('<option>')
									.val(res[i].dbNO)
									.text(res[i].dbTitle)
							);
						}

						sel.append(optnew);

						sel.val(selv);
						if (selv != 'new') {
							$('#drill-create').hide();
						}
						return false;
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

	$('select[name=d_select]').on('change', function() {

		if ($(this).val() == 'new') {
			$('#drill-create').show();
		} else {
			$('#drill-create').hide();
		}

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
	$('input[name=qScore]').val('10');
	$('select[name=qType]').val(0);
	$('textarea[name=qText]').val('');

	$('input[name=qImage]').replaceWith('<input type="file" value="" name="qImage">');
	$('#qImage').hide();
	$('input[name=tqImage]').val('');
	$('#qImage').attr('src','');
	$('#qImageDel').val('');
	$('#qImageDel').hide();

	$('input[name=qExplainImage]').replaceWith('<input type="file" value="" name="qExplainImage">');
	$('#qExplainImage').hide();
	$('input[name=tqExplainImage]').val('');
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
		$(this).find('.TestQueryImageDelete').val('');
		$(this).find('.TestQueryImageDelete').hide();
		$(this).removeClass('choice-block').addClass('choice-none');
	});

	$('div.choice-none').each(function(i) {
		if (i < 5) {
			$(this).css('display','block');
			$(this).removeClass('choice-none').addClass('choice-block');
		}
	});

	$('input.TestRightText').each(function(i) {
		$(this).val('');
	});

	$('div.QueryTypeSelect').show();
	$('div.QueryTypeText').hide();
	$('#QueryEditPanel').show();
	return false;
};

function PanelDisp(aObj) {
	$.ajax({
		url: "/t/ajax/test/QueryLoad.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"tb": aObj[0],
			"qn": aObj[1],
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
					$('#eNo').text(res.tqSort);
					$('.eLabelAdd').hide();
					$('.eLabelEdit').show();
					$('input[name=qSort]').val(res.tqSort);
					$('input[name=qNo]').val(res.tqNO);
					$('input[name=qScore]').val(res.tqScore);

					$('select[name=qType]').val(res.tqStyle);
					var aType = $('p.qType span');
					aType.css({'display':'none'});
					aType.eq(res.tqStyle).css({'display':'inline'});

					$('textarea[name=qText]').val(res.tqText);
					$('input[name=qImage]').replaceWith('<input type="file" value="" name="qImage">');
					if (res.tqImage) {
						$('input[name=tqImage]').val(res.tqImage);
						$('#qImage').attr('src',o.path+res.tqImage+'?'+getRand(0,99999));
						$('#qImage').show();
						$('#qImageDel').val(res.tbID+'_'+res.tqSort+'_'+res.tqImage);
						$('#qImageDel').show();
					} else {
						$('#qImage').hide();
						$('input[name=tqImage]').val('');
						$('#qImage').attr('src','');
						$('#qImageDel').val('');
						$('#qImageDel').hide();
					}
					$('textarea[name=qExplain]').val(res.tqExplain);
					$('input[name=qExplainImage]').replaceWith('<input type="file" value="" name="qExplainImage">');
					if (res.tqExplainImage) {
						$('input[name=tqExplainImage]').val(res.tqExplainImage);
						$('#qExplainImage').attr('src',o.path+res.tqExplainImage+'?'+getRand(0,99999));
						$('#qExplainImage').show();
						$('#qExplainImageDel').val(res.tbID+'_'+res.tqSort+'_'+res.tqExplainImage);
						$('#qExplainImageDel').show();
					} else {
						$('#qExplainImage').hide();
						$('input[name=tqExplainImage]').val('');
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
						$(this).find('.TestQueryImageDelete').val('');
						$(this).find('.TestQueryImageDelete').hide();
						$(this).removeClass('choice-block').addClass('choice-none');
					});

					$('input.TestRightText').each(function(i) {
						$(this).val('');
					});

					if (res.tqStyle != 2) {
						var tqRight = res.tqRight1.split("|");
						var check = false;
						var color = 'default';
						for (var i = 1; i <= res.tqChoiceNum; i++) {
							if ($.inArray(String(i),tqRight) >= 0) {
								check = true;
								color = 'confirm';
							} else {
								check = false;
								color = 'default';
							}
							$('#choice'+i).find('textarea').val(res['tqChoice'+i]);
							$('#choice'+i).find('input[type=checkbox]').prop('checked', check);
							$('#choice'+i).find('input[type=checkbox]').parents('label').removeClass('default');
							$('#choice'+i).find('input[type=checkbox]').parents('label').addClass(color);
							if (res['tqChoiceImg'+i]) {
								$('input[name=tqChoiceImage'+i+']').val(res['tqChoiceImg'+i]);
								$('#qChoiceImage'+i).attr('src',o.path+res['tqChoiceImg'+i]+'?'+getRand(0,99999));
								$('#qChoiceImage'+i).show();
								$('#qChoiceImageDel'+i).val(res.tbID+'_'+res.tqSort+'_'+res['tqChoiceImg'+i]);
								$('#qChoiceImageDel'+i).show();
							}
							$('#choice'+i).css('display','block');
							$('#choice'+i).removeClass('choice-none').addClass('choice-block');
						}
						$('div.QueryTypeText').hide();
						$('div.QueryTypeSelect').show();
					} else {
						for (var i = 1; i <= 5; i++) {
							$('input[name=qRightText'+i+']').val(res['tqRight'+i]);
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

function DLegendSet(iH,iW) {
	if ($('#DLegend').get(0)) {
		var LW = parseInt(iW * 0.43);
		if (iH < iW) {
			LW = parseInt(iH * 0.43);
		}
		var iHH = $('.QBNumHeader').height();

		$('#DLegend').width(LW+'px');
		$('#DLegend').css({
			'top': ($('#QChart').height()/2)-($('#DLegend').height()/2)+iHH-1+'px',
			'left': ($('#QChart').width()/2)-($('#DLegend').width()/2)-5+'px',
		});
	}
	return;
}


function DoughnutChartUpdate() {
	var sObj = $('.TestChart').attr('obj');
	var aObj = sObj.split("_");
	var Leg = $('#DLegend');

	$.ajax({
		url: "/t/ajax/test/Bent.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"tb" : aObj[0],
			"com": aObj[1],
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
					var bent = res.bent;

					Leg.find('dd.LD1 span:first-child').text(bent["1"].num);
					Leg.find('dd.LD2 span:first-child').text(bent["2"].num);
					Leg.find('dd.LD1 span:last-child').text(bent["1"].per);
					Leg.find('dd.LD2 span:last-child').text(bent["2"].per);

					$('.QBNumHeader span:first-child').text(res.test.tbNum);
					$('.QBNumHeader span:last-child').text(res.test.tbNum);

					if (ChartData[0].value == 0 && ChartData[1].value == 0) {
						ChartData[0].value = bent["1"].num;
						ChartData[1].value = bent["2"].num;
						objChart = Chart.Doughnut(ChartData, {
							responsive : false,
							animationEasing : 'easeInOutCubic',
							percentageInnerCutout : 50,
						});
					} else {
						objChart.segments[0].value = bent["1"].num;
						objChart.segments[1].value = bent["2"].num;
						objChart.update();
					}

					if (res.comment) {
						QBCommentListAdd(res.comment,aObj[0],false);
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

function BarChartUpdate() {
	var sObj = $('.TestChart').attr('obj');
	var aObj = sObj.split("_");

	$.ajax({
		url: "/t/ajax/test/Bent.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"tb" : aObj[0],
			"com": aObj[1],
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
					var bent = res.bent;

					$('.QBNumHeader span:first-child').text(res.test.tbNum);
					$('.QBNumHeader span:last-child').text(res.test.tbNum);

					$.each(objChart.datasets[0].bars, function(key, value) {
						objChart.datasets[0].bars[key].value = bent[(key+1).toString()].num;
					});

					objChart.update();

					if (res.comment) {
						QBCommentListAdd(res.comment,aObj[0],false);
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

function ComUpdate() {
	var sObj = $('.TestCommentOnly').attr('obj');

	$.ajax({
		url: "/t/ajax/test/BentComment.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"tb" : sObj,
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
					$('.QBNumHeader span:first-child').text(res.test.tbNum);
					$('.QBNumHeader span:last-child').text(res.test.tbNum);

					if (res.comment) {
						QBCommentListAdd(res.comment,sObj,true);
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

function QBCommentListAdd(comment,sObj,comOnly) {
	var qNO = (comOnly)? 1:2;
	var m = $('ul.QBCommentList').attr('mode');
	$.each(comment, function(key, com) {
		if (!$('.'+key).get(0) && com.text != '') {
			var cls = 'choice'+com.cNO;
			var btnID = sObj+'_'+qNO+'_'+key;
			var back = '';
			var img = 'icon_pick_b.png';
			switch (com.cPick) {
				case 1:
					back = 'back-yellow';
					img = 'icon_pick_a.png';
				break;
				case -1:
					back = 'back-silver';
					img = 'icon_pick_c.png';
				break;
				default:
				break;
			}
			var LabelBtn = $('<span class="'+cls+' largeShow" data="'+com.cNO+'">'+com.cName+'</span>');
			if (comOnly) {
				var LabelBtn = $('<span class="choiceDefault largeShow" data="Default" title="'+$.i18n.prop('cl_t_quest_QBCommentListAdd_1')+'"><i class="fa fa-search" style="vertical-align: middle;"></i></span>');
			}
			var nL = $('<li></li>')
				.css({'display':'none'})
				.addClass(key)
				.addClass(back)
				.append(LabelBtn)
				.append('<div class="dropdown inline-block"><button type="button" class="ans-dropdown-toggle" id="'+btnID+'"><div><img src="/assets/img/'+img+'" alt="" style="vertical-align: top;" pick="'+com.cPick+'"></div></button></div>')
				.append(' <span>'+com.text+'</span>');
			nL.prependTo('ul.QBCommentList');
			if (m != cls && m != 'all') {
				nL.addClass('hideLi');
			}
			nL.slideDown('normal');
			nL.removeAttr('style');
		}
	});
	return;
}

function pickListShow(eBtn) {
	var id = eBtn.attr('id');
	var mode = id.split('_');
	var list = $('.dropdown-list');
	var obj = list.attr('obj');

	if (id == obj && list.css('display') == 'block') {
		list.slideUp('fast');
		return;
	}

	list.hide();
	list.attr('obj',id);

	var offset = eBtn.offset();
	var height = eBtn.outerHeight();

	if (!$('nav#main-menu').get(0)) {
		FixedSize = 0;
	}

	list.css({
		top: (parseInt(offset.top)+height-FixedSize)+'px',
		left: parseInt(offset.left)+'px',
	});
	list.slideDown('fast');
}

function ArchiveDownload() {
	var btn = $('#archive-download-btn');
	var aObj = btn.attr('obj').split('_');

	$.ajax({
		url: "/t/ajax/test/ArchiveDownloadBtn.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"ct" : aObj[0],
			"type" : aObj[1],
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
						btn.prepend('<i class="fa fa-download mr0"></i> ');
						btn.off('click');
						clearInterval(timerID);
					} else if (res.status == 2) {
						btn.text(res.text);
						btn.prepend('<i class="fa fa-exclamation-triangle mr0"></i> ');
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

