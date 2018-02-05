$(function(){
	var breakpoint = 768;
	var windowWidth = $(window).width();

	var iconArrowRPath = "/assets/img/icon_arrow_r.png";
	var iconArrowLPath = "/assets/img/icon_arrow_l.png";
	var iconArrow32Path = "/assets/img/icon_arrow_32.png";
	var iconArrow32overPath = "/assets/img/icon_arrow_32_over.png";
	var iconHumbergerPath = "/assets/img/icon_humberger.png";
	var iconSettingPath = "/assets/img/icon_setting.png";
	var iconClosePath = "/assets/img/icon_close.png";

	var mainManuHideKey = 'cl_mainManu';

	// タッチアクションがある端末以外でメインメニューのホバーアクション（アンドロイドタブレット対応）
	var isTouch = ('ontouchstart' in window);
	if(!isTouch) {
		$("#main-menu li[class != 'landing'] a").hover(
		function(){
			$(this).css({background: '#eee url("'+iconArrow32overPath+'") no-repeat right center', color: '#545454'});
		},
		function(){
			$(this).css({background: '#545454 url("'+iconArrow32Path+'") no-repeat right center', color: '#CBCBCB'});
		});
	}

	// アンケートでチェックされた項目の背景色を変更
	$('.radio input:checked').parent("label").css({"backgroundColor":"#62BC64","color":"#FFFFFF"});
	$('.radio input:checked').parent("label").next(".plan-explain").css({"borderColor":"#62BC64"});


	// チェックされている支払情報を開く
	$('.billing-detail:checked').each(function(){
		$(this).parent().next(".billing-box").css("display","block");
	});

	// 集計公開設定 チェックされた公開先入力エリアを開く
	$('.publish-detail:checked').each(function(){
		$(this).parent().next(".publish-box").css("display","block");
	});

	// メインメニューの開閉
	$('#main-menu-button').click(function (e){
		e.preventDefault();

		var windowWidth = $(window).width();

		var mainMenu = $('#main-menu');
		var mainMenuWidth = $(mainMenu).width();
		var animateLength = -mainMenuWidth;

		var contentPadding = $('.content-padding');

		if(parseInt($(mainMenu).css("left")) == 0){
			$(mainMenu).stop(true, true).animate({ left: animateLength }, 300);
			// iPhoneでの表示用に追加した背景ボックスを削除
			$('#addon-box').remove();

			if(windowWidth >= breakpoint){ // PCサイズとSPサイズで挙動を変更
				setSessionStorage(mainManuHideKey, 1);
				$(contentPadding).css({'padding-left': 0});
				if ($('.comment-write-box').get(0)) {
					$('.comment-write-box').css({'margin-left':0});
				}
			}
			$(this).children('.pc-display').attr({src: iconArrowRPath});
			$(this).children('.sp-display').attr({src: iconHumbergerPath});
		} else {
			setSessionStorage(mainManuHideKey, 0);
			$(mainMenu).stop(true, true).animate({ left: 0 }, 300);

			// iPhoneでの表示用にfooterの後に背景ボックスを追加
			$('#addon-box').remove();
			$('footer').after(createAddonBox());
			$('#addon-box').stop(true, true).animate({ left: 0}, 300);

			if(windowWidth >= breakpoint){ // PCサイズとSPサイズで挙動を変更
				$(contentPadding).css({'padding-left': mainMenuWidth});
				if ($('.comment-write-box').get(0)) {
					$('.comment-write-box').css({'margin-left': mainMenuWidth});
				}
			}
			$(this).children('.pc-display').attr({src: iconArrowLPath});
			$(this).children('.sp-display').attr({src: iconClosePath});
		}
		initDisplay();
	});

	// 記憶したメインメニューの表示状態を復元
	if(('sessionStorage' in window) && (window.sessionStorage !== null)) {
		menuFlag = sessionStorage.getItem(mainManuHideKey);
		if (parseInt(menuFlag) == 1) {
			var windowWidth = $(window).width();
			var mainMenu = $('#main-menu');
			var mainMenuWidth = $(mainMenu).width();
			var animateLength = -mainMenuWidth;
			var contentPadding = $('.content-padding');

			$(mainMenu).stop(true, true).animate({ left: animateLength }, 1);

			$('#addon-box').remove();
			if(windowWidth >= breakpoint){ // PCサイズとSPサイズで挙動を変更
				$(contentPadding).css({'padding-left': 0});
				if ($('.comment-write-box').get(0)) {
					$('.comment-write-box').css({'margin-left':0});
				}
			}
			$(this).children('.pc-display').attr({src: iconArrowRPath});
			$(this).children('.sp-display').attr({src: iconHumbergerPath});
		} else {
			setSessionStorage(mainManuHideKey, 0);
		}
		initDisplay();
	}






	// システムメニューの開閉
	$('#system-menu-button').click(function (e){
		e.preventDefault();
		var systemMenu = $('#system-menu');
		var systemMenuWidth = $(systemMenu).width();
		var animateLength = -systemMenuWidth;

		if(parseInt($(systemMenu).css("right")) == "0"){
			$(systemMenu).stop(true, true).animate({ right: animateLength }, 300);
			$(this).children().attr({src: iconSettingPath});
		}else{
			$(systemMenu).stop(true, true).animate({ right: 0 }, 300);
			$(this).children().attr({src: iconClosePath});
		}
	});

	// クラス制御のイベント
	classEvent();

	// フッターの位置とメニューの高さ・デザインを調整
	initDisplay();


	/* 独自デザインでのスクロールの処理 */
	var mainMenu = $('#main-menu');
	var mainMenuInner = $('#main-menu .menu-inner');
	if(mainMenuInner[0]){
		var orgTop = $(mainMenuInner).position().top;
	}
	var endTop = endTopCul(mainMenuInner);

	var scrollRate = 30;
	var scrollbarRate = 0;

	var mainMenuHeight,mainMenuInnerHeight,scrollbarEnd;

	var mousewheelevent = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';

	$(mainMenuInner).on(mousewheelevent,function(e){
		var delta = e.originalEvent.deltaY ? -(e.originalEvent.deltaY) : e.originalEvent.wheelDelta ? e.originalEvent.wheelDelta : -(e.originalEvent.detail);
		if (delta < 0){
			// マウスホイールを下にスクロールしたとき
			e.preventDefault();
			endTop = endTopCul(mainMenuInner);
			mainMenuHeight = $(mainMenu).innerHeight();
			mainMenuInnerHeight = $(mainMenuInner).innerHeight();
			if(mainMenuHeight < mainMenuInnerHeight){
				scrollbarRate = scrollRate + Math.floor(scrollRate * mainMenuHeight * 0.6 / (mainMenuInnerHeight - mainMenuHeight));
				scrollbarEnd = Math.floor(mainMenuHeight * 0.6 + mainMenuInnerHeight - mainMenuHeight);
			}
			var scrollPos = parseInt($(this).css("top"));
			scrollPos = scrollPos - scrollRate;
			if(scrollPos < endTop){
				scrollPos = endTop;
			}

			$(this).css("top", scrollPos);

			if(mainMenuHeight < mainMenuInnerHeight){
				var scrollbarBox = $('#scrollbar-box');
				var scrollbarPos = parseInt($(scrollbarBox).css("top"));
				scrollbarPos = scrollbarPos + scrollbarRate;
				if(scrollbarPos > scrollbarEnd){
					scrollbarPos = scrollbarEnd;
				}
				$(scrollbarBox).css("top", scrollbarPos);
			}
		} else {
			// マウスホイールを上にスクロールしたとき
			e.preventDefault();
			mainMenuHeight = $(mainMenu).innerHeight();
			mainMenuInnerHeight = $(mainMenuInner).innerHeight();
			if(mainMenuHeight < mainMenuInnerHeight){
				scrollbarRate = scrollRate + Math.floor(scrollRate * mainMenuHeight * 0.6 / (mainMenuInnerHeight - mainMenuHeight));
				scrollbarEnd = Math.floor(mainMenuHeight * 0.6 + mainMenuInnerHeight - mainMenuHeight);
			}

			var scrollPos = parseInt($(this).css("top"));
			scrollPos = scrollPos + scrollRate;
			if(scrollPos > orgTop){
				scrollPos = orgTop;
			}
			$(this).css("top", scrollPos);

			if(mainMenuHeight < mainMenuInnerHeight){
				var scrollbarBox = $('#scrollbar-box');
				var scrollbarPos = parseInt($(scrollbarBox).css("top"));
				scrollbarPos = scrollbarPos - scrollbarRate;
				if(scrollbarPos < 0){
					scrollbarPos = 0;
				}
				$(scrollbarBox).css("top", scrollbarPos);
			}
		}
	});

	/* 独自デザインでのスクロールの処理(タッチデバイス用) */
	var startPageY,startTop;
	$(mainMenuInner).bind('touchstart', function(e){
		startPageY = e.originalEvent.touches[0].pageY;
		startTop = $(mainMenuInner).position().top;
	});
	$(mainMenuInner).bind('touchmove', function(e){
		e.preventDefault();
		endTop = endTopCul(mainMenuInner);
		var movePageY = e.originalEvent.touches[0].pageY;
		var moveTop = startTop + movePageY - startPageY;
		if(moveTop < orgTop && moveTop > endTop){
			$(mainMenuInner).css({top: moveTop});
			mainMenuHeight = $(mainMenu).innerHeight();
			mainMenuInnerHeight = $(mainMenuInner).innerHeight();
			if(mainMenuHeight < mainMenuInnerHeight){
				var scrollbarHeight = Math.floor($('#scrollbar-box').innerHeight());
				scrollbarRate = (mainMenuHeight - scrollbarHeight) / (mainMenuInnerHeight - mainMenuHeight);
				var scrollbarPos = Math.floor(moveTop * scrollbarRate);
				scrollbarPos = -(moveTop + scrollbarPos)
				$('#scrollbar-box').css({top: scrollbarPos});
			}
		}else{
			if(moveTop > orgTop){ moveTop = orgTop; }
			else if(moveTop < endTop){ moveTop = endTop; }
			$(mainMenuInner).css({top: moveTop});
			mainMenuHeight = $(mainMenu).innerHeight();
			mainMenuInnerHeight = $(mainMenuInner).innerHeight();
			if(mainMenuHeight < mainMenuInnerHeight){
				var scrollbarHeight = Math.floor($('#scrollbar-box').innerHeight());
				scrollbarRate = (mainMenuHeight - scrollbarHeight) / (mainMenuInnerHeight - mainMenuHeight);
				var scrollbarPos = Math.floor(moveTop * scrollbarRate);
				scrollbarPos = -(moveTop + scrollbarPos)
				$('#scrollbar-box').css({top: scrollbarPos});
			}
		}
	});
	$(mainMenuInner).bind('touchend', function(e){
	});

	// メニュー以外をスクロールした場合にメニューを再描画(iPhone Safari対応
	$(window).bind('touchmove', function(e){
		initDisplay();
	});

	// formパーツの初期設定
	formPartsInit();

	// ブラウザのサイズ変更時の動作
	$(window).resize(function(){
		var windowWidth = $(window).width();
		var mainMenu = $('#main-menu');
		var mainMenuWidth = parseInt($(mainMenu).width());
		var mainMenuLeft = parseInt($(mainMenu).css("left"));
		var mainMenuSpLeft = -mainMenuWidth;
		var contentPadding = $('.content-padding');
		var contentPaddingLength = parseInt($(contentPadding).css("paddingLeft"));

		if(windowWidth >= breakpoint){ // PCサイズ内での変更
			if(mainMenuLeft == 0){
				$(contentPadding).css("paddingLeft", mainMenuWidth);
				$('#main-menu-button').children('.pc-display').attr({src: iconArrowLPath});
				$('#main-menu-button').children('.sp-display').attr({src: iconClosePath});
			}else{
				$(mainMenu).css("left", mainMenuSpLeft);
				$(contentPadding).css("paddingLeft", "0px");
				$('#main-menu-button').children('.pc-display').attr({src: iconArrowRPath});
				$('#main-menu-button').children('.sp-display').attr({src: iconHumbergerPath});
			}
		}else{ // SPサイズ内での変更
			if(mainMenuLeft == 0){
				$('#main-menu-button').children('.pc-display').attr({src: iconArrowLPath});
				$('#main-menu-button').children('.sp-display').attr({src: iconClosePath});
			}else{
				$('#main-menu-button').children('.pc-display').attr({src: iconArrowRPath});
				$('#main-menu-button').children('.sp-display').attr({src: iconHumbergerPath});
			}
			if(contentPaddingLength == mainMenuWidth){
				$(contentPadding).css("paddingLeft", '0px');
			}
		}

		// フッターの位置とメニューの高さを初期化
		initDisplay();
	});
});


