<div class="info-box mt16">
	<div class="table-box record-table admin-table mt0">
	<?php if (!is_null($aCCategory)): ?>
		<table class="">
		<thead>
			<tr>
				<th><?php echo __('協働板名'); ?></th>
				<th><?php echo __('記事数'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
				foreach ($aCCategory as $sID => $aC):
					$sNew = ($aC['ccItemNum'] > $aC['already'])? '<span class="attention attn-emp">'.((int)$aC['ccItemNum'] - (int)$aC['already']).'</span>':'';
		?>
<tr>
<td class="sp-full">
	<a href="/s/coop/thread/<?php echo $sID; ?>" class="button na do width-auto"><?php echo $aC['ccName']; ?></a><?php echo $sNew; ?>
</td>
<td class=""><span class="sp-display-inline font-grey"><?php echo __('記事数'); ?>:</span
	><?php echo $aC['ccItemNum']; ?>
</td>
</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php else: ?>
		<p><?php echo __('協働板がありません。'); ?></p>
	<?php endif; ?>
	</div>
</div>
