<?php if ($aDCategory['dcAnalysisProgress'] == 1): ?>
<script type="text/javascript">
var intervalTime = 2000;
var timerID;
timerID = setInterval(function() { AggregationState() },intervalTime);
</script>
<?php endif; ?>

<div class="info-box mt0">
<hr>

<table id="DQAnalysis">

<?php if (!is_null($aDQGroup)): ?>

<tr>
	<th class="width-10 text-center"><?php echo __('番号'); ?></th>
	<th class="width-30 text-center"><?php echo __('グループ名'); ?></th>
	<th class="width-10 text-center"><?php echo __('問題数'); ?></th>
	<th class="width-40 text-center"><?php echo __('グラフ'); ?></th>
	<th class="width-10 text-center"><?php echo __('正答率'); ?></th>
</tr>

<?php foreach ($aDQGroup as $aQ): ?>

<tr>
	<td class="text-center"><?php echo $aQ['dgSort']; ?></td>
	<td><?php echo $aQ['dgName']; ?></td>
	<td class="text-right"><?php echo $aQ['dgQNum']; ?></td>
	<td class="DQGraphCell">
		<div class="DQGraphBox">
			<div class="DQGraph" style="width: <?php echo $aQ['dgRate']; ?>%; height: 1.3em;"></div>
		</div>
	</td>
	<td class="text-right">
		<?php echo $aQ['dgRate']; ?>%
		（<?php echo $aQ['dgRNum'].'/'.$aQ['dgANum']; ?>）
	</td>
</tr>

<?php endforeach; ?>
<?php endif; ?>

</table>

</div>