/* フッターの位置とメニューの高さを初期化 */
function initDisplay() {
	// フッターの位置を調整（ウィンドウの高さ）
	var windowHeight =  $(window).innerHeight();

	var content = $('#content');
	var footer = $('footer');

	$(content).css("paddingBottom", "0px");

	var contentHeight = $(content).outerHeight(true);
	var footerHeight = $(footer).outerHeight();
	var contentHeightTotal = contentHeight + footerHeight;

	if(contentHeightTotal < windowHeight){
		var addHeight = windowHeight-contentHeightTotal;
		$(content).css("paddingBottom", addHeight);
	}

	// メニューの高さを調整
	var mainMenu = $('#main-menu');
	var mainMenuInner = $('#main-menu .menu-inner');

	var headerHeight = $('header').height();
	var mainMenuGoalHeight = windowHeight - headerHeight;

	$(mainMenu).css("height", mainMenuGoalHeight);

	// スクロールバーの演出用ボックスを追加
	var mainMenuInnerHeight = $(mainMenuInner).innerHeight();

	if(mainMenuGoalHeight < mainMenuInnerHeight){
		if($('#scrollbar-box')[0]){ //もしスクロールバーが既在なら
			var scrollbarPos = parseInt($('#scrollbar-box').css("top"));
			if(scrollbarPos != 0){ //スクロールバーが移動しているなら
				$('#scrollbar-box').remove();
				$(mainMenu).children("div").append(createScrollbarBox(mainMenu));
				$('#scrollbar-box').css("top", scrollbarPos);
			}else{
				$('#scrollbar-box').remove();
				$(mainMenu).children("div").append(createScrollbarBox(mainMenu));
			}
		}else{
				$(mainMenu).children("div").append(createScrollbarBox(mainMenu));

		}
	}

	// ラジオボタンの挙動
	$('.radio').click(function (){
		$('.radio').each(function(){
			if ($(this).children('input').prop('checked')) {
				$(this).css({"backgroundColor":"#62BC64","color":"#FFFFFF"});
				$(this).next(".plan-explain").css({"borderColor":"#62BC64"});
				$(this).children("input").attr("checked","checked");
			}else{
				$(this).css({"backgroundColor":"#CBCBCB","color":"#545454"});
				$(this).next(".plan-explain").css({"borderColor":"#CBCBCB"});
				$(this).children("input").removeAttr("checked");
			}
		});
	});


	// 支払情報のラジオボタンの挙動
	$('.radio-lists label').click(function (){
		$(this).parent().children('label').each(function(){
			if ($(this).children('input').prop('checked')) {
				$(this).children("input").attr("checked","checked");
				$(this).next(".billing-box").slideDown("1500");
				$(this).next(".publish-box").slideDown("1500");
			}else{
				$(this).children("input").removeAttr("checked");
				$(this).next(".billing-box").slideUp("1500");
				$(this).next(".publish-box").slideUp("1500");
			}
		});
	});


	// セレクトボックスの挙動
	$('.checkbox-list label').click(function (){
		if ($(this).children('input').attr('checked') == "checked") {
			$(this).children("input").removeAttr("checked");
		}else{
			$(this).children("input").attr("checked","checked");
		}
	});

	modalDisplay();

}


