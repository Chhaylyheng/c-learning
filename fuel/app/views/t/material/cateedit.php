<?php
	$errClass = array('mc_name'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	if (isset($aMCategory)):
		$sAction = 'cateedit/'.$aMCategory['mcID'];
		$sSubmit = __('更新');
	else:
		$sAction = 'catecreate';
		$sSubmit = __('作成');
	endif;

?>


<div class="info-box">
<form action="/t/material/<?php echo $sAction; ?>" method="post">
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('カテゴリ名'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="mc_name" value="<?php echo $mc_name; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('カテゴリ名を入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['mc_name']; ?>>
				<?php echo $errMsg['mc_name']; ?>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$mc_mail] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('メール通知'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="mc_mail" value="0"<?php echo $aCheck[0]; ?>><?php echo __('通知しない'); ?></label>
				<label class="formChk"><input type="radio" name="mc_mail" value="1"<?php echo $aCheck[1]; ?>><?php echo __('通知する'); ?></label>
				<p class="font-silver"><?php echo __('※教材を公開した際に学生へメール通知をしたい場合は「通知する」を選択します。'); ?></p>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubmit; ?></button>
	</div>
</form>
</div>
