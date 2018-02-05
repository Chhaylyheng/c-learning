$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('form').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('form').find('input.Chk').prop('checked',false);
		}
	});

	$('.TeacherDelete').on('click', function() {
		var tid = $(this).attr('data');

		confirm($.i18n.prop("cl_adm_teacher_TeacherDelete_1"), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/adm/teacher/delete/'+tid;
		});
		return false;

	});

	$('.TeacherLock').on('click', function() {
		var tid = $(this).attr('data');
		location.href = '/adm/teacher/lock/'+tid;
		return false;

	});


	$('#contract-datepick').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		minDate: 'today',
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});
});
