<div class="NoPrint printCtrl">
	<p class="mt0"><?php echo __('印刷して学生への配布にご利用ください。'); ?></p>
	<p class="mt16">
		<?php $iCnt = count($aStudent); ?>
		<?php echo __('全:num名分が印刷されます。', array('num'=>$iCnt)); ?><br>
		<?php if ($iCnt > 1): ?>
		<?php echo __('※印刷時にページを指定することで、印刷対象を絞ることも可能です。'); ?>
		<?php endif; ?>
	</p>

	<div class="text-center mt16">
		<button type="button" class="button na do window-print"><?php echo __('印刷する'); ?></button>
<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
		<button type="button" class="button na cancel window-close"><?php echo __('画面を閉じる'); ?></button>
<?php endif; ?>
	</div>
</div>

<?php if ($aGroup['gtLDAP']): ?>
<?php $sURL = CL_PROTOCOL.'://'.CL_DOMAIN.DS.$aGroup['gtPrefix'].DS.'s'; ?>
<?php else: ?>
<?php $sURL = CL_MBURL.DS.'s'; ?>
<?php endif; ?>

<?php foreach($aStudent as $sStID => $aS): ?>

<div class="ScreenBorder printPage">
	<h2><?php echo __('<span>【:class】</span>の利用方法',array('class'=>$aClass['ctName'])); ?></h2>
	<p class="code"><?php echo __('講義コード'); ?>：<?php echo \Clfunc_Common::getCode($aClass['ctCode']); ?></p>
	<hr>
	<p><?php echo __('下記ログイン画面にアクセスして、あなたのログインIDとパスワードを入力します。'); ?></p>
	<p class="mt16"><?php echo __('ログインURL'); ?>：<span class="font-blue font-size-120"><?php echo $sURL; ?></span></p>
	<div class="loginQR mt16">
		<?php if ($aGroup['gtLDAP']): ?>
		<img src="/print/t/LDAPLoginQR/s">
		<?php else: ?>
		<img src="/print/t/LoginQR/s">
		<?php endif; ?>
		<div>
			<p class="name"><?php echo $aS['stNO'].' '.$aS['stName']; ?></p>
			<p class="loginid"><?php echo __('ログインID'); ?>：<span><?php echo $aS['stLogin']; ?></span></p>
			<?php if (!$aGroup['gtLDAP']): ?>
			<p class="passwd"><?php echo __('パスワード'); ?>：<span><?php echo ($aS['stFirst'])? $aS['stFirst']:__('（変更済）');?></span></p>
			<?php endif; ?>
		</div>
	</div>
	<hr>
	<div class="QRQA mt16">
		<h3><?php echo __('困ったときは？'); ?></h3>
		<div class="QABox">
			<h4>Q.1<span><?php echo __('QRコードを読み取れない。'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('ブラウザを起動して、以下のURLにアクセスしてください。'); ?></p>
				<p class="font-blue font-size-120"><?php echo $sURL; ?></p>
			</div>
		</div>
		<div class="QABox">
			<h4>Q.2<span><?php echo __('ログインができない。'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('ログインIDやパスワードが誤っていませんか？'); ?></p>
				<p class="mt16"><?php echo __('以下をお確かめください。'); ?></p>
				<ul>
					<li><?php echo __('英字の大文字と小文字を正しく入力されていますか？'); ?></li>
					<li><?php echo __('全角文字が混在していませんか？'); ?></li>
					<li><?php echo __('数字の<em>0</em>と英字の<em>O</em>、数字の<em>1</em>と英字の<em>l</em>,<em>I</em>などの似通った文字を間違えて入力していませんか？'); ?></li>
				</ul>
				<?php if (!$aGroup['gtLDAP']): ?>
				<p class="mt16 font-size-100"><?php echo __('※それでもログインできない場合は、先生にパスワードを「リセット」してもらい、新しいパスワードでログインしてください。'); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php endforeach; ?>













