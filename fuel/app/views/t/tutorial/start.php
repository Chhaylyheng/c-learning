<div id="content-inner" class="login">

<div class="info-box mt8">

	<h2 class="text-center font-blue font-size-140 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-check-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('学生に資料を配付して、実際にクイックアンケートを使いましょう'); ?></h2>

	<div class="button-box mt16 text-center">
		<a class="button confirm width-auto" href="<?php echo Asset::get_file(CL_PDF_PREFIX.'_student_entry_manual.pdf', 'docs'); ?>" target="_blank"><?php echo __('学生に配付する資料をダウンロード'); ?></a>
	</div>

	<div class="mt16 text-center">
		講義コード <span class="font-red font-size-180">【<?php echo \Clfunc_Common::getCode($aClass['ctCode']); ?>】</span>
	</div>

	<p class="mt16 font-green text-center"><i class="fa fa-question-circle"></i> <?php echo __('資料のダウンロードや学生の履修は後からでもできます。'); ?></p>

	<hr>

	<h3 class="font-size-120 ml8 mt16"><i class="fa fa-caret-down"></i> <?php echo __('学生の履修方法'); ?></h3>
	<ol class="ml28 mt4 mb8">
		<li><?php echo __('配布用資料をダウンロードする。'); ?></li>
		<li><?php echo __('印刷して、学生に配布する。'); ?></li>
		<li><?php echo __('講義コード【:code】を学生に伝える。', array('code'=>\Clfunc_Common::getCode($aClass['ctCode']))); ?></li>
		<li><?php echo __('学生は手持ちのスマートフォンや携帯電話で、:siteに登録して講義に履修する。', array('site'=>CL_SITENAME)); ?></li>
	</ol>

</div>

<div class="info-box mt8">

	<h2 class="text-center font-green font-size-140 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-info-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('学生登録なしで、すぐにアンケートをとるならコチラ！'); ?></h2>

	<div class="button-box mt16 text-center">
		<a class="button confirm width-auto" href="/print/t/GuestLogin/<?php echo $aClass['ctID']; ?>" target="_blank"><?php echo __('ゲスト回答で配布する資料をダウンロード'); ?></a>
	</div>

	<hr>

	<h3 class="font-size-120 ml8 mt16"><i class="fa fa-caret-down"></i> <?php echo __('アンケートでゲスト回答を行う方法'); ?></h3>

	<p style="overflow: hidden;"><img src="<?php echo Asset::get_file((($sLang == 'en')? 'guest_dropdown_en.png':'guest_dropdown.png'), 'img'); ?>" style="float: left; margin-right: 1em;"><?php echo __('TutoText07'); ?></p>

</div>

<form action="/t/tutorial/finish" method="post" class="info-box mt8">
	<div class="button-box mt0" style="overflow: hidden;">
		<button type="submit" name="back" value="1" class="button default na width-auto" style="float: left;"><i class="fa fa-chevron-left ml0 mr16"></i><?php echo __('戻る'); ?></button>
		<button type="submit" name="next" value="1" class="button do na width-auto" style="float: right; padding: 9px 16px;"><?php echo __('講義一覧画面に戻る'); ?><i class="fa fa-chevron-right ml16 mr0"></i></button>
	</div>
</form>


</div>
