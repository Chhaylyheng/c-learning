<div class="info-box mt16">
	<div class="table-box record-table admin-table mt0">
	<?php if (!is_null($aDrill)): ?>
		<table class="">
		<thead>
			<tr>
				<th><?php echo __('タイトル'); ?></th>
				<th><?php echo __('出題数'); ?></th>
				<th><?php echo __('実施回数'); ?></th>
				<th><?php echo __('平均'); ?></th>
				<th><?php echo __('最終日時'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
				foreach ($aDrill as $aD):
					$fAvg = '0%';
					$iPut = 0;
					$sDate = __('未実施');
					if (isset($aD['dpDate'])):
						$sDate = ClFunc_Tz::tz('Y/m/d H:i',$tz,$aD['dpDate']);
						$fAvg = round(((int)$aD['dpTotal'] / (int)$aD['dpNum']),1).'%';
						$iPut = $aD['dpNum'];
					endif;
		?>
<tr>
<td class="sp-full">
	<a href="/s/drill/ans/<?php echo $aD['dcID'].DS.$aD['dbNO']; ?>" class="button na do width-auto"><?php echo $aD['dbTitle']; ?></a>
</td>
<td><span class="sp-display-inline font-grey"><?php echo __('出題数'); ?>:</span
	><?php echo $aD['dbPublicNum']; ?>
</td>
<td><span class="sp-display-inline font-grey"><?php echo __('実施回数'); ?>:</span
	><a href="/s/drill/put/<?php echo $aD['dcID'].DS.$aD['dbNO']; ?>" class="button na default width-auto"><?php echo $iPut; ?></a>
</td>
<td><span class="sp-display-inline font-grey"><?php echo __('平均'); ?>:</span
	><?php echo $fAvg; ?>
</td>
<td><span class="sp-display-inline font-grey"><?php echo __('最終日時'); ?>:</span
	><?php echo $sDate; ?>
</td>
</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php else: ?>
		<p><?php echo __('実施可能なドリルはありません'); ?></p>
	<?php endif; ?>
	</div>
</div>
