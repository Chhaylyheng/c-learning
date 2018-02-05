<?php
	$errClass = array('q_name'=>'','q_auto_s_time'=>'','q_auto_e_time'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
?>


<div class="info-box">
<form action="/t/quest/create" method="post">
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('アンケートタイトル'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="q_name" value="<?php echo $q_name; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('アンケートタイトルを入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['q_name']; ?>>
				<?php echo $errMsg['q_name']; ?>
			</div>
		</div>
<?php
	$aCheck = array('','');
	$aCheck[$q_auto_public] = ' checked';
	$sDateDisp = ($q_auto_public)? 'block':'none';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('自動公開'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_auto_public" value="0"<?php echo $aCheck[0]; ?>><?php echo __('自動で公開しない'); ?></label>
				<label class="formChk"><input type="radio" name="q_auto_public" value="1"<?php echo $aCheck[1]; ?>><?php echo __('自動で公開する'); ?></label>
				<div class="auto-datetime mt8" style="display: <?php echo $sDateDisp; ?>;">
					<p><?php echo __('開始日時'); ?>
						<input type="text" name="q_auto_s_date" value="<?php echo $q_auto_s_date; ?>" id="datepick1" class="width-10em text-center" readonly<?php echo $errClass['q_auto_s_time']; ?>>
						<input type="text" name="q_auto_s_time" value="<?php echo $q_auto_s_time; ?>" class="timepick1 width-8em text-center ml8" maxlength="5"<?php echo $errClass['q_auto_s_time']; ?>>
					</p>
					<?php echo $errMsg['q_auto_s_time']; ?>
					<p><?php echo __('終了日時'); ?>
						<input type="text" name="q_auto_e_date" value="<?php echo $q_auto_e_date; ?>" id="datepick2" class="width-10em text-center" readonly<?php echo $errClass['q_auto_e_time']; ?>>
						<input type="text" name="q_auto_e_time" value="<?php echo $q_auto_e_time; ?>" class="timepick2 width-8em text-center ml8" maxlength="5"<?php echo $errClass['q_auto_e_time']; ?>>
					</p>
					<?php echo $errMsg['q_auto_e_time']; ?>
				</div>
			</div>
		</div>
<?php
	$aCheck = array(1=>'',2=>'',3=>'');
	$aCheck[$q_select_style] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('選択肢の表示方法'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_select_style" value="1"<?php echo $aCheck[1]; ?>><?php echo __('一列'); ?><?php echo Asset::img('sentaku_01.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
				<label class="formChk"><input type="radio" name="q_select_style" value="2"<?php echo $aCheck[2]; ?>><?php echo __('二列'); ?><?php echo Asset::img('sentaku_02.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
				<label class="formChk"><input type="radio" name="q_select_style" value="3"<?php echo $aCheck[3]; ?>><?php echo __('三列'); ?><?php echo Asset::img('sentaku_03.png',array('style'=>'border: none; margin-left: 0.5em;')); ?></label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$q_select_sort] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('選択肢の並び順'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_select_sort" value="0"<?php echo $aCheck[0]; ?>><?php echo __('昇順（1,2,3,4,5,…）'); ?></label>
				<label class="formChk"><input type="radio" name="q_select_sort" value="1"<?php echo $aCheck[1]; ?>><?php echo __('降順（…,5,4,3,2,1）'); ?></label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$q_retry] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('答えなおし'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_retry" value="0"<?php echo $aCheck[0]; ?>><?php echo __('不可'); ?></label>
				<label class="formChk"><input type="radio" name="q_retry" value="1"<?php echo $aCheck[1]; ?>><?php echo __('可'); ?></label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'',2=>'');
	$aCheck[$q_ans_public] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('個人の回答内容の公開範囲'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_ans_public" value="0"<?php echo $aCheck[0]; ?>><?php echo __('非公開'); ?></label>
				<label class="formChk"><input type="radio" name="q_ans_public" value="1"<?php echo $aCheck[1]; ?>><?php echo __('回答者を匿名で公開'); ?></label>
				<label class="formChk"><input type="radio" name="q_ans_public" value="2"<?php echo $aCheck[2]; ?>><?php echo __('公開'); ?></label>
			</div>
		</div>
<?php
	$aCheck = array(0=>'',1=>'',2=>'');
	$aCheck[$q_com_public] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('個人宛の先生コメントの公開範囲'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_com_public" value="0"<?php echo $aCheck[0]; ?>><?php echo __('非公開'); ?></label>
				<label class="formChk"><input type="radio" name="q_com_public" value="1"<?php echo $aCheck[1]; ?>><?php echo __('回答者本人のみに公開'); ?></label><br>
				<label class="formChk"><input type="radio" name="q_com_public" value="2"<?php echo $aCheck[2]; ?>><?php echo __('回答内容を閲覧できる全ての人に公開'); ?></label>
			</div>
		</div>

<?php
if (!is_null($aGroup)):
$aCheck = array(0=>'',1=>'');
$aCheck[$q_anonymous] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('匿名回答'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_anonymous" value="0"<?php echo $aCheck[0]; ?>><?php echo __('記名'); ?></label>
				<label class="formChk"><input type="radio" name="q_anonymous" value="1"<?php echo $aCheck[1]; ?>><?php echo __('匿名'); ?></label>
			</div>
		</div>
<?php endif; ?>

<?php
	$aCheck = array(0=>'',1=>'',2=>'');
	$aCheck[$q_open] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('ゲスト回答'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="q_open" value="0"<?php echo $aCheck[0]; ?>><?php echo __('許可しない'); ?></label>
				<label class="formChk"><input type="radio" name="q_open" value="1"<?php echo $aCheck[1]; ?>><?php echo __('許可する（匿名回答）'); ?></label>
				<label class="formChk"><input type="radio" name="q_open" value="2"<?php echo $aCheck[2]; ?>><?php echo __('許可する（記名回答）'); ?></label>
				<p class="font-gray font-size-80 mt4"><?php echo __('ゲスト回答とは…'); ?><br>　<?php echo __(':siteに登録していない方が、このアンケートに回答することができる機能です。',array('site'=>CL_SITENAME)); ?></p>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button confirm" name="finish" value="1"><?php echo __('作成'); ?></button>
		<button type="submit" class="button do" name="next" value="1"><?php echo __('作成して設問編集へ'); ?></button>
	</div>
</form>
</div>
