/* 協働板のタイル表示処理 */

// 設定値
var iWidth = 0;
var iHeight = 0;
var TimerMSec = 5000;
var baseData;

// 比較用
var iSelect = 0;

$(function() {
	/* 箱の高さを確定 */
	iWidth = $("ul.CoopTile li:first").width() - 4;
	iHeight = Math.floor(iWidth * (3/4));
	$("ul.CoopTile li div.img-box").css("min-height",iHeight+"px");
	$("ul.CoopTile li div.text-box").css("min-height",iHeight+"px");

	/* 基本情報取得 */
	baseData = $("#CoopTile").attr("data").split('_');

	/* スクリーンサイズに合わせて変更 */
	$(window).resize(function() {
		if (iWidth == $("ul.CoopTile li:first").width()) {
			return;
		}
		iWidth = $("ul.CoopTile li:first").width() - 4;
		iHeight = Math.floor(iWidth * (3/4));
		$("ul.CoopTile li div.img-box").css("min-height",iHeight+"px");
	});

	/* 比較選択 */
	$(".border-box").click(function() {
		if ($(this).hasClass("select-border")) {
			$(this).removeClass("select-border");
			iSelect -= 1;
			if (iSelect == 1) {
				$(".compare_button").text($.i18n.prop('cl_t_coop_tile_border_box_1'));
			} else if (iSelect < 1) {
				$(".compare_button").hide();
			}
		} else {
			var Img = $(this).parent().find("img");
			var Txt = $(this).parent().find(".text-box p");
			if (Img.attr("src") == "" && Txt.text() == "") {
				return;
			}
			$(this).addClass("select-border");
			iSelect += 1;
			if (iSelect == 1) {
				$(".compare_button").text($.i18n.prop('cl_t_coop_tile_border_box_1'));
			} else {
				$(".compare_button").text($.i18n.prop('cl_t_coop_tile_border_box_2'));
			}
			$(".compare_button").show();
		}
	});

	/* 比較表示 */
	$(".compare_button").click(function() {
		// オーバレイの表示
		overlayShow();
		// 画面サイズの取得
		var wWidth = $(window).width();
		var wHeight = $(window).height();

		// 選択情報の取得
		var oSelList = $(".select-border").parent();
		var iSel = oSelList.size();
		// 選択数が足りない場合
		if (iSel < 1) {
			alert($.i18n.prop('cl_t_coop_tile_compare_button_1'));
			overlayHide();
			compareReset();
			return;
		}
		var iCol = Math.ceil(Math.sqrt(iSel));
		iCol = (iCol > 6)? 6:iCol;
		var iWidth = PreRound((100/iCol),2) - 0.4;

		// 比較画面の生成
		$("body").append('<ul id="compare_field"></ul>');
		$.each(oSelList, function() {
			$("#compare_field").append($(this).clone());
		});
		$("#compare_field li img").attr("id","");
		$("#compare_field li").css("width", iWidth+"%");

		$("#compare_field").fadeIn();
		compareReset();
		return;
	});
	$(document).on("click", "#black-overlay,#compare_field",function() {
		overlayHide();
		return;
	});
	$(document).on("touchstart touchmove touchend", "#black-overlay,#compare_field",function(e) {
		if ('touchstart' == event.type) {
			$(this).attr('data-touchstarted', '');
			return;
		}
		if ('touchmove' == event.type) {
			$(this).removeAttr('data-touchstarted');
			return;
		}
		if ('undefined' != typeof $(this).attr('data-touchstarted')) {
			$(this).removeAttr('data-touchstarted');
			overlayHide();
			return;
		}
	});

	// オーバレイの表示
	function overlayShow() {
		$("body").append('<div id="black-overlay"></div>');
		$("#black-overlay").fadeIn();
		return;
	}
	// オーバレイの非表示
	function overlayHide() {
		$("#compare_field").fadeOut("normal",function() {
			$("#compare_field").remove();
			return;
		});
		$("#black-overlay").fadeOut("normal",function() {
			$("#black-overlay").remove();
			return;
		});
		return;
	}
	// 比較選択のリセット
	function compareReset() {
		$(".border-box").removeClass("select-border");
		$(".compare_button").hide();
		iSelect = 0;
		return;
	}


	/* 画像のリアルタイムチェック */
	setInterval(function() {
		CoopRepChecker();
	},TimerMSec);

	/* 画像のチェック処理 */
	function CoopRepChecker() {
		var sData = new Object;
		var stData = new Object;
		$.each($("#CoopTile>li"), function(i) {
			var LI = $("#CoopTile>li:eq("+i+")");
			var sID = LI.attr("id");
			var d = LI.attr("data");
			if (d) {
				stData[sID] = $.parseJSON(JSONReplace(decodeURIComponent(d)));
			} else {
				stData[sID] = "null";
			}
		});
		sData.Base = baseData;
		sData.Stu  = stData;

		// console.log(sData);

		$.ajax({
			url: "/t/ajax/coop/TileLoad.json",
			type: "POST",
			cache: false,
			dataType: "json",
			data: sData,
			success: function(o,state) {
				var res = o.res;
				switch (o.err)
				{
					case -3:
					case -2:
					case -1:
						addAlert(o.msg,'alert');
					break;
					case 0:
//						console.log(res);
						// 正常処理
						$.each(res, function(sStID,oData) {
							var oLI = $("#"+sStID);
							if (oData.n == 0) {
								// 前回チェックと変化がないため何もしない
								return;
							} else if (oData.n == 2) {
								// 画像が削除された、記事が削除された、画像でないファイルが指定された
								if (stData[sStID]['fID'] != '') {
									oLI.find('.img-box').fadeOut("slow", function() {
										$(this).find('img').attr({"src": ""});
										return false;
									});
									oLI.find('.text-box').fadeIn('slow');
								} else {
									oLI.find('.text-box p').text('');
									oLI.find('.text-box i').fadeIn('slow');
								}
								oLI.attr('data','');
								return;
							}

							oLI.attr('data',encodeURIComponent('{"cNO":'+oData.cNO+',"fID":"'+oData.fID+'","cText":"'+oData.cText+'"}'));

							if (oData.fID != '') {
								oLI.find('.text-box').hide();
								oLI.find('.text-box p').text('');
								oLI.find('.text-box i').show();

								oLI.find('.img-box').show();
								ImageLoad(oLI.find('.img-box img'),oData.path);
							} else {
								oLI.find('.img-box').fadeOut("slow", function() {
									$(this).find('img').attr({"src": ""});
									return false;
								});
								if (oData.cText != '') {
									oLI.find('.text-box i').hide();
									oLI.find('.text-box p').html(oData.cText);
								} else {
									oLI.find('.text-box i').show();
									oLI.find('.text-box p').text('');
								}
								oLI.find('.text-box').fadeIn('slow');
							}
						});
						return false;
					break;
				}
				return false;
			},
			error: function(res,status) {
				addAlert('Network Access Error','alert');
				return false;
			},
			complete: function() {
			},
		});
	}

	/* 画像の読み込み */
	function ImageLoad(Img,path) {
		// パス取得
		var imgLoader = new Image();
		imgLoader.onload = function() {
			Img.attr({"src":path});
			Img.fadeIn("slow");
		};
		imgLoader.onerror = function() {
			console.log();
			return false;
		};
		imgLoader.src=path;
	}

	// 小数点（桁数指定）で四捨五入
	function PreRound(val, precision) {
		digit = Math.pow(10, precision);
		val = val * digit;
		val = Math.round(val);
		val = val / digit;
		return val;
	}

	function JSONReplace(s) {
	// preserve newlines, etc - use valid JSON
		s = s.replace(/\\n/g, "\\n")
		.replace(/\\'/g, "\\'")
		.replace(/\\"/g, '\\"')
		.replace(/\\&/g, "\\&")
		.replace(/\\r/g, "\\r")
		.replace(/\\t/g, "\\t")
		.replace(/\\b/g, "\\b")
		.replace(/\\f/g, "\\f");

		// remove non-printable and other non-valid JSON chars
		s = s.replace(/[\u0000-\u0019]+/g,"");

		return s;
	}

	// 初回呼び出し
	CoopRepChecker();
});