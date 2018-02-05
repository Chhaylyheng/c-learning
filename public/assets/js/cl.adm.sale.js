$(function() {
	$('.sale-ymset').click(function() {
		var y = $('#year+a').text();
		var m = $('#month+a').text();

		$('#sale-ym').val(y+'/'+m);
		return;
	});
});
