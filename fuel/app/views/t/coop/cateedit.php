<?php
	$errClass = array('cc_name'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	if (isset($aCCategory)):
		$sAction = 'cateedit/'.$aCCategory['ccID'];
		$sSubmit = __('更新');
	else:
		$sAction = 'catecreate';
		$sSubmit = __('作成');
	endif;

?>


<div class="info-box">
<form action="/t/coop/<?php echo $sAction; ?>" method="post">
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('協働板名'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="cc_name" value="<?php echo $cc_name; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('協働板名を入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['cc_name']; ?>>
				<?php echo $errMsg['cc_name']; ?>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$cc_stuwrite] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('学生の記事投稿'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="cc_stuwrite" value="0"<?php echo $aCheck[0]; ?>><?php echo __('不可'); ?></label>
				<label class="formChk"><input type="radio" name="cc_stuwrite" value="1"<?php echo $aCheck[1]; ?>><?php echo __('可'); ?></label>
				<p class="font-silver"><?php echo __('※不可とすると学生は協働板の閲覧のみ可能となります。'); ?></p>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'',2=>'');
	$aCheck[$cc_anonymous] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('匿名'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="cc_anonymous" value="0"<?php echo $aCheck[0]; ?>><?php echo __('匿名'); ?></label>
				<label class="formChk"><input type="radio" name="cc_anonymous" value="1"<?php echo $aCheck[1]; ?>><?php echo __('先生のみ記名'); ?></label>
				<label class="formChk"><input type="radio" name="cc_anonymous" value="2"<?php echo $aCheck[2]; ?>><?php echo __('記名'); ?></label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'',2=>'');
	$aCheck[$cc_sturange] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('対象学生'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="cc_sturange" value="0"<?php echo $aCheck[0]; ?>><?php echo __('なし'); ?></label>
				<label class="formChk"><input type="radio" name="cc_sturange" value="1"<?php echo $aCheck[1]; ?>><?php echo __('選択'); ?></label>
				<label class="formChk"><input type="radio" name="cc_sturange" value="2"<?php echo $aCheck[2]; ?>><?php echo __('全員'); ?></label>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubmit; ?></button>
	</div>
</form>
</div>
