var FixedSize = 49;
var TutoMode = 'index';

$(function() {
	$(document).on('click', '.TutorialClassBtn', function() {
		$('#Tutorial1').hide();

		TutorialClassIndex();
	});

	$(document).on('click', '.TutorialBentStart', function() {
		$('#Tutorial5').hide();

		var sf = $('#smartphone-frame');

		sf.css({
			'top': ($(window).height() -  sf.innerHeight() - 8 - 40) + 'px',
			'left': ($(window).width() -  sf.innerWidth() - 8) + 'px'
		});

		sf.show();
		var tl = {'x': sf.offset().left, 'y': sf.offset().top};
		var br = {'x': sf.offset().left + sf.innerWidth(), 'y': sf.offset().top + sf.innerHeight()};

		var TBox = $('<div>')
			.attr('id', 'Tutorial6')
			.addClass('TutorialText TB-BR')
			.css({
				'z-index': '161'
			})
			.append(
				$('#TutoText6').html()
			)
		;
		$('body').append(TBox);

		TBox.css({
			'top': (tl.y - TBox.innerHeight() - 12) +'px',
			'left': (br.x - TBox.innerWidth()) +'px'
		});
	});

	$(document).on('click', '.TutorialBentMsgClose', function() {
		$('#Tutorial6').hide();
		shadowMask('off');
		$('#smartphone-frame').draggable({
			cancel: '.screen'
		});
	});

	$(document).on('click', '.TutorialAnsSubmit', function() {
		$('.SFErr').hide();

		var form = $(this).parents('.screen');

		var NO = form.find('input[name=radioSel]:checked').val();

		if (!NO) {
			$('.SFErr').show();
			return false;
		}

		var Label = form.find('input[name=radioSel]:checked').attr('label');
		var Text = form.find('textarea[name=textAns]').val();
		var ID = 'D' + all;
		var Name = 'DummyUser' + all;
		all++;

		gBent.ALL[NO].num++;

		gBent.ALL['1'].per = Math.round((gBent.ALL['1'].num / all) * 100);
		gBent.ALL['2'].per = Math.round((gBent.ALL['2'].num / all) * 100);

		if (Text != '') {
			gComment.ALL[ID] = {
				'text': Text,
				'cName': Label,
				'cNO': NO,
				'cPick': 0,
				'cPosted': Name
			}
		}

		form.find('input[name=radioSel]:checked').parent('label').removeClass('check');
		form.find('input[name=radioSel]:checked').parent('label').addClass('default');
		form.find('input[name=radioSel]:checked').parent('label').find('i').removeClass('fa-dot-circle-o');
		form.find('input[name=radioSel]:checked').parent('label').find('i').addClass('fa-circle-o');
		form.find('input[name=radioSel]:checked').prop('checked',false);

		form.find('textarea[name=textAns]').val('');

		gQuest.qpNum = all;

		DoughnutChartUpdateDemo('ALL');
	})

	$('.QuestAnsChoice input[type=checkbox]').change(function() {
		if ($(this).prop('checked')) {
			$(this).parent('label').removeClass('default');
			$(this).parent('label').addClass('check');
			$(this).parent('label').find('i').removeClass('fa-square-o');
			$(this).parent('label').find('i').addClass('fa-check-square-o');
		} else {
			$(this).parent('label').removeClass('check');
			$(this).parent('label').addClass('derault');
			$(this).parent('label').find('i').removeClass('fa-check-square-o');
			$(this).parent('label').find('i').addClass('fa-square-o');
		}
	});
	$('.QuestAnsChoice input[type=radio]').change(function() {
		$(this).parents('.QuestAnsChoice').find('label').removeClass('check');
		$(this).parents('.QuestAnsChoice').find('label').addClass('default');
		$(this).parents('.QuestAnsChoice').find('i').removeClass('fa-dot-circle-o');
		$(this).parents('.QuestAnsChoice').find('i').addClass('fa-circle-o');

		if ($(this).prop('checked')) {
			$(this).parent('label').removeClass('default');
			$(this).parent('label').addClass('check');
			$(this).parent('label').find('i').removeClass('fa-circle-o');
			$(this).parent('label').find('i').addClass('fa-dot-circle-o');
		}
	});

	$('#smartphone-frame textarea#textAns').on('click', function() {
		$(this).focus();
		return false;
	});

});

