<?php if (isset($ses['SES_S_ERROR_MSG'])): ?>
<p class="error-box mb16"><?php echo nl2br($ses['SES_S_ERROR_MSG']); ?></p>
<?php Session::delete('SES_S_ERROR_MSG'); ?>
<?php endif; ?>
<?php if (isset($ses['SES_S_NOTICE_MSG'])): ?>
<div class="info-box tmp mb16">
	<p><?php echo nl2br($ses['SES_S_NOTICE_MSG']); ?></p>
	<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png',array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
</div>
<?php Session::delete('SES_S_NOTICE_MSG'); ?>
<?php endif; ?>

<?php echo View::forge('selectlang'); ?>

<div id="content-inner" class="login">
	<h1 class="mt8 mr0"><?php echo __('学生ログイン'); ?></h1>
	<div class="info-box mt16">
		<form action="/s/login/loginchk" method="post">
		<input type="hidden" name="ltzone" value="">

		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php if (isset($error['login'])): ?>
			<p class="error-msg"><?php echo $error['login'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('slgn_id'=>'','slgn_pass'=>'');
			$errMsg = array('slgn_id'=>'','slgn_pass'=>'');

			if (isset($error['slgn_id'])):
				$errClass['slgn_id'] = ' input-error';
				$errMsg['slgn_id'] = '<p class="error-msg">'.$error['slgn_id'].'</p>';
			endif;
			if (isset($error['slgn_pass'])):
				$errClass['slgn_pass'] = ' input-error';
				$errMsg['slgn_pass'] = '<p class="error-msg">'.$error['slgn_pass'].'</p>';
			endif;
		?>
		<p><input type="text" name="slgn_id" id="form_slgn_id" value="<?php echo (isset($slgn_id))? $slgn_id:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('ログインIDまたはメールアドレス'); ?>" class="allow_submit<?php echo $errClass['slgn_id']; ?>"></p>
		<?php echo $errMsg['slgn_id']; ?>

		<p><input type="password" name="slgn_pass" id="form_slgn_pass" value="<?php echo (isset($slgn_pass))? $slgn_pass:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('パスワード'); ?>" class="allow_submit<?php echo $errClass['slgn_pass']; ?>"></p>
		<?php echo $errMsg['slgn_pass']; ?>

		<p class="checkbox-box">
			<?php echo Form::label(Form::checkbox('slgn_chk',1,$slgn_chk,array('id'=>'form_memchk')).__('次回からログイン情報の入力を省略する'),'memchk'); ?>
		</p>
		<p class="subtext"><a href="/s/password"><?php echo __('パスワードを忘れた方はこちら'); ?></a></p>
		<p class="button-box"><button type="submit" class="button do register" name="state" value="1"><?php echo __('ログイン'); ?></button></p>
		<p class="subtext"><a href="/s/entry" class="button na default width-auto"><?php echo __('新規登録される方はこちら'); ?></a></p>
		</form>
	</div>
</div>
<div id="footer-menu">
	<nav>
		<ul>
		<li><a href="http://c-learning.jp/"><?php echo __(':siteについて',array('site'=>CL_SITENAME)); ?></a></li>
		<li><a href="http://c-learning.jp/prd/truste.html" target="_blank"><?php echo __('プライバシーポリシー'); ?></a></li>
		<li><a href="http://netman.co.jp/"><?php echo __('運営会社について'); ?></a></li>
		</ul>
	</nav>
</div>
