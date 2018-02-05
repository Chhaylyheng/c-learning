$(function() {

});

$(window).on('load', function(){
	var intVal = 70;
	var LB = $('div.mat-list');
	var index = 0;
	var timeID = 0;

	if (LB) {
		var lu = $('div#thread-group').attr('data');
		timeID = setInterval(function() {
			var no = $(LB[index]).attr('id').substr(1);
			$(LB[index]).load(lu + no);
			index++;
			if (!LB[index]) {
				clearInterval(timeID);
			}
		}, intVal);
	}
});

