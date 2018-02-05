<?php
$aStyleName = array(__('択一形式'),__('選択形式（複数回答可）'),__('テキスト入力形式'));
$sDqPath = DS.$aDrill['dcID'].DS.$aDrill['dbNO'].DS;
?>
<div class="info-box mt0">
<hr>

<table id="DQAnalysis">

<?php if (!is_null($aQuery)): ?>

<tr>
	<th class="width-10 text-center"><?php echo __('番号'); ?></th>
	<th class="width-40 text-center"><?php echo __('問題'); ?></th>
	<th class="width-40 text-center"><?php echo __('グラフ'); ?></th>
	<th class="width-10 text-center"><?php echo __('正答率'); ?></th>
</tr>

<?php foreach ($aQuery as $sDQ => $aQ): ?>

<tr>
	<td class="text-center"><?php echo $aQ['dqSort']; ?></td>
	<td>
		<p class="mt0"><?php echo nl2br($aQ['dqText']); ?></p>
		<?php if ($aQ['dqImage'] && file_exists(CL_UPPATH.$sDqPath.$aQ['dqNO'].DS.$aQ['dqImage'])): ?>
			<p><span class="ShowToggle" data="tbi-<?php echo $sDQ; ?>"><i class="fa fa-picture-o fa-fw"></i><?php echo __('画像の表示/非表示'); ?></span></p>
			<div class="DQImage" id="tbi-<?php echo $sDQ; ?>"><img src="<?php echo DS.CL_UPDIR.$sDqPath.$aQ['dqNO'].DS.$aQ['dqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></div>
		<?php endif; ?>
		<p class="text-right font-silver font-size-90 mt0">
			<?php echo $aStyleName[$aQ['dqStyle']]; ?>
			[<?php echo $aDQGroup[$aQ['dgNO']]['dgName']; ?>]
		</p>
	</td>
	<td class="DQGraphCell">
		<div class="DQGraphBox">
			<div class="DQGraph" style="width: <?php echo $aBent[$sDQ]['dqaRate']; ?>%; height: 1.3em;"></div>
		</div>
	</td>
	<td class="text-right">
		<?php echo $aBent[$sDQ]['dqaRate']; ?>%
		（<?php echo $aBent[$sDQ]['dqaRNum'].'/'.$aBent[$sDQ]['dqaANum']; ?>）
	</td>
</tr>

<?php endforeach; ?>
<?php endif; ?>

</table>

</div>