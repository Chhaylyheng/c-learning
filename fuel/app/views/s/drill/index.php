<div class="info-box mt16">
	<div class="table-box record-table admin-table mt0">
	<?php if (!is_null($aDCategory)): ?>
		<table class="">
		<thead>
			<tr>
				<th><?php echo __('カテゴリ名'); ?></th>
				<th><?php echo __('登録件数'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
				foreach ($aDCategory as $aC):
		?>
<tr>
<td class="sp-full">
	<a href="/s/drill/list/<?php echo $aC['dcID']; ?>" class="button na do"><?php echo $aC['dcName']; ?></a>
</td>
<td class=""><span class="sp-display-inline font-grey"><?php echo __('登録件数'); ?>:</span
	><?php echo $aC['dcPubNum']; ?>
</td>
</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php else: ?>
		<p><?php echo __('ドリルはありません'); ?></p>
	<?php endif; ?>
	</div>
</div>
