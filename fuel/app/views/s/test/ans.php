<?php
	$iWidth = ((int)$aTest['tbQueryStyle'] == 2)? 45:(((int)$aTest['tbQueryStyle'] == 3)? 30:95);
?>

<?php if ($aTest['tbLimitTime'] > 0): ?>
<div class="LimitTime" obj="<?php echo $aTest['tbID']; ?>"><i class="fa fa-clock-o mr8"></i><span class="font-blue"></span></div>
<?php endif; ?>

<div class="info-box">
<?php if (!is_null($aMsg)): ?>
	<p class="error-box mb16"><?php echo __('入力に誤りがあります。各設問をご確認ください。'); ?></p>
<?php endif; ?>
<form action="/s/test/ans/<?php echo $aTest['tbID']; ?>" method="POST" id="AnsForm">
	<?php foreach ($aQuery as $aQ): ?>
	<h2 class="QAHeader"><span><?php echo __('問題').'.'.$aQ['tqSort']; ?></span></h2>
	<p class="ml16 font-size-120"><?php echo nl2br($aQ['tqText']); ?></p>
	<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></p>
	<?php endif; ?>
	<?php if (isset($aMsg[$aQ['tqNO']])): ?>
	<p class="error-msg mt16"><?php echo $aMsg[$aQ['tqNO']]; ?></p>
	<?php endif; ?>
	<?php if ($aQ['tqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice">
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['tqChoiceNum']; $i++):
			$aChoice[$i] = '<li class="width-'.$iWidth.'" style="">';
			$aStyle = ($aQ['tqStyle'])? array('square-o','check-square-o'):array('circle-o','dot-circle-o');
			if ($aQ['tqStyle'] == 1):
				$aSel = explode('|',$aInput[$aQ['tqNO']]['select']);
				$sCheck = (array_search($i, $aSel) !== false)? ' checked':'';
				$sLabel = (array_search($i, $aSel) !== false)? 'check':'default';
				$sIcon  = (array_search($i, $aSel) !== false)? $aStyle[1]:$aStyle[0];
				$aChoice[$i] .= '<label class="QueryChoice text-left '.$sLabel.'"><input type="checkbox" name="checkSel_'.$aQ['tqNO'].'[]" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			else:
				$sCheck = ($aInput[$aQ['tqNO']]['select'] == $i)? ' checked':'';
				$sLabel = ($aInput[$aQ['tqNO']]['select'] == $i)? 'check':'default';
				$sIcon  = ($aInput[$aQ['tqNO']]['select'] == $i)? $aStyle[1]:$aStyle[0];
				$aChoice[$i] .= '<label class="QueryChoice text-left '.$sLabel.'"><input type="radio" name="radioSel_'.$aQ['tqNO'].'" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			endif;
			$aChoice[$i] .= '<p class="font-size-120"><i class="fa fa-'.$sIcon.' fa-fw"></i>'.nl2br($aQ['tqChoice'.$i]).'</p>';
			if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto;">';
			endif;
			$aChoice[$i] .= '</label></li>';
		endfor;
		if ($aTest['tbQueryRand']):
			$aChoice = Clfunc_Common::array_shuffle($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</ul>
	<?php else: ?>
	<?php $sText = (isset($aInput[$aQ['tqNO']]['text']))? $aInput[$aQ['tqNO']]['text']:''; ?>
	<div class="mt16"><input type="text" name="textAns_<?php echo $aQ['tqNO']; ?>" class="" value="<?php echo $sText; ?>"></div>
	<?php endif; ?>
	<hr>
	<?php endforeach; ?>
	<div class="button-box mt16">
		<button type="submit" class="button do formSubmit" value="check" name="sub_state"><?php echo __('提出確認'); ?></button>
	</div>
</form>
</div>
