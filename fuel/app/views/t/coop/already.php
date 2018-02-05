<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_coopalready_sort';
	var defaultSort = [[0,0]];

	var currentSort = new Array();
	if(('sessionStorage' in window) && (window.sessionStorage !== null)) {
		store = sessionStorage.getItem(sskey);
		if (store) {
			store = store.split('|');
			for (i = 0; i < store.length; i++) {
				currentSort.push(store[i].split(','));
			}
		}
	}
	if (!currentSort || currentSort == null || currentSort.length == 0) {
		currentSort = defaultSort;
	}

	$('table.table-sort').tablesorter({
		cssHeader: 'headerSort',
		headers: {
		},
		sortList: currentSort,
		widgets: ['zebra']
	}).bind("sortEnd", function(sorter) {
		currentSort = sorter.target.config.sortList;
		currentSort = currentSort.join('|');
		setSessionStorage(sskey, currentSort);
	});
});
</script>
<?php endif; ?>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th><?php echo __('学籍番号'); ?></th>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('クラス'); ?></th>
				<th><?php echo __('既読日時'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','class'=>'','date'=>'─');

					$aM['no'] = $aS['stNO'];
					$aM['name'] = $aS['stName'];
					$aM['class'] = $aS['stClass'];
					$aM['date'] = (isset($aS['already']))? Clfunc_Tz::tz('Y/m/d H:i',$tz,$aS['already']):'─';
		?>
			<tr>
				<td><?php echo $aM['no']; ?></td>
				<td><?php echo $aM['name']; ?></td>
				<td><?php echo $aM['class']; ?></td>
				<td><?php echo $aM['date']; ?></td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>
