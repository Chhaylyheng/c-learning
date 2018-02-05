<div id="content-inner" class="login">

	<h1 class="mt8">契約管理センターログイン</h1>

	<div class="info-box mt16">
		<form action="/adm/AdminLogin/loginchk" method="post" role="form">
		<input type="hidden" name="ltzone" value="">

		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php if (isset($error['login'])): ?>
			<p class="error-msg"><?php echo $error['login']; ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('algn_id'=>'','algn_pass'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $k => $v):
				if (isset($error[$k])):
					$errClass[$k] = ' input-error';
					$errMsg[$k] = '<p class="error-msg">'.$error[$k].'</p>';
				endif;
			endforeach;
		?>
		<p><input type="text" name="algn_id" id="form_algn_id" value="<?php echo $algn_id; ?>" size="30" maxlength="200" placeholder="ログインID" class="allow_submit<?php echo $errClass['algn_id']; ?>"></p>
		<?php echo $errMsg['algn_id']; ?>

		<p><input type="password" name="algn_pass" id="form_algn_pass" value="<?php echo $algn_pass; ?>" size="30" maxlength="200" placeholder="パスワード" class="allow_submit<?php echo $errClass['algn_pass']; ?>"></p>
		<?php echo $errMsg['algn_pass']; ?>

		<p class="checkbox-box"><?php echo Form::label(Form::checkbox('algn_chk',1,$algn_chk,array('id'=>'form_memchk')).'<span>次回からログイン情報の入力を省略する</span>','memchk'); ?></p>
		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1">ログイン</button></p>
		</form>
	</div>
</div>
