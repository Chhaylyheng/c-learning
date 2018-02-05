<?php echo View::forge('selectlang'); ?>

<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo $aGroup['gtName']; ?> <?php echo __('先生ログイン'); ?></h1>

	<div class="info-box mt16">
		<form action="/<?php echo $aGroup['gtPrefix']; ?>/t" method="post" role="form">
		<input type="hidden" name="ltzone" value="">

		<?php $sMsg = \Session::get('SES_T_ERROR_MSG', false); ?>
		<?php if ($sMsg): ?>
		<p class="error-msg"><?php echo nl2br($sMsg); ?></p>
		<?php \Session::delete('SES_T_ERROR_MSG'); ?>
		<?php endif; ?>

		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php if (isset($error['login'])): ?>
			<p class="error-msg"><?php echo $error['login'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('tlgn_id'=>'','tlgn_pass'=>'');
			$errMsg = $errClass;

			if (!is_null($error)):
				foreach ($errClass as $key => $val):
					if (isset($error[$key])):
						$errClass[$key] = ' input-error';
						$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
					endif;
				endforeach;
			endif;
		?>
		<p><input type="text" name="tlgn_id" id="form_tlgn_id" value="<?php echo (isset($tlgn_id))? $tlgn_id:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('ログインID'); ?>" class="allow_submit<?php echo $errClass['tlgn_id']; ?>"></p>
		<?php echo $errMsg['tlgn_id']; ?>
		<p><input type="password" name="tlgn_pass" id="form_tlgn_pass" value="<?php echo (isset($tlgn_pass))? $tlgn_pass:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('パスワード'); ?>" class="allow_submit<?php echo $errClass['tlgn_pass']; ?>"></p>
		<?php echo $errMsg['tlgn_pass']; ?>

		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('ログイン'); ?></button></p>
		</form>
	</div>
</div>

