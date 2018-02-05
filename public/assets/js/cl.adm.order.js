$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('form').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('form').find('input.Chk').prop('checked',false);
		}
	});

	$('#payment-datepick').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});

	$('.paychk button[type=submit]').on('click', function() {
		var form = $(this).parents('form');
		var chk = form.find('input.Chk');
		var flg = false;
		$.each(chk,function(i) {
			if ($(chk[i]).prop("checked")) {
				flg = true;
			}
		});
		if (!flg) {
			alert($.i18n.prop('cl_adm_order_payck_1'));
			return false;
		} else {
			confirm($.i18n.prop('cl_adm_order_payck_2'), function(bOK) {
				if (bOK) {
					form.submit();
				}
				return false;
			});
			return false;
		}
	});


	$('.paymentremove').on('click', function() {
		var url = $(this).attr('href');

		confirm($.i18n.prop('cl_adm_order_paymentremove_1'), function(bOK) {
			if (bOK) {
				location.href = url;
			}
			return false;
		});
		return false;
	});
});
