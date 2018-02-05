<div id="content-inner" class="login">

<ul id="breadcrumbs">
	<li><a href="/t"><?php echo __('先生ログイン'); ?></a></li>
	<li><a href="/t/entry"><?php echo __('先生アカウントの新規登録'); ?></a></li>
</ul>

<?php if (isset($t_mail)): ?>

	<h1><?php echo __('メールアドレスはすでに使用されています。'); ?></h1>
	<div class="info-box">
		<p><?php echo __('入力して頂いた、:mailを利用する先生はすでに登録されています。', array('mail'=>$t_mail)); ?></p>
		<p><?php echo __('すでに:siteをご利用中の場合は、:linksログインページ:linkeよりログインできます。', array('links'=>'<a href="/t">', 'linke'=>'</a>', 'site'=>CL_SITENAME)); ?></p>

<?php else: ?>

	<h1><?php echo __('指定のソーシャルアカウントで先生は既に利用されています。'); ?></h1>

	<div class="info-box">
		<p><?php echo __('指定して頂いた、:providerアカウントで利用する先生はすでに登録されています。', array('provider'=>$provider)); ?></p>
		<p><?php echo __('すでに:siteをご利用中の場合は、:linksログインページ:linkeより、指定のソーシャルアカウントでログインできます。', array('links'=>'<a href="/t">', 'linke'=>'</a>', 'site'=>CL_SITENAME)); ?></p>

<?php endif; ?>

		<p class="button-box"><a href="/t" class="button do register"><?php echo __('ログインに戻る'); ?></a></p>
	</div>

</div>