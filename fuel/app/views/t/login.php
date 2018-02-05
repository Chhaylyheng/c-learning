<?php echo View::forge('selectlang'); ?>

<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('先生ログイン'); ?></h1>

	<div class="info-box mt16">
		<form action="/t/login/loginchk" method="post" role="form">
		<input type="hidden" name="ltzone" value="">
		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php if (isset($ses['SES_T_ERROR_MSG'])): ?>
			<p class="error-box"><?php echo nl2br($ses['SES_T_ERROR_MSG']); ?></p>
			<?php Session::delete('SES_T_ERROR_MSG'); ?>
		<?php endif; ?>

		<?php if (isset($error['login'])): ?>
			<p class="error-msg"><?php echo $error['login'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('tlgn_mail'=>'','tlgn_pass'=>'');
			$errMsg = array('tlgn_mail'=>'','tlgn_pass'=>'');

			if (isset($error['tlgn_mail'])):
				$errClass['tlgn_mail'] = ' input-error';
				$errMsg['tlgn_mail'] = '<p class="error-msg">'.$error['tlgn_mail'].'</p>';
			endif;
			if (isset($error['tlgn_pass'])):
				$errClass['tlgn_pass'] = ' input-error';
				$errMsg['tlgn_pass'] = '<p class="error-msg">'.$error['tlgn_pass'].'</p>';
			endif;
		?>
		<p><input type="text" name="tlgn_mail" id="form_tlgn_mail" value="<?php echo (isset($tlgn_mail))? $tlgn_mail:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('メールアドレス'); ?>" class="allow_submit<?php echo $errClass['tlgn_mail']; ?>"></p>
		<?php echo $errMsg['tlgn_mail']; ?>

		<?php
		?>
		<p><input type="password" name="tlgn_pass" id="form_tlgn_pass" value="<?php echo (isset($tlgn_pass))? $tlgn_pass:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('パスワード'); ?>" class="allow_submit<?php echo $errClass['tlgn_pass']; ?>"></p>
		<?php echo $errMsg['tlgn_pass']; ?>

		<p class="checkbox-box"><?php echo Form::label(Form::checkbox('tlgn_chk',1,$tlgn_chk,array('id'=>'form_memchk')).'<span>'.__('次回からログイン情報の入力を省略する').'</span>','memchk'); ?></p>
		<p class="subtext"><a href="/t/password"><?php echo __('パスワードを忘れた方はこちら'); ?></a></p>
		<p class="subtext"><?php echo __('ログインすることで、利用規約およびプライバシーポリシーに同意いただいたものとします。'); ?></p>
		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('ログイン'); ?></button></p>
		</form>
		<hr>
		<p class="button-box mt16"><a href="/auth/login/facebook/TLOGIN" class="button do facebook social-service"><i class="fa fa-facebook-official"></i><?php echo __(':providerでログインする',array('provider'=>'Facebook')); ?></a></p>
		<p class="button-box mt12"><a href="/auth/login/google/TLOGIN" class="button do google social-service"><i class="fa fa-google"></i><?php echo __(':providerでログインする',array('provider'=>'Google')); ?></a></p>
		<?php if (!CL_CAREERTASU_MODE && !preg_match('/CL_AIR/i', Input::user_agent())): ?>
		<p class="subtext"><a href="/t/entry" class="button na width-auto default"><?php echo __('新規登録される方はこちら'); ?></a></p>
		<?php endif; ?>
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
