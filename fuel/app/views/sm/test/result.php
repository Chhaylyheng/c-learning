<?php
	$bScore = ($aTest['tbScorePublic'] == 1 || $aTest['tbScorePublic'] == 3);
	$bExplain = ($aTest['tbScorePublic'] == 2 || $aTest['tbScorePublic'] == 3);
	$sTime  = Clfunc_Common::Sec2Min($aPut['tpTime']);
?>

<?php if ($bScore): ?>
<div style="text-align: center; font-size: 150%;"><span style="color: #CC0000;"><?php echo $aPut['tpScore']; ?></span>/<?php echo __(':num点',array('num'=>$aTest['tbTotal'])); ?><span style="font-size: 80%;">（<?php echo $sTime; ?>）</span></div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<?php if ($bExplain && ($aTest['tbExplain'] || $aTest['tbExplainImage'])): ?>
	<div style="background-color: #0000CC; color: #FFFFFF;"><?php echo __('解説'); ?></div>
	<?php if ($aTest['tbExplain']): ?>
	<div><?php echo nl2br($aTest['tbExplain']); ?></div>
	<?php endif; ?>
	<?php if ($aTest['tbExplainImage'] && file_exists(CL_UPPATH.DS.$aTest['tbID'].DS.'base'.DS.CL_Q_SMALL_PREFIX.$aTest['tbExplainImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aTest['tbID'].DS.'base'.DS.CL_Q_SMALL_PREFIX.$aTest['tbExplainImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<?php
	foreach ($aAns as $k => $aA):
		if (!$aA['tqText']):
			continue;
		endif;
		$sIcon = ($aA['taRight'])? '○':'×';
		$sColor = ($aA['taRight'])? 'red':'gray';
?>
	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px;"><?php echo __('問題').'.'.$aA['tqSort']; ?></div>
	<div style="color: <?php echo $sColor; ?>; font-size: 300%; text-align: center;"><?php echo $sIcon; ?></div>
	<div><?php echo nl2br($aA['tqText']); ?></div>
	<?php $aQ = $aQuery[$k]; ?>
	<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>

	<?php if ($aA['tqStyle'] != 2): ?>
	<p>
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aA['tqChoiceNum']; $i++):
			$sColor = ($aA['taChoice'.$i])? '#00CC00':'#000000';
			$sCheck = ($aA['tqStyle'])? (($aA['taChoice'.$i])? '■':'□'):(($aA['taChoice'.$i])? '●':'○');
			$aChoice[$i] = '<div style="color: '.$sColor.';">'.$sCheck.nl2br($aA['tqChoice'.$i]).'<br>';
			if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqChoiceImg'.$i].'" style="max-width: 100%; width: auto; height: auto; margin-bottom: 5px;">';
			endif;
			$aChoice[$i] .= '</div>';
		endfor;
		if ($aTest['tbQueryRand']):
			$aChoice = Clfunc_Common::array_shuffle($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</p>
	<?php endif; ?>
	<?php
		if ($aA['tqStyle'] == 2):
			$sText = ($aA['taText'])? nl2br($aA['taText']):__('（無解答）');
	?>
	<p><span style="color: #00CC00;"><?php echo $sText; ?></span></p>
	<?php endif; ?>

	<?php if ($bExplain && ($aQ['tqExplain'] || $aQ['tqExplainImage'])): ?>
		<div style="background-color: #0000CC; color: #FFFFFF;"><?php echo __('解説'); ?></div>
		<?php if ($aQ['tqExplain']): ?>
		<div><?php echo nl2br($aQ['tqExplain']); ?></div>
		<?php endif; ?>
		<?php if ($aQ['tqExplainImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqExplainImage'])): ?>
		<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqExplainImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>

