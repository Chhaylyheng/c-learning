<?php
	$bQuick = ($aQuest['qbQuickMode'])? true:false;
?>

<?php if (!is_null($aMsg)): ?>
	<div style="color: #CC0000; margin-bottom: 5px;"><?php echo Clfunc_Mobile::emj('WARN'); ?><?php echo __('入力に誤りがあります。各設問をご確認ください。'); ?></div>
<?php endif; ?>

<form action="/s/quest/ans/<?php echo $aQuest['qbID'].Clfunc_Mobile::SesID(); ?>" method="POST">
<?php echo Clfunc_Mobile::SesID('post'); ?>

<?php if ($bQuick): ?>
	<?php $aQ = $aQuery[0]; ?>
	<div><?php echo nl2br($aQ['qqText']); ?></div>
	<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>

	<?php if (isset($aMsg[$aQ['qqNO']])): ?>
	<div style="color: #CC0000;"><?php echo $aMsg[$aQ['qqNO']]; ?></div>
	<?php endif; ?>

	<?php if ($aQ['qqStyle'] != 2): ?>
	<p>
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
			$sCheck = ($aInput[$aQ['qqNO']]['select'] == $i)? ' checked':'';
			$aChoice[$i]  = '<label><input type="radio" name="radioSel_'.$aQ['qqNO'].'" value="'.$i.'" autocomplete="off"'.$sCheck.'>'.nl2br($aQ['qqChoice'.$i]);
			if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i])):
				$aChoice[$i] .= '<br><img src="'.DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i].'" style="max-width: 100%; width: auto; height: auto;">';
			endif;
			$aChoice[$i] .= '</label><br>';
		endfor;
		if ($aQuest['qbQuerySort']):
			krsort($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</p>
	<?php else: ?>
	<?php $sText = (isset($aInput[$aQ['qqNO']]['text']))? $aInput[$aQ['qqNO']]['text']:''; ?>
	<p><textarea name="textAns_<?php echo $aQ['qqNO']; ?>" class="" rows="4"><?php echo $sText; ?></textarea></p>
	<?php endif; ?>

	<?php if (isset($aQuery[1])): ?>
	<?php $aQ = $aQuery[1]; ?>
	<div><?php echo nl2br($aQ['qqText']); ?></div>
	<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>

	<?php if (isset($aMsg[$aQ['qqNO']])): ?>
	<div style="color: #CC0000;"><?php echo $aMsg[$aQ['qqNO']]; ?></div>
	<?php endif; ?>

	<?php $sText = (isset($aInput[$aQ['qqNO']]['text']))? $aInput[$aQ['qqNO']]['text']:''; ?>
	<p><textarea name="textAns_<?php echo $aQ['qqNO']; ?>" class="" rows="4"><?php echo $sText; ?></textarea></p>
	<?php endif; ?>
	<?php echo Clfunc_Mobile::hr(); ?>
<?php else: ?>
	<?php foreach ($aQuery as $aQ): ?>
	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px; padding: 2px 0;">
		<?php echo __('設問.:no',array('no'=>$aQ['qqSort'])); ?>
		<?php echo ($aQ['qqRequired'])? ' <span style="color: #ffccbb;">('.__('必須').')</span>':''; ?>
	</div>
	<div><?php echo nl2br($aQ['qqText']); ?></div>
	<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>
	<?php if (isset($aMsg[$aQ['qqNO']])): ?>
	<div style="color: #CC0000;"><?php echo $aMsg[$aQ['qqNO']]; ?></div>
	<?php endif; ?>
	<?php if ($aQ['qqStyle'] != 2): ?>
	<p>
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
			$aChoice[$i] = '<label>';
			$aStyle = ($aQ['qqStyle'])? array('square-o','check-square-o'):array('circle-o','dot-circle-o');
			if ($aQ['qqStyle'] == 1):
				$aSel = explode('|',$aInput[$aQ['qqNO']]['select']);
				$sCheck = (array_search($i, $aSel) !== false)? ' checked':'';
				$aChoice[$i] .= '<input type="checkbox" name="checkSel_'.$aQ['qqNO'].'[]" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			else:
				$sCheck = ($aInput[$aQ['qqNO']]['select'] == $i)? ' checked':'';
				$aChoice[$i] .= '<br><input type="radio" name="radioSel_'.$aQ['qqNO'].'" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			endif;
			$aChoice[$i] .= nl2br($aQ['qqChoice'.$i]);
			if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i])):
				$aChoice[$i] .= '<br><img src="'.DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto; margin-bottom: 5px;">';
			endif;
			$aChoice[$i] .= '</label><br>';
		endfor;
		if ($aQuest['qbQuerySort']):
			krsort($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</p>
	<?php else: ?>
	<?php $sText = (isset($aInput[$aQ['qqNO']]['text']))? $aInput[$aQ['qqNO']]['text']:''; ?>
	<p><textarea name="textAns_<?php echo $aQ['qqNO']; ?>" class="" rows="4"><?php echo $sText; ?></textarea></p>
	<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php echo Clfunc_Mobile::hr(); ?>
<?php if (!is_null($aMsg)): ?>
	<div style="color: #CC0000; margin-bottom: 5px;"><?php echo Clfunc_Mobile::emj('WARN'); ?><?php echo __('入力に誤りがあります。各設問をご確認ください。'); ?></div>
<?php endif; ?>
<div style="text-align: center;"><input type="submit" value="<?php echo __('提出確認'); ?>" name="sub_state"></div>
</form>

