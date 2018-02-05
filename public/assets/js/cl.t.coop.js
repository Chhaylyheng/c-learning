$(function() {
	$('.quest-dropdown-toggle').on('click',function() {
		var id = $(this).attr('id');
		var mode = id.split('_');
		var list = $('.dropdown-list-'+mode[2]);
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

		if (mode[4] == 'gc') {
			list.find('.CoopSortTop').parents('li').hide();
		} else if (mode[4] == 'nondel') {
			list.find('.CoopDelete').parents('li').hide();
		}

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.CoopCateEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/coop/cateedit/'+aObj[1];
		return false;
	});

	$('.CoopCateDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_coop_CoopCateDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/coop/catedelete/'+aObj[1];
		});
		return false;
	});

	$('.CoopCateSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.CoopCateSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/coop/CateSort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"cc": aObj[1],
				"m": aObj[2],
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
						if (res.m == 'up') {
							if ($(TR).prev('tr')) {
								var oB = $(TR).prev('tr').find('.CoopCateSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.CoopCateSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertAfter($(TR).next("tr")[0]);
							}
						}
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		$(TR).css({'background-color':'transparent'});
		return false;
	});

	$('.CoopRange').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/coop/RangeChange.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"cc": aObj[1],
				"m":  m,
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
						$(Btn).html('<div></div>');
						$(Btn).removeClass('font-blue');
						$(Btn).removeClass('font-red');
						$(Btn).removeClass('font-default');
						$(Btn).addClass(res.class);
						$(Btn).find('div').text(res.text);
						addAlert(o.msg,'tmp');

						if (m == 'select') {
							window.location.href = '/t/coop/stuadd/'+aObj[1];
							return false;
						}
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		$(list).slideUp('fast');
		return false;
	});


	$('td.CoopRootIcon > i').on('click', function() {
		var tb = $(this).parents('tbody');
		var id = $(this).parents('tr').attr('id');
		var dt = $(this).attr('data');

		if (dt > 0) {
			$(this).removeClass('fa-minus-square-o');
			$(this).addClass('fa-plus-square-o');
			$(this).attr('data',0);

			tb.find('tr.'+id).hide();
			tb.find('tr#'+id).show();
		} else {
			$(this).removeClass('fa-plus-square-o');
			$(this).addClass('fa-minus-square-o');
			$(this).attr('data',1);
			tb.find('tr.'+id).show();
		}
	});

	$('.CoopSort').click(function() {
		var TB = $(this).parents('tbody');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TB).find('.CoopSort');
		$(TB).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/coop/Sort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"cc": aObj[0],
				"cn": aObj[1],
				"m": aObj[2],
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
						if (res.m == 'up') {
							if ($(TB).prev('tbody')) {
								var oB = $(TB).prev('tbody').find('.CoopSort');
								SortBtnDisabled(oA,oB);
								$(TB).insertBefore($(TB).prev('tbody')[0]);
							}
						} else {
							if ($(TB).next('tbody')) {
								var oB = $(TB).next('tbody').find('.CoopSort');
								SortBtnDisabled(oA,oB);
								$(TB).insertAfter($(TB).next("tbody")[0]);
							}
						}
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		$(TB).css({'background-color':'transparent'});
		return false;
	});

	$('.CoopSortTop').click(function() {
		var list = $(this).parents('ul');
		list.slideUp('fast');
		var sObj = $(list).attr('obj');
		var aObj = sObj.split("_");
		var Mine = $('#c'+aObj[1]);
		var LIs  = $('.c'+aObj[1]);
		var cRoot = $(Mine).parents('tbody').find('tr:first-child');

		console.log(Mine.attr('class'));

		$(LIs).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/coop/ChildSort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"cc": aObj[0],
				"cn": aObj[1],
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
						$(cRoot).after($(LIs));
					break;
				}
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error','alert');
				return false;
			}
		});
		$(LIs).css({'background-color':'transparent'});
		return false;
	});


	$('.StudentCoopAdd').on('click',function() {
		var addbox = $('select[name=add]');
		var rembox = $('select[name=remove]');
		var aObj = $(this).parents('ul').attr('obj').split("_");

		var sel = rembox.val();

		if (!sel) {
			addAlert($.i18n.prop('cl_t_coop_StudentCoopAdd_1'),'alert');
			return false;
		}

		$.ajax({
			url: "/t/ajax/coop/CoopAdd.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"cc": aObj[1],
				"st": sel,
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
							var opt = rembox.find('option[value='+sel[i]+']');
							addbox.append(opt);
						}
						addAlert($.i18n.prop('cl_t_coop_StudentCoopAdd_2')+'('+res+')','success');
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

	$('.StudentCoopRemove').on('click',function() {
		var addbox = $('select[name=add]');
		var rembox = $('select[name=remove]');
		var aObj = $(this).parents('ul').attr('obj').split("_");

		var sel = addbox.val();

		if (!sel) {
			addAlert($.i18n.prop('cl_t_coop_StudentCoopRemove_1'),'alert');
			return false;
		}

		$.ajax({
			url: "/t/ajax/coop/CoopRemove.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"cc": aObj[1],
				"st": sel,
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
						addAlert($.i18n.prop('cl_t_coop_StudentCoopRemove_2')+'('+res+')','success');
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

	$(document).on('click', '.CoopAlreadyShow', function(e) {
		var aObj = $(this).val().split("_");

		$.ajax({
			url: "/t/ajax/coop/AlreadyMember.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"cc": aObj[0],
				"no": aObj[1],
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
						var member;
						if (res) {
							member = res;
						} else {
							return false;
						}

						$('.QBFloatBox .QBFloatMember').html(member);
						$('.QBFloatBox').show();

						$('.QBFloatBox').css({
							'top': (e.pageY - 43)+'px',
							'left': (e.pageX + 5)+'px',
						});

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

	$('.QBFloatBox .QBFloatClose').on('click', function() {
		$('.QBFloatBox').hide();
		$('.QBFloatBox .QBFloatMember').text('');
	});

});

function SortBtnDisabled(oA,oB) {
	var A0 = $(oA[0]).attr('disabled');
	var A1 = $(oA[1]).attr('disabled');
	var B0 = $(oB[0]).attr('disabled');
	var B1 = $(oB[1]).attr('disabled');
	if (A0 == null) { $(oB[0]).removeAttr('disabled'); } else { $(oB[0]).attr('disabled',A0); }
	if (A1 == null) { $(oB[1]).removeAttr('disabled'); } else { $(oB[1]).attr('disabled',A1); }
	if (B0 == null) { $(oA[0]).removeAttr('disabled'); } else { $(oA[0]).attr('disabled',B0); }
	if (B1 == null) { $(oA[1]).removeAttr('disabled'); } else { $(oA[1]).attr('disabled',B1); }
}
