<?php
	$iWidth = ((int)$aTest['tbQueryStyle'] == 2)? 45:(((int)$aTest['tbQueryStyle'] == 3)? 30:95);
?>

<ul class="QBTabMenu">
	<li class="QBTabActive" data="QUERY"><?php echo __('問題画面'); ?></li>
	<li class="" data="RESULT"><?php echo __('解説画面'); ?></li>
</ul>

<div class="mt0 QBTabContents" id="QUERY" style="display: block;">
<?php if ($aTest['tbLimitTime'] > 0): ?>
<div class="LimitTime" obj="<?php echo $aTest['tbID']; ?>"><i class="fa fa-clock-o mr8"></i><span class="font-blue"></span></div>
<?php endif; ?>

<div class="info-box mt0">
	<?php foreach ($aQuery as $aQ): ?>
	<h2 id="q<?php echo $aQ['tqSort']; ?>" class="QAHeader"><span><?php echo __('問題').'.'.$aQ['tqSort']; ?></span></h2>
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
				$sCheck = '';
				$sLabel = 'default';
				$sIcon  = $aStyle[0];
				$aChoice[$i] .= '<label class="QueryChoice text-left '.$sLabel.'"><input type="checkbox" name="checkSel_'.$aQ['tqNO'].'[]" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			else:
				$sCheck = '';
				$sLabel = 'default';
				$sIcon  = $aStyle[0];
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
	<?php $sText = ''; ?>
	<div class="mt16"><input type="text" name="textAns_<?php echo $aQ['tqNO']; ?>" class="" value="<?php echo $sText; ?>"></div>
	<?php endif; ?>
	<hr>
	<?php endforeach; ?>
	<div class="button-box mt16">
		<button type="submit" class="button do" name="state" value="check" disabled="disabled"><?php echo __('提出確認'); ?></button>
	</div>
</div>
</div>

<div class="mt0 QBTabContents" id="RESULT" style="display: none;">

<?php
	$bScore = ($aTest['tbScorePublic'] == 1 || $aTest['tbScorePublic'] == 3);
	$bExplain = ($aTest['tbScorePublic'] == 2 || $aTest['tbScorePublic'] == 3);

	$iTimeSec = mt_rand(30,(($aTest['tbLimitTime'] > 0)? $aTest['tbLimitTime']*60:500));
	$iScore = $aTest['tbTotal'];

	$sTime  = Clfunc_Common::Sec2Min($iTimeSec);
?>

<div class="info-box mt0">

<?php if ($bScore): ?>
<div class="TestScore"><span class="Score font-red"><?php echo $iScore; ?></span>/<?php echo __(':num点',array('num'=>$aTest['tbTotal'])); ?><span class="Time">（<?php echo $sTime; ?>）</span></div>
<hr>
<?php endif; ?>

<?php if ($bExplain && ($aTest['tbExplain'] || $aTest['tbExplainImage'])): ?>
<div>
	<h2 class="QAHeader"><span><?php echo __('解説'); ?></span></h2>
	<?php if ($aTest['tbExplain']): ?>
	<p class="ml16 font-size-120 line-height-14"><?php echo nl2br($aTest['tbExplain']); ?></p>
	<?php endif; ?>
	<?php if ($aTest['tbExplainImage'] && file_exists(CL_UPPATH.DS.$aTest['tbID'].DS.'base'.DS.$aTest['tbExplainImage'])): ?>
	<p class="ml16"><img src="<?php echo DS.CL_UPDIR.DS.$aTest['tbID'].DS.'base'.DS.$aTest['tbExplainImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></p>
	<?php endif; ?>
</div>
<hr>
<?php endif; ?>

<?php
	foreach ($aQuery as $aQ):
		$sIcon = 'fa-circle-o';
		$sColor = 'font-red';
