<?php
	$errClass = array(
		'al_title'  =>'',
		'al_time_s' =>'',
		'al_time_e' =>'',
		'al_opt1'   =>'',
		'al_opt2'   =>'',
		'al_text'   =>'',
		'al_file'   =>''
	);
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' input-error';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '登録';
	$sAction = 'create/'.$aALTheme['altID'];
	if (isset($aALog)):
		$sSubBtn = '更新';
		$sAction = 'edit/'.$aALTheme['altID'].DS.$aALog['no'];
	endif;
?>


<div class="info-box">
<form action="/s/alog/<?php echo $sAction; ?>" method="post">
	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altGoalLabel']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-blue" id="GoalText"><?php echo nl2br($aALGoal['algText']); ?></p>
				<a href="#" class="AlogGoalSet button na default" id="<?php echo $aALTheme['altID']; ?>" title="<?php echo __(':goalの設定',array('goal'=>$aALTheme['altGoalLabel'])); ?>"><i class="fa fa-flag mr0"></i><?php echo __(':goalの設定',array('goal'=>$aALTheme['altGoalLabel'])); ?></a>
			</div>
		</div>
<?php if ($aALTheme['altTitle']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTitleLabel']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altTitleDescription']; ?></p>
				<input type="text" name="al_title" value="<?php echo $al_title; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" class="width-40em <?php echo $errClass['al_title']; ?>">
				<?php echo $errMsg['al_title']; ?>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altRange']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altRangeLabel']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altRangeDescription']; ?></p>

				<input type="text" name="al_date_s" value="<?php echo $al_date_s; ?>" id="datepick3" class="width-10em text-center inline-block <?php echo $errClass['al_time_s']; ?>"
				><input type="text" name="al_time_s" value="<?php echo $al_time_s; ?>" maxlength="5" class="timepick1 width-8em text-center ml8 inline-block<?php echo $errClass['al_time_s']; ?>">
				～
				<input type="text" name="al_date_e" value="<?php echo $al_date_e; ?>" id="datepick4" class="width-10em text-center inline-block <?php echo $errClass['al_time_e']; ?>"
				><input type="text" name="al_time_e" value="<?php echo $al_time_e; ?>" maxlength="5" class="timepick2 width-8em text-center ml8 inline-block<?php echo $errClass['al_time_e']; ?>">

				<?php echo $errMsg['al_time_s']; ?>
				<?php echo $errMsg['al_time_e']; ?>
			</div>
		</div>
<?php endif; ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTextLabel']; ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altTextDescription']; ?></p>
				<textarea name="al_text" class="width-60em <?php echo $errClass['al_text']; ?>" rows="6"><?php echo $al_text; ?></textarea>
				<?php echo $errMsg['al_text']; ?>
			</div>
		</div>
<?php if ($aALTheme['altFile']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altFileLabel']; ?></div>
			<div class="formContent inline-box">
				<ul class="file-uploader">
<?php
	$bAlready = false;
	if ($fileinfo['file']):
		$bAlready = true;
		$sName = $fileinfo['name'];
		$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'temp','file'=>$fileinfo['file'], 'name'=>$fileinfo['name']));
		$sSize = \Clfunc_Common::FilesizeFormat($fileinfo['size'],1);
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
						<input type="hidden" name="al_file" value="<?php echo (($bAlready)? htmlspecialchars(serialize($fileinfo)):''); ?>">
					</li>
				</ul>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt1']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt1Label']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altOpt1Description']; ?></p>
				<textarea name="al_opt1" class="width-60em <?php echo $errClass['al_opt1']; ?>"><?php echo $al_opt1; ?></textarea>
				<?php echo $errMsg['al_opt1']; ?>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt2']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt2Label']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altOpt2Description']; ?></p>
				<textarea name="al_opt2" class="width-60em <?php echo $errClass['al_opt2']; ?>"><?php echo $al_opt2; ?></textarea>
				<?php echo $errMsg['al_opt2']; ?>
			</div>
		</div>
<?php endif; ?>
	</div>
	<div class="button-box mt32">
		<?php if (isset($aALog)): ?>
		<a href="/s/alog/delete/<?php echo $aALTheme['altID'].DS.$aALog['no'];; ?>" class="button default na width-auto mt16 AlogDelete" style="float:left;"><i class="fa fa-trash-o mr4"></i><?php echo __('削除'); ?></a>
		<?php endif; ?>
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?><?php echo __('確認'); ?></button>
	</div>
</form>
</div>

<form action="" method="post" id="GoalEditBox" class="width-95" style="display: none;">
	<a href="" class="font-size-180 GoalEditClose"><i class="fa fa-times mr0"></i></a>
	<input type="hidden" name="alt" value="">
	<div class="formControl font-size-90 width-100" style="margin: auto;">
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><?php echo __('活動履歴テーマ'); ?></div>
			<div class="formContent inline-box width-100 font-bold" id="ALTheme"></div>
		</div>
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;" id="ALGoalLabel"></div>
			<div class="formContent inline-box width-100">
				<p id="ALGoalDesc"></p>
				<textarea name="ag_text" class="width-100"></textarea>
			</div>
		</div>
	</div>
	<div class="button-box text-center mt4">
		<button class="button na do width-auto"><?php echo __('更新'); ?></button>
	</div>
</form>
