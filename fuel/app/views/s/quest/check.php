<?php
	$bQuick = ($aQuest['qbQuickMode'])? true:false;
	$iWidth = ((int)$aQuest['qbQueryStyle'] == 2)? 45:(((int)$aQuest['qbQueryStyle'] == 3)? 30:100);
?>

<div class="info-box">
	<p class="text-center font-red font-size-120 mt8 mb8 font-bold"><i class="fa fa-exclamation-circle fa-lg va-top"></i> <?php echo __('まだ提出は完了していません。'); ?></p>
	<p class="text-center mb8"><?php echo __('回答内容を確認の上、「提出する」ボタンを押してください。'); ?></p>
	<hr>
	<form action="/s/quest/submit/<?php echo $aQuest['qbID']; ?>" method="POST">
<?php
	foreach ($aQuery as $aQ):
		if (!$aQ['qqText']):
			continue;
		endif;
?>
	<h2 class="QAHeader">
		<span><?php echo __('設問.:no',array('no'=>$aQ['qqSort'])); ?></span>
		<?php echo ($aQ['qqRequired'])? ' <div class="font-red font-size-80" style="display: inline; vertical-align: bottom;">('.__('必須').')</div>':''; ?>
	</h2>
	<p class="ml16 font-size-120"><?php echo nl2br($aQ['qqText']); ?></p>
	<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
	<?php endif; ?>

	<?php if ($aQ['qqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice QuestAnsChoiceCheck">
	<?php
		if ($aQ['qqStyle'] == 1):
			$aSel = explode('|',$aInput[$aQ['qqNO']]['select']);
		else:
			$aSel = array($aInput[$aQ['qqNO']]['select']);
		endif;
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
			$bSel = array_search($i,$aSel);
			$sColor = ($bSel !== false)? 'check':'default';
			$sCheck = ($aQ['qqStyle'])? (($bSel !== false)? 'fa-check-square-o':'fa-square-o'):(($bSel !== false)? 'fa-dot-circle-o':'fa-circle-o');
			$aChoice[$i] = '<li class="width-'.$iWidth.'" style=""><label class="'.$sColor.' text-left"><p class="font-size-120"><i class="fa '.$sCheck.' fa-fw"></i>'.nl2br($aQ['qqChoice'.$i]).'</p>';
			if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i].'" style="max-width: 100%; max-height: 90px; width: auto; height: auto;">';
			endif;
			$aChoice[$i] .= '</label></li>';
		endfor;
		if ($aQuest['qbQuerySort']):
			krsort($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</ul>
	<?php endif; ?>
	<?php
		if ($aQ['qqStyle'] == 2):
			$sText = ($aInput[$aQ['qqNO']]['text'])? nl2br($aInput[$aQ['qqNO']]['text']):__('（無回答）');
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
