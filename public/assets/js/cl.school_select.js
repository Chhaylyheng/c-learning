$(document).ready( function() {
	$("#form_t_school,#form_s_school,#form_c_school").autocomplete({
		delay: 300,
		minLength: 2,
		highlight: true,
		source: function(req, resp){
			$.ajax({
				url: "/t/ajax/schoolname.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					param1: req.term
				},
				success: function(o){
					resp(o);
				},
				error: function(xhr, ts, err){
					if (xhr.status == 406) {
						var o = xhr.responseJSON;
						resp(o);
						return;
					}
					resp();
				}
			});
		},
		search: function(event, ui){
			if (event.keyCode == 229) return false;
				return true;
		},
		open: function() {
			$(this).removeClass("ui-corner-all");
		}
	})
	.keyup(function(event){
		if (event.keyCode == 13) {
			$(this).autocomplete("search");
		}
	});
/*
	$("#form_t_school,#form_s_school").bind("blur keyup", function() {
		var Sel = $("#form_t_dept,#form_s_dept");
		$.ajax({
			url: "/t/ajax/deptlist.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"college": $(this).val()
			},
			success: function(o){
				Sel.empty();
				var res = o.res;
				switch (o.err)
				{
					case -3:
						Sel.append('<option value="">大学を先に指定してください</option>');
					break;
					case -2:
						Sel.append('<option value="">大学の指定が誤っています</option>');
					break;
					case -1:
						Sel.append('<option value="">学部指定なし</option>');
					break;
					case 0:
						for (var i = 0; i < res.length; i++) {
							Sel.append('<option value="'+res[i].dmName+'">'+res[i].dmName+'</option>');
						}
					break;
				}
			},
			error: function(xhr, ts, err){
				Sel.empty();
			}
		});
	});
*/
});
/*
$(document).ready( function() {
	$("#form_c_school").bind("blur keyup", function() {
		var Sel = $("#form_c_dept");
		var Sel2 = $("#form_c_period");
		var Sel3 = $("#form_c_hour");
		$.ajax({
			url: "/t/ajax/deptformlist.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"college": $(this).val()
			},
			success: function(o){
				Sel.empty();
				Sel2.empty();
				Sel3.empty();
				var res = o.res;
				switch (o.err)
				{
					case -3:
						Sel.append('<option value="">大学を先に指定してください</option>');
						Sel2.append('<option value="">大学を先に指定してください</option>');
						Sel3.append('<option value="">大学を先に指定してください</option>');
					break;
					case -2:
						Sel.append('<option value="">大学の指定が誤っています</option>');
						Sel2.append('<option value="">大学の指定が誤っています</option>');
						Sel3.append('<option value="">大学の指定が誤っています</option>');
					break;
					case -1:
						Sel.append('<option value="">指定大学の学部情報が見つかりませんでした</option>');
						Sel2.append('<option value="">指定大学の学部情報が見つかりませんでした</option>');
						Sel3.append('<option value="">指定大学の学部情報が見つかりませんでした</option>');
					break;
					case 0:
						var dept = res.dept;
						var period = res.period;
						var hour = res.hour;
						for (var i = 0; i < dept.length; i++) {
							Sel.append('<option value="'+dept[i].dmName+'">'+dept[i].dmName+'</option>');
						}
						for (var i = 0; i < period.length; i++) {
							Sel2.append('<option value="'+period[i].dpNO+'">'+period[i].dpText+'</option>');
						}
						for (var i = 0; i < hour.length; i++) {
							Sel3.append('<option value="'+hour[i].dhNO+'">'+hour[i].dhText+'</option>');
						}
					break;
				}
			},
			error: function(xhr, ts, err){
				Sel.empty();
				Sel2.empty();
				Sel3.empty();
			}
		});
	});

	$("#form_c_dept").bind("change", function() {
		var Sel2 = $("#form_c_period");
		var Sel3 = $("#form_c_hour");
		$.ajax({
			url: "/t/ajax/periodlist.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"college": $("#form_c_college").val()
				,"dept": $(this).val()
			},
			success: function(o){
				Sel2.empty();
				Sel3.empty();
				var res = o.res;
				switch (o.err)
				{
					case -3:
						Sel2.append('<option value="">大学を先に指定してください</option>');
						Sel3.append('<option value="">大学を先に指定してください</option>');
					break;
					case -2:
						Sel2.append('<option value="">大学と学部の指定が誤っています</option>');
						Sel3.append('<option value="">大学と学部の指定が誤っています</option>');
					break;
					case 0:
						var period = res.period;
						var hour = res.hour;
						for (var i = 0; i < period.length; i++) {
							Sel2.append('<option value="'+period[i].dpNO+'">'+period[i].dpText+'</option>');
						}
						for (var i = 0; i < hour.length; i++) {
							Sel3.append('<option value="'+hour[i].dhNO+'">'+hour[i].dhText+'</option>');
						}
					break;
				}
			},
			error: function(xhr, ts, err){
				Sel2.empty();
				Sel3.empty();
			}
		});
	});
});
*/

