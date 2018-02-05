<?php
	$bQuick = ($aQuest['qbQuickMode'])? true:false;
	$iWidth = ((int)$aQuest['qbQueryStyle'] == 2)? 45:(((int)$aQuest['qbQueryStyle'] == 3)? 30:95);
?>

<div class="info-box">
<?php
	foreach ($aQuery as $aQ):
		if (!$aQ['qqText']):
			continue;
		endif;
		$aA = $aAns[$aQ['qqNO']];
?>
	<h2 class="QAHeader mt16">
		<span><?php echo __('設問.:no',array('no'=>$aQ['qqSort'])); ?></span>
		<?php echo ($aQ['qqRequired'])? ' <div class="font-red font-size-80" style="display: inline; vertical-align: bottom;">('.__('必須').')</div>':''; ?>
	</h2>
	<p class="ml16 font-size-120 line-height-14"><?php echo nl2br($aQ['qqText']); ?></p>
	<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
	<?php endif; ?>

	<?php if ($aQ['qqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice">
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
			$sColor = ($aA['qaChoice'.$i])? 'check':'default';
			$sCheck = ($aQ['qqStyle'])? (($aA['qaChoice'.$i])? 'fa-check-square-o':'fa-square-o'):(($aA['qaChoice'.$i])? 'fa-dot-circle-o':'fa-circle-o');
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
			$sText = ($aA['qaText'])? nl2br($aA['qaText']):__('（無回答）');
	?>
	<div>
		<p class="font-size-120 font-green mt8 ml16 pl20">
			<?php echo $sText; ?>
		</p>
	</div>
	<?php endif; ?>
	<hr>
<?php endforeach; ?>

<?php if ($aQuest['qbReAnswer'] && $aQuest['qbPublic'] == 1 && !$bOther): ?>
	<div class="button-box mt32">
		<form action="/g/quest/ans/<?php echo $aQuest['qbID']; ?>" method="GET">
			<button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('答えなおす'); ?></button>
		</form>
	</div>
<?php endif; ?>
</div>
