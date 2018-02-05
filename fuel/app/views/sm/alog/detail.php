<?php
$aT = $aALTheme;
$aL = $aALog;
$sAltID = $aT['altID'];
$iNO = $aL['no'];

if ($aT['altFile']):
	$sPath = '';
	$sSize = '';
	if ($aL['fID'] != ''):
		$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'e'));
		$sSize = \Clfunc_Common::FilesizeFormat($aL['fSize'],1);
		$sThumb = null;
		if ($aL['fFileType'] == 2):
			$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'t'));
		endif;
		$sFile = '<div>'.ClFunc_Mobile::emj('CLIP').'<a href="'.$sPath.'" target="_blank">'.$aL['fName'].'('.$sSize.')</a></div>';
	endif;
endif;
?>

<div style="margin-top: 8px; font-size: 80%;">
<div style="background-color: #CCCCFF; padding: 3px 0px;">
	<span class="font-bold"><?php echo $aT['altName']; ?></span> <span class="font-size-80"><?php echo date('y/n/j H:i',strtotime($aL['alDate'])); ?></span>
	<?php if ($aT['altTitle']): ?>
		<?php echo ($aL['alTitle'])? '<div style="font-weight: bold;">'.$aL['alTitle'].'</div>':''; ?>
	<?php endif; ?>
</div>

<div style="border-bottom: 1px dashed silver; background-color: #FFCCCC; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo __('先生コメント'); ?>】</div>
	<?php echo nl2br($aL['alCom']); ?>
</div>

<?php if ($aT['altRange']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altRangeLabel']; ?>】</div>
	<?php if ($aL['alStart'] && $aL['alStart'] != CL_DATETIME_DEFAULT): ?>
	<?php echo date('y/n/j H:i',strtotime($aL['alStart'])); ?> ～ <?php echo date('y/n/j H:i',strtotime($aL['alEnd'])); ?>
	<?php endif; ?>
</div>
<?php endif; ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altTextLabel']; ?>】</div>
	<?php echo nl2br($aL['alText']); ?>
</div>
<?php if ($aT['altFile']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altFileLabel']; ?>】</div>
	<?php if ($sPath): ?>
		<?php echo $sFile; ?>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($aT['altOpt1']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altOpt1Label']; ?>】</div>
	<?php echo nl2br($aL['alOpt1']); ?>
</div>
<?php endif; ?>
<?php if ($aT['altOpt2']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altOpt2Label']; ?>】</div>
	<?php echo nl2br($aL['alOpt2']); ?>
</div>
<?php endif; ?>

</div>

