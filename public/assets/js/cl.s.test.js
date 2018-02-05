var startTime = 0;
var limitTime = 0;
var serverTime = 0;

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

	if ($('.LimitTime').get(0)) {
		var sObj = $('.LimitTime').attr('obj');
		$.ajax({
			url: "/s/ajax/test/LimitTime.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"tb": sObj,
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
						startTime = res.start;
						limitTime = res.limit;
						serverTime = res.server;
						countDown();
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error ['+err+']','alert');
				return false;
			}
		});
		return false;
	}

});

function countDown() {
	var elem = $('.LimitTime span');
	var date = new Date();
	var now = date.getTime();
	if (serverTime > 0) {
		now = serverTime;
		serverTime = 0;
	}
	var diff = (startTime + limitTime) - now;
	var times = 24 * 60 * 60 * 1000;

	if (startTime > (now + 60000) || diff <= 0) {
		elem.text('0:00');
		if ($('form#AnsForm').get(0)) {
			$('form#AnsForm').submit();
		}
		if ($('button[name=back]').get(0)) {
			$('button[name=back]').remove();
		}
		return;
	}

	var min   = Math.floor(diff % times / (60 * 1000));
	var sec   = Math.floor(diff % times / 1000) % 60 % 60;

	elem.text(min+':'+(('0'+sec).slice(-2)));
	setTimeout('countDown()', 100);
	return;
}
