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

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$(document).on('click','.ans-dropdown-toggle', function() {
		pickListShow($(this));
	});

	$('.MaterialPublic').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var Btn  = $('#'+id);
		var m    = $(this).attr('obj');
		var aObj = id.split("_");

		$.ajax({
			url: "/t/ajax/material/Public.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"mc": aObj[0],
				"mn": aObj[1],
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

	$('.MatCateEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/material/cateedit/'+aObj[1];
		return false;
	});

	$('.MatCateDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_material_MatCateDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/material/catedelete/'+aObj[1];
		});
		return false;
	});

	$('.MatCateSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.MatCateSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/material/CateSort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct": aObj[0],
				"mc": aObj[1],
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
								var oB = $(TR).prev('tr').find('.MatCateSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.MatCateSort');
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

	$('.MaterialEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/material/edit/'+aObj[0]+'/'+aObj[1];
		return false;
	});

	$('.MaterialDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_material_MaterialDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/material/delete/'+aObj[0]+'/'+aObj[1];
		});
		return false;
	});

	$('.MaterialSort').click(function() {
		var TR = $(this).parents('tr');
		var sObj = $(this).val();
		var aObj = sObj.split("_");
		var oA = $(TR).find('.MaterialSort');
		$(TR).css({'background-color':'#62BC64'});

		$.ajax({
			url: "/t/ajax/material/Sort.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"mc": aObj[0],
				"mn": aObj[1],
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
								var oB = $(TR).prev('tr').find('.MaterialSort');
								SortBtnDisabled(oA,oB);
								$(TR).insertBefore($(TR).prev('tr')[0]);
							}
						} else {
							if ($(TR).next('tr')) {
								var oB = $(TR).next('tr').find('.MaterialSort');
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

	/* ファイルリンク */
	$('.file-uploader .input-cover a').on('click', function(e) {
		e.stopPropagation();
		return true;
	});
	/* ファイル削除 */
	$('.file-uploader .uploaded-file .remove').on('click', function(e) {
		var $ufile  = $(this).parents('li').find('.uploaded-file');
		var $hidden = $(this).parents('li').find('input[type=hidden]');
		var $cover  = $(this).parents('li').find('.input-cover');

		$hidden.val('');
		$ufile.find('.file').attr('href','');
		$ufile.find('.name').text('');
		$ufile.find('.size').text('');
		$ufile.hide();
		$cover.css({
			'background-image':'none',
		});

		e.stopPropagation();
		return false;
	});

	/* ファイルを選択 */
	$('.file-uploader .input-cover').on('click', function() {
		$(this).parents('li').find('input[type=file]').click();
	});
	/* ファイルアップロード */
	$(document).on('change','.file-uploader input[type=file]',function() {
		var $this   = $(this);
		var $p_bar   = $(this).parents('li').find('.upload-progress-bar');
		var $input  = $(this).parents('span');
		var $ufile  = $(this).parents('li').find('.uploaded-file');
		var $hidden = $(this).parents('li').find('input[type=hidden]');
		var $cover  = $(this).parents('li').find('.input-cover');

		var $fd = new FormData();
		if ($(this).val() !== '') {
			$fd.append("file", $(this).prop("files")[0]);
		}
		$fd.append("prefix", '_material_');

		$.ajax({
			async: true,
			xhr: function() {
				var $XHR = $.ajaxSettings.xhr();
				if ($XHR.upload) {
					$XHR.upload.addEventListener('progress',function($e) {
						var $progre = parseInt($e.loaded/$e.total*10000)/100;
						$p_bar.width(parseInt($progre)+'%');
					});
				}
				return $XHR;
			},
			url: "/uploadfile.json",
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: $fd,
			processData: false,
			contentType: false,
			success: function($o) {
				console.log($o);
				if ($o.error) {
					addAlert($o.error,'alert');
					return false;
				}
				$hidden.val($o.hval);
				$ufile.find('.file').attr('href',$o.file);
				$ufile.find('.name').text($o.name);
				$ufile.find('.size').text($o.size);
				if ($o.isimg) {
					$cover.css({
						'background-image':'url("'+$o.file+'")',
						'background-size':'cover',
					});
				} else {
					$cover.css({
						'background-image':'none',
					});
				}
				$ufile.show();
				return false;
			},
			error: function(xhr, ts, err) {
				addAlert('Network Access Error','alert');
				return false;
			},
			complete: function() {
				var $tmp = $input.html();
				$input.html($tmp);
				$p_bar.width('0%');
				return false;
			}
		});
	});

	$('.ListChoice').on('click', function() {
		shadowMask('on');

		var no = $(this).parents('.ExtURLBox').attr('data');
		var input = $(this).parents('.ExtURLBox').find('input[type=text]');
		var chain = $('#material-chain-'+no);
		var mode = $(this).attr('data');
		var ctID = $(this).parent('div').attr('data');
		var title = $(this).text();

		var CloseBtn = $('<div>')
			.addClass('BTClose')
			.append('<i class="fa fa-times"></i>')
			.on('click', function() {
				$('#ListBox').hide();
				shadowMask('off');
				$('#ListBox').remove();
			});
		var ListBox = $('<div>')
			.attr('id','ListBox')
			.append(
				$('<div>')
					.text(title)
			)
			.append(
				$('<div>')
					.addClass('text-center load-spinner')
					.append(
						$('<i>')
						.addClass('fa fa-spinner fa-pulse fa-fw fa-3x')
					)
			)
			.append(
				$('<table>')
					.addClass('QuestListBox')
			)
			.append(CloseBtn)
			.css({
				'max-width':'90%',
				'height':'90vh',
			})
			.on('click',function(){
				return false;
			})
			.on('scroll', function(){
				var sc = $(this).scrollTop();
				$('.BTClose').css({
					'top': 3+sc+'px'
				});
				return false;
			});

		$('#shadowMask').on('click', function() {
			$('#ListBox').hide();
			shadowMask('off');
			$('#ListBox').remove();
		});
		$('#shadowMask').append(ListBox);

		ListBox.css({
			'top': (($(window).height()-ListBox.outerHeight())/2)+'px',
			'left': (($(window).width()-ListBox.outerWidth())/2)+'px'
		});

		$.ajax({
			url: "/t/ajax/material/ListData.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"m": mode,
				"c": ctID
			},
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -3:
					case -2:
					case -1:
						addAlert(o.msg,'alert');
						$('#ListBox').hide();
						shadowMask('off');
						$('#ListBox').remove();
					break;
					case 0:
						$('.load-spinner').hide();
						$('.load-spinner').remove();
						var tr;
						for (i = 0; i < res.length; i++) {
							if (!res[i].cat) {
								tr = $('<tr>')
									.addClass('choiceListItem font-size-90')
									.attr('data',res[i].url)
									.attr('type',res[i].type)
									.attr('root',res[i].tree)
									.append(
										$('<td>')
											.attr('nowrap','nowrap')
											.addClass('text-center')
											.addClass(res[i].stateColor)
											.text(res[i].status)
									)
									.append(
										$('<td>')
											.addClass('listTitle')
											.text(res[i].title)
									)
									.append(
										$('<td>')
											.attr('nowrap','nowrap')
											.addClass('font-size-80')
											.text(res[i].date)
									)
									.on('click',function(){
										var url = decodeURIComponent($(this).attr('data'));
										input.val(url);
										chain.html('<i class="fa fa-chain"></i> ' + $(this).attr('type') + '「' + $(this).find('.listTitle').text() + '」');

										$('#ListBox').hide();
										shadowMask('off');
										$('#ListBox').remove();
										addAlert($.i18n.prop('cl_t_material_ListChoice_3')+'【'+$(this).find('.listTitle').text()+'】','tmp');
										return false;
									})
									;
							} else {
								tr = $('<tr>')
								.attr('tree',res[i].tree)
								.addClass('choiceListItem font-size-90')
								.append(
									$('<th>')
										.attr('colspan','3')
										.attr('nowrap','nowrap')
										.addClass('listTitle')
										.text(res[i].title)
										.prepend(
											$('<i>')
												.addClass('fa fa-minus-square-o mr4')
										)
								)
								.on('click',function() {
									var treeID = $(this).attr('tree');
									$(this).parents('table').find('tr[root='+treeID+']').toggle();
									if ($(this).find('i.fa-minus-square-o').length > 0) {
										$(this).find('i.fa-minus-square-o').addClass('fa-plus-square-o').removeClass('fa-minus-square-o');
									} else {
										$(this).find('i.fa-plus-square-o').addClass('fa-minus-square-o').removeClass('fa-plus-square-o');
									}
								})
								;
							}
							ListBox.find('table').append(tr);
						}
						ListBox.css({
							'top': (($(window).height()-ListBox.outerHeight())/2)+'px',
							'left': (($(window).width()-ListBox.outerWidth())/2)+'px'
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

