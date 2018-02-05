<?php if (!is_null($aTeachers)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_classteacherlist_sort';
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
			1: {sorter: false},
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

	<section class="pt0">
		<div class="info-box table-box record-table admin-table">
		<table class="kreport-data table-sort" obj="<?php echo $aClass['ctID']; ?>">
		<thead>
			<tr>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('主'); ?></th>
				<th><?php echo __('メールアドレス'); ?></th>
				<th><?php echo __('学校'); ?></th>
				<th class="string-bottom"><?php echo __('最終ログイン'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aTeachers)):
					$iMax = count($aTeachers);
					foreach ($aTeachers as $i => $aT):
						$sTtID = $aT['ttID'];
						$sMaster = ($aT['tpMaster'])? 'dot-circle-o':'circle-o';
			?>
<tr>
<td class="">
<?php echo $aT['ttName']; ?>
</td>
<td class="">
<i class="fa fa-<?php echo $sMaster; ?>"></i>
</td>
<td class="">
<?php echo $aT['ttMail']; ?>
</td>
<td class="">
<?php echo $aT['cmName']; ?>
</td>
<td class="">
<?php echo ($aT['ttLoginNum'])? Clfunc_Tz::tz('Y/m/d H:i',$tz,$aT['ttLoginDate']).' ('.$aT['ttLoginNum'].')':__('未ログイン'); ?>
</td>
</tr>
					<?php
							endforeach;
						endif;
					?>
				</tbody>
			</table>
		</div>
	</section>
