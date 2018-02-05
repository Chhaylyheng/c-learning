<?php
	$sContact = ($iContact)? '<span class="attention attn-emp">'.$iContact.'</span>':null;
	$sCoop = ($iCoop)? '<span class="attention attn-emp">'.$iCoop.'</span>':null;
?>

<?php if (!$aTeacher['gtID'] && $aTeacher['coTermDate'] < date('Y-m-d')): ?>

<div class="info-box">
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_QUEST || $aTeacher['ttStatus'] == 2): ?>
	<p><a href="/t/quest" class="link-out" id="QuestLink"><?php echo __('アンケート'); ?></a></p>
	<?php endif; ?>
	<p><a href="/t/student" class="link-out"><?php echo __('学生管理'); ?></a></p>

<?php if (!$aAssistant): ?>
	<hr>
	<div style="display: inline-block;"><a href="/t/attend" class="button na default width-auto" style="padding: 8px;"><?php echo __('出席管理'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/test" class="button na default width-auto" style="padding: 8px;"><?php echo __('小テスト'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/drill" class="button na default width-auto" style="padding: 8px;"><?php echo __('ドリル'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/material" class="button na default width-auto" style="padding: 8px;"><?php echo __('教材倉庫'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/coop" class="button na default width-auto" style="padding: 8px;"><?php echo __('協働板'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/report" class="button na default width-auto" style="padding: 8px;"><?php echo __('レポート'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/alog" class="button na default width-auto" style="padding: 8px;"><?php echo __('活動履歴'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/contact" class="button na default width-auto" style="padding: 8px;"><?php echo __('連絡・相談'); ?></a></div>
	<div style="display: inline-block;"><a href="/t/news" class="button na default width-auto" style="padding: 8px;"><?php echo __('ニュース'); ?></a></div>
<?php endif; ?>
</div>

<?php elseif (CL_CAREERTASU_MODE) : ?>

<div class="info-box">
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_QUEST || $aTeacher['ttStatus'] == 2): ?>
	<p><a href="/t/quest" class="link-out" id="QuestLink"><?php echo __('アンケート'); ?></a></p>
	<?php endif; ?>

<?php if ($aTeacher['ttCTPlan'] > 0): ?>

	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ATTEND): ?>
	<p><a href="/t/attend" class="link-out"><?php echo __('出席管理'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_TEST): ?>
	<p><a href="/t/test" class="link-out"><?php echo __('小テスト'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_DRILL): ?>
	<p><a href="/t/drill" class="link-out"><?php echo __('ドリル'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_MATERIAL): ?>
	<p><a href="/t/material" class="link-out"><?php echo __('教材倉庫'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_COOP): ?>
	<p><a href="/t/coop" class="link-out"><?php echo __('協働板').$sCoop; ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_REPORT): ?>
	<p><a href="/t/report" class="link-out"><?php echo __('レポート'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ALOG): ?>
	<p><a href="/t/alog" class="link-out"><?php echo __('活動履歴'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_CONTACT): ?>
	<p><a href="/t/contact" class="link-out"><?php echo __('連絡・相談').$sContact; ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_NEWS): ?>
	<p><a href="/t/news" class="link-out"><?php echo __('ニュース'); ?></a></p>
	<?php endif; ?>

<?php endif; ?>

	<p><a href="/t/student" class="link-out"><?php echo __('学生管理'); ?></a></p>
</div>

<?php else: ?>

<div class="info-box">
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ATTEND): ?>
	<p><a href="/t/attend" class="link-out"><?php echo __('出席管理'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_QUEST || $aTeacher['ttStatus'] == 2): ?>
	<p><a href="/t/quest" class="link-out" id="QuestLink"><?php echo __('アンケート'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_TEST): ?>
	<p><a href="/t/test" class="link-out"><?php echo __('小テスト'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_DRILL): ?>
	<p><a href="/t/drill" class="link-out"><?php echo __('ドリル'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_MATERIAL): ?>
	<p><a href="/t/material" class="link-out"><?php echo __('教材倉庫'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_COOP): ?>
	<p><a href="/t/coop" class="link-out"><?php echo __('協働板').$sCoop; ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_REPORT): ?>
	<p><a href="/t/report" class="link-out"><?php echo __('レポート'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ALOG): ?>
	<p><a href="/t/alog" class="link-out"><?php echo __('活動履歴'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_CONTACT): ?>
	<p><a href="/t/contact" class="link-out"><?php echo __('連絡・相談').$sContact; ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_NEWS): ?>
	<p><a href="/t/news" class="link-out"><?php echo __('ニュース'); ?></a></p>
	<?php endif; ?>
	<p><a href="/t/student" class="link-out"><?php echo __('学生管理'); ?></a></p>
</div>


<?php endif; ?>

<?php if ($aTeacher['ttStatus'] == 2): ?>

