$(function() {
	$('.geoButton').click(function() {
		if ($(this).hasClass('gis')) {
			if (navigator.geolocation) {
				// 現在の位置情報取得を実施
				navigator.geolocation.getCurrentPosition(
					// 位置情報取得成功時
					function (pos) {
						$('input[name=geoLat]').val(pos.coords.latitude);
						$('input[name=geoLon]').val(pos.coords.longitude);
						var intID = setInterval(function() {
							if ($('input[name=geoLat]').val() != "" && $('input[name=geoLon]').val() != "") {
								clearInterval(intID);
								$('#attendForm').submit();
								return false;
							}
						},50);
					},
					// 位置情報取得失敗時
					function (error) {
						var message = "";
						switch (error.code) {
							// Geolocationの使用が許可されない場合
							case error.PERMISSION_DENIED:
								message = $.i18n.prop('cl_s_class_geoButton_1');
								alert(message);
							break;
							// 位置情報が取得できない場合（タイムアウトなど）
							case error.POSITION_UNAVAILABLE:
							case error.PERMISSION_DENIED_TIMEOUT:
							default:
								$('#attendForm').submit();
							break;
						}
						return false;
					},
					{
						enableHighAccuracy: true,
						timeout : 5000
					}
				);
				return false;
			}
		}
		$('#attendForm').submit();
		return false;
	});
});

