<div id="content-inner" class="login">

	<h1><?php echo __('副担当パスワードの再設定'); ?></h1>

	<div class="info-box">
		<form action="/a/password/reset/<?php echo $hash; ?>" method="post" role="form">
		<?php if (isset($error['pass'])): ?>
			<p class="error-msg"><?php echo $error['pass'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('pre_pass'=>'','pre_passchk'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $k => $v):
				if (isset($error[$k])):
					$errClass[$k] = ' class="input-error"';
					$errMsg[$k] = '<p class="error-msg">'.$error[$k].'</p>';
				endif;
			endforeach;
		?>

		<p><?php echo __('新しいパスワード'); ?></p>
		<p><input type="password" name="pre_pass" id="form_pre_pass" value="<?php echo $pre_pass; ?>" size="30" maxlength="32" placeholder="<?php echo __('パスワードを入力してください'); ?>"<?php echo $errClass['pre_pass']; ?>></p>
		<p class="mt4 font-silver font-size-90"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?></p>
		<?php echo $errMsg['pre_pass']; ?>

		<p><?php echo __('パスワード（確認）'); ?></p>
		<p><input type="password" name="pre_passchk" id="form_pre_passchk" value="<?php echo $pre_passchk; ?>" size="30" maxlength="32" placeholder="<?php echo __('確認のため再度入力してください'); ?>"<?php echo $errClass['pre_passchk']; ?>></p>
		<?php echo $errMsg['pre_passchk']; ?>

		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('パスワードの再設定'); ?></button></p>
		</form>
	</div>
</div>