?>
	<h2 class="QAHeader"><span><?php echo __('問題').'.'.$aQ['tqSort']; ?></span></h2>
	<div class="TestResultBox mt8">
		<div class="width-6em text-center <?php echo $sColor; ?>" style="float: left;">
			<i class="fa <?php echo $sIcon; ?> fa-5x"></i>
		</div>
		<div class="width-90" style="float: left;">
			<p class="ml16 font-size-120 line-height-14"><?php echo nl2br($aQ['tqText']); ?></p>
			<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage'])): ?>
			<p class="ml16"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
			<?php endif; ?>

			<div class="mt16">
			<?php if ($aQ['tqStyle'] != 2): ?>
			<h3 class="QAHeader"><span class="QA-Answer"><?php echo __('選択肢とあなたの解答'); ?></span></h3>
			<ul class="mt8 QuestAnsChoice">
			<?php
				$aChoice = array();
				for ($i = 1; $i <= (int)$aQ['tqChoiceNum']; $i++):
					$aA = explode('|',$aQ['tqRight1']);

					$sColor = (array_search($i,$aA) !== false)? 'check':'default';
					$sCheck = ($aQ['tqStyle'])? ((array_search($i,$aA) !== false)? 'fa-check-square-o':'fa-square-o'):((array_search($i,$aA) !== false)? 'fa-dot-circle-o':'fa-circle-o');
					$aChoice[$i] = '<li class="width-'.$iWidth.'" style=""><label class="'.$sColor.' text-left"><p class="font-size-120"><i class="fa '.$sCheck.' fa-fw"></i>'.nl2br($aQ['tqChoice'.$i]).'</p>';
					if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i])):
						$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i].'" style="max-width: 100%; max-height: 90px; width: auto; height: auto;">';
					endif;
					$aChoice[$i] .= '</label></li>';
				endfor;
				foreach ($aChoice as $sC):
					echo $sC;
				endforeach;
			?>
			</ul>
			<?php else: ?>
			<?php
				$sText = nl2br($aQ['tqRight1']);
			?>
			<h3 class="QAHeader"><span class="QA-Answer"><?php echo __('あなたの解答'); ?></span></h3>
			<div class="mt8">
				<p class="font-size-140 font-green mt8 ml16">
					<?php echo $sText; ?>
				</p>
			</div>
			<?php endif; ?>
			</div>

			<div class="mt16">
				<h3 class="QAHeader"><span class="QA-Right"><?php echo __('正解'); ?></span></h3>
				<div class="mt8">
				<?php if ($aQ['tqStyle'] != 2): ?>
					<?php
						$aRight = explode('|',$aQ['tqRight1']);
						foreach ($aRight as $i):
							$sRight = '['.$i.'] '.nl2br($aQ['tqChoice'.$i]);
					?>
					<p class="font-size-140 font-red mt8 ml16"><?php echo $sRight; ?></p>
					<?php endforeach; ?>
				<?php else: ?>
					<?php for ($i = 1; $i <= 5; $i++): ?>
					<p class="font-size-140 font-red mt8 ml16"><?php echo $aQ['tqRight'.$i]; ?></p>
					<?php endfor; ?>
				<?php endif; ?>
				</div>
			</div>

			<?php if ($bExplain && ($aQ['tqExplain'] || $aQ['tqExplainImage'])): ?>
			<div class="mt16">
				<h3 class="QAHeader"><span class="QA-Right"><?php echo __('解説'); ?></span></h3>
				<?php if ($aQ['tqExplain']): ?>
				<p class="mt8 ml16 font-size-120 line-height-14"><?php echo nl2br($aQ['tqExplain']); ?></p>
				<?php endif; ?>
				<?php if ($aQ['tqExplainImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqExplainImage'])): ?>
				<p class="ml16"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqExplainImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
	<hr>
<?php endforeach; ?>

</div>

</div>


<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
<div class="info-box">
<div class="button-box mt0">
	<button type="button" class="button default window-close">プレビューを閉じる</button>
</div>
</div>
<?php endif; ?>