/* アニメーション実行が遅い端末におけるアニメーション終了時の再描画 */
function reRender(contents){
	var tmpHTML;

	$(contents).each(function(i){
		tmpHTML = $(this).html();
		$(this).empty();
		$(this).append(tmpHTML);
	});

	/* formパーツの再描画 */
	formPartsInit(1);

	classEvent();

	initDisplay();
}

/* スクロール領域を制限するための値を計算 */
function endTopCul(mainMenuInner){
	var windowHeight = $(window).innerHeight();
	return 	windowHeight - $(mainMenuInner).outerHeight() - $('header').height();
}

// iPhoneでの表示用にfooterの後に背景ボックスを作成
function createAddonBox(){
	var addonBox;
	var addonBoxHeight = $('body').innerHeight();
	addonBox = '<div id="addon-box" style="background-color: #545454; height: ' + addonBoxHeight + 'px; position: absolute; top: 0; left: -240px; width:240px; z-index: 15;"></div>';

	return addonBox;
}

/* スクロールバー演出用のボックスを作成 */
function createScrollbarBox(mainMenu){
	var scrollbarBox;
	var scrollbarBoxHeight = $(mainMenu).innerHeight() * 0.4;
	scrollbarBox = '<div id="scrollbar-box" style="background-color: #989898; height: ' + scrollbarBoxHeight + 'px; position: absolute; top: 0; right: 2px; width:6px; z-index: 15;"></div>';

	return scrollbarBox;
}

