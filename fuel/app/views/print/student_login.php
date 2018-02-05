<div class="NoPrint printCtrl">
	<p class="mt0"><?php echo __('印刷して学生への配布にご利用ください。'); ?></p>

	<div class="text-center mt16">
		<button type="button" class="button na do window-print"><?php echo __('印刷する'); ?></button>
<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
		<button type="button" class="button na cancel window-close"><?php echo __('画面を閉じる'); ?></button>
<?php endif; ?>
	</div>
</div>

<div class="ScreenBorder printPage">
	<h2><?php echo __('<span>【:class】</span>の履修方法',array('class'=>$aClass['ctName'])); ?></h2>
	<p class="code font-size-140"><?php echo __('講義コード'); ?>：<?php echo \Clfunc_Common::getCode($aClass['ctCode']); ?></p>
	<hr>
	<div style="position: relative;">
	<p><?php echo __('下記学生ログイン画面にアクセスします。'); ?></p>
	<p class="mt8"><?php echo __('ログインURL'); ?>：<span class="font-blue font-size-120"><?php echo CL_MBURL.DS.'s'; ?></span></p>
	<img src="/print/t/LoginQR/s" style="position: absolute; top: 0; right: 0;">
	</div>
	<div class="QRQA mt12">
		<div class="QABox">
			<h4><span><?php echo __(':siteの学生アカウント取得',array('site'=>CL_SITENAME)); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('まだ:siteの学生アカウントをお持ちでない方は「新規登録される方はこちら」より、氏名・メールアドレス・パスワードを入力して登録します。',array('site'=>CL_SITENAME)); ?></p>
				<p><?php echo __('登録完了後、「早速、:siteを使う」とログインが完了します。',array('site'=>CL_SITENAME)); ?></p>
			</div>
		</div>
		<div class="QABox">
			<h4><span><?php echo __(':siteにログイン',array('site'=>CL_SITENAME)); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('既に:siteの学生アカウントをお持ちの方は、ログインIDまたはメールアドレスとパスワードを入力してログインします。',array('site'=>CL_SITENAME)); ?></p>
			</div>
		</div>
		<div class="QABox">
			<h4><span><?php echo __('履修登録'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('ログイン完了後、「履修登録」欄に講義コード<span class="font-size-180 font-red">【:code】</span>を入力して、履修登録をします。',array('code'=>\Clfunc_Common::getCode($aClass['ctCode']))); ?></p>
			</div>
		</div>
	</div>
	<hr>
	<div class="QRQA mt12">
		<h3><?php echo __('困ったときは？'); ?></h3>
		<div class="QABox">
			<h4>Q.1<span><?php echo __('QRコードを読み取れない。'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('ブラウザを起動して、以下のURLにアクセスしてください。'); ?></p>
				<p class="font-blue font-size-120"><?php echo CL_MBURL.DS.'s'; ?></p>
			</div>
		</div>
		<div class="QABox">
			<h4>Q.2<span><?php echo __('学生アカウントの作成ができない。'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('すでに登録されているメールアドレスを指定していませんか？'); ?></p>
				<p><?php echo __('メールアドレスが登録済みならば、学生アカウントをお持ちと思われます。学生ログイン画面よりメールアドレスとパスワードを入力してログインしてください。'); ?></p>
				<p class="mt16 font-size-100"><?php echo __('※パスワードを忘れてしまった方は、「パスワードを忘れた方はこちら」より、新しいパスワードを設定することができます。'); ?></p>
			</div>
		</div>
		<div class="QABox">
			<h4>Q.3<span><?php echo __('ログインができない。'); ?></span></h4>
			<div class="Answer">
				<p><?php echo __('ログインIDまたはメールアドレス、パスワードが誤っていませんか？'); ?></p>
				<p class="mt16"><?php echo __('以下をお確かめください。'); ?></p>
				<ul>
					<li><?php echo __('英字の大文字と小文字を正しく入力されていますか？'); ?></li>
					<li><?php echo __('全角文字が混在していませんか？'); ?></li>
					<li><?php echo __('数字の<em>0</em>と英字の<em>O</em>、数字の<em>1</em>と英字の<em>l</em>,<em>I</em>などの似通った文字を間違えて入力していませんか？'); ?></li>
				</ul>
				<p class="mt16 font-size-100"><?php echo __('※メールアドレスを登録されている方は、「パスワードを忘れた方はこちら」より、新しいパスワードを設定することができます。'); ?></p>
				<p class="font-size-100"><?php echo __('※メールアドレスを登録していない方は、先生にパスワードの「リセット」してもらい、新しいパスワードでログインしてください。'); ?></p>
			</div>
		</div>
	</div>
</div>













