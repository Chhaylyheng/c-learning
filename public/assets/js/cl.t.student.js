$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('form').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('form').find('input.Chk').prop('checked',false);
		}
	});

	$('.password_reset').on('click', function() {
		$('#stErr').hide();
		var bros = $(this).prev('span');
		var stID = $(this).attr('data');
		var data = $(this).parents('table').attr('data').split('|');

		confirm($.i18n.prop('cl_t_student_password_reset_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			$.ajax({
				url: "/t/ajax/StudentPassReset.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"st":stID,
					"tt":data[0],
					"ct":data[1]
				},
				success: function(o){
					var res = o.res;
					if (o.err != 0) {
						addAlert(o.msg,'tmp');
						return false;
					}
					bros.text(res.pw);
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

	$('#StudentMailSend').on('click', function() {
		var form = $('#StudentCheckForm');
		var input = form.find('input.Chk');
		var bChk = false;

		for (var i = 0; i < input.length; i++) {
			if (input.eq(i).prop('checked')) {
				bChk = true;
				break;
			}
		}

		if (!bChk) {
			addAlert($.i18n.prop('cl_t_student_StudentMailSend_1'),'alert');
			return false;
		}

		form.submit();
		return false;
	});

	$('.sendto-box-toggle').on('click',function() {
		$(this).parents('.formLabel').next().find('.sendto-list').toggle();
		$(this).find('i').toggle();
		return false;
	});

	$('.mail-history-bhead').on('click', function() {
		$(this).hide();
		$(this).parents('td').find('.mail-history-body').show();
	});
	$('.mail-history-body').on('click', function() {
		$(this).hide();
		$(this).parents('td').find('.mail-history-bhead').show();
	});
	$('.mail-history-num').on('click', function() {
		$(this).parents('td').find('.sendto-list').toggle();
	});
});