/* formパーツの初期設定 */
function formPartsInit(reRenderFlag){

	// selectのplaceholder対応

	if(reRenderFlag == 1){
		if($('.pulldown')[0]){
			$('.pulldown').remove();
			$('.select').remove();
			if($('.select-box').next('p').html() == ""){
				$('.select-box').next('p').remove();
			}
			if($('.select-box').next('p').html() == ""){
				$('.select-box').next('p').remove();
			}
			if($('.select-box').next('p').html() == ""){
				$('.select-box').next('p').remove();
			}
			if($('.select-box').next('p').html() == ""){
				$('.select-box').next('p').remove();
			}
		}
	}

	$('select[placeholder]').each(function (){
		var selectPlaceHolder = $(this).attr("placeholder");
		var selectedOption = "";

		$(this).children().each(function (){
			if($(this).attr("class") == "selected-option"){
				selectedOption = $(this).html();
			}
		});

		if(selectedOption != ""){
			selectPlaceHolder = selectedOption;
		}

		var selectWrap = '<div class="pulldown">' + $(this).html().replace(/(option|OPTION)/g,'a') + '</div>';
		$(this).after(selectWrap);
		var addonSpan = '<a href="#" class="select select-placeholder">' + selectPlaceHolder + '</a>';
		$(this).after(addonSpan);
	});

	$('.select').click(function (e){
		e.preventDefault();
		$(this).next().slideToggle("1500");
		$(this).toggleClass("select-clicked");
	});

	$('.pulldown a').click(function (e){
		e.preventDefault();
		$(this).parent().slideToggle("1500");
		$(this).parent().prev().toggleClass("select-clicked");
		var selectOptionValue = $(this).html();
		$(this).parent().prev().html(selectOptionValue);
		$(this).parent().prev().prev().children().each(function(){
			var optionValue = $(this).html();
			if(optionValue == selectOptionValue){
				$(this).attr("selected","selected");
				$(this).attr("class","selected-option");
			}else{
				$(this).removeAttr("selected");
				$(this).removeAttr("class","selected-option");
			}
		});
	});



	// placeholderのIE対応
	var supportsInputAttribute = function (attr) {
		var input = document.createElement('input');
		return attr in input;
	};

	if (!supportsInputAttribute('placeholder')) {

		$('[placeholder]').each(function () {
		if($(this).attr('type') == 'password'){
			$(this).attr("class","pw-field-org");
			var inputName = $(this).attr('name');
			var inputSize = $(this).attr('size');
			var inputMaxLength = $(this).attr('maxlength');
			var inputPlaceHolder = $(this).attr('placeholder');
			var inputClone = '<input type="text" name="' + inputName + '-clone" size="' + inputSize + '" maxlength="' + inputMaxLength + '" placeholder="' + inputPlaceHolder + '" style="position: absolute; top: 0; left: 0;" class="pw-field">';
			$(this).parent('p').css("position","relative");
			$(this).after(inputClone);
		}
		});

		$('[placeholder]').each(function () {
		if($(this).prop("tagName") != 'SELECT'){
			var input = $(this);
			var placeholderText = $(input).attr('placeholder');

			var placeholderColor = '#989898';
			var defaultColor = $(input).css('color');

			$(input).
			focus(function () {
				if ($(input).attr('class') === "pw-field") {
					$(input).css("display","none");
					input = $(input).prev();
					$(input).focus();
				}

				if ($(input).val() === placeholderText) {
					$(input).val('').css('color', defaultColor);
				}
			}).
			blur(function () {
				if (input.val() === '') {
					$(input).val(placeholderText).css('color', placeholderColor);
					if($(input).attr('class') === "pw-field-org"){
						input = $(input).next();
						$(input).css("display","block");
					}

				} else if (input.val() === placeholderText) {
					$(input).css('color', placeholderColor);
					if($(input).attr('class') === "pw-field-org"){
						input = $(input).next();
						$(input).css("display","block");
					}
				}
			}).
			blur()
			/* formのsubmitで値を受け渡す場合はコメントアウトを削除
			.
			parents('form').
			submit(function () {
				if (input.val() === placeholderText) {
				input.val('');
			}
			});*/
		}
		});
	}
}

