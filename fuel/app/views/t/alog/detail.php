<button type="button" class="button default na width-auto VisibleToggle" data="TeachComment">
	<i class="fa fa-comment fa-flip-horizontal"></i><?php echo __('コメント入力'); ?>
</button>

<?php
if ($aALTheme['altFile']):
	$sPath = '';
	$sSize = '';
	$sFile = '';
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
				<p><?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aALog['alStart']); ?> ～ <?php echo Clfunc_Tz::tz('Y/m/d H:i',$tz,$aALog['alEnd']); ?></p>
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

<div class="comment-write-box" id="TeachComment" style="visibility: hidden;">
<table>
<tr>
	<td>
		<button class="VisibleToggle" data="TeachComment" style="cursor: pointer;"><i class="fa fa-comment-o fa-2x fa-flip-horizontal"></i></button>
	</td>
	<td>
		<textarea class="comment-write-text" rows="1" placeholder="<?php echo __('先生コメントを入力'); ?>"><?php echo $aALog['alCom']; ?></textarea>
	</td>
	<td><button type="button" class="button na do TeachCommentUpdate" style="min-width: 1em;" data="<?php echo $aALTheme['altID'].'_'.$aALog['no']; ?>"><?php echo __('更新'); ?></button></td>
</tr>
</table>
</div>
