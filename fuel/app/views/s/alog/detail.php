<?php
if ($aALTheme['altFile']):
	$sPath = '';
	$sSize = '';
	if ($aALog['fID'] != ''):
		$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aALog['fID'],'mode'=>'e'));
		$sSize = \Clfunc_Common::FilesizeFormat($aALog['fSize'],1);
		$sIcon = 'paperclip';
		$sThumb = null;
		if ($aALog['fFileType'] == 2):
			$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aALog['fID'],'mode'=>'t'));
			$sIcon = 'film';
		endif;
		$sFile = '<p><i class="fa fa-'.$sIcon.'"></i> <a href="'.$sPath.'" target="_blank">'.$aALog['fName'].'（'.$sSize.'）</a></p>';
	endif;
endif;
?>

<div class="info-box">
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('先生コメント'); ?></div>
			<div class="formContent inline-box">
				<p class="font-blue"><?php echo nl2br($aALog['alCom']); ?></p>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altGoalLabel']; ?></div>
			<div class="formContent inline-box">
				<p><?php echo nl2br($aALGoal['algText']); ?></p>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('記録日時'); ?></div>
			<div class="formContent inline-box">
				<p><?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aALog['alDate']); ?></p>
			</div>
		</div>
<?php if ($aALTheme['altTitle']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTitleLabel']; ?></div>
			<div class="formContent inline-box">
				<p><?php echo $aALog['alTitle']; ?></p>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altRange']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altRangeLabel']; ?></div>
			<div class="formContent inline-box">
				<p><?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aALog['alStart']); ?> ～ <?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aALog['alEnd']); ?></p>
			</div>
		</div>
<?php endif; ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTextLabel']; ?></div>
			<div class="formContent inline-box">
				<p><?php echo nl2br($aALog['alText']); ?></p>
			</div>
		</div>
<?php if ($aALTheme['altFile']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altFileLabel']; ?></div>
			<div class="formContent inline-box">
				<p style="width: 100%; max-width: 640px;">
	<?php
			switch ($aALog['fFileType']):
				case 2:	# 映像の場合
	?>
	<video class="width-100"  controls="controls" preload="none" src="<?php echo $sPath; ?>" poster="<?php echo $sThumb; ?>"></video>
	<?php
				break;
				case 1:
	?>
	<img class="width-100" src="<?php echo $sPath; ?>" alt="<?php echo $aALog['fName'].'（'.$sSize.'）'; ?>">
	<?php
				break;
				default:
					echo $sFile;
				break;
			endswitch;
	?>
				</p>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt1']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt1Label']; ?></div>
			<div class="formContent inline-box">
				<p><?php echo nl2br($aALog['alOpt1']); ?></p>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt2']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt2Label']; ?></div>
			<div class="formContent inline-box">
				<p><?php echo nl2br($aALog['alOpt2']); ?></p>
			</div>
		</div>
<?php endif; ?>
	</div>
</div>
