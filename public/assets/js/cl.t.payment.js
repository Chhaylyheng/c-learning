$(function() {
	$('#estimate-check button').on('click', function() {

		$('#estimate-check input[name=mode]').val($(this).val());

		$('#estimate-check button').css({'cursor': 'default'});
		$('#estimate-check button').attr('disabled','disabled');

		$('#estimate-check').submit();
		return false;
	});

	/* 見積・購入履歴 */
	$(".estimatedelete").click(function() {
		var url = $(this).attr('href');
		confirm($.i18n.prop('cl_t_payment_setimatedelete_1'), function(bOK) {
			if (!bOK) {
				return false;
			}
			location.href = url;
			return false;
		});
		return false;
	});
	$(".purchase").click(function() {
		var url = $(this).attr('href');
		var bill = $(this).attr('billing');
		var msg = '';
		var shadow = false;

		switch (bill) {
		case '1':
			return true;
			break;
		case '2':
			msg = 'cl_t_payment_purchase_1';
			break;
		case '4':
			msg = 'cl_t_payment_purchase_2';
			shadow = true;
			break;
		}

		confirm($.i18n.prop(msg), function(bOK) {
			if (!bOK) {
				return false;
			}
			if (shadow) {
				shadowMask('on',$.i18n.prop('cl_t_payment_purchase_3'));
			}
			location.href = url;
			return false;
		});
		return false;
	});

	$('.purchaseBtn').on('click', function() {
		var bill = $(this).attr('billing');

		switch (bill) {
		case '1':
		case '2':
			break;
		case '4':
			shadowMask('on',$.i18n.prop('cl_t_payment_purchase_3'));
			break;
		}
		return true;
	});

	/* カード更新 */
	$('.CardEdit').click(function() {
		$('#AddCard').find('input[name=card_number]').val('');
		$('#AddCard').find('select[name=card_month]').val('');
		$('#AddCard').find('select[name=card_month]+a').text('--');
		$('#AddCard').find('select[name=card_year]').val('');
		$('#AddCard').find('select[name=card_year]+a').text('----');
		$('#AddCard').find('input[name=card_seqcode]').val('');
		$('#cardInfo').hide();
		$('#cardinfo').hide();
		$('#cardinput').show();
	});

	/* カード登録 */
	$('.CardSave').click(function() {
		shadowMask('on',$.i18n.prop('cl_t_payment_CardSave_1'));

		var tt = $('#AddCard').attr('data');
		var cN = convertAlpha($('#AddCard').find('input[name=card_number]').val())*1;
		var cM = $('#AddCard').find('select[name=card_month]').val();
		var cY = $('#AddCard').find('select[name=card_year]').val();
		var cC = $('#AddCard').find('input[name=card_seqcode]').val();

		var bErr = false;

		if (!$.isNumeric(cN) || (cN.toString().length < 14 && cN.toString().length > 16)) {
			$('#AddCard').find('input[name=card_number]').css('color','red');
			$('#AddCard').find('input[name=card_number]').css('border-color','red');
			bErr = true;
		} else {
			$('#AddCard').find('input[name=card_number]').val(cN);
			$('#AddCard').find('input[name=card_number]').css('color','');
			$('#AddCard').find('input[name=card_number]').css('border-color','');
		}
		if (!cM) {
			$('#AddCard').find('select[name=card_month]+a').css('color','red');
			$('#AddCard').find('select[name=card_month]+a').css('border-color','red');
			bErr = true;
		} else {
			$('#AddCard').find('select[name=card_month]+a').css('color','');
			$('#AddCard').find('select[name=card_month]+a').css('border-color','');
		}
		if (!cY) {
			$('#AddCard').find('select[name=card_year]+a').css('color','red');
			$('#AddCard').find('select[name=card_year]+a').css('border-color','red');
			bErr = true;
		} else {
			$('#AddCard').find('select[name=card_year]+a').css('color','');
			$('#AddCard').find('select[name=card_year]+a').css('border-color','');
		}
		if (!$.isNumeric(cC) || (cC.toString().length != 3 && cC.toString().length != 4)) {
			$('#AddCard').find('input[name=card_seqcode]').css('color','red');
			$('#AddCard').find('input[name=card_seqcode]').css('border-color','red');
			bErr = true;
		} else {
			$('#AddCard').find('input[name=card_seqcode]').css('color','');
			$('#AddCard').find('input[name=card_seqcode]').css('border-color','');
		}

		if (bErr) {
			$('#cardErr').text($.i18n.prop('cl_t_payment_CardSave_2'));
			$('#cardErr').show();
			shadowMask('off');
			return false;
		}

		$('#cardErr').hide();

		$.ajax({
			url: "/t/ajax/payment/CardRegist.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"cN":cN,
				"cM":cM,
				"cY":cY,
				"cC":cC,
				"tt":tt
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					$('#cardErr').text(o.msg);
					$('#cardErr').show();
					shadowMask('off');
					return false;
				}
				$('#cardInfo p').text($.i18n.prop('cl_t_payment_CardSave_3'));
				$('#cardInfo').show();
				$('#cardNumber > span').text(res.CardNo);
				$('#cardExpire > span').text(res.Expire);
				$('#cardinput').hide();
				$('#cardinfo').show();
				shadowMask('off');
				return false;
			},
			error: function(xhr, ts, err){
				$('#cardErr').text($.i18n.prop('cl_t_payment_Common_1'));
				$('#cardErr').show();
				shadowMask('off');
				return false;
			}
		});
		return false;
	});

	/* 購入処理 */
	$('#Purchase').submit(function() {
		shadowMask('on',$.i18n.prop('cl_t_payment_PurchaseSubmit_1'));
		$('#cardInfo').hide();
		$('#purchaseErr').hide();
		var tt = $(this).attr('data');
		var eN = $(this).find('input[name=eNO]').val();
		var pw = $(this).find('input[name=passwd]').val();

		$.ajax({
			url: "/t/ajax/payment/CardPurchase.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				"eN":eN,
				"pw":pw,
				"tt":tt
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					$('#purchaseErr').text(o.msg);
					$('#purchaseErr').show();
					shadowMask('off');
					return false;
				}
				$('#cardinfo').hide();
				$('#cardinfo').remove();
				$('#cardinput').remove();
				$('#purchaseInfo p').text(res.fin);
				$('#purchaseInfo').show();
				$('#purchaseinfo').show();
				shadowMask('off');

				shadowMask('on');
				$('#shadowMask')
				.append(
					$('<div>')
					.attr('id','content')
					.addClass('purchaseMsgBox')
					.append(
						$('<p>')
						.addClass('font-blue msg')
						.html($.i18n.prop('cl_t_payment_PurchaseSubmit_2'))
					)
					.append(
						$('<p>')
						.addClass('button-box mt16')
						.append(
							$('<button>')
							.addClass('button na confirm')
							.text($.i18n.prop('cl_t_payment_Common_2'))
							.click(function() {
								$('#shadowMask').hide();
								$('#shadowMask').remove();
							})
						)
					)
				);

				return false;
			},
			error: function(xhr, ts, err){
				$('#purchaseErr').text($.i18n.prop('cl_t_payment_Common_1'));
				$('#purchaseErr').show();
				shadowMask('off');
				return false;
			}
		});
		return false;
	});

	var cn = 0;
	var sn = 0;
	var pt = 0;
	var cc = 0;
	var rg = 0;
	var bi = 0;

