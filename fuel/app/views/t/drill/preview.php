<?php
	$iQqNO++;
	$iWidth = ((int)$aDrill['dbQueryStyle'] == 2)? 45:(((int)$aDrill['dbQueryStyle'] == 3)? 30:95);
	$sDbPath = DS.$aQuery['dcID'].DS.$aQuery['dbNO'].DS.$aQuery['dqNO'].DS;
?>

<ul class="QBTabMenu">
	<li class="QBTabActive" data="QUERY"><?php echo __('問題画面'); ?></li>
	<li class="" data="RESULT"><?php echo __('解説画面'); ?></li>
</ul>

<div class="mt0 QBTabContents" id="QUERY" style="display: block;">

<div class="info-box mt0">
	<h2 id="q<?php echo $aQuery['dqSort']; ?>" class="QAHeader"><span><?php echo __('問題').'.'.$aQuery['dqSort']; ?></span></h2>
	<p class="ml16 font-size-120"><?php echo nl2br($aQuery['dqText']); ?></p>
	<?php if ($aQuery['dqImage'] && file_exists(CL_UPPATH.$sDbPath.$aQuery['dqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.$sDbPath.$aQuery['dqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></p>
	<?php endif; ?>
	<?php if (isset($aMsg[$aQuery['dqNO']])): ?>
	<p class="error-msg mt16"><?php echo $aMsg[$aQuery['dqNO']]; ?></p>
	<?php endif; ?>
	<?php if ($aQuery['dqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice">
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQuery['dqChoiceNum']; $i++):
			$aChoice[$i] = '<li class="width-'.$iWidth.'" style="">';
			$aStyle = ($aQuery['dqStyle'])? array('square-o','check-square-o'):array('circle-o','dot-circle-o');
			if ($aQuery['dqStyle'] == 1):
				$sCheck = '';
				$sLabel = 'default';
				$sIcon  = $aStyle[0];
				$aChoice[$i] .= '<label class="QueryChoice text-left '.$sLabel.'"><input type="checkbox" name="checkSel_'.$aQuery['dqNO'].'[]" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			else:
				$sCheck = '';
				$sLabel = 'default';
				$sIcon  = $aStyle[0];
				$aChoice[$i] .= '<label class="QueryChoice text-left '.$sLabel.'"><input type="radio" name="radioSel_'.$aQuery['dqNO'].'" value="'.$i.'" autocomplete="off"'.$sCheck.'>';
			endif;
			$aChoice[$i] .= '<p class="font-size-120"><i class="fa fa-'.$sIcon.' fa-fw"></i>'.nl2br($aQuery['dqChoice'.$i]).'</p>';
			if ($aQuery['dqChoiceImg'.$i] && file_exists(CL_UPPATH.$sDbPath.$aQuery['dqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.$sDbPath.$aQuery['dqChoiceImg'.$i].'" style="max-width: 100%; max-height: 180px; width: auto; height: auto;">';
			endif;
			$aChoice[$i] .= '</label></li>';
		endfor;
		if ($aDrill['dbQueryRand']):
			$aChoice = Clfunc_Common::array_shuffle($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</ul>
	<?php else: ?>
	<?php $sText = ''; ?>
	<div class="mt16"><input type="text" name="textAns_<?php echo $aQuery['dqNO']; ?>" class="" value="<?php echo $sText; ?>"></div>
	<?php endif; ?>
	<hr>

<?php if ($iQqNO > 0 && $iQqNO < (int)$aDrill['dbPublicNum']): ?>
	<div class="button-box mt16">
		<a href="/t/drill/preview/<?php echo $aQuery['dcID'].DS.$aQuery['dbNO'].DS.'?qq='.($iQqNO + 1); ?>" class="button do"><?php echo __('次へ'); ?></a>
	</div>
<?php endif; ?>

</div>
</div>

<div class="mt0 QBTabContents" id="RESULT" style="display: none;">

<div class="info-box mt0">

<?php
	$sIcon = 'fa-circle-o';
	$sColor = 'font-red';
?>
	<h2 class="QAHeader"><span><?php echo __('問題').'.'.$aQuery['dqSort']; ?></span></h2>
	<div class="TestResultBox mt8">
		<div class="width-6em text-center <?php echo $sColor; ?>" style="float: left;">
			<i class="fa <?php echo $sIcon; ?> fa-5x"></i>
		</div>
		<div class="width-90" style="float: left;">
			<p class="ml16 font-size-120 line-height-14"><?php echo nl2br($aQuery['dqText']); ?></p>
			<?php if ($aQuery['dqImage'] && file_exists(CL_UPPATH.$sDbPath.$aQuery['dqImage'])): ?>
			<p class="ml16"><img src="<?php echo DS.CL_UPDIR.$sDbPath.$aQuery['dqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
			<?php endif; ?>

			<div class="mt16">
			<?php if ($aQuery['dqStyle'] != 2): ?>
			<h3 class="QAHeader"><span class="QA-Answer"><?php echo __('選択肢とあなたの解答'); ?></span></h3>
			<ul class="mt8 QuestAnsChoice">
			<?php
				$aChoice = array();
				for ($i = 1; $i <= (int)$aQuery['dqChoiceNum']; $i++):
					$aA = explode('|',$aQuery['dqRight1']);

					$sColor = (array_search($i,$aA) !== false)? 'check':'default';
					$sCheck = ($aQuery['dqStyle'])? ((array_search($i,$aA) !== false)? 'fa-check-square-o':'fa-square-o'):((array_search($i,$aA) !== false)? 'fa-dot-circle-o':'fa-circle-o');
					$aChoice[$i] = '<li class="width-'.$iWidth.'" style=""><label class="'.$sColor.' text-left"><p class="font-size-120"><i class="fa '.$sCheck.' fa-fw"></i>'.nl2br($aQuery['dqChoice'.$i]).'</p>';
					if ($aQuery['dqChoiceImg'.$i] && file_exists(CL_UPPATH.$sDbPath.$aQuery['dqChoiceImg'.$i])):
						$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.$sDbPath.$aQuery['dqChoiceImg'.$i].'" style="max-width: 100%; max-height: 90px; width: auto; height: auto;">';
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
				$sText = nl2br($aQuery['dqRight1']);
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
							$sRight = '['.$i.'] '.nl2br($aQuery['dqChoice'.$i]);
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
				<?php if ($aQuery['dqExplainImage'] && file_exists(CL_UPPATH.$sDbPath.$aQuery['dqExplainImage'])): ?>
				<p class="ml16"><img src="<?php echo DS.CL_UPDIR.$sDbPath.$aQuery['dqExplainImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
	<hr>

<?php if ($iQqNO > 0 && $iQqNO < (int)$aDrill['dbPublicNum']): ?>
	<div class="button-box mt16">
		<a href="/t/drill/preview/<?php echo $aQuery['dcID'].DS.$aQuery['dbNO'].DS.'?qq='.($iQqNO + 1); ?>" class="button do"><?php echo __('次へ'); ?></a>
	</div>
<?php endif; ?>

</div>
</div>


<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
<div class="info-box">
<div class="button-box mt0">
	<button type="button" class="button default window-close">プレビューを閉じる</button>
</div>
</div>
<?php endif; ?>

