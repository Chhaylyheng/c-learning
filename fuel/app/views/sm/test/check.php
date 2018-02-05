<?php
$bClose = false;
if ($aTest['tbLimitTime'] > 0):
	$aTimer = Session::get('SES_S_TEST_TIMER',false);
	$aTimer = ($aTimer)? unserialize($aTimer):null;
	$iEndTime = 0;
	if (isset($aTimer[$aTest['tbID']])):
		$iEndTime = $aTimer[$aTest['tbID']] + ($aTest['tbLimitTime'] * 60);
		$bClose = ($iEndTime <= time());
	endif;
?>
<div style="text-align: center; font-size: 120%;">
	<?php echo __('制限時間'); ?>：<?php echo __(':num分',array('num'=>$aTest['tbLimitTime'])); ?><br>
	<?php echo ($iEndTime)? '＜～'.date('Y/m/d H:i:s',$iEndTime).'＞':''; ?>
</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<div style="color: red;"><?php echo \Clfunc_Mobile::emj('WARN').__('まだ提出は完了していません。'); ?></div>
<div><?php echo __('回答内容を確認の上、「提出する」ボタンを押してください。'); ?></div>

<form action="/s/test/submit/<?php echo $aTest['tbID'].Clfunc_Mobile::SesID(); ?>" method="POST">
<?php echo Clfunc_Mobile::SesID('post'); ?>
<br>
<?php
	foreach ($aQuery as $aQ):
		if (!$aQ['tqText']):
			continue;
		endif;
?>
	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px;"><?php echo __('問題').'.'.$aQ['tqSort']; ?></div>
	<div><?php echo nl2br($aQ['tqText']); ?></div>
	<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>

	<?php if ($aQ['tqStyle'] != 2): ?>
	<p>
	<?php
		if ($aQ['tqStyle'] == 1):
			$aSel = explode('|',$aInput[$aQ['tqNO']]['select']);
		else:
			$aSel = array($aInput[$aQ['tqNO']]['select']);
		endif;
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['tqChoiceNum']; $i++):
			$bSel = array_search($i,$aSel);
			$sColor = ($bSel !== false)? '#00CC00':'#000000';
			$sCheck = ($aQ['tqStyle'])? (($bSel !== false)? '■':'□'):(($bSel !== false)? '●':'○');
			$aChoice[$i] = '<div style="color: '.$sColor.';">'.$sCheck.nl2br($aQ['tqChoice'.$i]).'<br>';
			if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqChoiceImg'.$i].'" style="max-width: 100%; width: auto; height: auto; margin-bottom: 5px;">';
			endif;
			$aChoice[$i] .= '</div>';
		endfor;
		if ($aTest['tbQueryRand']):
			$aChoice = Clfunc_Common::array_shuffle($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</p>
	<?php endif; ?>
	<?php
		if ($aQ['tqStyle'] == 2):
			$sText = ($aInput[$aQ['tqNO']]['text'])? nl2br($aInput[$aQ['tqNO']]['text']):__('（無解答）');
	?>
	<p><span style="color: #00CC00;"><?php echo $sText; ?></span></p>
	<?php endif; ?>
<?php endforeach; ?>

<?php echo Clfunc_Mobile::hr(); ?>
<div style="text-align: center;">
	<input type="submit" name="check" value="<?php echo __('提出する'); ?>"><br>
<?php if (!$bClose): ?>
	<input type="submit" name="back" value="<?php echo __('戻る'); ?>">
<?php endif; ?>
</div>
</form>