/************************************************************
 *
 * Contract
 *
 ************************************************************/

	/* クーポンコード判定 */
	$('#contract-form #coupon-code').on('keyup', function() {
		$(this).val($(this).val().toUpperCase());

		var fm = $('#contract-form');
		var cnc = fm.find('#class-num').val();
		var rgc = fm.find('#contract-range').val();
		var ptc = fm.find('input[name=pt]').val();
		var bic = fm.find('input[name=billing]:checked').val();
		var ccc = 0;

		var snc = 0;
		if (fm.find('#stu-num').get(0)) {
			snc = fm.find('#stu-num').val();
		}

		if ($(this).val().length == 10)
		{
			ccc = CouponCheck(pt, $(this), rgc, bic);
		}
		else
		{
			$('p.coupon-text').text('');
			$(this).next('div.coupon-check').find('i').hide();
			$(this).next('div.coupon-check').find('i.fa-circle-o').show();
		}
		if (ptc != pt || cnc != cn || snc != sn || rgc != rg || ccc != cc || bic != bi) {
			pt = ptc;
			cn = cnc;
			sn = snc;
			rg = rgc;
			cc = ccc;
			bi = bic;
			ContractMath(pt,cn,sn,rg,cc);
		}
		return false;
	});

	/* 金額計算 */
	if ($('#contract-form').get(0)) {
		var fm = $('#contract-form');
		cn = fm.find('#class-num').val();
		rg = fm.find('#contract-range').val();
		pt = fm.find('input[name=pt]').val();
		bi = fm.find('input[name=billing]:checked').val();
		cc = 0;
		if (fm.find('#coupon-code').get(0) && fm.find('#coupon-code').val().length == 10) {
			cc = CouponCheck(pt, fm.find('#coupon-code'), rg, bi);
		}
		sn = 0;
		if (fm.find('#stu-num').get(0)) {
			sn = fm.find('#stu-num').val();
		}
		ContractMath(pt,cn,sn,rg,cc);
	}

	$(document).on('change','#contract-form input[name=billing],#contract-form #contract-range,#contract-form #class-num,#contract-form #stu-num', function() {
		var fm = $('#contract-form');
		cn = fm.find('#class-num').val();
		rg = fm.find('#contract-range').val();
		pt = fm.find('input[name=pt]').val();
		bi = fm.find('input[name=billing]:checked').val();
		cc = 0;
		if (fm.find('#coupon-code').get(0) && fm.find('#coupon-code').val().length == 10) {
			cc = CouponCheck(pt, fm.find('#coupon-code'), rg, bi);
		}
		sn = 0;
		if (fm.find('#stu-num').get(0)) {
			sn = fm.find('#stu-num').val();
		}
		ContractMath(pt,cn,sn,rg,cc);
	});

	function ContractMath(ptc,cnc,snc,rgc,ccc) {
		var fm = $('#contract-form');
		var data = fm.attr('data').split('|');
		var tax = 1.00 + Number(data[0]);

		var spr = 0;

		$.ajax({
			async: false,
			url: "/t/ajax/payment/ContractMath.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				'pt': ptc,
				'cn': cnc,
				'sn': snc,
				'rg': rgc,
				'cc': ccc
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					$('#mathContractErr').text(o.msg);
					$('#mathContractErr').show();
				}

				fm.find('.class-price').text(number_format(res.ClassPrice));
				fm.find('.stu-price').text(number_format(res.StuPrice));
				fm.find('#sum-price').text(number_format(res.Price));
				fm.find('#exp').text(res.Calc);
				fm.find('.range-start').text(res.start);
				fm.find('.range-term').text(res.term);

				spr = res.Price;
			},
			error: function(xhr, ts, err){
				$('#mathContractErr').text($.i18n.prop('cl_t_payment_Common_1'));
				$('#mathContractErr').show();
				return false;
			}
		});

		var bl = Number(fm.find('input[name=billing]:checked').attr('value'));

		$('#sum-pay').text(number_format(Math.floor(spr*tax)));

		if (spr > 0 && bl > 0) {
			$('#pay-submit').removeAttr('disabled');
			$('#pay-submit').removeClass('cancel');
			$('#pay-submit').addClass('do');
			$('#mathContractErr').hide();
		}
		else
		{
			$('#pay-submit').attr('disabled','disabled');
			$('#pay-submit').removeClass('do');
			$('#pay-submit').addClass('cancel');
			if (bl <= 0) {
				var msg = $.i18n.prop('cl_t_payment_Common_4');
				$('#mathContractErr').text(msg);
				$('#mathContractErr').show();
			}
		}
	}

