<?php
	$errMsg = array(
		'al_title'  =>'',
		'al_time_s' =>'',
		'al_time_e' =>'',
		'al_opt1'   =>'',
		'al_opt2'   =>'',
		'al_text'   =>'',
		'al_file'   =>''
	);

	if (!is_null($error)):
		foreach ($errMsg as $key => $val):
			if (isset($error[$key])):
				$errMsg[$key] = '<div style="color:#CC0000;">'.$error[$key].'</div>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '登録';
	$sAction = 'create/'.$aALTheme['altID'];
	if (isset($aALog)):
		$sSubBtn = '更新';
		$sAction = 'edit/'.$aALTheme['altID'].'/'.$aALog['no'];
	endif;
?>

<form action="/s/alog/<?php echo $sAction; ?>" method="post" style="font-size: 80%;">
	<div style="text-align: right;"><?php echo __(':astは必須項目',array('ast'=>'※')); ?></div>

	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altGoalLabel']; ?></label>
		<div style="color: #0000CC;"><?php echo nl2br($aALGoal['algText']); ?></div>
	</div>
<?php if ($aALTheme['altTitle']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altTitleLabel']; ?></label>
		<div>
			<div style="color: gray;"><?php echo $aALTheme['altTitleDescription']; ?></div>
			<input type="text" name="al_title" value="<?php echo $al_title; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" style="width: 100%;">
			<?php echo $errMsg['al_title']; ?>
		</div>
	</div>
<?php endif; ?>
<?php if ($aALTheme['altRange']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altRangeLabel']; ?></label>
		<div>
			<div style="color: gray;"><?php echo $aALTheme['altRangeDescription']; ?></div>

			<input type="text" name="al_date_s" value="<?php echo $al_date_s; ?>" maxlength="10" style="width: 5.5em;"
			><input type="text" name="al_time_s" value="<?php echo $al_time_s; ?>" maxlength="5" style="width: 3em;">
			～
			<input type="text" name="al_date_e" value="<?php echo $al_date_e; ?>" maxlength="10" style="width: 5.5em;"
			><input type="text" name="al_time_e" value="<?php echo $al_time_e; ?>" maxlength="5" style="width: 3em;">

			<?php echo $errMsg['al_time_s']; ?>
			<?php echo $errMsg['al_time_e']; ?>
		</div>
	</div>
<?php endif; ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altTextLabel']; ?> ※</label>
		<div>
			<div style="color: gray;"><?php echo $aALTheme['altTextDescription']; ?></div>
			<textarea name="al_text" rows="4" style="width: 100%;"><?php echo $al_text; ?></textarea>
			<?php echo $errMsg['al_text']; ?>
		</div>
	</div>
<?php if ($aALTheme['altFile']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altFileLabel']; ?></label>
		<div>
<?php
if ($aALog['fID']):
	$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aALog['fID']));
	$sSize = \Clfunc_Common::FilesizeFormat($aALog['fSize'],1);
?>
<div><?php echo \Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $sLink; ?>"><?php echo $aALog['fName'].'('.$sSize.')'; ?></a></div>
<?php
endif;
?>
			<span style="color: gray;"><?php echo __('ファイルはPC等からアップロード可能です。'); ?></span>
		</div>
	</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt1']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altOpt1Label']; ?></label>
		<div>
			<div style="color: gray;"><?php echo $aALTheme['altOpt1Description']; ?></div>
			<textarea name="al_opt1" rows="4" style="width: 100%;"><?php echo $al_opt1; ?></textarea>
			<?php echo $errMsg['al_opt1']; ?>
		</div>
	</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt2']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altOpt2Label']; ?></label>
		<div>
			<div style="color: gray;"><?php echo $aALTheme['altOpt2Description']; ?></div>
			<textarea name="al_opt2" rows="4" style="width: 100%;"><?php echo $al_opt2; ?></textarea>
			<?php echo $errMsg['al_opt2']; ?>
		</div>
	</div>
<?php endif; ?>
	<div style="margin-top: 8px; text-align: center;">
		<input type="submit" value="<?php echo $sSubBtn.__('確認'); ?>" name="sub_state">
	</div>
</form>
