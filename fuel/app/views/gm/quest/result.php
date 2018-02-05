<?php
	$bQuick = ($aQuest['qbQuickMode'])? true:false;
?>

<?php
	foreach ($aQuery as $aQ):
		if (!$aQ['qqText']):
			continue;
		endif;
		$aA = $aAns[$aQ['qqNO']];
?>
	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px; padding: 2px 0;">
		<?php echo __('設問.:no',array('no'=>$aQ['qqSort'])); ?>
		<?php echo ($aQ['qqRequired'])? ' <span style="color: #ffccbb;">('.__('必須').')</span>':''; ?>
	</div>
	<div><?php echo nl2br($aQ['qqText']); ?></div>
	<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>

	<?php if ($aQ['qqStyle'] != 2): ?>
	<p>
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
			$sColor = ($aA['qaChoice'.$i])? '#00CC00':'#000000';
			$sCheck = ($aQ['qqStyle'])? (($aA['qaChoice'.$i])? '■':'□'):(($aA['qaChoice'.$i])? '●':'○');
			$aChoice[$i] = '<div style="color: '.$sColor.';">'.$sCheck.nl2br($aQ['qqChoice'.$i]).'<br>';
			if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i].'" style="max-width: 100%; width: auto; height: auto;">';
			endif;
			$aChoice[$i] .= '</div>';
		endfor;
		if ($aQuest['qbQuerySort']):
			krsort($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</p>
	<?php endif; ?>
	<?php
		if ($aQ['qqStyle'] == 2):
			$sText = ($aA['qaText'])? nl2br($aA['qaText']):__('（無回答）');
	?>
	<p><span style="color: #00CC00;"><?php echo $sText; ?></span></p>
	<?php endif; ?>
<?php endforeach; ?>
<?php if ($aQuest['qbReAnswer'] && $aQuest['qbPublic'] == 1 && !$bOther): ?>
	<?php echo Clfunc_Mobile::hr(); ?>
	<form action="/g/quest/ans/<?php echo $aQuest['qbID'].Clfunc_Mobile::SesID(); ?>" method="GET">
		<div style="text-align: center;"><input type="submit" value="<?php echo __('答えなおす'); ?>"></div>
	</form>
<?php endif; ?>