/************************************************************
 *
 * Change
 *
 ************************************************************/

	/* クーポンコード判定 */
	$('#change-form #coupon-code').on('keyup', function() {
		$(this).val($(this).val().toUpperCase());

		var fm = $('#change-form');
		var cnc = fm.find('#class-num').val();
		var snc = 0;
		var ptc = fm.find('input[name=pt]').val();
		var rgc = fm.find('.con-range > span:first').text();
		var bic = fm.find('input[name=billing]:checked').val();
		var ccc = 0;

		if ($(this).val().length == 10)
		{
			ccc = CouponCheck(pt, $(this), rgc, bic);
		}
		else
		{
			$('p.coupon-text').text('');
			$(this).next('div.coupon-check').find('i').hide();
			$(this).next('div.coupon-check').find('i.fa-circle-o').show();
		}

		if (fm.find('#stu-num').get(0)) {
			snc = fm.find('#stu-num').val();
		}

		if (ptc != pt || cnc != cn || snc != sn || ccc != cc) {
			pt = ptc;
			cn = cnc;
			sn = snc;
			cc = ccc;
			ChangeMath(pt,cn,sn,cc);
		}
		return false;
	});

	/* 金額計算 */
	if ($('#change-form').get(0)) {
		var fm = $('#change-form');
		cn = fm.find('#class-num').val();
		pt = fm.find('input[name=pt]').val();
		rg = fm.find('.con-range > span:first').text();
		bi = fm.find('input[name=billing]:checked').val();
		cc = 0;
		if (fm.find('#coupon-code').get(0) && fm.find('#coupon-code').val().length == 10) {
			cc = CouponCheck(pt, fm.find('#coupon-code'), rg, bi);
		}
		sn = 0;
		if (fm.find('#stu-num').get(0)) {
			sn = fm.find('#stu-num').val();
		}
		ChangeMath(pt,cn,sn,cc);
	}

	$(document).on('change','#change-form input[name=billing],#change-form #class-num,#change-form #stu-num', function() {
		var fm = $('#change-form');
		cn = fm.find('#class-num').val();
		pt = fm.find('input[name=pt]').val();
		rg = fm.find('.con-range > span:first').text();
		bi = fm.find('input[name=billing]:checked').val();
		cc = 0;
		if (fm.find('#coupon-code').get(0) && fm.find('#coupon-code').val().length == 10) {
			cc = CouponCheck(pt, fm.find('#coupon-code'), rg, bi);
		}
		sn = 0;
		if (fm.find('#stu-num').get(0)) {
			sn = fm.find('#stu-num').val();
		}
		ChangeMath(pt,cn,sn,cc);
	});

	function ChangeMath(ptc,cnc,snc,ccc) {
		var fm = $('#change-form');
		var data = fm.attr('data').split('|');
		var tax = 1.00 + Number(data[0]);
		var spr = 0;

		$.ajax({
			async: false,
			url: "/t/ajax/payment/ChangeMath.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				'pt': ptc,
				'cn': cnc,
				'sn': snc,
				'cc': ccc
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					$('#mathChangeErr').text(o.msg);
					$('#mathChangeErr').show();
				}

				fm.find('.class-price').text(number_format(res.ClassPrice));
				fm.find('.stu-price').text(number_format(res.StuPrice));
				fm.find('.class-price-different').text(number_format(res.ClassPriceDifferent));
				fm.find('.stu-price-different').text(number_format(res.StuPriceDifferent));
				fm.find('#sum-price').text(number_format(res.Price));
				fm.find('#exp').text(res.Calc);

				spr = res.Price;
			},
			error: function(xhr, ts, err){
				$('#mathChangeErr').text($.i18n.prop('cl_t_payment_Common_1'));
				$('#mathChangeErr').show();
				return false;
			}
		});

		if (spr > 0) {
			fm.find('.directGo').hide();
			fm.find('.estimateGo').show();

			var bl = Number(fm.find('input[name=billing]:checked').attr('value'));

			$('#sum-pay').text(number_format(Math.floor(spr*tax)));

			if (spr > 0 && bl > 0) {
				$('#pay-submit').removeAttr('disabled');
				$('#pay-submit').removeClass('cancel');
				$('#pay-submit').addClass('do');
				$('#mathChangeErr').hide();
			}
			else
			{
				$('#pay-submit').attr('disabled','disabled');
				$('#pay-submit').removeClass('do');
				$('#pay-submit').addClass('cancel');
				if (bl <= 0) {
					var msg = $.i18n.prop('cl_t_payment_Common_4');
					$('#mathChangeErr').text(msg);
					$('#mathChangeErr').show();
				}
			}
		} else {
			fm.find('.estimateGo').hide();
			fm.find('.directGo').show();

			$('#sum-pay').text(number_format(Math.floor(spr*tax)));

			$('#change-submit').removeAttr('disabled');
			$('#change-submit').removeClass('cancel');
			$('#change-submit').addClass('do');
			$('#mathChangeErr').hide();
		}
	}


	/************************************************************
	 *
	 * Add
	 *
	 ************************************************************/

		/* クーポンコード判定 */
		$('#add-form #coupon-code').on('keyup', function() {
			$(this).val($(this).val().toUpperCase());

			var fm = $('#add-form');
			var cnc = fm.find('#class-num').val();
			var ptc = fm.find('input[name=pt]').val();
			var rgc = fm.find('.con-range > span:first').text();
			var bic = fm.find('input[name=billing]:checked').val();
			var ccc = 0;

			if ($(this).val().length == 10)
			{
				ccc = CouponCheck(pt, $(this), rgc, bic);
			}
			else
			{
				$('p.coupon-text').text('');
				$(this).next('div.coupon-check').find('i').hide();
				$(this).next('div.coupon-check').find('i.fa-circle-o').show();
			}
			if (ptc != pt || cnc != cn || ccc != cc) {
				pt = ptc;
				cn = cnc;
				cc = ccc;
				AddMath(pt,cn,cc);
			}
			return false;
		});

		/* 金額計算 */
		if ($('#add-form').get(0)) {
			var fm = $('#add-form');
			cn = fm.find('#class-num').val();
			pt = fm.find('input[name=pt]').val();
			rg = fm.find('.con-range > span:first').text();
			bi = fm.find('input[name=billing]:checked').val();

			cc = 0;
			if (fm.find('#coupon-code').get(0) && fm.find('#coupon-code').val().length == 10) {
				cc = CouponCheck(pt, fm.find('#coupon-code'), rg, bi);
			}
			AddMath(pt,cn,cc);
		}

		$(document).on('change','#add-form input[name=billing],#add-form #class-num', function() {
			var fm = $('#add-form');
			cn = fm.find('#class-num').val();
			pt = fm.find('input[name=pt]').val();
			rg = fm.find('.con-range > span:first').text();
			bi = fm.find('input[name=billing]:checked').val();

			cc = 0;
			if (fm.find('#coupon-code').get(0) && fm.find('#coupon-code').val().length == 10) {
				cc = CouponCheck(pt, fm.find('#coupon-code'), rg, bi);
			}
			AddMath(pt,cn,cc);
		});

		function AddMath(ptc,cnc,ccc) {
			var fm = $('#add-form');
			var data = fm.attr('data').split('|');
			var tax = 1.00 + Number(data[0]);
			var spr = 0;

			$.ajax({
				async: false,
				url: "/t/ajax/payment/AddMath.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: {
					'pt': ptc,
					'cn': cnc,
					'cc': ccc
				},
				success: function(o){
					var res = o.res;
					if (o.err != 0) {
						$('#mathAddErr').text(o.msg);
						$('#mathAddErr').show();
					}

					fm.find('.class-price').text(number_format(res.ClassPrice));
					fm.find('#sum-price').text(number_format(res.Price));
					fm.find('#exp').text(res.Calc);

					spr = res.Price;
				},
				error: function(xhr, ts, err){
					$('#mathAddErr').text($.i18n.prop('cl_t_payment_Common_1'));
					$('#mathAddErr').show();
					return false;
				}
			});

			if (spr > 0) {
				var bl = Number(fm.find('input[name=billing]:checked').attr('value'));

				$('#sum-pay').text(number_format(Math.floor(spr*tax)));

				if (spr > 0 && bl > 0) {
					$('#pay-submit').removeAttr('disabled');
					$('#pay-submit').removeClass('cancel');
					$('#pay-submit').addClass('do');
					$('#mathAddErr').hide();
				}
				else
				{
					$('#pay-submit').attr('disabled','disabled');
					$('#pay-submit').removeClass('do');
					$('#pay-submit').addClass('cancel');
					if (bl <= 0) {
						var msg = $.i18n.prop('cl_t_payment_Common_4');
						$('#mathAddErr').text(msg);
						$('#mathAddErr').show();
					}
				}
			} else {
				$('#sum-pay').text(number_format(Math.floor(spr*tax)));

				$('#change-submit').removeAttr('disabled');
				$('#change-submit').removeClass('cancel');
				$('#change-submit').addClass('do');
				$('#mathAddErr').hide();
			}
		}

















	/* クーポンチェック */
	function CouponCheck(pt, coupon, rg, bi) {
		var code = coupon.val();
		var icon = coupon.next('div.coupon-check');
		var cn = 0;
		var ctext = $('p.coupon-text');
		ctext.text('');

		icon.find('i').hide();
		icon.find('i.fa-spinner').show();

		if (!bi) {
			bi = 0;
		}

		$.ajax({
			async: false,
			url: "/t/ajax/payment/CouponCheck.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: {
				'pt': pt,
				'code': code,
				'rg': rg,
				'bi': bi,
			},
			success: function(o){
				var res = o.res;
				if (o.err != 0) {
					$('#mathContractErr').text(o.msg);
					$('#mathContractErr').show();

					icon.find('i').hide();
					icon.find('i.fa-circle-o').show();

					return false;
				}
				ctext.text(res.text);
				if (res.discount > 0) {
					cn = res.discount;
					icon.find('i').hide();
					icon.find('i.fa-check-circle').show();
					return false;
				} else {
					icon.find('i').hide();
					icon.find('i.fa-circle-o').show();
					return false;
				}
			},
			error: function(xhr, ts, err){
				$('#mathPointErr').text($.i18n.prop('cl_t_payment_Common_1'));
				$('#mathPointErr').show();
				icon.find('i').hide();
				icon.find('i.fa-circle-o').show();
				return false;
			}
		});
		return cn;
	}

	/* CLポイントチャージ */
	var basePr = 6000;
	var cMonth = 6;
	var cPrice = 1000;

	if ($('#charge-form').get(0)) {
		$('#charge-form input[name=price]').val(basePr);
		PointMath(0);
	}
	$('#charge-form input[name=point]').change(function() {
		PointMath(0,'pt');
	});
	$(document).on('click', '#charge-form #class-num .pulldown a', function() {
		var cNum = $(this).attr('value');
		var Price = (cNum * cPrice * cMonth) + basePr;
		var exp = '（'+cNum+' × '+(cPrice.toString().replace(/(\d)(?=(\d{3})+$)/g,'$1,'))+' × '+cMonth+'）＋ ' + basePr.toString().replace(/(\d)(?=(\d{3})+$)/g,'$1,');

		$('#exp').text(exp);
		$('#charge-form input[name=price]').val(Price);
		PointMath(0,'pr');
	});
	$('#charge-form input[name=price]').change(function() {
		PointMath(0,'pr');
	});
	$('#charge-form input[name=billing]').change(function() {
		PointMath(0);
	});
