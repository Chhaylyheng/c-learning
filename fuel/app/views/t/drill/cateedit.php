<?php
	$errClass = array('dc_name'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	if (isset($aDCategory)):
		$sAction = 'cateedit/'.$aDCategory['dcID'];
		$sSubmit = __('更新');
	else:
		$sAction = 'catecreate';
		$sSubmit = __('作成');
	endif;

?>


<div class="info-box">
<form action="/t/drill/<?php echo $sAction; ?>" method="post">
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('カテゴリ名'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="dc_name" value="<?php echo $dc_name; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('カテゴリ名を入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['dc_name']; ?>>
				<?php echo $errMsg['dc_name']; ?>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubmit; ?></button>
	</div>
</form>
</div>
