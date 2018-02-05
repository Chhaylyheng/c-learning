<?php
	$errClass = array(
		'd_title'=>'','d_pubnum'=>''
	);
	$errMsg = $errClass;

	$sErr = null;
	if (isset($aInput['error'])):
		$sErr = __('入力内容に誤りがあります。項目のメッセージを参考に修正してください。');
		foreach ($errClass as $key => $val):
			if (isset($aInput['error'][$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$aInput['error'][$key].'</p>';
			endif;
		endforeach;
	endif;

	$sDisp = 'none';
	if ($aInput['d_select'] == 'new')
	{
		$sDisp = 'table';
	}
?>

<?php if (!is_null($sErr)): ?>
<p class="error-box mb16"><?php echo $sErr; ?></p>
<?php endif; ?>

<div class="info-box">
	<div class="info-box mt0">
		<h4 class="mb16">2. <?php echo __('コピー先のドリルを選択してください。'); ?></h4>
		<form action="/t/test/dbselect/<?php echo $aTest['tbID']; ?>" method="post">
		<input type="hidden" name="subchk" value="1">
			<div>
				<?php echo __('カテゴリ名'); ?>
				<select class="dropdown text-left" name="d_category">
				<?php foreach ($aDrillCate as $aC): ?>
					<?php $sSel = ($aC['dcID'] == $aInput['d_category'])? ' selected':''; ?>
					<option value="<?php echo $aC['dcID']; ?>" class="text-left"<?php echo $sSel; ?>><?php echo $aC['dcName']; ?></option>
				<?php endforeach; ?>
				</select>　
				<?php echo __('ドリル'); ?>
				<select class="dropdown text-left" name="d_select">
					<option value="0" class="font-silver text-left"><?php echo __('選択してください'); ?></option>
					<?php if (!is_null($aDrill)): ?>
					<?php foreach ($aDrill as $aD): ?>
						<?php $sSel = ($aD['dbNO'] == $aInput['d_select'])? ' selected':''; ?>
						<option value="<?php echo $aD['dbNO']; ?>" class="text-left"<?php echo $sSel; ?>><?php echo $aD['dbTitle']; ?></option>
					<?php endforeach; ?>
					<?php endif; ?>
					<?php $sSel = ($aInput['d_select'] == 'new')? ' selected':''; ?>
					<option value="new" class="text-left"<?php echo $sSel; ?>>*<?php echo __('ドリルの新規登録'); ?></option>
				</select>
			</div>
		</div>

	<div class="formControl mt16 ml16" id="drill-create" style="margin: 0; display: <?php echo $sDisp; ?>">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('タイトル'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="d_title" value="<?php echo $aInput['d_title']; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('タイトルを入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['d_title']; ?>>
				<?php echo $errMsg['d_title']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('出題数'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="d_pubnum" value="<?php echo $aInput['d_pubnum']; ?>" maxlength="3" placeholder="10" class="width-6em text-right"<?php echo $errClass['d_pubnum']; ?>>
				<?php echo $errMsg['d_pubnum']; ?>
			</div>
		</div>
<?php
	$aCheck = array(1=>'',2=>'',3=>'');
	$aCheck[$aInput['d_select_style']] = ' checked';
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
	$aCheck[$aInput['d_query_rand']] = ' checked';
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
	$aCheck[$aInput['d_rand']] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('問題の出題順'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="d_rand" value="0"<?php echo $aCheck[0]; ?>><?php echo __('標準'); ?>（1,2,3,4,5,…）</label>
				<label class="formChk"><input type="radio" name="d_rand" value="1"<?php echo $aCheck[1]; ?>><?php echo __('ランダム'); ?>（…,3,1,5,2,4）</label>
			</div>
		</div>
	</div>

		<div class="button-box mt16 text-center">
			<button type="submit" class="button na default width-auto" name="back" value="1" style="float: left;"><?php echo __('戻る'); ?></button>
			<button type="submit" class="button na do width-auto"><?php echo __('実行する'); ?></button>
		</div>
		</form>
	</div>
</div>
