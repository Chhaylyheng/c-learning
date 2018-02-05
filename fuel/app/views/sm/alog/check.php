<?php
	$sSubBtn = '登録';
	$sMode = 'create';
	if (isset($aALog)):
		$sSubBtn = '更新';
		$sMode = 'edit';
	endif;
?>

<form action="/s/alog/check/<?php echo $aALTheme['altID'].(($iNO)? DS.$iNO:''); ?>" method="post" style="font-size: 80%;">
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altGoalLabel']; ?></label>
		<div style="color: #0000CC;"><?php echo nl2br($aALGoal['algText']); ?></div>
	</div>
<?php if ($aALTheme['altTitle']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altTitleLabel']; ?></label>
		<div style="color: #0000CC;"><?php echo $al_title; ?></div>
	</div>
<?php endif; ?>
<?php if ($aALTheme['altRange']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altRangeLabel']; ?></label>
		<div style="color: #0000CC;">
			<?php echo $al_date_s.' '.$al_time_s; ?>
			～
			<?php echo $al_date_e.' '.$al_time_e; ?>
		</div>
	</div>
<?php endif; ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altTextLabel']; ?></label>
		<div style="color: #0000CC;"><?php echo nl2br($al_text); ?></div>
	</div>
<?php if ($aALTheme['altFile']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altFileLabel']; ?></label>
		<div style="color: #0000CC;">
<?php
if ($aALog['fID']):
	$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aALog['fID']));
	$sSize = \Clfunc_Common::FilesizeFormat($aALog['fSize'],1);
?>
<div><?php echo \Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $sLink; ?>"><?php echo $aALog['fName'].'('.$sSize.')'; ?></a></div>
<?php
endif;
?>
		</div>
	</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt1']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altOpt1Label']; ?></label>
		<div style="color: #0000CC;"><?php echo nl2br($al_opt1); ?></div>
	</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt2']): ?>
	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altOpt2Label']; ?></label>
		<div style="color: #0000CC;"><?php echo nl2br($al_opt2); ?></div>
	</div>
<?php endif; ?>
	</div>
	<div style="margin-top: 8px; text-align: center;">
		<input type="submit" name="state" value="<?php echo $sSubBtn; ?>"><br>
		<input type="submit" name="back" value="<?php echo __('戻る'); ?>">
	</div>
</form>

