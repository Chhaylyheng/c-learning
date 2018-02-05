<?php
	$iWidth = ((int)$aTest['tbQueryStyle'] == 2)? 45:(((int)$aTest['tbQueryStyle'] == 3)? 30:95);
	$bScore = ($aTest['tbScorePublic'] == 1 || $aTest['tbScorePublic'] == 3);
	$bExplain = ($aTest['tbScorePublic'] == 2 || $aTest['tbScorePublic'] == 3);

	$sTime  = Clfunc_Common::Sec2Min($aPut['tpTime']);
?>

<div class="info-box">

<?php if ($bScore): ?>
<div class="TestScore"><span class="Score font-red"><?php echo $aPut['tpScore']; ?></span>/<?php echo __(':num点',array('num'=>$aTest['tbTotal'])); ?><span class="Time">（<?php echo $sTime; ?>）</span></div>
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
	foreach ($aAns as $k => $aA):
		if (!$aA['tqText']):
			continue;
		endif;
		$sIcon = ($aA['taRight'])? 'fa-circle-o':'fa-times';
		$sColor = ($aA['taRight'])? 'font-red':'font-silver';
?>
	<h2 class="QAHeader"><span><?php echo __('問題').'.'.$aA['tqSort']; ?></span></h2>
	<div class="TestResultBox mt8">
		<div class="width-6em text-center <?php echo $sColor; ?>" style="float: left;">
			<i class="fa <?php echo $sIcon; ?> fa-5x"></i>
		</div>
		<div class="width-90" style="float: left;">
			<p class="ml16 font-size-120 line-height-14"><?php echo nl2br($aA['tqText']); ?></p>
			<?php $aQ = $aQuery[$k]; ?>
			<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage'])): ?>
			<p class="ml16"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
			<?php endif; ?>

			<div class="mt16">
			<?php if ($aA['tqStyle'] != 2): ?>
			<h3 class="QAHeader"><span class="QA-Answer"><?php echo __('選択肢とあなたの解答'); ?></span></h3>
			<ul class="mt8 QuestAnsChoice">
			<?php
				$aChoice = array();
				for ($i = 1; $i <= (int)$aA['tqChoiceNum']; $i++):
					$sColor = ($aA['taChoice'.$i])? 'check':'default';
					$sCheck = ($aA['tqStyle'])? (($aA['taChoice'.$i])? 'fa-check-square-o':'fa-square-o'):(($aA['taChoice'.$i])? 'fa-dot-circle-o':'fa-circle-o');
					$aChoice[$i] = '<li class="width-'.$iWidth.'" style=""><label class="'.$sColor.' text-left"><p class="font-size-120"><i class="fa '.$sCheck.' fa-fw"></i>'.nl2br($aA['tqChoice'.$i]).'</p>';
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
				$sText = ($aA['taText'])? nl2br($aA['taText']):__('（無解答）');
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
				<?php if ($aA['tqStyle'] != 2): ?>
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
