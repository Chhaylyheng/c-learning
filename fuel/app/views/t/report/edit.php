<?php
	$errClass = array('r_name'=>'','r_text'=>'','r_auto_s_time'=>'','r_auto_e_time'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = __('登録');
	$sAction = 'create';
	if (isset($aReport)):
		$sSubBtn = __('更新');
		$sAction = 'edit/'.$aReport['rbID'];
	endif;
?>


<div class="info-box">
<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>
<form action="/t/report/<?php echo $sAction; ?>" method="post">
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('テーマタイトル'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="r_name" value="<?php echo $r_name; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('レポートのテーマタイトルを入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['r_name']; ?>>
				<?php echo $errMsg['r_name']; ?>
			</div>
		</div>
<?php
	$aCheck = array('','');
	$aCheck[$r_auto_public] = ' checked';
	$sDateDisp = ($r_auto_public)? 'block':'none';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('自動公開'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="r_auto_public" value="0"<?php echo $aCheck[0]; ?>><?php echo __('自動で公開しない'); ?></label>
				<label class="formChk"><input type="radio" name="r_auto_public" value="1"<?php echo $aCheck[1]; ?>><?php echo __('自動で公開する'); ?></label>
				<div class="auto-datetime mt8" style="display: <?php echo $sDateDisp; ?>;">
					<p><?php echo __('開始日時'); ?>
						<input type="text" name="r_auto_s_date" value="<?php echo $r_auto_s_date; ?>" id="datepick1" class="width-10em text-center" readonly<?php echo $errClass['r_auto_s_time']; ?>>
						<input type="text" name="r_auto_s_time" value="<?php echo $r_auto_s_time; ?>" class="timepick1 width-8em text-center ml8" maxlength="5"<?php echo $errClass['r_auto_s_time']; ?>>
					</p>
					<?php echo $errMsg['r_auto_s_time']; ?>
					<p><?php echo __('終了日時'); ?>
						<input type="text" name="r_auto_e_date" value="<?php echo $r_auto_e_date; ?>" id="datepick2" class="width-10em text-center" readonly<?php echo $errClass['r_auto_e_time']; ?>>
						<input type="text" name="r_auto_e_time" value="<?php echo $r_auto_e_time; ?>" class="timepick2 width-8em text-center ml8" maxlength="5"<?php echo $errClass['r_auto_e_time']; ?>>
					</p>
					<?php echo $errMsg['r_auto_e_time']; ?>
				</div>
			</div>
		</div>

		<div class="formGroup">
			<div class="formLabel"><?php echo __('内容/備考'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<textarea name="r_text" class="width-50em text-left<?php echo $errClass['r_text']; ?>" rows="6"><?php echo $r_text; ?></textarea>
				<?php echo $errMsg['r_text']; ?>
			</div>
		</div>

		<div class="formGroup">
			<div class="formLabel"><?php echo __('添付ファイル'); ?></div>
			<div class="formContent inline-box">
				<ul class="file-uploader">
<?php
	$bAlready = false;
	if ($r_file):
		$bAlready = true;
		$sName = $base_fileinfo['name'];
		$sFile = \Uri::create('getfile/s3file/:fid',array('fid'=>$base_fileinfo['file']));
		$sSize = \Clfunc_Common::FilesizeFormat($base_fileinfo['size'],1);
	endif;
?>
					<li class="width-20em">
						<div class="input-cover text-center" style="background-size: cover;<?php echo (($bAlready)? 'background-image: url(\''.$sFile.'\')':'');?>">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="uploaded-file" style="display: <?php echo (($bAlready)? 'block':'none'); ?>;">
								<p><i class="fa fa-paperclip"></i> <a href="<?php echo (($bAlready)? $sFile:'');; ?>" class="file" target="_blank"><span class="name"><?php echo (($bAlready)? $sName:''); ?></span></a><br><span class="size"><?php echo (($bAlready)? $sSize:''); ?></span></p>
								<p class="remove"><i class="fa fa-times fa-2x"></i></p>
							</div>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="r_file" value="<?php echo (($bAlready)? htmlspecialchars(serialize($base_fileinfo)):''); ?>">
					</li>
				</ul>
			</div>
		</div>

<?php if (isset($aReport)): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('結果ファイル'); ?></div>
			<div class="formContent inline-box">
				<ul class="file-uploader">
<?php
	$bAlready = false;
	if ($r_result):
		$bAlready = true;
		$sName = $result_fileinfo['name'];
		$sFile = \Uri::create('getfile/s3file/:fid',array('fid'=>$result_fileinfo['file']));
		$sSize = \Clfunc_Common::FilesizeFormat($result_fileinfo['size'],1);
	endif;
?>
					<li class="width-20em">
						<div class="input-cover text-center" style="background-size: cover;<?php echo (($bAlready)? 'background-image: url(\''.$sFile.'\')':'');?>">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="uploaded-file" style="display: <?php echo (($bAlready)? 'block':'none'); ?>;">
								<p><i class="fa fa-paperclip"></i> <a href="<?php echo (($bAlready)? $sFile:'');; ?>" class="file" target="_blank"><span class="name"><?php echo (($bAlready)? $sName:''); ?></span></a><br><span class="size"><?php echo (($bAlready)? $sSize:''); ?></span></p>
								<p class="remove"><i class="fa fa-times fa-2x"></i></p>
							</div>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="r_result" value="<?php echo (($bAlready)? htmlspecialchars(serialize($result_fileinfo)):''); ?>">
					</li>
				</ul>
			</div>
		</div>
<?php endif; ?>

<?php
	$aCheck = array(0=>'', 1=>'', 2=>'');
	$aCheck[$r_share] = ' checked';
	$disp = ($r_share)? 'block':'none';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('共有設定'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="r_share" value="0"<?php echo $aCheck[0]; ?>><?php echo __('共有しない'); ?></label>
				<label class="formChk"><input type="radio" name="r_share" value="1"<?php echo $aCheck[1]; ?>><?php echo __('共有する'); ?></label>
				<label class="formChk"><input type="radio" name="r_share" value="2"<?php echo $aCheck[2]; ?>><?php echo __('共有する（相互評価）'); ?></label>
				<p class="font-gray font-size-80 mt4"><?php echo __('共有とは…'); ?><br>　<?php echo __('提出されたレポートを学生同士で閲覧し、コメントができるようになります。<br>また、「相互評価」を選択することで、互いのレポートを5段階で評価できるようになります。'); ?></p>

<?php
	$aCheck = array(0=>'',1=>'',2=>'');
	$aCheck[$r_anonymous] = ' checked';
?>
				<div class="mt8 rAnonymous" style="display: <?php echo $disp; ?>">
					<p class="font-bold"><?php echo __('コメントや評価の匿名設定'); ?></p>
					<label class="formChk"><input type="radio" name="r_anonymous" value="0"<?php echo $aCheck[0]; ?>><?php echo __('匿名'); ?></label>
					<label class="formChk"><input type="radio" name="r_anonymous" value="1"<?php echo $aCheck[1]; ?>><?php echo __('先生のみ記名'); ?></label>
					<label class="formChk"><input type="radio" name="r_anonymous" value="2"<?php echo $aCheck[2]; ?>><?php echo __('記名'); ?></label>
				</div>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="finish" value="1"><?php echo $sSubBtn; ?></button>
	</div>
</form>
</div>
