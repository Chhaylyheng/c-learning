<?php echo View::forge('selectlang'); ?>

<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('団体管理ログイン'); ?></h1>

	<div class="info-box mt16">
		<form action="/org/login/loginchk" method="post" role="form">
		<input type="hidden" name="ltzone" value="">

		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php if (isset($error['login'])): ?>
			<p class="error-msg"><?php echo $error['login']; ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('olgn_id'=>'','olgn_pass'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $k => $v):
				if (isset($error[$k])):
					$errClass[$k] = ' input-error';
					$errMsg[$k] = '<p class="error-msg">'.$error[$k].'</p>';
				endif;
			endforeach;
		?>
		<p><input type="text" name="olgn_id" id="form_olgn_id" value="<?php echo $olgn_id; ?>" size="30" maxlength="200" placeholder="<?php echo __('ログインID'); ?>" class="allow_submit<?php echo $errClass['olgn_id']; ?>"></p>
		<?php echo $errMsg['olgn_id']; ?>

		<p><input type="password" name="olgn_pass" id="form_olgn_pass" value="<?php echo $olgn_pass; ?>" size="30" maxlength="200" placeholder="<?php echo __('パスワード'); ?>" class="allow_submit<?php echo $errClass['olgn_pass']; ?>"></p>
		<?php echo $errMsg['olgn_pass']; ?>

		<p class="checkbox-box"><?php echo Form::label(Form::checkbox('olgn_chk',1,$olgn_chk,array('id'=>'form_memchk')).'<span>'.__('次回からログイン情報の入力を省略する').'</span>','memchk'); ?></p>

		<p class="subtext"><?php echo __('ログインIDまたはパスワードを忘れた方は、お手数ですが契約管理センター宛てに「学校名」「氏名」「メールアドレス」をご明記の上、:mailtosメール:mailtoeにてお問い合わせください。', array('mailtos'=>'<a href="mailto:keiyaku@netman.co.jp">','mailtoe'=>'</a>')); ?></p>

		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('ログイン'); ?></button></p>
		</form>
	</div>
</div>