/*
	$('#charge-form div.pulldown a').click(function() {
		var cn = $(this).text();
		PointMath(cn);
	});
*/
	function PointMath(cn,mp) {
		var fm = $('#charge-form');
		var data = fm.attr('data').split('|');

		var up  = Number(data[0]);
		var np  = Number(data[1]);
		var tax = 1.00 + Number(data[2]);
//		var bline = Number(data[3]) * 1;

		var spo = convertAlpha(fm.find('input[name=point]').val())*1;
		var spr = convertAlpha(fm.find('input[name=price]').val())*1;

		if (mp == 'pt' || mp == 'pr') {
			if (mp == 'pt') {
				var post = { 'pt':spo };
			} else {
				var post = { 'pr':spr };
			}
			$.ajax({
				async: false,
				url: "/t/ajax/PointMath.json",
				type: "POST",
				cache: false,
				dataType: "json",
				data: post,
				success: function(o){
					var res = o.res;
					if (o.err != 0) {
						$('#mathPointErr').text(o.msg);
						$('#mathPointErr').show();
					}
					fm.find('input[name=point]').val(res.pt);
					fm.find('input[name=price]').val(res.pr);
					spo = res.pt;
					spr = res.pr;
				},
				error: function(xhr, ts, err){
					$('#mathPointErr').text($.i18n.prop('cl_t_payment_Common_1'));
					$('#mathPointErr').show();
					return false;
				}
			});
		}

		var bl = Number(fm.find('input[name=billing]:checked').attr('value'));

		if (!spo) {
			spo = 0;
		}
		if (!spr) {
			spr = 0;
		}
		if (!bl) {
			bl = 0;
		}

/*
		if (!cn) {
			cn = Number(fm.find('#class-num+a').text());
		}

		var pp = cn*up;
		var sp = np+spo;
		var mo = Number(Math.floor(sp/pp));
		var ms = '';


		if (mo > 12) {
			ms = Math.floor(mo/12)+'年'+(mo%12)+'ヶ月';
		} else {
			ms = mo+'ヶ月';
		}

		$('#sum-pt').text(number_format(sp));
		$('#pay-range').text(ms);
*/
		$('#sum-pay').text(number_format(Math.floor(spr*tax)));

//		if (spo >= bline && spr > 0 && bl > 0) {
		if (spr > 0 && bl > 0) {
			$('#pay-submit').removeAttr('disabled');
			$('#pay-submit').removeClass('cancel');
			$('#pay-submit').addClass('do');
			$('#mathPointErr').hide();
			/*
			fm.find('input[name=point]').css({
				'color':'',
				'border-color':''
			});
			*/
		}
		else
		{
			$('#pay-submit').attr('disabled','disabled');
			$('#pay-submit').removeClass('do');
			$('#pay-submit').addClass('cancel');
/*
			if (spo < bline) {
				var msg = 'チャージするCLポイントは、'+number_format(bline)+'ポイント以上で指定してください。';
				fm.find('input[name=point]').css({
					'color':'red',
					'border-color':'red'
				});
			} else
*/
			if (bl <= 0) {
				var msg = $.i18n.prop('cl_t_payment_Common_4');
			}
			$('#mathPointErr').text(msg);
			$('#mathPointErr').show();
		}
	}


	/* 見積作成・編集 */
	if ($('#estimate-form').get(0)) {
		EstimateSum();
	}
	$('#estimate-form').submit(function() {
		return EstimateSum();
	});
	$('.input-price,.input-num,.input-name,.input-unit').change(function() {
		EstimateSum();
	});

	$('#estimate-datepick').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: ((GetCookie('CL_LANG') == 'ja' || GetCookie('CL_LANG') == 'ct')? 'yy年m月d日':'d MM, yy'),
		defaultDate: null,
		maxDate: 'today',
		minDate: '-30d',
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});

	$('.detail-add').click(function() {
		var i = $('#estimate-detail .detail').length;
		if (i >= 8) {
			alert($.i18n.prop('cl_t_payment_detail_add_1'));
			return false;
		}
		var Item = $('#detail-base').clone(true);
		$('#estimate-detail .detail:last').after(Item);
		Item.removeAttr('id');
		Item.show();
		return false;
	});

	$('.detail-remove').click(function() {
		$(this).parents('tr').remove();
		EstimateSum();
		return false;
	});

	function EstimateSum() {
		var Prices = $('#estimate-detail .detail input.input-price');
		var k = 0;

		$.each(Prices,function() {
			var er = false;
			var price = convertAlpha($(this).val())*1;
			var num = convertAlpha($(this).parents('tr').find('.input-num').val())*1;
			var name = $(this).parents('tr').find('.input-name').val();
			var unit = $(this).parents('tr').find('.input-unit').val();

			if (!$.isNumeric(price)) {
				$(this).css('color','red');
				$(this).css('border-color','red');
				er = true;
			} else {
				$(this).val(price);
				$(this).css('color','');
				$(this).css('border-color','');
			}
			if (!$.isNumeric(num)) {
				$(this).parents('tr').find('.input-num').css('color','red');
				$(this).parents('tr').find('.input-num').css('border-color','red');
				er = true;
			} else {
				$(this).parents('tr').find('.input-num').css('color','');
				$(this).parents('tr').find('.input-num').css('border-color','');
				$(this).parents('tr').find('.input-num').val(num);
			}
			if (name == '') {
				$(this).parents('tr').find('.input-name').val('C-Learning'+$.i18n.prop('cl_t_payment_EstimateSum_1'));
			}
			if (unit == '') {
				$(this).parents('tr').find('.input-unit').val($.i18n.prop('cl_t_payment_EstimateSum_2'));
			}

			if (er) {
				$(this).parents('tr').find('.amount').html('&yen;─');
				return;
			}

			var sum = price * num;

			k += sum;

			$(this).parents('tr').find('.amount').html('&yen;'+number_format(sum));
		});

		var cnt = $('#estimate-detail .detail').length;
		var sum = $('#estimate-detail').attr('sum');

		if ($.isNumeric(k)) {
			$('#estimate-detail .amount-sum').html('&yen;'+number_format(sum - k));
		} else {
			$('#estimate-detail .amount-sum').html('&yen;─');
		}

		if (k == sum) {
			$('#estimate-detail .amount').removeClass('font-red');
			$('#estimate-detail .amount-sum').removeClass('font-red');
			$('#sum-price').removeClass('font-red');
			$('#estimate-submit').removeAttr('disabled');
			$('#estimate-submit').removeClass('cancel');
			$('#estimate-submit').addClass('do');
			$('#estimate-form').removeAttr('disabled');
			$('#estimateErr').hide();
			return true;
		} else {
			$('#estimate-detail .amount').addClass('font-red');
			$('#estimate-detail .amount-sum').addClass('font-red');
			$('#sum-price').addClass('font-red');
			$('#estimate-submit').attr('disabled','disabled');
			$('#estimate-submit').removeClass('do');
			$('#estimate-submit').addClass('cancel');
			$('#estimate-form').attr('disabled','disabled');
			$('#estimateErr').text($.i18n.prop('cl_t_payment_EstimateSum_3'));
			$('#estimateErr').show();
			return false;
		}
	}

	/* 納品書作成 */
	$('#license-datepick').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: ((GetCookie('CL_LANG') == 'ja' || GetCookie('CL_LANG') == 'ct')? 'yy年m月d日':'d MM, yy'),
		defaultDate: null,
		maxDate: 'today',
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});
});


$(window).on('load', function() {
	if ($('#pbank').get(0)) {

		setTimeout(function() {
			if (!i18nSet) {
				setTimeout(arguments.callee, 500);
			}

			shadowMask('on');
			$('#shadowMask')
			.append(
				$('<div>')
				.attr('id','content')
				.addClass('purchaseMsgBox')
				.append(
					$('<p>')
					.addClass('font-blue msg')
					.html($.i18n.prop('cl_t_payment_pbank_1'))
				)
				.append(
					$('<p>')
					.addClass('button-box mt16')
					.append(
						$('<button>')
						.addClass('button na confirm')
						.text($.i18n.prop('cl_t_payment_Common_2'))
						.click(function() {
							$('#shadowMask').hide();
							$('#shadowMask').remove();
						})
					)
				)
			);

		}, 500);
		return false;
	}

});

