$(function() {
	$('.group-dropdown-toggle').on('click',function() {
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

	$(document).on('click','.ans-dropdown-toggle', function() {
		pickListShow($(this));
	});

	$('.GroupEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/adm/group/edit/'+aObj[0];
		return false;
	});

	$('.GroupDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop("cl_adm_group_GroupEdit_1"), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/adm/group/delete/'+aObj[0];
		});
		return false;
	});

	$('.password_reset').on('click', function() {
		$('#gaErr').hide();

		var bros = $(this).prev('span');
		var gaID = $(this).attr('data');

		$.ajax({
			url: "/adm/ajax/GroupAdminPassReset.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ga":gaID,
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

	$('.GroupAdminEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/adm/group/admedit/'+aObj[0]+'/'+aObj[1];
		return false;
	});

	$('.GroupAdminDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop("cl_adm_group_GroupAdminDelete_1"), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/adm/group/admdelete/'+aObj[0]+'/'+aObj[1];
		});
		return false;
	});

	$('input[name=gt_ldap]').on('change', function() {

		if ($(this).prop('checked')) {
			$('.LDAPSetting').show();
		} else {
			$('.LDAPSetting').hide();
		}
	});

	$('.LDAPSetting input,.LDAPSetting select').on('change', function() {
		var command = 'ldapsearch -x -LLL';

		command += ' -H "'+$('.LDAPSetting select[name=gt_l_protocol]').val()+'://'+$('.LDAPSetting input[name=gt_l_server]').val();
		command += ($('.LDAPSetting input[name=gt_l_port]').val() > 0)? ':'+$('.LDAPSetting input[name=gt_l_port]').val()+'/"':'/"';

		command += ' -D "'+$('.LDAPSetting input[name=gt_l_dn]').val()+'"';
		command += ' -W [PASSWORD]';

		command += ' -b "'+$('.LDAPSetting input[name=gt_l_sb]').val()+'"';

		command += ' "'+$('.LDAPSetting select[name=gt_l_uid]').val()+'=[USER]"';

		$('.LDAPCommand').text(command);
	});
});
