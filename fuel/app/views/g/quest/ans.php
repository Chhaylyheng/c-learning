<?php
	$bQuick = ($aQuest['qbQuickMode'])? true:false;
	$iWidth = ((int)$aQuest['qbQueryStyle'] == 2)? 45:(((int)$aQuest['qbQueryStyle'] == 3)? 30:95);
?>
<form action="/g/quest/ans/<?php echo $aQuest['qbID']; ?>" method="POST">

<?php if (!is_null($aMsg)): ?>
	<p class="error-box mb16"><?php echo __('入力に誤りがあります。各設問をご確認ください。'); ?></p>
<?php endif; ?>

<?php if ($aQuest['qbOpen'] == 2): ?>
<div class="info-box">
	<div class="info-box mt8 pt0 pb0">
		<p class="font-size-120"><?php echo __('氏名を入力してください。（必須）'); ?></p>
		<?php if (isset($aMsg['g_name'])): ?>
		<p class="error-msg mt4 mb4"><?php echo $aMsg['g_name']; ?></p>
		<?php endif; ?>
		<div class="mt16"><input name="g_name" class="" value="<?php echo $aInput['g_name']; ?>" maxlength="50"></div>
	</div>
</div>
<?php endif; ?>

<div class="info-box mt16">
<?php if ($bQuick): ?>
	<div class="info-box mt0 pt0 pb0">
		<?php $aQ = $aQuery[0]; ?>
		<p class="font-size-120"><?php echo nl2br($aQ['qqText']); ?></p>
		<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'])): ?>
		<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></p>
		<?php endif; ?>

		<?php if (isset($aMsg[$aQ['qqNO']])): ?>
		<p class="error-msg mt4 mb4"><?php echo $aMsg[$aQ['qqNO']]; ?></p>
		<?php endif; ?>

		<?php if ($aQ['qqStyle'] != 2): ?>
		<ul class="mt16 QuestAnsChoice">
		<?php
			$aChoice = array();
			for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
				$sCheck = ($aInput[$aQ['qqNO']]['select'] == $i)? ' checked':'';
				$aStyle = ($aQ['qqStyle'])? array('square-o','check-square-o'):array('circle-o','dot-circle-o');
				$sIcon  = ($aInput[$aQ['qqNO']]['select'] == $i)? $aStyle[1]:$aStyle[0];
				$sLabel = ($aInput[$aQ['qqNO']]['select'] == $i)? 'check':'default';
				$aChoice[$i]  = '<li class="width-'.$iWidth.'" style=""><label class="QueryChoice text-left '.$sLabel.'"><input type="radio" name="radioSel_'.$aQ['qqNO'].'" value="'.$i.'" autocomplete="off"'.$sCheck.'><p class="font-size-120"><i class="fa fa-'.$sIcon.' fa-fw"></i>'.nl2br($aQ['qqChoice'.$i]).'</p>';
				if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i])):
					$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto;">';
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
		<?php else: ?>
		<?php $sText = (isset($aInput[$aQ['qqNO']]['text']))? $aInput[$aQ['qqNO']]['text']:''; ?>
		<div class="mt16"><textarea name="textAns_<?php echo $aQ['qqNO']; ?>" class="" rows="4"><?php echo $sText; ?></textarea></div>
		<?php endif; ?>
	</div>
	<hr>
	<?php if (isset($aQuery[1])): ?>
	<?php $aQ = $aQuery[1]; ?>
	<div class="info-box mt0 pt0 pb0">
		<p class="font-size-120"><?php echo nl2br($aQ['qqText']); ?></p>
		<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'])): ?>
		<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></p>
		<?php endif; ?>

		<?php if (isset($aMsg[$aQ['qqNO']])): ?>
		<p class="error-msg mt16"><?php echo $aMsg[$aQ['qqNO']]; ?></p>
		<?php endif; ?>
		<?php $sText = (isset($aInput[$aQ['qqNO']]['text']))? $aInput[$aQ['qqNO']]['text']:''; ?>
		<div class="mt16"><textarea name="textAns_<?php echo $aQ['qqNO']; ?>" class="" rows="4"><?php echo $sText; ?></textarea></div>
	</div>
	<hr>
	<?php endif; ?>
<?php else: ?>
	<?php foreach ($aQuery as $aQ): ?>
	<h2 class="QAHeader">
		<span><?php echo __('設問.:no',array('no'=>$aQ['qqSort'])); ?></span>
		<?php echo ($aQ['qqRequired'])? ' <div class="font-red font-size-80" style="display: inline; vertical-align: bottom;">('.__('必須').')</div>':''; ?>
	</h2>
	<p class="ml16 font-size-120"><?php echo nl2br($aQ['qqText']); ?></p>
	<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></p>
	<?php endif; ?>
	<?php if (isset($aMsg[$aQ['qqNO']])): ?>
	<p class="error-msg mt16"><?php echo $aMsg[$aQ['qqNO']]; ?></p>
	<?php endif; ?>
	<?php if ($aQ['qqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice">
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
			$aChoice[$i] = '<li class="width-'.$iWidth.'" style="">';
			$aStyle = ($aQ['qqStyle'])? array('square-o','check-square-o'):array('circle-o','dot-circle-o');
			if ($aQ['qqStyle'] == 1):
				$aSel = explode('|',$aInput[$aQ['qqNO']]['select']);
				$sCheck = (array_search($i, $aSel) !== false)? ' checked':'';
				$sLabel = (array_search($i, $aSel) !== false)? 'check':'default';
				$sIcon  = (array_search($i, $aSel) !== false)? $aStyle[1]:$aStyle[0];
				$aChoice[$i] .= '<label class="QueryChoice text-left '.$sLabel.'"><input type="checkbox" name="checkSel_'.$aQ['qqNO'].'[]" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			else:
				$sCheck = ($aInput[$aQ['qqNO']]['select'] == $i)? ' checked':'';
				$sLabel = ($aInput[$aQ['qqNO']]['select'] == $i)? 'check':'default';
				$sIcon  = ($aInput[$aQ['qqNO']]['select'] == $i)? $aStyle[1]:$aStyle[0];
				$aChoice[$i] .= '<label class="QueryChoice text-left '.$sLabel.'"><input type="radio" name="radioSel_'.$aQ['qqNO'].'" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			endif;
			$aChoice[$i] .= '<p class="font-size-120"><i class="fa fa-'.$sIcon.' fa-fw"></i>'.nl2br($aQ['qqChoice'.$i]).'</p>';
			if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto;">';
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
	<?php else: ?>
	<?php $sText = (isset($aInput[$aQ['qqNO']]['text']))? $aInput[$aQ['qqNO']]['text']:''; ?>
	<div class="mt16"><textarea name="textAns_<?php echo $aQ['qqNO']; ?>" class="" rows="4"><?php echo $sText; ?></textarea></div>
	<?php endif; ?>
	<hr>
	<?php endforeach; ?>
<?php endif; ?>
	<div class="button-box mt16">
		<button type="submit" class="button do formSubmit" name="state" value="check"><?php echo __('提出確認'); ?></button>
	</div>
</div>
</form>
