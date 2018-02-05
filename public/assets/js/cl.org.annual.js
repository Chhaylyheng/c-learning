$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('form').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('form').find('input.Chk').prop('checked',false);
		}
	});

	$('.annualClass').on('click', function() {
		var form = $(this).parents('form');
		confirm($.i18n.prop('cl_org_annual_annualClass_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			form.submit();
		});
		return false;
	});

	$('.annualStudentDelete').on('click', function() {
		var form = $(this).parents('form');
		confirm($.i18n.prop('cl_org_annual_annualStudentDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			form.submit();
		});
		return false;
	});

	$('.annualStudentYearIncrement').on('click', function() {
		var form = $(this).parents('form');
		confirm($.i18n.prop('cl_org_annual_annualStudentYearIncrement_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			form.submit();
		});
		return false;
	});

});
