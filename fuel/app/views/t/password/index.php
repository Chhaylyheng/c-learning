<div id="content-inner" class="login">
	<ul id="breadcrumbs">
		<li><a href="/t"><?php echo __('先生ログイン'); ?></a></li>
		<li><?php echo __('先生パスワードの再設定'); ?></li>
	</ul>

	<h1><?php echo __('先生パスワードの再設定'); ?></h1>

	<div class="info-box">
		<form action="/t/password" method="post" role="form">

		<?php
			$errClass = array('reset_mail'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $k => $v):
				if (isset($error[$k])):
					$errClass[$k] = ' class="input-error"';
					$errMsg[$k] = '<p class="error-msg">'.$error[$k].'</p>';
				endif;
			endforeach;
		?>
		<p><input type="text" name="reset_mail" id="form_reset_mail" value="<?php echo $reset_mail; ?>" size="30" maxlength="200" placeholder="<?php echo __('メールアドレス'); ?>"<?php echo $errClass['reset_mail']; ?>></p>
		<?php echo $errMsg['reset_mail']; ?>

		<p class="subtext"><?php echo __('パスワードを再設定するメールアドレスを入力してください。'); ?></p>
		<p class="subtext"><?php echo __('入力されたメールアドレスに再設定手続きのメールを送信いたします。'); ?></p>
		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('パスワード再設定メールを送信'); ?></button></p>
		</form>
	</div>
</div>

