<?php echo View::forge('selectlang'); ?>

<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('副担当').__('ログイン'); ?></h1>

	<div class="info-box mt16">
		<form action="/a/login/loginchk" method="post" role="form">
		<input type="hidden" name="ltzone" value="">
		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php if (isset($error['login'])): ?>
			<p class="error-msg"><?php echo $error['login'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('algn_mail'=>'','algn_pass'=>'');
			$errMsg = $errClass;

			if (isset($error['algn_mail'])):
				$errClass['algn_mail'] = ' input-error';
				$errMsg['algn_mail'] = '<p class="error-msg">'.$error['algn_mail'].'</p>';
			endif;
			if (isset($error['algn_pass'])):
				$errClass['algn_pass'] = ' input-error';
				$errMsg['algn_pass'] = '<p class="error-msg">'.$error['algn_pass'].'</p>';
			endif;
		?>
		<p><input type="text" name="algn_mail" id="form_algn_mail" value="<?php echo (isset($algn_mail))? $algn_mail:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('メールアドレス'); ?>" class="allow_submit<?php echo $errClass['algn_mail']; ?>"></p>
		<?php echo $errMsg['algn_mail']; ?>

		<?php
		?>
		<p><input type="password" name="algn_pass" id="form_algn_pass" value="<?php echo (isset($algn_pass))? $algn_pass:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('パスワード'); ?>" class="allow_submit<?php echo $errClass['algn_pass']; ?>"></p>
		<?php echo $errMsg['algn_pass']; ?>

		<p class="checkbox-box"><?php echo Form::label(Form::checkbox('algn_chk',1,$algn_chk,array('id'=>'form_memchk')).'<span>'.__('次回からログイン情報の入力を省略する').'</span>','memchk'); ?></p>
		<p class="subtext"><a href="/a/password"><?php echo __('パスワードを忘れた方はこちら'); ?></a></p>
		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('ログイン'); ?></button></p>
		</form>
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

