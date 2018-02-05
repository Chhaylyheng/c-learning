$(function() {

	$('.QuestAnsChoice input[type=checkbox]').change(function() {
		if ($(this).prop('checked')) {
			$(this).parent('label').removeClass('default');
			$(this).parent('label').addClass('check');
			$(this).parent('label').find('i').removeClass('fa-square-o');
			$(this).parent('label').find('i').addClass('fa-check-square-o');
		} else {
			$(this).parent('label').removeClass('check');
			$(this).parent('label').addClass('derault');
			$(this).parent('label').find('i').removeClass('fa-check-square-o');
			$(this).parent('label').find('i').addClass('fa-square-o');
		}
	});
	$('.QuestAnsChoice input[type=radio]').change(function() {
		$(this).parents('.QuestAnsChoice').find('label').removeClass('check');
		$(this).parents('.QuestAnsChoice').find('label').addClass('default');
		$(this).parents('.QuestAnsChoice').find('i').removeClass('fa-dot-circle-o');
		$(this).parents('.QuestAnsChoice').find('i').addClass('fa-circle-o');

		if ($(this).prop('checked')) {
			$(this).parent('label').removeClass('default');
			$(this).parent('label').addClass('check');
			$(this).parent('label').find('i').removeClass('fa-circle-o');
			$(this).parent('label').find('i').addClass('fa-dot-circle-o');
		}
	});

	var BentMode = ['ALL'];
	for (var i in BentMode) {
		var mode = BentMode[i];
		if ($('.QuestChart #QChart'+mode).get(0)) {
			initQChart(mode);
		}
		if ($('.QuestCommentOnly #QChartNum'+mode).get(0)) {
			initQCom(mode);
		}
	}
/*
	$(window).on('resize',function() {
		for (var i in BentMode) {
			var mode = BentMode[i];
			if ($('.QuestChart #QChart'+mode).get(0)) {
				var canvas = $('div.ChartBox').find('canvas');
				var iS;
				var iH = ($(window).height() - canvas.offset().top - 20);
				var iW = canvas.width();
				iS = iH;
				if (iH > iW) {
					iH = iW;
					iS = iW;
				}
				canvas.width(iS);
				canvas.height(iS);
				objChart[mode].update();

				DLegendSet(mode,iH,iW);
			}
		}
	});
*/
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
});

function DLegendSet(mode,iH,iW) {
	if ($('#DLegend'+mode).get(0)) {
		var LW = parseInt(iW * 0.43);
		if (iH < iW) {
			LW = parseInt(iH * 0.43);
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
