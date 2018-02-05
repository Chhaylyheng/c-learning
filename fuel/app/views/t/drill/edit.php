<?php
	$errClass = array(
		'd_title'=>'','d_pubnum'=>''
	);
	$errMsg = $errClass;

	$sErr = null;
	if (!is_null($error)):
		$sErr = __('入力内容に誤りがあります。項目のメッセージを参考に修正してください。');
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	if (isset($aDrill)) {
		$sAction = 'edit/'.$aDCategory['dcID'].DS.$aDrill['dbNO'];
	} else {
		$sAction = 'create/'.$aDCategory['dcID'];
	}

?>

<div class="info-box">
<?php if (!is_null($sErr)): ?>
<p class="error-msg"><?php echo $sErr; ?></p>
<?php endif; ?>
<form action="/t/drill/<?php echo $sAction; ?>" method="post" enctype="multipart/form-data">
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('タイトル'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="d_title" value="<?php echo $d_title; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('タイトルを入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['d_title']; ?>>
				<?php echo $errMsg['d_title']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('出題数'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="d_pubnum" value="<?php echo $d_pubnum; ?>" maxlength="3" placeholder="10" class="width-6em text-right"<?php echo $errClass['d_pubnum']; ?>>
				<?php echo $errMsg['d_pubnum']; ?>
			</div>
		</div>
<?php
	$aCheck = array(1=>'',2=>'',3=>'');
	$aCheck[$d_select_style] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('選択肢の表示方法'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="d_select_style" value="1"<?php echo $aCheck[1]; ?>><?php echo __('一列'); ?><?php echo Asset::img('sentaku_01.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
				<label class="formChk"><input type="radio" name="d_select_style" value="2"<?php echo $aCheck[2]; ?>><?php echo __('二列'); ?><?php echo Asset::img('sentaku_02.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
				<label class="formChk"><input type="radio" name="d_select_style" value="3"<?php echo $aCheck[3]; ?>><?php echo __('三列'); ?><?php echo Asset::img('sentaku_03.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$d_query_rand] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('選択肢の並び順'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="d_query_rand" value="0"<?php echo $aCheck[0]; ?>><?php echo __('標準'); ?>（1,2,3,4,5,…）</label>
				<label class="formChk"><input type="radio" name="d_query_rand" value="1"<?php echo $aCheck[1]; ?>><?php echo __('ランダム'); ?>（…,3,1,5,2,4）</label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$d_rand] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('問題の出題順'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="d_rand" value="0"<?php echo $aCheck[0]; ?>><?php echo __('標準'); ?>（1,2,3,4,5,…）</label>
				<label class="formChk"><input type="radio" name="d_rand" value="1"<?php echo $aCheck[1]; ?>><?php echo __('ランダム'); ?>（…,3,1,5,2,4）</label>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
<?php if (isset($aDrill)): ?>
		<input type="hidden" name="t_open" value="0">
		<button type="submit" class="button confirm" name="finish" value="1"><?php echo __('更新'); ?></button>
		<button type="submit" class="button do" name="next" value="1"><?php echo __('更新して問題編集へ'); ?></button>
<?php else: ?>
		<button type="submit" class="button confirm" name="finish" value="1"><?php echo __('作成'); ?></button>
		<button type="submit" class="button do" name="next" value="1"><?php echo __('作成して問題編集へ'); ?></button>
<?php endif; ?>
	</div>
</form>
</div>