function TutorialIndex() {
	TutoMode = 'index';
	shadowMask('on');

	var TutorialStart = $('<div>')
		.attr('id', 'Tutorial1')
		.addClass('TutorialText')
		.css({
			'z-index': '160'
		})
		.append(
			$('#TutoText1').html()
		)
	;

	$('body').append(TutorialStart);

	TutorialStart.css({
		'top': (($(window).height()-TutorialStart.outerHeight())/2 - FixedSize)+'px',
		'left': (($(window).width()-TutorialStart.outerWidth())/2)+'px'
	});

}

function TutorialClassIndex() {
	TutoMode = 'class';
	shadowMask('on');

	var btn = $('#QuestLink');

	var tl = {'x': btn.offset().left, 'y': btn.offset().top - FixedSize};
	var br = {'x': btn.offset().left + btn.innerWidth(), 'y': btn.offset().top + btn.innerHeight() - FixedSize};

	var btnC = btn.clone(true);
	btnC.css({
		'width': (br.x - tl.x) + 'px',
	});

	var btnBack = $('<div>')
		.attr('id', 'BtnBack')
		.css({
			'position': 'absolute',
			'top': (tl.y - 2) + 'px',
			'left': (tl.x - 2) + 'px',
			'z-index': '160',
			'background-color': 'white',
			'padding': '2px'
		})

	btnBack.append(btnC);
	$('body').append(btnBack);

	var TBox = $('<div>')
		.attr('id', 'Tutorial3')
		.addClass('TutorialText TB-TL')
		.css({
			'z-index': '161'
		})
		.append(
			$('#TutoText3').html()
		)
	;

	$('body').append(TBox);

	TBox.css({
		'top': (br.y + 12) +'px',
		'left': (tl.x + 4) +'px'
	});

}

function TutorialQuestIndex() {
	TutoMode = 'quest';
	shadowMask('on');

	var btn = $('#QuickQuestTutorial');

	var tl = {'x': btn.offset().left, 'y': btn.offset().top - FixedSize};
	var br = {'x': btn.offset().left + btn.innerWidth(), 'y': btn.offset().top + btn.innerHeight() - FixedSize};

	var btnC = btn.clone(true);
	btnC.css({
		'width': (br.x - tl.x) + 'px',
	});
	btnC.attr('href', '/t/tutorial/questbent');
	btnC.removeAttr('target');

	var btnBack = $('<ul>')
		.attr('id', 'BtnBack')
		.addClass('QuestQuickBtn')
		.css({
			'position': 'absolute',
			'top': (tl.y - 2) + 'px',
			'left': (tl.x - 2) + 'px',
			'z-index': '160',
			'background-color': 'white',
			'padding': '2px'
		})
		.append(
			$('<li>')
				.addClass('Tutorial')
				.append(btnC)
		);
	$('body').append(btnBack);

	var TBox = $('<div>')
		.attr('id', 'Tutorial4')
		.addClass('TutorialText TB-TL')
		.css({
			'z-index': '161'
		})
		.append(
			$('#TutoText4').html()
		)
	;

	$('body').append(TBox);

	TBox.css({
		'top': (br.y + 12) +'px',
		'left': (tl.x + 4) +'px'
	});

}

function TutorialBentStart() {
	TutoMode = 'bent1';
	shadowMask('on');

	var TutorialStart = $('<div>')
		.attr('id', 'Tutorial5')
		.addClass('TutorialText')
		.css({
			'z-index': '160'
		})
		.append(
			$('#TutoText5').html()
		)
	;

	$('body').append(TutorialStart);

	TutorialStart.css({
		'top': (($(window).height()-TutorialStart.outerHeight())/2 - FixedSize)+'px',
		'left': (($(window).width()-TutorialStart.outerWidth())/2)+'px'
	});
}

