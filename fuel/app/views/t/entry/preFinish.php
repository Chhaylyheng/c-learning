<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('認証メールを送信しました'); ?></h1>

	<div class="info-box mt16">
		<p class="text-center font-red font-size-160 mt8 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-exclamation-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('まだ登録は完了していません'); ?></p>
		<p class="text-center mb16"><?php echo __('メールに記載されているURLにアクセスして登録完了手続きを行ってください。'); ?></p>
		<ul class="entry-process">
			<li><?php echo Asset::img('entrymail_1.png',array('alt'=>__('届いたメールをチェック'))); ?><p><?php echo __('届いたメールをチェック'); ?></p><i class="fa fa-arrow-right fa-lg"></i></li>
			<li><?php echo Asset::img('entrymail_2.png',array('alt'=>__('メールのURLをクリック'))); ?><p><?php echo __('メールのURLをクリック'); ?></p><i class="fa fa-arrow-right fa-lg"></i></li>
			<li><?php echo Asset::img('entrymail_3.png',array('alt'=>__('登録完了！'))); ?><p><?php echo __('登録完了！'); ?></p></li>
		</ul>
		<hr>
		<h2 class="mt16 mb8"><?php echo __('送信先メールアドレス'); ?></h2>
		<p class="ml8 mt4 font-blue font-bold"><?php echo $sMail; ?></p>

		<p class="mt16 font-gray">
			<?php echo __('登録完了手続きはお申し込みから:time時間以内に行ってください。',array('time'=>CL_PRE_TIME)); ?><br>
			<?php echo __('認証メールが届かない方はドメイン「c-learning.jp」の受信設定後に、登録をやり直してください。'); ?>
		</p>

		<p class="subtext mt16"><a href="/t/entry"><?php echo __('登録をやり直す'); ?></a></p>
		<p class="subtext mt4"><a href="/t/login"><?php echo __('すでにアカウントをお持ちの方はこちら'); ?></a></p>
	</div>

</div>
