<div class="NoPrint printCtrl">
	<p class="mt0"><?php echo __('印刷してゲストログインする方への配布にご利用ください。'); ?></p>

	<div class="text-center mt16">
		<button type="button" class="button na do window-print"><?php echo __('印刷する'); ?></button>
<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
		<button type="button" class="button na cancel window-close"><?php echo __('画面を閉じる'); ?></button>
<?php endif; ?>
	</div>
</div>

<div class="ScreenBorder printPage">
	<h2><?php echo __('<span>【:class】</span>のゲストログイン方法',array('class'=>$aClass['ctName'])); ?></h2>
	<p class="code font-size-140"><?php echo __('講義コード'); ?>：<?php echo \Clfunc_Common::getCode($aClass['ctCode']); ?></p>
	<hr>
	<div style="position: relative;">
	<img src="/print/t/LoginQR/g" style="position: absolute; top:0; left:0;">
	<p style="margin-left: 105px; padding-top: 16px;"><?php echo __('下記ゲストログイン画面にアクセスします。'); ?></p>
	<p style="margin-left: 105px;" class="mt8"><?php echo __('ログインURL'); ?>：<span class="font-blue font-size-120"><?php echo CL_MBURL.DS.'g'; ?></span></p>
	</div>
	<div class="QRQA mt12">
		<div class="QABox">
			<h4><span><?php echo __(':siteにゲストログイン',array('site'=>CL_SITENAME)); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('「講義コード」欄に講義コード<span class="font-size-180 font-red">【:code】</span>を入力して、ログインをします。',array('code'=>\Clfunc_Common::getCode($aClass['ctCode']))); ?></p>
			</div>
		</div>
		<div class="QABox">
			<h4><span><?php echo __('アンケートの回答'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('ログイン完了後、回答可能なアンケートが一覧表示されますので、アンケートに回答します。'); ?></p>
			</div>
		</div>
		<div class="QABox mt16">
			<div class="Answer">
				<?php echo Asset::img('GUEST01.png',array('style'=>'width: 25%')); ?>
				<?php echo Asset::img('GUEST02.png',array('style'=>'width: 25%','class'=>'ml16')); ?>
			</div>
		</div>
	</div>
	<hr>
	<div class="QRQA mt12">
		<h3><?php echo __('困ったときは？'); ?></h3>
		<div class="QABox">
			<h4><span><?php echo __('ログインができない。'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('入力した講義コードが誤っていませんか？'); ?></p>
				<p class="mt16"><?php echo __('以下をお確かめください。'); ?></p>
				<ul>
					<li><?php echo __('英字の大文字と小文字を正しく入力されていますか？'); ?></li>
					<li><?php echo __('全角文字が混在していませんか？'); ?></li>
					<li><?php echo __('数字の<em>0</em>と英字の<em>O</em>、数字の<em>1</em>と英字の<em>l</em>,<em>I</em>などの似通った文字を間違えて入力していませんか？'); ?></li>
				</ul>
				<p class="mt16 font-size-100"><?php echo __('※ゲスト回答可能なアンケートがない講義にはゲストログインはできません。'); ?></p>
			</div>
		</div>
	</div>
</div>













