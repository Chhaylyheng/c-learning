<?php
	$iQqNO++;
	$sDqPath = DS.$aQuery['dcID'].DS.$aQuery['dbNO'].DS.$aQuery['dqNO'].DS;
	$aQ = $aQuery;

	$sIcon = ($iRight)? '○':'×';
	$sColor = ($iRight)? 'red':'gray';
?>
	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px;"><?php echo __('問題').'.'.$iQqNO; ?></div>
	<div style="color: <?php echo $sColor; ?>; font-size: 300%; text-align: center; margin-top: 10px; margin-bottom: 10px;"><?php echo $sIcon; ?></div>
	<div><?php echo nl2br($aQ['dqText']); ?></div>
	<?php if ($aQ['dqImage'] && file_exists(CL_UPPATH.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>

	<?php if ($aQ['dqStyle'] != 2): ?>
	<div style="margin-top: 8px; margin-bottom: 8px;"><div style="background-color: #008800; color: #FFFFFF;"><?php echo __('選択肢とあなたの解答'); ?></div>
	<?php
		$aAns = explode('|', $sAnswer);
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['dqChoiceNum']; $i++):
			$sColor = (array_search($i, $aAns) !== false)? '#008800':'#000000';
			$sCheck = ($aQ['dqStyle'])? ((array_search($i, $aAns) !== false)? '■':'□'):((array_search($i, $aAns) !== false)? '●':'○');
			$aChoice[$i] = '<div style="color: '.$sColor.';">'.$sCheck.nl2br($aQ['dqChoice'.$i]).'<br>';
			if ($aQ['dqChoiceImg'.$i] && file_exists(CL_UPPATH.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqChoiceImg'.$i].'" style="max-width: 100%; width: auto; height: auto; margin-bottom: 5px;">';
			endif;
			$aChoice[$i] .= '</div>';
		endfor;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</div>

	<div style="margin-top: 8px; margin-bottom: 8px;"><div style="background-color: #CC0000; color: #FFFFFF;"><?php echo __('正解'); ?></div>
	<?php
		$aRight = explode('|',$aQuery['dqRight1']);
		foreach ($aRight as $i):
			$sRight = '・'.nl2br($aQuery['dqChoice'.$i]);
	?>
	<span style="color: #CC0000;"><?php echo $sRight; ?></span><br>
	<?php endforeach; ?>
	</div>

	<?php else:
		$sText = ($sAnswer)? nl2br($sAnswer):__('（無解答）');
	?>
	<div style="margin-top: 8px; margin-bottom: 8px;"><div style="background-color: #008800; color: #FFFFFF;"><?php echo __('あなたの解答'); ?></div>
		<span style="color: #008800;"><?php echo $sText; ?></span>
	</div>

	<div style="margin-top: 8px; margin-bottom: 8px;"><div style="background-color: #CC0000; color: #FFFFFF;"><?php echo __('正解'); ?></div>
		<?php for ($i = 1; $i <= 5; $i++): ?>
		<?php if (!$aQuery['dqRight'.$i]) continue;?>
		<span style="color: #CC0000;"><?php echo $aQuery['dqRight'.$i]; ?></span><br>
		<?php endfor; ?>
	</div>

	<?php endif; ?>

	<?php if (($aQ['dqExplain'] || $aQ['dqExplainImage'])): ?>
		<div style="background-color: #0000CC; color: #FFFFFF;"><?php echo __('解説'); ?></div>
		<?php if ($aQ['dqExplain']): ?>
		<div><?php echo nl2br($aQ['dqExplain']); ?></div>
		<?php endif; ?>
		<?php if ($aQ['dqExplainImage'] && file_exists(CL_UPPATH.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqExplainImage'])): ?>
		<div><img src="<?php echo DS.CL_UPDIR.$sDqPath.CL_Q_SMALL_PREFIX.$aQ['dqExplainImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
		<?php endif; ?>
	<?php endif; ?>

<?php echo Clfunc_Mobile::hr(); ?>
<div style="text-align: center;">
<?php if ($iQqNO >= $aDrill['dbPublicNum']): ?>
	<form action="/s/drill/fin/<?php echo $aDrill['dcID'].DS.$aDrill['dbNO'].Clfunc_Mobile::SesID(); ?>" method="POST"><input type="submit" value="<?php echo __('完了'); ?>" name="sub_state"></form>
<?php else: ?>
	<?php $sSes = Clfunc_Mobile::SesID(); ?>
	<?php $sSep = ($sSes)? '&':'?'; ?>
	<form action="/s/drill/ans/<?php echo $aDrill['dcID'].DS.$aDrill['dbNO'].$sSes.$sSep.'qq='.($iQqNO + 1); ?>" method="POST"><input type="submit" value="<?php echo __('次へ'); ?>" name="sub_state"></form>
<?php endif; ?>
</div>
