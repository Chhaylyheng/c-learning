<?php
	$iQqNO++;
	$sDqPath = DS.$aQuery['dcID'].DS.$aQuery['dbNO'].DS.$aQuery['dqNO'].DS;
	$aQ = $aQuery;
?>

<form action="/s/drill/anschk/<?php echo $aDrill['dcID'].DS.$aDrill['dbNO']; ?>" method="POST">
	<?php echo Clfunc_Mobile::SesID('post'); ?>
	<input type="hidden" name="qq" value="<?php echo $iQqNO; ?>">

	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px;"><?php echo __('問題').'.'.$iQqNO; ?></div>
	<div><?php echo nl2br($aQ['dqText']); ?></div>
	<?php if ($aQ['dqImage'] && file_exists(CL_UPPATH.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>
	<?php if (isset($aMsg[$aQ['dqNO']])): ?>
	<div style="color: #CC0000;"><?php echo $aMsg[$aQ['dqNO']]; ?></div>
	<?php endif; ?>
	<?php if ($aQ['dqStyle'] != 2): ?>
	<p>
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['dqChoiceNum']; $i++):
			$aChoice[$i] = '<label>';
			$aStyle = ($aQ['dqStyle'])? array('square-o','check-square-o'):array('circle-o','dot-circle-o');
			if ($aQ['dqStyle'] == 1):
				$aChoice[$i] .= '<input type="checkbox" name="checkSel[]" value="'.$i.'" autocomplete="off">';
			else:
				$aChoice[$i] .= '<br><input type="radio" name="radioSel" value="'.$i.'" autocomplete="off">';
			endif;
			$aChoice[$i] .= nl2br($aQ['dqChoice'.$i]);
			if ($aQ['dqChoiceImg'.$i] && file_exists(CL_UPPATH.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqChoiceImg'.$i])):
				$aChoice[$i] .= '<br><img src="'.DS.CL_UPDIR.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto; margin-bottom: 5px;">';
			endif;
			$aChoice[$i] .= '</label><br>';
		endfor;
		if ($aDrill['dbQueryRand']):
			$aChoice = Clfunc_Common::array_shuffle($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</p>
	<?php else: ?>
	<p><input type="text" name="textAns" class="" value=""></p>
	<?php endif; ?>

<?php echo Clfunc_Mobile::hr(); ?>
<div style="text-align: center;"><input type="submit" value="<?php echo __('次へ'); ?>" name="sub_state"></div>
</form>
