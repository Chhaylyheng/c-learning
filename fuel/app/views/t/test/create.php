<?php
	$errClass = array(
		't_name'=>'','t_qualify_score'=>'','t_limit_time'=>'',
		't_auto_s_time'=>'','t_auto_e_time'=>'',
		't_explain'=>'','tbImage'=>''
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
?>

<div class="info-box">
<?php if (!is_null($sErr)): ?>
<p class="error-msg"><?php echo $sErr; ?></p>
<?php endif; ?>
<form action="/t/test/create/<?php echo $fKey; ?>" method="post" enctype="multipart/form-data">
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('小テストタイトル'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="t_name" value="<?php echo $t_name; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('小テストタイトルを入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['t_name']; ?>>
				<?php echo $errMsg['t_name']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('合格点数'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="t_qualify_score" value="<?php echo $t_qualify_score; ?>" maxlength="5" placeholder="0" class="width-6em text-right"<?php echo $errClass['t_qualify_score']; ?>>
				<?php echo $errMsg['t_qualify_score']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('制限時間'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="t_limit_time" value="<?php echo $t_limit_time; ?>" maxlength="3" placeholder="0" class="width-6em text-right"<?php echo $errClass['t_limit_time']; ?>> 分
				<p class="font-gray"><?php echo __('※0分を指定すると制限時間が無制限となります。'); ?></p>
				<?php echo $errMsg['t_limit_time']; ?>
			</div>
		</div>
<?php
	$aCheck = array('','');
	$aCheck[$t_auto_public] = ' checked';
	$sDateDisp = ($t_auto_public)? 'block':'none';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('自動公開'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="t_auto_public" value="0"<?php echo $aCheck[0]; ?>><?php echo __('自動で公開しない'); ?></label>
				<label class="formChk"><input type="radio" name="t_auto_public" value="1"<?php echo $aCheck[1]; ?>><?php echo __('自動で公開する'); ?></label>
				<div class="auto-datetime mt8" style="display: <?php echo $sDateDisp; ?>;">
					<p><?php echo __('開始日時'); ?>
						<input type="text" name="t_auto_s_date" value="<?php echo $t_auto_s_date; ?>" id="datepick1" class="width-10em text-center" readonly<?php echo $errClass['t_auto_s_time']; ?>>
						<input type="text" name="t_auto_s_time" value="<?php echo $t_auto_s_time; ?>" class="timepick1 width-8em text-center ml8" maxlength="5"<?php echo $errClass['t_auto_s_time']; ?>>
					</p>
					<?php echo $errMsg['t_auto_s_time']; ?>
					<p><?php echo __('終了日時'); ?>
						<input type="text" name="t_auto_e_date" value="<?php echo $t_auto_e_date; ?>" id="datepick2" class="width-10em text-center" readonly<?php echo $errClass['t_auto_e_time']; ?>>
						<input type="text" name="t_auto_e_time" value="<?php echo $t_auto_e_time; ?>" class="timepick2 width-8em text-center ml8" maxlength="5"<?php echo $errClass['t_auto_e_time']; ?>>
					</p>
					<?php echo $errMsg['t_auto_e_time']; ?>
				</div>
			</div>
		</div>
<?php
	$aCheck = array(1=>'',2=>'',3=>'');
	$aCheck[$t_select_style] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('選択肢の表示方法'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="t_select_style" value="1"<?php echo $aCheck[1]; ?>><?php echo __('一列'); ?><?php echo Asset::img('sentaku_01.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
				<label class="formChk"><input type="radio" name="t_select_style" value="2"<?php echo $aCheck[2]; ?>><?php echo __('二列'); ?><?php echo Asset::img('sentaku_02.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
				<label class="formChk"><input type="radio" name="t_select_style" value="3"<?php echo $aCheck[3]; ?>><?php echo __('三列'); ?><?php echo Asset::img('sentaku_03.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$t_query_rand] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('選択肢の並び順'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="t_query_rand" value="0"<?php echo $aCheck[0]; ?>><?php echo __('標準'); ?>（1,2,3,4,5,…）</label>
				<label class="formChk"><input type="radio" name="t_query_rand" value="1"<?php echo $aCheck[1]; ?>><?php echo __('ランダム'); ?>（…,3,1,5,2,4）</label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'',2=>'',3=>'');
	$aCheck[$t_score_public] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('点数、解説の公開'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="t_score_public" value="0"<?php echo $aCheck[0]; ?>><?php echo __('非公開'); ?></label>
				<label class="formChk"><input type="radio" name="t_score_public" value="1"<?php echo $aCheck[1]; ?>><?php echo __('点数の公開'); ?></label>
				<label class="formChk"><input type="radio" name="t_score_public" value="2"<?php echo $aCheck[2]; ?>><?php echo __('解説の公開'); ?></label>
				<label class="formChk"><input type="radio" name="t_score_public" value="3"<?php echo $aCheck[3]; ?>><?php echo __('点数と解説の公開'); ?></label>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('小テストの全体的な解説'); ?><br><?php echo __('（省略可）'); ?></div>
			<div class="formContent inline-box">
				<textarea name="t_explain" class="width-40em"<?php echo $errClass['t_explain']; ?>><?php echo $t_explain; ?></textarea>
				<p class="font-gray"><?php echo __('※学生の解答後に表示されます。（解説が公開されている必要があります。）'); ?></p>
				<?php echo $errMsg['t_explain']; ?>
			</div>
		</div>
<?php
	$sImgPath = DS.CL_UPDIR.DS.'temp'.DS.'test'.DS.$fKey.DS;
	$aBaseImg = array('disp'=>'none','img'=>'','src'=>'','value'=>'');
	if ($tbImage):
		$aBaseImg['disp'] = '';
		$aBaseImg['img'] = $tbImage;
		$aBaseImg['src'] = $sImgPath.$tbImage.'?'.mt_rand();
		$aBaseImg['value'] = $fKey.'_'.$tbImage;
	endif;
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('小テストの全体的な解説画像'); ?><br><?php echo __('（省略可）'); ?></div>
			<div class="formContent inline-box">
				<p><img src="<?php echo $aBaseImg['src']; ?>" style="max-width: 200px; max-height: 150px; display: <?php echo $aBaseImg['disp']; ?>;" id="bImage"></p>
				<p>
					<button type="button" class="button default na TestBaseImageDelete" value="<?php echo $aBaseImg['value']; ?>" style="display: <?php echo $aBaseImg['disp']; ?>; margin-bottom: 8px;" id="bImageDel"><i class="fa fa-trash-o"></i> <?php echo __('画像削除'); ?></button>
					<input type="file" value="" name="bImage"<?php echo $errClass['tbImage']; ?>>
					<input type="hidden" value="<?php echo $aBaseImg['img']; ?>" name="tbImage">
					<span class="font-size-80"><?php echo __('※:sizeMBまでの画像ファイル（JPG,JPEG,GIF,PNG）が設定できます。',array('size'=>CL_IMGSIZE)); ?></span><br>
				</p>
				<?php echo $errMsg['tbImage']; ?>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button confirm" name="finish" value="1"><?php echo __('作成'); ?></button>
		<button type="submit" class="button do" name="next" value="1"><?php echo __('作成して問題編集へ'); ?></button>
	</div>
</form>
</div>
