<?php if (!is_null($aHist)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_mail_sort';
	var defaultSort = [[4,1]];

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
			2: {sorter: false},
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

<div class="info-box">
	<div class="info-box table-box record-table admin-table mt0">
		<p class="error-box mb16" style="display: none;" id="stErr"></p>
		<table class="table-sort kreport-data">
		<thead>
			<tr>
				<th><?php echo __('送信者名'); ?></th>
				<th><?php echo __('件名'); ?></th>
				<th><?php echo __('本文'); ?></th>
				<th><?php echo __('連絡先'); ?></th>
				<th><?php echo __('連絡日時'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aHist)):
				foreach ($aHist as $aH):
					$sBHead = mb_strimwidth($aH['cmBody'],0,48,'…','UTF-8');
					$aStus = unserialize(base64_decode($aH['cmSendMember']));
		?>
			<tr>
				<td class="width-10"><?php echo $aH['ttName']; ?></td>
				<td class="width-20"><?php echo $aH['cmSubject']; ?></td>
				<td class="width-40">
					<p class="mail-history-bhead link-style"><?php echo $sBHead; ?></p>
					<div class="mail-history-body link-style" style="display: none;"><?php echo nl2br($aH['cmBody']); ?></div>
				</td>
				<td class="width-20">
					<p class="mail-history-num link-style"><?php echo __(':num名', array('num'=>count($aStus))); ?></p>
					<ul class="sendto-list font-blue" style="display: none;">
<?php
	foreach ($aStus as $aS):
?>
<li class="sendto"><?php echo $aS['name']; ?></li>
<?php
	endforeach;
?>
					</ul>
				</td>
				<td class="width-10"><?php echo ClFunc_Tz::tz('Y/m/d<\\b\\r>H:i',$tz,$aH['cmDate']); ?></td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>
