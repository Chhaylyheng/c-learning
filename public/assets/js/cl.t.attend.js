var Map = null;
var removeMap = [];

$(function() {
	$('.keygen').on('click', function() {
		var sKeyNum = "";
		for (i = 0; i < 4; i++) {
			sKeyNum += Math.floor(Math.random() * 10);
		}
		$('.keyfield').val(sKeyNum);
		return false;
	});

	$('#datepick').datepicker({
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
	});

	$('#datepick2').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: 'yy/mm/dd',
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});

	if ($('.timepick').get(0)) {
		$('.timepick').timepicker({
			'timeFormat': 'H:i',
			'minTime': '6:00',
			'maxTime': '22:55',
			'forceRoundTime': true,
			'step': 5,
		});
	}

	$('.geochk').on('change', function() {
		if ($(".geochk").prop('checked')) {
			$('#map_canvas').parent('div').show();
			if (Map != null) {
				google.maps.event.trigger(Map, 'resize');
			} else {
				GMAP_initialize($('#map_canvas').attr('lat'),$('#map_canvas').attr('lon'),'marker');
			}
		} else {
			$('#map_canvas').parent('div').hide();
		}
	});

	if ($('#map_canvas').get(0)) {
		if ($(".geochk").get(0)) {
			if ($(".geochk").prop('checked')) {
				$('#map_canvas').parent('div').show();
				GMAP_initialize($('#map_canvas').attr('lat'),$('#map_canvas').attr('lon'),'marker');
			} else {
				$('#map_canvas').parent('div').hide();
			}
		} else {
			GMAP_initialize($('#map_canvas').attr('lat'),$('#map_canvas').attr('lon'),'view');
		}
	}

	$('.MarkerJump').on('click',function() {
		var lat = $(this).attr('lat');
		var lon = $(this).attr('lon');
		var MapCenter = new google.maps.LatLng(lat,lon);
		Map.setOptions({ center: MapCenter });
		return false;
	});

	if (!navigator.geolocation) {
		$('.CurrentPosition').hide();
	}

	$('.CurrentPosition').on('click',function() {
		MAP_CurrentBtn(true);
		navigator.geolocation.getCurrentPosition(
			function(pos) {
				// 成功した場合
				var curLat = pos.coords.latitude;
				var curLon = pos.coords.longitude;
				GMAP_initialize(curLat,curLon,'marker');
				$('input[name=lat]').val(curLat);
				$('input[name=lon]').val(curLon);
				MAP_CurrentBtn(false);
				return;
			},
			function(error) {
				// 失敗した場合
				addAlert($.i18n.prop('cl_t_attend_CurrentPosition_1'),'alert');
				MAP_CurrentBtn(false);
				return;
			}
		);
		return;
	});

	$('.AddressSubmit').on('click',function() {
		var address = $('.AddressPosition').val();
		if (address == '') {
			addAlert($.i18n.prop('cl_t_attend_AddressSubmit_1'),'alert');
			return;
		}

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode(
			{
				'address': address,
				'language': 'ja',
				'region': 'jp'
			},
			function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var adrLat = results[0].geometry.location.lat();
					var adrLon = results[0].geometry.location.lng();
					GMAP_initialize(adrLat,adrLon,'marker');
					$('input[name=lat]').val(adrLat);
					$('input[name=lon]').val(adrLon);
					return;
				} else {
					addAlert($.i18n.prop('cl_t_attend_AddressSubmit_2'),'alert');
					return;
				}
			}
		);
		return;
	});



	$('.attendstate-dropdown-toggle').on('click',function() {
		var id = $(this).attr('id');
		var list = $('.dropdown-list-attendstate');
		var obj = $('.dropdown-list-attendstate').attr('obj');

		if (id == obj && list.css('display') == 'block') {
			list.slideUp('fast');
			return;
		}

		list.hide();
		list.attr('obj',id);

		var offset = $(this).offset();
		var height = $(this).outerHeight();

		list.css({
			top: (parseInt(offset.top)+height-49)+'px',
			left: parseInt(offset.left)+'px',
		});
		list.slideDown('fast');
	});

	$('.SwitchAttendState').on('click',function() {
		$('#stErr').hide();

		var sMode = $('#attend-mode').attr('val');
		var list = $(this).parents('ul.dropdown-list-attendstate');
		var id = $(list).attr('obj');
		var obj = $(this).attr('obj').split('_');
		var obj2 = id.split('_');

		var btn = $('#'+id);

		$.ajax({
			url: "/t/ajax/SwitchAttendState.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"ct":    obj[0],
				"state": obj[1],
				"st":    obj2[0],
				"date":  obj2[1],
				"no":    obj2[2],
			},
			success: function(o){
				var res = o.res;
				switch (o.err)
				{
					case -3:
						addAlert($.i18n.prop('cl_t_attend_SwitchAttendState_1'),'alert');
					break;
					case -2:
						addAlert($.i18n.prop('cl_t_attend_SwitchAttendState_2'),'alert');
					break;
					case -1:
						addAlert($.i18n.prop('cl_t_attend_SwitchAttendState_3'),'alert');
					break;
					case 0:
						if (sMode == 'history') {
							var stNum = $('.stNum').text();
							var aNum  = $('.aNum').text();

							$(btn).find('div').text(res.amShort);
							$('#'+res.stID+'_Num span:first-child').text(res.stAbNum);
							if (aNum > 0) {
								$('#'+res.stID+'_Num span:last-child').text(Math.round((res.stAbNum/aNum)*1000)/10);
							}
							$('#'+res.abDate+'_'+res.acNO+'_Num').text(res.AbNum);
							if (stNum > 0) {
								$('#'+res.abDate+'_'+res.acNO+'_Num').nextAll('span').text(Math.round((res.AbNum/stNum)*1000)/10);
							}
						} else {
							$(btn).find('div').text(res.amName);
							$('#'+res.stID+'_Date').text(res.abModifyDate);
						}
						$(btn).removeClass('font-red');
						$(btn).removeClass('font-green');
						$(btn).removeClass('font-blue');
						$(btn).addClass(res.amClass);

					break;
				}
				$(list).slideUp('fast');
				return false;
			},
			error: function(xhr, ts, err){
				addAlert('Network Access Error ['+err+']','alert');
				return false;
			}
		});
		return false;
	});

});

