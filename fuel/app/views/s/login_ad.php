<?php echo View::forge('selectlang'); ?>

<div id="content-inner" class="login">

	<h1 class="mt8 mr0"><?php echo $aGroup['gtName']; ?> <?php echo __('学生ログイン'); ?></h1>

	<div class="info-box mt16">
		<form action="/<?php echo $aGroup['gtPrefix']; ?>/s" method="post" role="form">
		<input type="hidden" name="ltzone" value="">

		<?php $sMsg = \Session::get('SES_S_ERROR_MSG', false); ?>
		<?php if ($sMsg): ?>
		<p class="error-msg"><?php echo nl2br($sMsg); ?></p>
		<?php \Session::delete('SES_S_ERROR_MSG'); ?>
		<?php endif; ?>

		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php if (isset($error['login'])): ?>
			<p class="error-msg"><?php echo $error['login'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('slgn_id'=>'','slgn_pass'=>'');
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
		<p><input type="text" name="slgn_id" id="form_slgn_id" value="<?php echo (isset($slgn_id))? $slgn_id:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('ログインID'); ?>" class="allow_submit<?php echo $errClass['slgn_id']; ?>"></p>
		<?php echo $errMsg['slgn_id']; ?>

		<p><input type="password" name="slgn_pass" id="form_slgn_pass" value="<?php echo (isset($slgn_pass))? $slgn_pass:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('パスワード'); ?>" class="allow_submit<?php echo $errClass['slgn_pass']; ?>"></p>
		<?php echo $errMsg['slgn_pass']; ?>

		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('ログイン'); ?></button></p>
		</form>
	</div>
</div>
