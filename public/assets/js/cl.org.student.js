$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('form').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('form').find('input.Chk').prop('checked',false);
		}
	});

	$('.checkdrop-dropdown-toggle').on('click',function() {
		var id = $(this).attr('id');
		var mode = id.split('_');
		var list = $('.dropdown-list-'+mode[1]);
		var obj = list.attr('obj');

		if (id == obj && list.css('display') == 'block') {
			list.slideUp('fast');
			return;
		}

		list.hide();
		list.attr('obj',id);

		var offset = $(this).offset();
		var height = $(this).outerHeight();

		list.find('li').show();

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.CheckDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		var form = $('#CheckForm');
		var input = form.find('input.Chk');
		var bChk = false;

		for (var i = 0; i < input.length; i++) {
			if (input.eq(i).prop('checked')) {
				bChk = true;
				break;
			}
		}

		if (!bChk) {
			addAlert($.i18n.prop('cl_org_student_CheckDelete_1'),'alert');
			return false;
		}

		confirm($.i18n.prop('cl_org_student_CheckDelete_2'), function(bOK) {
			if (!bOK) {
				return false;
			}
			form.find('input[name=mode]').val('delete');
			form.submit();
			return false;
		});
		return false;
	});

	$('.password_reset').on('click', function() {
		$('#stErr').hide();

		var bros = $(this).prev('span');
		var stID = $(this).attr('data');

		confirm($.i18n.prop('cl_org_student_password_reset_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			$.ajax({
				url: "/org/ajax/student/PassReset.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"st":stID,
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

	$('.student-dropdown-toggle').on('click',function() {
		var id = $(this).attr('id');
		var mode = id.split('_');
		var list = $('.dropdown-list-'+mode[1]);
		var obj = list.attr('obj');

		if (id == obj && list.css('display') == 'block') {
			list.slideUp('fast');
			return;
		}

		list.hide();
		list.attr('obj',id);

		var offset = $(this).offset();
		var height = $(this).outerHeight();

		list.find('li').show();

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.StudentEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/org/student/edit/'+aObj[0];
		return false;
	});

	$('.StudentDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_org_student_StudentDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/org/student/delete/'+aObj[0];
		});
		return false;
	});

	$('.StudentRemove').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_org_student_StudentRemove_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/org/student/remove/'+aObj[2]+'/'+aObj[0];
		});
		return false;
	});

});
