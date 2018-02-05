<div class="info-box mt16">
	<div class="table-box record-table admin-table mt0">
	<?php if (!is_null($aPut)): ?>
		<table class="">
		<thead>
			<tr>
				<th><?php echo __('実施日時'); ?></th>
				<th><?php echo __('正答率'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
				foreach ($aPut as $aP):
		?>
<tr>
<td class=""><span class="sp-display-inline font-grey"><?php echo __('実施日時'); ?>:</span
	><?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aP['dpDate'])?></td>
<td class=""><span class="sp-display-inline font-grey"><?php echo __('正答率'); ?>:</span
	><?php echo $aP['dpAvg'].'%'; ?></td>
</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php else: ?>
		<p><?php echo __('実施結果はありません'); ?></p>
	<?php endif; ?>
	</div>
</div>
