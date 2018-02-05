<?php
if ($aTest['tbLimitTime'] > 0):
	$aTimer = Session::get('SES_S_TEST_TIMER',false);
	$aTimer = ($aTimer)? unserialize($aTimer):null;
	$iEndTime = 0;
	if (isset($aTimer[$aTest['tbID']])):
		$iEndTime = $aTimer[$aTest['tbID']] + ($aTest['tbLimitTime'] * 60);
	endif;
?>
<div style="text-align: center; font-size: 120%;">
	<?php echo __('制限時間'); ?>：<?php echo __(':num分',array('num'=>$aTest['tbLimitTime'])); ?><br>
	<?php echo ($iEndTime)? '＜～'.date('Y/m/d H:i:s',$iEndTime).'＞':''; ?>
</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<?php if (!is_null($aMsg)): ?>
	<div style="color: #CC0000; margin-bottom: 5px;"><?php echo Clfunc_Mobile::emj('WARN'); ?><?php echo __('入力に誤りがあります。各設問をご確認ください。'); ?></div>
<?php endif; ?>

<form action="/s/test/ans/<?php echo $aTest['tbID'].Clfunc_Mobile::SesID(); ?>" method="POST">
<?php echo Clfunc_Mobile::SesID('post'); ?>

	<?php foreach ($aQuery as $aQ): ?>
	<div style="background-color: #0000CC; color: #FFFFFF; margin-top: 5px;"><?php echo __('問題').'.'.$aQ['tqSort']; ?></div>
	<div><?php echo nl2br($aQ['tqText']); ?></div>
	<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqImage'])): ?>
	<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
	<?php endif; ?>
	<?php if (isset($aMsg[$aQ['tqNO']])): ?>
	<div style="color: #CC0000;"><?php echo $aMsg[$aQ['tqNO']]; ?></div>
	<?php endif; ?>
	<?php if ($aQ['tqStyle'] != 2): ?>
	<p>
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['tqChoiceNum']; $i++):
			$aChoice[$i] = '<label>';
			$aStyle = ($aQ['tqStyle'])? array('square-o','check-square-o'):array('circle-o','dot-circle-o');
			if ($aQ['tqStyle'] == 1):
				$aSel = explode('|',$aInput[$aQ['tqNO']]['select']);
				$sCheck = (array_search($i, $aSel) !== false)? ' checked':'';
				$aChoice[$i] .= '<input type="checkbox" name="checkSel_'.$aQ['tqNO'].'[]" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			else:
				$sCheck = ($aInput[$aQ['tqNO']]['select'] == $i)? ' checked':'';
				$aChoice[$i] .= '<br><input type="radio" name="radioSel_'.$aQ['tqNO'].'" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			endif;
			$aChoice[$i] .= nl2br($aQ['tqChoice'.$i]);
			if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqChoiceImg'.$i])):
				$aChoice[$i] .= '<br><img src="'.DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['tqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto; margin-bottom: 5px;">';
			endif;
			$aChoice[$i] .= '</label><br>';
		endfor;
		if ($aTest['tbQueryRand']):
			$aChoice = Clfunc_Common::array_shuffle($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</p>
	<?php else: ?>
	<?php $sText = (isset($aInput[$aQ['tqNO']]['text']))? $aInput[$aQ['tqNO']]['text']:''; ?>
	<p><input type="text" name="textAns_<?php echo $aQ['tqNO']; ?>" class="" value="<?php echo $sText; ?>"></p>
	<?php endif; ?>
	<?php endforeach; ?>

<?php echo Clfunc_Mobile::hr(); ?>
<?php if (!is_null($aMsg)): ?>
	<div style="color: #CC0000; margin-bottom: 5px;"><?php echo Clfunc_Mobile::emj('WARN'); ?><?php echo __('入力に誤りがあります。各設問をご確認ください。'); ?></div>
<?php endif; ?>
<div style="text-align: center;"><input type="submit" value="<?php echo __('提出確認'); ?>" name="sub_state"></div>
</form>

