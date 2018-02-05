<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('先生アカウントの新規登録'); ?></h1>

	<div class="info-box mt16">

		<p class="text-center font-blue font-size-160 mt8 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-info-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('メールアドレスだけで簡単登録'); ?></p>

		<form action="/t/entry/index" method="post" role="form">
		<?php if (isset($error['entry'])): ?>
			<p class="error-msg"><?php echo $error['entry'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('tent_mail'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $c => $v):
				if (isset($error[$c])):
					$errClass[$c] = ' class="input-error"';
					$errMsg[$c] = '<p class="error-msg">'.$error[$c].'</p>';
				endif;
			endforeach;
		?>

		<p><input type="text" name="tent_mail" value="<?php echo (isset($tent_mail))? $tent_mail:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('メールアドレス'); ?>"<?php echo $errClass['tent_mail']; ?>></p>
		<?php echo $errMsg['tent_mail']; ?>

		<p class="subtext mt20"><?php echo __('利用規約およびプライバシーポリシーをお読みになり、すべての内容に同意のうえ、認証メールを送信してください。'); ?></p>
		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('認証メールを送信'); ?></button></p>
		</form>
		<hr>
		<p class="button-box mt16"><a href="/auth/login/facebook/TENTRY" class="button do facebook"><i class="fa fa-facebook-official"></i><?php echo __(':providerで登録する',array('provider'=>'Facebook')); ?></a></p>
		<p class="button-box mt12"><a href="/auth/login/google/TENTRY" class="button do google"><i class="fa fa-google"></i><?php echo __(':providerで登録する',array('provider'=>'Google')); ?></a></p>
		<p class="subtext"><a href="/t/login" class="button na default width-auto"><?php echo __('すでにアカウントをお持ちの方はこちら'); ?></a></p>
	</div>
</div>
<div id="footer-menu">
	<nav>
		<ul>
		<li><a href="http://c-learning.jp/"><?php echo __(':siteについて',array('site'=>CL_SITENAME)); ?></a></li>
		<li><a href="<?php echo Asset::get_file('cl_terms_of_service.pdf', 'docs'); ?>" target="_blank"><?php echo __('利用規約'); ?></a></li>
		<li><a href="http://c-learning.jp/prd/truste.html" target="_blank"><?php echo __('プライバシーポリシー'); ?></a></li>
		<li><a href="http://netman.co.jp/"><?php echo __('運営会社について'); ?></a></li>
		</ul>
	</nav>
</div>