function GMAP_initialize(lat, lon, mode) {
	var MapCenter = new google.maps.LatLng(lat,lon);
	var Options = {
		zoom: 18,
		streetViewControl: false,
		mapTypeControl: false,
		disableDoubleClickZoom: false,
		center: MapCenter,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		noClear: true
	};
	Map = new google.maps.Map(document.getElementById("map_canvas"), Options);
	var CenterMarker = new google.maps.Marker({
		position: MapCenter,
		map: Map,
		icon: 'http://maps.google.co.jp/mapfiles/ms/icons/green-dot.png',
	});

	var input = document.getElementById('MapSearchText');
	var options = {
		types: ['(cities)'],
	};
	autocomplete = new google.maps.places.Autocomplete(input, options);

	if (mode == 'marker') {
		CenterMarker.setOptions({ draggable: true});
		removeMap = CenterMarker;
		google.maps.event.addListener(Map, 'click', MAP_setMarker);
		google.maps.event.addListener(CenterMarker, 'dragend', MAP_setDrag);
	} else if (mode == 'view') {
		GMAP_MarkerSet(Map);
	}
}

function MAP_setMarker(event) {
	var latlng = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
	var Marker = new google.maps.Marker({
		map:Map,
		draggable: true,
		position: latlng,
		icon: 'http://maps.google.co.jp/mapfiles/ms/icons/green-dot.png',
	});
	Marker.setMap(Map);
	removeMap.setMap(null);
	removeMap = Marker;
	$('#map_canvas').attr('lat',event.latLng.lat());
	$('#map_canvas').attr('lon',event.latLng.lng());
	$('input[name=lat]').val(event.latLng.lat());
	$('input[name=lon]').val(event.latLng.lng());
	google.maps.event.addListener(Marker, 'dragend', MAP_setDrag);
}

function MAP_setDrag(event) {
	$('#map_canvas').attr('lat',event.latLng.lat());
	$('#map_canvas').attr('lon',event.latLng.lng());
	$('input[name=lat]').val(event.latLng.lat());
	$('input[name=lon]').val(event.latLng.lng());
}

function MAP_CurrentBtn(onoff) {
	if (onoff) {
		$('.CurrentPosition > i').removeClass('fa-map-marker');
		$('.CurrentPosition > i').addClass('fa-spinner');
		$('.CurrentPosition > i').addClass('fa-pulse');
		$('.CurrentPosition > span').text($.i18n.prop('cl_t_attend_Map_CurrentBtn_1'));
	} else {
		$('.CurrentPosition > i').removeClass('fa-spinner');
		$('.CurrentPosition > i').removeClass('fa-pulse');
		$('.CurrentPosition > i').addClass('fa-map-marker');
		$('.CurrentPosition > span').text($.i18n.prop('cl_t_attend_Map_CurrentBtn_2'));
	}
}