/* モーダルウィンドウ用のスクリプト */
function modalDisplay(){
	$('.modal').click(function(e){
		e.preventDefault();
		$('body').append('<div class="modal-overlay"></div>');
		$('.modal-overlay').fadeIn('slow');

		var modal = $(this).attr('href');
		var modalElement = '<div id="modal-window" class="modal-content"><img src="' + modal + '" alt=""></div>';
		$('body').append(modalElement);
		modalResize();
 		$('#modal-window').fadeIn('slow');

		$('.modal-overlay, .modal-close').off().click(function(){
			$('#modal-window').fadeOut('slow');
			$('.modal-overlay').fadeOut('slow',function(){
					$('.modal-overlay').remove();
			});
		});
	});

	$(window).on('resize', function(){
		modalResize();
	});

}

/* モーダルコンテンツの表示位置を設定する関数 */
function modalResize(){
	var w = $(window).width();
	var h = $(window).height();
	var x = (w - $('#modal-window').outerWidth(true)) / 2;
	var y = h / 8;
	$('#modal-window').css({'left': x + 'px','top': y + 'px'});
}

/* クラス制御のイベントに関する関数 */
function classEvent(){
	// アコーディオン
	$("a.accordion").click(function(e){
		e.preventDefault();
		$(this).toggleClass("acc-open");
		$(this).parent().next().slideToggle("fast");
	});

	// クローズボタンを押した際の動作
	$(".close-button").click(function(e){
		e.preventDefault();
		$(this).parent().css("display", "none");
	});
}