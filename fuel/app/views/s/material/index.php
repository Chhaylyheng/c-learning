<div class="info-box mt16">
	<div class="table-box record-table admin-table mt0">
	<?php if (!is_null($aMCategory)): ?>
		<table class="">
		<thead>
			<tr>
				<th><?php echo __('カテゴリ名'); ?></th>
				<th><?php echo __('登録件数'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
				foreach ($aMCategory as $sID => $aC):
					$sNew = ($aC['mcPubNum'] > $aC['already'])? '<span class="attention attn-emp">'.($aC['mcPubNum'] - $aC['already']).'</span>':'';
		?>
<tr>
<td class="sp-full">
	<a href="/s/material/list/<?php echo $sID; ?>" class="button na do"><?php echo $aC['mcName']; ?></a><?php echo $sNew; ?>
</td>
<td class=""><span class="sp-display-inline font-grey"><?php echo __('登録件数'); ?>:</span
	><?php echo $aC['mcPubNum']; ?>
</td>
</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php else: ?>
		<p><?php echo __('教材倉庫はありません'); ?></p>
	<?php endif; ?>
	</div>
</div>
