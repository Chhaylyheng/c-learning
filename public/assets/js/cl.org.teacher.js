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
			addAlert($.i18n.prop('cl_org_teacher_CheckDelete_1'),'alert');
			return false;
		}

		confirm($.i18n.prop('cl_org_teacher_CheckDelete_2'), function(bOK) {
			if (!bOK) {
				return false;
			}
			form.find('input[name=mode]').val('delete');
			form.submit();
			return false;
		});
		return false;
	});

	$('input[name=MasterCheck]:radio').on('change',function() {
		var tid = $(this).val();
		var cid = $(this).parents('table').attr('obj');

		$.ajax({
			url: "/org/ajax/teacher/MasterChange.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": cid,
				"tt": tid,
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
						addAlert($.i18n.prop('cl_org_teacher_MasterCheck_1'),'success');
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


	$('.TeacherClassAdd').on('click',function() {
		var addbox = $('select[name=add]');
		var rembox = $('select[name=remove]');
		var cid = $(this).parents('ul').attr('obj');

		var sel = rembox.val();

		if (!sel) {
			addAlert($.i18n.prop('cl_org_teacher_TeacherClassAdd_1'),'alert');
			return false;
		}

		$.ajax({
			url: "/org/ajax/teacher/ClassAdd.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": cid,
				"tt": sel,
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
						for(var i = 0; i < sel.length; i++) {
							var opt = rembox.find('option[value='+sel[i]+']');
							addbox.append(opt);
						}
						if ($('span.master-teacher').text() == "") {
							location.reload();
						}
						addAlert($.i18n.prop('cl_org_teacher_TeacherClassAdd_2')+'('+res+')','success');
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

	$('.TeacherClassRemove').on('click',function() {
		var addbox = $('select[name=add]');
		var rembox = $('select[name=remove]');
		var cid = $(this).parents('ul').attr('obj');

		var sel = addbox.val();

		if (!sel) {
			addAlert($.i18n.prop('cl_org_teacher_TeacherClassRemove_1'),'alert');
			return false;
		}

		$.ajax({
			url: "/org/ajax/teacher/ClassRemove.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": cid,
				"tt": sel,
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
						for(var i=0; i< sel.length; i++) {
							var opt = addbox.find('option[value='+sel[i]+']');
							rembox.append(opt);
						}
						addAlert($.i18n.prop('cl_org_teacher_TeacherClassRemove_2')+'('+res+')','success');
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
	})

	$('.password_reset').on('click', function() {
		var bros = $(this).prev('span');
		var ttID = $(this).attr('data');

		confirm($.i18n.prop('cl_org_student_password_reset_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			$.ajax({
				url: "/org/ajax/teacher/PassReset.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"tt":ttID,
				},
				success: function(o){
					var res = o.res;
					if (o.err != 0) {
						addAlert(o.msg,'alert');
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

	$('.teacher-dropdown-toggle').on('click',function() {
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

	$('.TeacherEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/org/teacher/edit/'+aObj[0];
		return false;
	});

	$('.TeacherDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_org_teacher_TeacherDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/org/teacher/delete/'+aObj[0];
		});
		return false;
	});

	var dateFormat = 'yy/mm/dd';
	var from = $('#from').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		minDate: null,
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	})
	.on('change', function() {
		to.datepicker('option', 'minDate', getDate(this));
	});
	var to = $('#to').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		minDate: null,
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	})
	.on('change', function() {
		from.datepicker('option', 'maxDate', getDate(this));
	});

	function getDate(ele) {
		var date;
		date = $.datepicker.parseDate(dateFormat, ele.value);
		return date;
	}

});
