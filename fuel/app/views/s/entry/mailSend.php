<div id="content-inner" class="login">

<ul id="breadcrumbs">
	<li><a href="/s"><?php echo __('トップ'); ?></a></li>
</ul>

<h1 class="mr0"><?php echo __('学生アカウントの登録手続きが完了しました。'); ?></h1>

	<div class="info-box">
		<?php if ($sent_mail): ?>
		<p><?php echo __('アカウント登録手続き完了のお知らせを <strong>:mail</strong> へメールで送信しました。', array('mail'=>$sent_mail)); ?></p>
		<?php else: ?>
		<p><?php echo __('アカウント登録手続きが完了しました。'); ?></p>
		<?php endif; ?>

		<p class="button-box"><a href="/s/index" class="button do register"><?php echo __('早速、:siteを使う',array('site'=>CL_SITENAME)); ?></a></p>
	</div>

</div>
