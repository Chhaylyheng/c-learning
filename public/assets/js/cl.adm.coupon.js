$(function() {
	$('.coupon-dropdown-toggle').on('click',function() {
		var id = $(this).attr('id');
		var mode = id.split('_');
		var list = $('.dropdown-list-'+mode[(mode.length - 1)]);
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
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.CouponEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/adm/coupon/edit/'+aObj[0];
		return false;
	});

	$('.CouponDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop("cl_adm_coupon_CouponDelete_1"), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/adm/coupon/delete/'+aObj[0];
		});
		return false;
	});

	$('#coupon-code').on('keyup', function() {
		$(this).val($(this).val().toUpperCase());
		return false;
	});

	$('#range-datepick').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});

	$('input[name=infinityRange]').on('change', function() {

		if($(this).prop('checked')) {
			$('div.CouponRangeDate').hide();
		} else {
			$('div.CouponRangeDate').show();
		}

	});

	$('input[name=cpDiscount]').on('keyup', function() {
		var disc = $(this).val();
		var list = $('.discountList');

		$.each($('.discountList'), function() {
			var num = number_format(Math.round($(this).attr('data') * (1 - (disc / 100))));
			$(this).find('span').text(num+'円');
		});
		return false;
	});

});

