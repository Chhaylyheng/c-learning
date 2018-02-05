$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('table').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('table').find('input.Chk').prop('checked',false);
		}
	});

	$('.quest-dropdown-toggle').on('click',function() {
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
			list.find('.QuestToCSV').parent('li').hide();
		} else {
			list.find('.QuestToCSV').parent('li').show();
		}

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.open-dropdown-toggle').on('click',function() {
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
			top: (parseInt(offset.top)+height-1)+'px',
			left: parseInt(offset.left - list.width() + $(this).width() + 5)+'px',
		});
		list.slideDown('fast');
	});

	$(document).on('click','.ans-dropdown-toggle', function() {
		pickListShow($(this));
	});

	$('.QuestPublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var Row  = Btn.parents('tr');
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/quest/Public.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"qb": aObj[1],
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

						if ($(Row).find('.QuestTeacherAns').get(0) && $('.QuestTeacherTemplate').get(0)) {
							$(Row).find('.QuestTeacherAns').empty();
							var AnsBtn = $('.QuestTeacherTemplate').find('.QTT-'+res.mode).clone(true);

							if (!res.put) {
								AnsBtn.find('span.attention').remove();
							}
							if (res.url) {
								AnsBtn.attr('href',res.url);
							}
							$(Row).find('.QuestTeacherAns').append(AnsBtn);
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

	$('.QuestBentPublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/quest/BentPublic.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"qb": aObj[1],
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

	$('.QuestEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/quest/edit/'+aObj[1];
		return false;
	});

	$('.QuestQueryEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/quest/querylist/'+aObj[1];
		return false;
	});

	$('.QuestCopy').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/quest/copy/'+aObj[1];
		return false;
	});

	$('#QuestCopyExec').on('click', function() {
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

	$('.QuestResultToCSV').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/output/questresult/'+aObj[1]+".csv";

		$('.dropdown-list').slideUp('fast');
		return false;
	});

	$('.QuestToCSV').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/output/quest/'+aObj[1]+".csv";

		$('.dropdown-list').slideUp('fast');
		return false;
	});

	$('.QuestPutReset').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");
		var oLead = $('#'+aObj[1]).find('span.PutNum');
		var oGLead = $('#'+aObj[1]).find('span.GPutNum');
		var oTLead = $('#'+aObj[1]).find('span.TPutNum');
		var oTAns = $('#'+aObj[1]).find('div.QuestTeacherAns span.attention');

		confirm($.i18n.prop('cl_t_quest_QuestPutReset_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			$.ajax({
				url: "/t/ajax/quest/PutReset.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"ct": aObj[0],
					"qb": aObj[1],
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
							$(oGLead).text('0');
							$(oTLead).text('0');
							$(oTAns).remove();
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

	$('.QuestDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_quest_QuestDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/quest/delete/'+aObj[1];
		});
		return false;
	});

	$('.QuestSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.QuestSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/quest/Sort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"qb": aObj[1],
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
								var oB = $(TR).prev('tr').find('.QuestSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.QuestSort');
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

	$('select[name=qType]').on('change',function() {
		if ($(this).val() == 2) {
			$('div.QueryTypeSelect').hide();
		} else {
			$('div.QueryTypeSelect').show();
		}
	});

	$('.QuestQueryImageDelete').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");

		confirm($.i18n.prop('cl_t_quest_QuestQueryImageDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			$.ajax({
				url: "/t/ajax/quest/QueryImageDelete.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"qb": aObj[0],
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
								$('input[name=qqImage]').val('');
								$('#qImage').attr('src','');
								$('#qImageDel').val('');
								$('#qImageDel').hide();
							} else {
								$('#qChoiceImage'+res.del).hide();
								$('#choice'+res.del).find('input[name=qqChoiceImage'+res.del+']').val('');
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

	$('.QuestQuerySort').click(function() {
		var LI = $(this).parents('div.QPanel');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(LI).find('.QuestQuerySort');

		$.ajax({
			url: "/t/ajax/quest/QuerySort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"qb": aObj[0],
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
								var oB = $(oPrev).find('.QuestQuerySort');
								SortBtnDisabled(oA,oB);
								$(LI).insertBefore($(oPrev));
								$(oPrev).insertAfter($(oPrev).next());
								$(LI).find('.QQS').text(parseInt(res.qs)-1);
								$(oPrev).find('.QQS').text(res.qs);
								$(LI).find('.PreviewLink').attr('href','/t/quest/preview/'+aObj[0]+'#q'+(parseInt(res.qs)-1));
								$(oPrev).find('.PreviewLink').attr('href','/t/quest/preview/'+aObj[0]+'#q'+parseInt(res.qs));
							}
						} else {
							var oNext = $(LI).nextAll('div.QPanel')[0];
							if (oNext) {
								var oB = $(oNext).find('.QuestQuerySort');
								SortBtnDisabled(oA,oB);
								$(LI).insertAfter($(oNext));
								$(oNext).insertBefore($(oNext).prev());
								$(LI).find('.QQS').text(parseInt(res.qs)+1);
								$(oNext).find('.QQS').text(res.qs);
								$(LI).find('.PreviewLink').attr('href','/t/quest/preview/'+aObj[0]+'#q'+(parseInt(res.qs)+1));
								$(oNext).find('.PreviewLink').attr('href','/t/quest/preview/'+aObj[0]+'#q'+parseInt(res.qs));
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

	$('.QuestQueryDelete').click(function() {
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var url = '/t/quest/querydelete/'+aObj[0]+'/'+aObj[1];

		confirm($.i18n.prop('cl_t_quest_QuestQueryDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			window.location.href = url;
		});
		return;
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

	$('.QuestTextPickUp').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var btn  = $('#'+id);
		var m    = $(this).attr('obj');

		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/quest/TextPickUp.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"qb": aObj[0],
				"qn": aObj[1],
				"st": aObj[2],
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
						if (res != 0) {
							if (aObj.length == 5 || aObj.length == 3) {
								$(btn).find('img').attr('src',res.img);
								$(btn).find('img').attr('pick',m);
							}
							var mode = ['ALL','STUDENT','GUEST'];
							for (var key in mode) {
								var btn2 = $('#'+aObj[0]+'_'+aObj[1]+'_'+aObj[2]+'_'+mode[key]);

								btn2.find('img').attr('src',res.img);
								btn2.find('img').attr('pick',m);

								if (btn2.parents('tr,li')) {
									var tr = btn2.parents('tr,li');
									switch (m) {
										case "1":
											tr.removeClass('back-silver');
											tr.addClass('back-yellow');
										break;
										case "-1":
											tr.addClass('back-silver');
											tr.removeClass('back-yellow');
										break;
										default:
											tr.removeClass('back-silver');
											tr.removeClass('back-yellow');
										break;
									}
								}
							}

							if (btn.parents('tr,li')) {
								var tr = btn.parents('tr,li');
								switch (m) {
									case "1":
										tr.removeClass('back-silver');
										tr.addClass('back-yellow');
									break;
									case "-1":
										tr.addClass('back-silver');
										tr.removeClass('back-yellow');
									break;
									default:
										tr.removeClass('back-silver');
										tr.removeClass('back-yellow');
									break;
								}
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
		$(list).slideUp('fast');
		return false;
	});

	$('.QuestTextPickUp2').click(function() {
		var DIV = $(this).parents('div.lead');
		var sObj = $(this).attr('value');
		var aObj = sObj.split("_");

		$.ajax({
			url: "/t/ajax/quest/TextPickUp.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"qb": aObj[0],
				"qn": aObj[1],
				"st": aObj[2],
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
						if (res != 0) {
							$(DIV).find(".dropdown-toggle>img").attr("src",res.img);
							addAlert($.i18n.prop('cl_t_quest_QuestTextPickUp2_1'),'success');
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

	$('.SwitchOpen').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/quest/SwitchOpen.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"qb": aObj[1],
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
						window.location.reload();
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

	$('.QBTabMenu li').click(function() {
		var mode = $(this).attr('data');
		$('.QBTabMenu li').removeClass('QBTabActive');
		$('.QBTabContents').hide();

		$('#'+mode).show();
		$(this).addClass('QBTabActive');
	});

	if ($('.ChartBox').get(0)) {
		var iOffsetTop = $('.ChartBox').eq(0).offset().top;
	}
	$('.ChartBoxResize').on('click', function() {
		var cbox = $(this).parents('.ChartBox');
		var canvas = cbox.find('canvas');
		var m = $(this).attr('data');

		for (var i in BentMode) {
			var mode = BentMode[i];
			if ($('.QuestChart #QChart'+mode).get(0)) {
				var iS;
				var iO;
				if (m == 'small') {
					$(this).find('i').removeClass('fa-caret-left');
					$(this).find('i').addClass('fa-caret-right');
					$(this).attr('data','large');

					iS = parseInt(($(window).height() - iOffsetTop - 20) / 3);
					if (iS < 170) {
						iS = 170;
					}
				} else {
					$(this).find('i').removeClass('fa-caret-right');
					$(this).find('i').addClass('fa-caret-left');
					$(this).attr('data','small');

					var iH = parseInt(($(window).height() - iOffsetTop - 20));
					var iW = parseInt((cbox.parents('div.QuestChart').width()) * 0.48);

					console.log(iS+'/'+iH+'/'+iW);

					iS = iH;
					if (iH > iW) {
						iH = iW;
						iS = iW;
					}
				}
				canvas.width(iS);
				canvas.height(iS);
				cbox.width(iS);
				objChart[mode].update();
				DLegendSet(mode,iS,iS);
			}
		}
	});

	$(window).on('resize',function() {
		for (var i in BentMode) {
			var mode = BentMode[i];
			if ($('.QuestChart #QChart'+mode).get(0)) {
				var canvas = $('div.ChartBox').find('canvas');
				var m = $('div.ChartBox').find('.ChartBoxResize').attr('data');

				if (m == 'large') {
					continue;
				}

				var iS;
				var iH = parseInt(($(window).height() - canvas.offset().top - 20));
				var iW = parseInt(($('div.QuestChart').width()) * 0.48);
				iS = iH;
				if (iH > iW) {
					iH = iW;
					iS = iW;
				}
				canvas.width(iS);
				canvas.height(iS);
				$('div.ChartBox').width(iS);
				objChart[mode].update();

				DLegendSet(mode,iH,iW);
			}
		}
	});
	var BentMode = ['ALL','STUDENT','GUEST', 'TEACH'];
	for (var i in BentMode) {
		var mode = BentMode[i];
		if ($('.QuestChart #QChart'+mode).get(0)) {
			var canvas = $('div.ChartBox').find('canvas');
			var iS;
//			var iH = parseInt(($(window).height() - canvas.offset().top - 20));
			var iH = parseInt(($(window).height() - canvas.offset().top - 90));
			var iW = $('div.ChartBox').width();
			iS = iH;
			if (iH > iW) {
				iH = iW;
				iS = iW;
			}
			canvas.width(iS);
			canvas.height(iS);
			$('div.ChartBox').width(iS + 20);

			initQChart(mode);
		}
		if ($('.QuestCommentOnly #QChartNum'+mode).get(0)) {
			initQCom(mode);
		}
	}

	$('.QBChartOn').on('click', function() {
		var grp = $(this).parents('table').find('.QBGraphCell');
		var cht = $(this).parents('table').find('.QBChartCell');
		var cv  = $(this).parents('table').find('canvas.QBChartBox');
		var cvid = cv.attr('id').split('_');
		var fnc = 'initChart'+cvid[1];

		var LC = $(this).parents('table').find('td.QAns');
		LC.removeClass('LCN');
		LC.addClass('LC');

		grp.hide();
		cht.show();
		eval(fnc + '()');

		return false;
	});

	$('.QBChartOff').on('click', function() {
		var grp = $(this).parents('table').find('.QBGraphCell');
		var cht = $(this).parents('table').find('.QBChartCell');

		var LC = $(this).parents('table').find('td.QAns');
		LC.removeClass('LC');
		LC.addClass('LCN');

		grp.show();
		cht.hide();

		return false;
	});

	$(document).on({
		'click':function() {
			shadowMask('on');
			var data = $(this).attr('data');
			var pickBtn = $(this).nextAll('div.dropdown').clone();
			var text = $(this).nextAll('span').html();
			var posted = $(this).nextAll('span.posted').clone();
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
				.append(text+"\n")
				.append(posted)
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
			var pickBtn = $(this).parents('tr').find('div.dropdown').clone();
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
	},'.QBBentAnsList span.largeShow');

	$('.QBCommentFilter span').on('click',function() {
		if ($(this).hasClass('posted-toggle')) {
			if ($(this).hasClass('posted-toggle-on')) {
				$(this).removeClass('posted-toggle-on');
				$(this).parents('div.CommentBox,div.QuestCommentOnly').find('ul li span.posted').hide();
			} else {
				$(this).addClass('posted-toggle-on');
				$(this).parents('div.CommentBox,div.QuestCommentOnly').find('ul li span.posted').show();
			}
			return false;
		} else {
			var cls = $(this).attr('class');
			if (cls == 'choiceAll') {
				$(this).parents('div').next('ul').attr('mode','all');
				$(this).parents('div').next('ul').find('li').removeClass('hideLi');
			} else {
				$(this).parents('div').next('ul').attr('mode',cls);
				$(this).parents('div').next('ul').find('li').addClass('hideLi');
				$(this).parents('div').next('ul').find('.'+cls).parent('li').removeClass('hideLi');
			}
		}
	});

	$('.TeachCommentUpdate').on('click', function() {
		var textarea = $(this).parents('#TeachComment').find('textarea');
		var text = textarea.val();
		var sObj = $(this).attr('data');
		var aObj = sObj.split("_");

		$.ajax({
			url: "/t/ajax/quest/TeachComment.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"qb": aObj[0],
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
	$('.QBTEXT > button.QBTextEditShow').on('click', function() {
		var mine = $(this).parents('.QBTEXT');
		if (mine.find('.QBTextEdit').css('display') == 'none') {
			mine.find('.QBTextEdit').show();
			mine.find('.QBText').hide();
		} else {
			mine.find('.QBTextEdit').hide();
			mine.find('.QBText').show();
		}
		return false;
	});
	$('.QBTEXT .QBTextEdit button').on('click', function() {
		var mine = $(this).parents('.QBTEXT');
		var qb = mine.attr('obj');
		var text = mine.find('.QBTextEdit input[type=text]').val();

		$.ajax({
			url: "/t/ajax/quest/QueryTextUpdate.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"qb": qb,
				"text": text
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
						$('.QBTEXT .QBTextEdit input[type=text]').val(res.text);
						$('.QBTEXT .QBText').text(res.text);
						$('.QBTEXT .QBTitle').text(res.title);

						mine.find('.QBText').show();
						mine.find('.QBTextEdit').hide();
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

	$('.QBNumMember').on('click', function(e) {
		$('.QBFloatBox').hide();
		$('.QBFloatBox .QBFloatMember').text('');

		var sObj = $(this).attr('data');
		var aObj = sObj.split("_");

		$.ajax({
			url: "/t/ajax/quest/BentMember.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"qb": aObj[0],
				"qq": aObj[1],
				"ch": aObj[2],
				"mode": aObj[3],
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

	$('.QBFloatBox .QBFloatClose').on('click', function() {
		$('.QBFloatBox').hide();
		$('.QBFloatBox .QBFloatMember').text('');
	});

	$('.QBPersonalOn').on('click', function() {
		var table = $(this).parents('table');
		table.find('.QBAnsTextHeader').removeClass('width-80');
		table.find('.QBAnsTextHeader').addClass('width-70');
		table.find('.QBAnsPersonalHeader').show();
		$(this).hide();

		table.find('.QBAnsPersonal').show();
		return false;
	});
	$('.QBPersonalOff').on('click', function() {
		var table = $(this).parents('table');
		table.find('.QBAnsTextHeader').removeClass('width-70');
		table.find('.QBAnsTextHeader').addClass('width-80');
		table.find('.QBAnsPersonalHeader').hide();
		table.find('.QBAnsStarHeader .QBPersonalOn').show();

		table.find('.QBAnsPersonal').hide();
		return false;
	});

	if ($('.QBCommentList').get(0)) {
		var QBCFSKey = 'cl_t_questbent_comment_size';
		var QBCFSize = {'small':'100', 'middle':'140', 'large':'240'};
		var defaultSize = 'middle';
		var currentSize = defaultSize;
		var store;
		if(('sessionStorage' in window) && (window.sessionStorage !== null)) {
			store = sessionStorage.getItem(QBCFSKey);
			if (store) {
				currentSize = store;
			}
		}
		if (!currentSize || currentSize == null) {
			currentSize = defaultSize;
		}
		$('.QBCommentList').removeClass('font-size-' + QBCFSize['small'] + ' font-size-' + QBCFSize['middle'] + ' font-size-' + QBCFSize['large']);
		$('.QBCommentList').addClass('font-size-' + QBCFSize[currentSize]);
		$('.QBCommentFontSize[data=' + currentSize + ']').addClass('active');
	}

	$('.QBCommentFontSize').on('click', function() {
		var m = $(this).attr('data');

		if ($('.QBCommentList').get(0)) {
			$('.QBCommentFontSize').removeClass('active');
			$('.QBCommentList').removeClass('font-size-' + QBCFSize['small'] + ' font-size-' + QBCFSize['middle'] + ' font-size-' + QBCFSize['large']);
			$('.QBCommentList').addClass('font-size-' + QBCFSize[m]);
			$('.QBCommentFontSize[data=' + m + ']').addClass('active');

			setSessionStorage(QBCFSKey, m);
		}
		return false;
	});

	$('input[name=q_auto_public]').on('change',function() {
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


	if ($('.ChartBox').get(0)) {
		var boxDef = $('.ChartBox').eq(0).offset().top;
		$(window).on('scroll', function(e) {
			var scroll = $(window).scrollTop();

			$.each($('.ChartBox'), function(i) {
				var boxTop = $('.ChartBox').eq(i).offset().top;
				var comTop = $('.CommentBox').eq(i).offset().top;
				var sc = scroll - boxTop;
				var posi = scroll - boxDef;

				if (comTop > boxTop) {
					$('.ChartBox').eq(i).css({'top': '0px'});
				} else if (posi < 0) {
					$('.ChartBox').eq(i).css({'top': '0px'});
				} else {
					$('.ChartBox').eq(i).css({top: parseInt($('.ChartBox').eq(i).css('top'))+sc+'px'});
				}
			});

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
	$('#eNo').text(eNO);
	$('.eLabelAdd').show();
	$('.eLabelEdit').hide();
	$('input[name=qSort]').val(eNO);
	$('input[name=qNo]').val('');
	$('select[name=qType]').val(0);
	$('input[name=qRequired]').prop('checked',true);
	$('input[name=qRequired]').parent('label').removeClass('default').addClass('confirm');
	$('textarea[name=qText]').val('');
	$('input[name=qImage]').replaceWith('<input type="file" value="" name="qImage">');
	$('#qImage').hide();
	$('input[name=qqImage]').val('');
	$('#qImage').attr('src','');
	$('#qImageDel').val('');
	$('#qImageDel').hide();

	$('div.choice-block').each(function(i) {
		$(this).hide();
		$(this).find('textarea,input').val('');
		var inputName = $(this).find('input[type=file]').attr('name');
		$(this).find('input[type=file]').replaceWith('<input type="file" value="" name="'+inputName+'">');
		$(this).find('img').attr('src','');
		$(this).find('img').hide();
		$(this).find('.QuestQueryImageDelete').val('');
		$(this).find('.QuestQueryImageDelete').hide();
		$(this).removeClass('choice-block').addClass('choice-none');
	});

	$('div.choice-none').each(function(i) {
		if (i < 5) {
			$(this).css('display','block');
			$(this).removeClass('choice-none').addClass('choice-block');
		}
	});
	$('div.QueryTypeSelect').show();
	$('#QueryEditPanel').show();
	return false;
};

function PanelDisp(aObj) {
	$.ajax({
		url: "/t/ajax/quest/QueryLoad.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"qb": aObj[0],
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
					$('#eNo').text(res.qqSort);
					$('.eLabelAdd').hide();
					$('.eLabelEdit').show();
					$('input[name=qSort]').val(res.qqSort);
					$('input[name=qNo]').val(res.qqNO);

					$('select[name=qType]').val(res.qqStyle);
					var aType = $('p.qType span');
					aType.css({'display':'none'});
					aType.eq(res.qqStyle).css({'display':'inline'});

					if (res.qqRequired == 1) {
						$('input[name=qRequired]').prop('checked',true);
						$('input[name=qRequired]').parent('label').removeClass('default').addClass('confirm');
					} else {
						$('input[name=qRequired]').prop('checked',false);
						$('input[name=qRequired]').parent('label').removeClass('confirm').addClass('default');
					}

					$('textarea[name=qText]').val(res.qqText);
					$('input[name=qImage]').replaceWith('<input type="file" value="" name="qImage">');
					if (res.qqImage) {
						$('input[name=qqImage]').val(res.qqImage);
						$('#qImage').attr('src',o.path+res.qqImage+'?'+getRand(0,99999));
						$('#qImage').show();
						$('#qImageDel').val(res.qbID+'_'+res.qqSort+'_'+res.qqImage);
						$('#qImageDel').show();
					} else {
						$('#qImage').hide();
						$('input[name=qqImage]').val('');
						$('#qImage').attr('src','');
						$('#qImageDel').val('');
						$('#qImageDel').hide();
					}
					$('div.choice-block').each(function(i) {
						$(this).hide();
						$(this).find('textarea,input').val('');
						var inputName = $(this).find('input[type=file]').attr('name');
						$(this).find('input[type=file]').replaceWith('<input type="file" value="" name="'+inputName+'">');
						$(this).find('img').attr('src','');
						$(this).find('img').hide();
						$(this).find('.QuestQueryImageDelete').val('');
						$(this).find('.QuestQueryImageDelete').hide();
						$(this).removeClass('choice-block').addClass('choice-none');
					});

					if (res.qqStyle != 2) {
						for (var i = 1; i <= res.qqChoiceNum; i++) {
							$('#choice'+i).find('textarea').val(res['qqChoice'+i]);
							if (res['qqChoiceImg'+i]) {
								$('input[name=qqChoiceImage'+i+']').val(res['qqChoiceImg'+i]);
								$('#qChoiceImage'+i).attr('src',o.path+res['qqChoiceImg'+i]+'?'+getRand(0,99999));
								$('#qChoiceImage'+i).show();
								$('#qChoiceImageDel'+i).val(res.qbID+'_'+res.qqSort+'_'+res['qqChoiceImg'+i]);
								$('#qChoiceImageDel'+i).show();
							}
							$('#choice'+i).css('display','block');
							$('#choice'+i).removeClass('choice-none').addClass('choice-block');
						}
						$('div.QueryTypeSelect').show();
					} else {
						$('div.QueryTypeSelect').hide();
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

function DLegendSet(mode,iH,iW) {
	if ($('#DLegend'+mode).get(0)) {
		var LW = parseInt(iW * 0.4);
		if (iH < iW) {
			LW = parseInt(iH * 0.4);
		}
		if (LW < 160) {
			LW = 160;
		}
		var iHH = $('#QChartNum'+mode).height();

		$('#DLegend'+mode).width(LW+'px');
		$('#DLegend'+mode).css({
			'top': ($('#QChart'+mode).height()/2)-($('#DLegend'+mode).height()/2)+iHH-1+'px',
			'left': ($('#QChart'+mode).width()/2)-($('#DLegend'+mode).width()/2)-5+'px',
		});
	}
	return;
}


function DoughnutChartUpdate(mode) {
	var sObj = $('.QuestChart').attr('obj');
	var aObj = sObj.split("_");
	var Leg = $('#DLegend'+mode);

	$.ajax({
		url: "/t/ajax/quest/Bent.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"qb" : aObj[0],
			"com": aObj[1],
			"mode": mode,
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
					var bent = res.bent[mode];

					Leg.find('li.LD1 span:first-child').text(bent["1"].num);
					Leg.find('li.LD2 span:first-child').text(bent["2"].num);
					Leg.find('li.LD1 span:last-child').text(bent["1"].per);
					Leg.find('li.LD2 span:last-child').text(bent["2"].per);

					$('#QChartNum'+mode+' span.sp-num').text(res.quest.qpNum);
					$('#QChartNum'+mode+' span.s-num').text(res.quest.scNum);
					$('#QChartNum'+mode+' span.g-num').text(res.quest.qpGNum);
					$('#QChartNum'+mode+' span.t-num').text(res.quest.qpTNum);

					if (ChartData[mode][0].value == 0 && ChartData[mode][1].value == 0) {
						ChartData[mode][0].value = bent["1"].num;
						ChartData[mode][1].value = bent["2"].num;
						objChart[mode] = Chart[mode].Doughnut(ChartData[mode], {
							responsive : false,
							animationEasing : 'easeInOutCubic',
							percentageInnerCutout : 50,
						});
					} else {
						objChart[mode].segments[0].value = bent["1"].num;
						objChart[mode].segments[1].value = bent["2"].num;
						objChart[mode].update();
					}

					if (res.comment) {
						if (res.comment[mode]) {
							QBCommentListAdd(mode,res.comment[mode],aObj[0],false);
						}
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


function DoughnutChartUpdateDemo(mode) {
	var sObj = $('.QuestChart').attr('obj');
	var aObj = sObj.split("_");
	var Leg = $('#DLegend'+mode);

	var bent = gBent[mode];
	var comment = gComment[mode];
	var quest = gQuest;

	Leg.find('li.LD1 span:first-child').text(bent["1"].num);
	Leg.find('li.LD2 span:first-child').text(bent["2"].num);
	Leg.find('li.LD1 span:last-child').text(bent["1"].per);
	Leg.find('li.LD2 span:last-child').text(bent["2"].per);

	$('#QChartNum'+mode+' span.sp-num').text(quest.qpNum);
	$('#QChartNum'+mode+' span.s-num').text(quest.scNum);
	$('#QChartNum'+mode+' span.g-num').text(quest.qpGNum);
	$('#QChartNum'+mode+' span.t-num').text(quest.qpTNum);

	if (ChartData[mode][0].value == 0 && ChartData[mode][1].value == 0) {
		ChartData[mode][0].value = bent["1"].num;
		ChartData[mode][1].value = bent["2"].num;
		objChart[mode] = Chart[mode].Doughnut(ChartData[mode], {
			responsive : false,
			animationEasing : 'easeInOutCubic',
			percentageInnerCutout : 50,
		});
	} else {
		objChart[mode].segments[0].value = bent["1"].num;
		objChart[mode].segments[1].value = bent["2"].num;
		objChart[mode].update();
	}

	if (comment) {
		QBCommentListAdd(mode,comment,aObj[0],false);
	}

	return;
}



function BarChartUpdate(mode) {
	var sObj = $('.QuestChart').attr('obj');
	var aObj = sObj.split("_");

	$.ajax({
		url: "/t/ajax/quest/Bent.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"qb" : aObj[0],
			"com": aObj[1],
			"mode": mode,
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
					var bent = res.bent[mode];

					$('#QChartNum'+mode+' span.sp-num').text(res.quest.qpNum);
					$('#QChartNum'+mode+' span.s-num').text(res.quest.scNum);
					$('#QChartNum'+mode+' span.g-num').text(res.quest.qpGNum);
					$('#QChartNum'+mode+' span.t-num').text(res.quest.qpTNum);

					$.each(objChart[mode].datasets[0].bars, function(key, value) {
						objChart[mode].datasets[0].bars[key].value = bent[(key+1).toString()].num;
					});

					objChart[mode].update();

					if (res.comment) {
						if (res.comment[mode]) {
							QBCommentListAdd(mode,res.comment[mode],aObj[0],false);
						}
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

function ComUpdate(mode) {
	var sObj = $('.QuestCommentOnly').attr('obj');

	$.ajax({
		url: "/t/ajax/quest/BentComment.json",
		type: "POST",
		cache: false,
		dataType: "json",
		data: {
			"qb" : sObj,
			"mode": mode,
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
					$('#QChartNum'+mode+' span.sp-num').text(res.quest.qpNum);
					$('#QChartNum'+mode+' span.s-num').text(res.quest.scNum);
					$('#QChartNum'+mode+' span.g-num').text(res.quest.qpGNum);
					$('#QChartNum'+mode+' span.t-num').text(res.quest.qpTNum);
					if (res.comment) {
						if (res.comment[mode]) {
							QBCommentListAdd(mode,res.comment[mode],sObj,true);
						}
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

function QBCommentListAdd(mode,comment,sObj,comOnly) {
	var qNO = (comOnly)? 1:2;
	var m = $('ul#QChartCom'+mode).attr('mode');
	$.each(comment, function(key, com) {
		if (!$('ul#QChartCom'+mode+' .'+key).get(0) && com.text != '') {
			var cls = 'choice'+com.cNO;
			var btnID = sObj+'_'+qNO+'_'+key+'_'+mode;
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
				.append(LabelBtn);
			if (key.charAt(0) == 's') {
				nL.append('<div class="dropdown inline-block"><button type="button" class="ans-dropdown-toggle" id="'+btnID+'"><div><img src="/assets/img/'+img+'" alt="" style="vertical-align: top;" pick="'+com.cPick+'"></div></button></div>');
			}
			nL.append(' <span>'+com.text+'</span>');

			var Disp = ' style="display: none;"';
			if ($('span.posted-toggle').hasClass('posted-toggle-on')) {
				Disp = ' style=""';
			}
			nL.append(' <span class="posted font-size-60"'+Disp+'>（'+com.cPosted+'）</span>');

			nL.prependTo('ul#QChartCom'+mode);
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
	var list = $('.dropdown-list-pick');
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
		url: "/t/ajax/quest/ArchiveDownloadBtn.json",
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

$(window).on('load', function() {
		LangChk.done(function() {
		if ($('.QueryDefaultPanel').get(0)) {
			PanelDisp($('.QueryDefaultPanel').attr('obj').split("_"));
		}
		if ($('.QueryDefaultNewPanel').get(0)) {
			var qNO = $('.QueryDefaultNewPanel').attr('value');
			PanelDispNew(qNO);
		}
	});
});

