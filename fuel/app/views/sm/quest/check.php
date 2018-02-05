<?php
	$bQuick = ($aQuest['qbQuickMode'])? true:false;
?>

<div style="color: red;"><?php echo \Clfunc_Mobile::emj('WARN').__('まだ提出は完了していません。'); ?></div>
<div><?php echo __('回答内容を確認の上、「提出する」ボタンを押してください。'); ?></div>
<form action="/s/quest/submit/<?php echo $aQuest['qbID'].Clfunc_Mobile::SesID(); ?>" method="POST">
<?php echo Clfunc_Mobile::SesID('post'); ?>
<br>
<?php
	foreach ($aQuery as $aQ):
		if (!$aQ['qqText']):
			continue;
		endif;
?>
	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px;">
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
		if ($aQ['qqStyle'] == 1):
			$aSel = explode('|',$aInput[$aQ['qqNO']]['select']);
		else:
			$aSel = array($aInput[$aQ['qqNO']]['select']);
		endif;
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
			$bSel = array_search($i,$aSel);
			$sColor = ($bSel !== false)? '#00CC00':'#000000';
			$sCheck = ($aQ['qqStyle'])? (($bSel !== false)? '■':'□'):(($bSel !== false)? '●':'○');
			$aChoice[$i] = '<div style="color: '.$sColor.';">'.$sCheck.nl2br($aQ['qqChoice'.$i]).'<br>';
			if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i].'" style="max-width: 100%; width: auto; height: auto; margin-bottom: 5px;">';
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
			$sText = ($aInput[$aQ['qqNO']]['text'])? nl2br($aInput[$aQ['qqNO']]['text']):__('（無回答）');
	?>
	<p><span style="color: #00CC00;"><?php echo $sText; ?></span></p>
	<?php endif; ?>
<?php endforeach; ?>

<?php echo Clfunc_Mobile::hr(); ?>
<div style="text-align: center;">
	<input type="submit" name="check" value="<?php echo __('提出する'); ?>"><br>
	<input type="submit" name="back" value="<?php echo __('戻る'); ?>">
</div>
</form>
