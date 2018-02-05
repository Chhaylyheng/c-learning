<?php
	$iQqNO++;
	$iWidth = ((int)$aDrill['dbQueryStyle'] == 2)? 45:(((int)$aDrill['dbQueryStyle'] == 3)? 30:95);
	$sDqPath = DS.$aQuery['dcID'].DS.$aQuery['dbNO'].DS.$aQuery['dqNO'].DS;
?>

<div class="info-box">
<form action="/s/drill/anschk/<?php echo $aDrill['dcID'].DS.$aDrill['dbNO']; ?>" method="POST" id="AnsForm">
	<input type="hidden" name="qq" value="<?php echo $iQqNO; ?>">
	<h2 class="QAHeader"><span><?php echo __('問題').'.'.$iQqNO; ?></span></h2>
	<p class="ml16 font-size-120"><?php echo nl2br($aQuery['dqText']); ?></p>
	<?php if ($aQuery['dqImage'] && file_exists(CL_UPPATH.$sDqPath.$aQuery['dqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.$sDqPath.$aQuery['dqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></p>
	<?php endif; ?>
	<?php if ($aQuery['dqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice">
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQuery['dqChoiceNum']; $i++):
			$aChoice[$i] = '<li class="width-'.$iWidth.'" style="">';
			$sIcon = ($aQuery['dqStyle'])? 'square-o':'circle-o';
			if ($aQuery['dqStyle'] == 1):
				$aChoice[$i] .= '<label class="QueryChoice text-left default"><input type="checkbox" name="checkSel[]" value="'.$i.'" autocomplete="off">';
			else:
				$aChoice[$i] .= '<label class="QueryChoice text-left default"><input type="radio" name="radioSel" value="'.$i.'" autocomplete="off">';
			endif;
			$aChoice[$i] .= '<p class="font-size-120"><i class="fa fa-'.$sIcon.' fa-fw"></i>'.nl2br($aQuery['dqChoice'.$i]).'</p>';
			if ($aQuery['dqChoiceImg'.$i] && file_exists(CL_UPPATH.$sDqPath.$aQuery['dqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.$sDqPath.$aQuery['dqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto;">';
			endif;
			$aChoice[$i] .= '</label></li>';
		endfor;
		if ($aDrill['dbQueryRand']):
			$aChoice = Clfunc_Common::array_shuffle($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</ul>
	<?php else: ?>
	<div class="mt16"><input type="text" name="textAns" class="" value=""></div>
	<?php endif; ?>
	<hr>
	<div class="button-box mt16">
		<button type="submit" class="button do formSubmit" value="check" name="sub_state"><?php echo __('次へ'); ?></button>
	</div>
</form>
</div>
