<?php
	$errClass = array(
		'ga_name'=>'',
		'ga_login'=>'',
		'ga_pass'=>''
	);
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '登録';
	$sAction = 'admcreate/'.$aGroup['gtID'];
	if (isset($aGAdmin)):
		$sSubBtn = '更新';
		$sAction = 'admedit/'.$aGroup['gtID'].DS.$aGAdmin['gaID'];
	endif;
?>



<div class="info-box">
	<form action="/adm/group/<?php echo $sAction; ?>" method="post">

	<p class="mt0 text-right"><sup>*</sup>は必須項目</p>
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel">ログインID<sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="ga_login" maxlength="20" value="<?php echo $ga_login; ?>" placeholder="ログインIDを入力してください"<?php echo $errClass['ga_login']; ?>>
				<?php echo $errMsg['ga_login']; ?>
			</div>
		</div>
		<?php if (!isset($aGAdmin)): ?>
		<div class="formGroup">
			<div class="formLabel">パスワード</div>
			<div class="formContent inline-box">
				<input type="password" name="ga_pass" maxlength="32" value="<?php echo $ga_pass; ?>" placeholder="パスワードを入力してください"<?php echo $errClass['ga_pass']; ?>>
				<p class="font-size-80 mt4 font-silver">
					※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。<br>
					※パスワードを省略すると、パスワードを自動で生成します。</p>
				<?php echo $errMsg['ga_pass']; ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="formGroup">
			<div class="formLabel">氏名<sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="ga_name" maxlength="50" value="<?php echo $ga_name; ?>" placeholder="氏名を入力してください"<?php echo $errClass['ga_name']; ?>>
				<?php echo $errMsg['ga_name']; ?>
			</div>
		</div>
	</div>
	<div class="button-box mt16">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?></button>
	</div>
	</form>
</div>

