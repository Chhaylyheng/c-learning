$(function() {
	$('.KReportPutReset').click(function() {
		var oLead = $(this).parent().parent().parent().find('.KReportPutColumn').find('.lead');
		var sObj = $(this).attr('value');
		var aObj = sObj.split("_");

		confirm($.i18n.prop('cl_adm_kreport_KReportPutReset_1'), function(bOK) {
			if (!bOK) {
				return false;
			}

			$.ajax({
				url: "/adm/ajax/KReportPutReset.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					"iy": aObj[0],
					"ip": aObj[1],
				},
				success: function(o){
					switch (o.err)
					{
						case -3:
						case -2:
						case -1:
							addAlert(o.msg,'alert');
						break;
						case 0:
							oLead.text('0');
							addAlert(o.msg,'tmp');
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
		return false;
	});

	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('form').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('form').find('input.Chk').prop('checked',false);
		}
	});


});