<?php echo Asset::js('cl.tutorial.js'); ?>
<script type="text/javascript">
$(function() {
	$(window).on('load', function() {
		TutorialIndex();
	});
});
</script>

<div id="TutoText1" style="display: none;">
<h3><?php echo __('クイックツアー'); ?></h3>
<p><?php echo __('TutoText01',array('site'=>CL_SITENAME)); ?></p>
<?php /*<p class="mt0 mb4 font-size-80 font-silver"><?php echo __('TutoText01-01'); ?></p> */ ?>
<div class="button-box">
<button class="button na do width-auto TutorialClassBtn"><?php echo __('続ける'); ?></button>
<a href="/t/tutorial/suspend" class="button na default width-auto" style="float: right;"><?php echo __('今はやめる'); ?></a>
</div>
</div>

<div id="TutoText3" style="display: none;">
<p><?php echo __('TutoText03'); ?></p>
</div>

<?php endif; ?>


<?php if ($aCreateKey): ?>
<script type="text/javascript">
$(function() {
	$(window).on('load', function() {
		shadowMask('on');
		var ccf = $('#ClassCreateFinish');
		$('body').append(ccf);

		var ccfTop = (($(window).height()-ccf.outerHeight())/2 - FixedSize);
		var ccfLeft = (($(window).width()-ccf.outerWidth())/2);

		ccfTop = (ccfTop < (FixedSize * -1))? (FixedSize - 8) * -1:ccfTop;
		ccfLeft = (ccfLeft < 0)? 0:ccfLeft;

		ccf.css({
			'top': ccfTop+'px',
			'left': ccfLeft+'px'
		});
		ccf.show();
	});

	$('.CCFClose').on('click', function() {
		var ccf = $('#ClassCreateFinish');
		ccf.hide();
		shadowMask('off');
		ccf.remove();
	});
});


</script>

<div id="ClassCreateFinish" style="position: absolute; z-index: 160; display: none;">

<?php if (is_null($aGroup) || !($aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDY)): ?>
<div class="info-box">
	<div class="mt16 text-center">
		<?php echo __('講義コード'); ?> <span class="font-red font-size-240">【<?php echo \Clfunc_Common::getCode($aCreateKey[1]); ?>】</span>
	</div>

	<div class="mt16 text-center font-size-140">
		<?php echo __('学生に講義コードを伝え、履修登録させてください。'); ?>
	</div>

<?php if (!$aGroup['gtLDAP']): ?>
	<div class="button-box mt16 text-center">
		<a class="button confirm width-auto" href="<?php echo Asset::get_file(CL_PDF_PREFIX.'_student_entry_manual.pdf', 'docs'); ?>" target="_blank"><?php echo __('学生に配付する資料をダウンロード'); ?></a>
	</div>

	<p class="mt16 font-green text-center"><i class="fa fa-question-circle"></i> <?php echo __('資料のダウンロードや学生の履修は後からでもできます。'); ?></p>

	<hr>

	<h3 class="font-size-120 ml8 mt16"><i class="fa fa-caret-down"></i> <?php echo __('学生の履修方法'); ?></h3>
	<ol class="ml28 mt4 mb8">
		<li><?php echo __('配布用資料をダウンロードする。'); ?></li>
		<li><?php echo __('印刷して、学生に配布する。'); ?></li>
		<li><?php echo __('講義コード【:code】を学生に伝える。', array('code'=>\Clfunc_Common::getCode($aCreateKey[1]))); ?></li>
		<li><?php echo __('学生は手持ちのスマートフォンや携帯電話で、:siteに登録して講義に履修する。', array('site'=>CL_SITENAME)); ?></li>
	</ol>
<?php endif; ?>
</div>
<?php endif; ?>

<div class="info-box mt4">

	<h2 class="text-center font-green font-size-140 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-info-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('学生登録なしで、すぐにアンケートをとるならコチラ！'); ?></h2>

	<div class="button-box mt16 text-center">
		<a class="button confirm width-auto" href="/print/t/GuestLogin/<?php echo $aCreateKey[0]; ?>" target="_blank"><?php echo __('ゲスト回答で配布する資料をダウンロード'); ?></a>
	</div>

	<hr>

	<h3 class="font-size-120 ml8 mt16"><i class="fa fa-caret-down"></i> <?php echo __('アンケートでゲスト回答を行う方法'); ?></h3>

	<p style="overflow: hidden;"><img src="<?php echo Asset::get_file((($sLang == 'en')? 'guest_dropdown_en.png':'guest_dropdown.png'), 'img'); ?>" style="float: left; margin-right: 1em;"><?php echo __('TutoText07'); ?></p>

</div>

<div class="info-box text-center mt4" style="overflow: hidden;">
	<button type="button" class="button do na width-auto CCFClose" style="padding: 9px 16px;"><?php echo __('閉じる'); ?></button>
</div>

</div>

<?php endif; ?>
