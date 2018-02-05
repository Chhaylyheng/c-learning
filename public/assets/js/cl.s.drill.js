$(function() {

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

});
