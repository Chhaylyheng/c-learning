<?php
	$iQqNO++;
	$iWidth = ((int)$aDrill['dbQueryStyle'] == 2)? 45:(((int)$aDrill['dbQueryStyle'] == 3)? 30:95);
	$sDqPath = DS.$aQuery['dcID'].DS.$aQuery['dbNO'].DS.$aQuery['dqNO'].DS;
?>

<div class="info-box">
<?php
	$sIcon = ($iRight)? 'fa-circle-o':'fa-times';
	$sColor = ($iRight)? 'font-red':'font-silver';
?>
	<h2 class="QAHeader"><span><?php echo __('問題').'.'.$iQqNO; ?></span></h2>
	<div class="TestResultBox mt8">
		<div class="width-6em text-center <?php echo $sColor; ?>" style="float: left;">
			<i class="fa <?php echo $sIcon; ?> fa-5x"></i>
		</div>
		<div class="width-90" style="float: left;">
			<p class="ml16 font-size-120 line-height-14"><?php echo nl2br($aQuery['dqText']); ?></p>
			<?php if ($aQuery['dqImage'] && file_exists(CL_UPPATH.$sDqPath.$aQuery['dqImage'])): ?>
			<p class="ml16"><img src="<?php echo DS.CL_UPDIR.$sDqPath.$aQuery['dqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
			<?php endif; ?>

			<div class="mt16">
			<?php if ($aQuery['dqStyle'] != 2): ?>
			<h3 class="QAHeader"><span class="QA-Answer"><?php echo __('選択肢とあなたの解答'); ?></span></h3>
			<ul class="mt8 QuestAnsChoice">
			<?php
				$aAns = explode('|', $sAnswer);
				$aChoice = array();
				for ($i = 1; $i <= (int)$aQuery['dqChoiceNum']; $i++):
					$sColor = (array_search($i, $aAns) !== false)? 'check':'default';
					$sCheck = ($aQuery['dqStyle'])? ((array_search($i, $aAns) !== false)? 'fa-check-square-o':'fa-square-o'):((array_search($i, $aAns) !== false)? 'fa-dot-circle-o':'fa-circle-o');
					$aChoice[$i] = '<li class="width-'.$iWidth.'" style=""><label class="'.$sColor.' text-left"><p class="font-size-120"><i class="fa '.$sCheck.' fa-fw"></i>'.nl2br($aQuery['dqChoice'.$i]).'</p>';
					if ($aQuery['dqChoiceImg'.$i] && file_exists(CL_UPPATH.$sDqPath.$aQuery['dqChoiceImg'.$i])):
						$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.$sDqPath.$aQuery['dqChoiceImg'.$i].'" style="max-width: 100%; max-height: 90px; width: auto; height: auto;">';
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
				$sText = ($sAnswer)? nl2br($sAnswer):__('（無解答）');
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
				<?php if ($aQuery['dqStyle'] != 2): ?>
					<?php
						$aRight = explode('|',$aQuery['dqRight1']);
						foreach ($aRight as $i):
							$sRight = '・'.nl2br($aQuery['dqChoice'.$i]);
					?>
					<p class="font-size-140 font-red mt8 ml16"><?php echo $sRight; ?></p>
					<?php endforeach; ?>
				<?php else: ?>
					<?php for ($i = 1; $i <= 5; $i++): ?>
					<p class="font-size-140 font-red mt8 ml16"><?php echo $aQuery['dqRight'.$i]; ?></p>
					<?php endfor; ?>
				<?php endif; ?>
				</div>
			</div>

			<?php if ($aQuery['dqExplain'] || $aQuery['dqExplainImage']): ?>
			<div class="mt16">
				<h3 class="QAHeader"><span class="QA-Right"><?php echo __('解説'); ?></span></h3>
				<?php if ($aQuery['dqExplain']): ?>
				<p class="mt8 ml16 font-size-120 line-height-14"><?php echo nl2br($aQuery['dqExplain']); ?></p>
				<?php endif; ?>
				<?php if ($aQuery['dqExplainImage'] && file_exists(CL_UPPATH.$sDqPath.$aQuery['dqExplainImage'])): ?>
				<p class="ml16"><img src="<?php echo DS.CL_UPDIR.$sDqPath.$aQuery['dqExplainImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
	<hr>

	<div class="button-box mt16">
	<?php if ($iQqNO >= $aDrill['dbPublicNum']): ?>
		<a href="/s/drill/fin/<?php echo $aDrill['dcID'].DS.$aDrill['dbNO']; ?>" class="button do"><?php echo __('完了'); ?></a>
	<?php else: ?>
		<a href="/s/drill/ans/<?php echo $aDrill['dcID'].DS.$aDrill['dbNO'].DS.'?qq='.($iQqNO + 1); ?>" class="button do"><?php echo __('次へ'); ?></a>
	<?php endif; ?>
	</div>

</div>
