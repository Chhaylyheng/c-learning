<?php
	$iWidth = ((int)$aTest['tbQueryStyle'] == 2)? 45:(((int)$aTest['tbQueryStyle'] == 3)? 30:100);
?>

<?php if ($aTest['tbLimitTime'] > 0): ?>
<div class="LimitTime" obj="<?php echo $aTest['tbID']; ?>"><i class="fa fa-clock-o mr8"></i><span class="font-blue"></span></div>
<?php endif; ?>

<div class="info-box">
	<p class="text-center font-red font-size-120 mt8 mb8 font-bold"><i class="fa fa-exclamation-circle fa-lg va-top"></i> <?php echo __('まだ提出は完了していません。'); ?></p>
	<p class="text-center mb8"><?php echo __('回答内容を確認の上、「提出する」ボタンを押してください。'); ?></p>
	<hr>
	<form action="/s/test/submit/<?php echo $aTest['tbID']; ?>" method="POST">
<?php
	foreach ($aQuery as $aQ):
		if (!$aQ['tqText']):
			continue;
		endif;
?>
	<h2 class="QAHeader"><span><?php echo __('問題').'.'.$aQ['tqSort']; ?></span></h2>
	<p class="ml16 font-size-120"><?php echo nl2br($aQ['tqText']); ?></p>
	<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
	<?php endif; ?>

	<?php if ($aQ['tqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice">
	<?php
		if ($aQ['tqStyle'] == 1):
			$aSel = explode('|',$aInput[$aQ['tqNO']]['select']);
		else:
			$aSel = array($aInput[$aQ['tqNO']]['select']);
		endif;
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['tqChoiceNum']; $i++):
			$bSel = array_search($i,$aSel);
			$sColor = ($bSel !== false)? 'check':'default';
			$sCheck = ($aQ['tqStyle'])? (($bSel !== false)? 'fa-check-square-o':'fa-square-o'):(($bSel !== false)? 'fa-dot-circle-o':'fa-circle-o');
			$aChoice[$i] = '<li class="width-'.$iWidth.'" style=""><label class="'.$sColor.' text-left"><p class="font-size-120"><i class="fa '.$sCheck.' fa-fw"></i>'.nl2br($aQ['tqChoice'.$i]).'</p>';
			if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i].'" style="max-width: 100%; max-height: 90px; width: auto; height: auto;">';
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
	<?php endif; ?>
	<?php
		if ($aQ['tqStyle'] == 2):
			$sText = ($aInput[$aQ['tqNO']]['text'])? nl2br($aInput[$aQ['tqNO']]['text']):__('（無解答）');
	?>
	<div>
		<p class="font-size-120 font-green mt8 ml16"><?php echo $sText; ?></p>
	</div>
	<?php endif; ?>
	<hr>
<?php endforeach; ?>

		<div class="button-box mt16">
			<button class="button default na width-auto mt16" style="float: left;" value="back" name="back" type="submit"><?php echo __('戻る'); ?></button>
			<button class="button do" value="check" name="check" type="submit"><?php echo __('提出する'); ?></button>
		</div>
	</form>
</div>
