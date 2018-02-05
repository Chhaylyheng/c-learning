$(function() {
	$('.AllChk').change(function() {
		if ($(this).prop("checked")) {
			$(this).parents('form').find('input.Chk').prop('checked',true);
		} else {
			$(this).parents('form').find('input.Chk').prop('checked',false);
		}
	});

	$('.news-dropdown-toggle').on('click',function() {
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

		list.css({
			top: (parseInt(offset.top)+height-FixedSize)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.NewsEdit').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		location.href = '/t/news/edit/'+aObj[0];
		return false;
	});

	$('.NewsDelete').click(function() {
		var list = $(this).parents('ul');
		var id   = $(list).attr('obj');
		var aObj = id.split("_");

		confirm($.i18n.prop('cl_t_news_NewsDelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = '/t/news/delete/'+aObj[0];
		});
		return false;
	});

	$('.NewsFinish').click(function() {
		var url = $(this).attr('href');

		confirm($.i18n.prop('cl_t_news_NewsFinish_1'), function(bOK) {
			if (bOK) {
				location.href = url;
			}
			return false;
		});
		return false;
	});

	$('.news-bhead').on('click', function() {
		$(this).hide();
		$(this).parents('td').find('.news-body').show();
	});
	$('.news-body').on('click', function() {
		$(this).hide();
		$(this).parents('td').find('.news-bhead').show();
	});

	var dateFormat = 'yy/mm/dd';
	var from = $('#from').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		minDate: 'today',
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
		minDate: 'today',
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

	if ($('.timepick').get(0)) {
		$('.timepick').timepicker({
			'timeFormat': 'H:i',
			'minTime': '0:00',
			'maxTime': '23:55',
			'forceRoundTime': true,
			'step': 5,
		});
	}


	$('.ListChoice').on('click', function() {
		shadowMask('on');

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
										$('#dburl').val(url);
										$('#news-chain').html('<i class="fa fa-chain"></i> ' + $(this).attr('type') + '「' + $(this).find('.listTitle').text() + '」 <a href="#" class="ChoiceContentsDelete button na default width-auto" style="padding: 4px;"><i class="fa fa-times mr0"></i></a>');

										$('#ListBox').hide();
										shadowMask('off');
										$('#ListBox').remove();
										addAlert($.i18n.prop('cl_t_news_ListChoice_3')+'【'+$(this).find('.listTitle').text()+'】','tmp');
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

	$(document).on('click', 'a.ChoiceContentsDelete', function() {
		$('#dburl').val('');
		$('#news-chain').html('');
		return false;
	});

});
