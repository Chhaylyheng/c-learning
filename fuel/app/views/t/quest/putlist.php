<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_questputstudent_sort';
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

	$('#Student-Table').tablesorter({
		cssHeader: 'headerSort',
		headers: {
			0: {sorter: 'digit'},
			<?php if (!is_null($aQuest)): ?>
			<?php for($i = 5;$i <= count($aQuest)+5; $i++ ):?>
			<?php echo $i; ?>: {sorter: false},
			<?php endfor; ?>
			<?php endif; ?>
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

<div class="info-box mt0">
	<div class="info-box table-box record-table admin-table scroll-box mt0">
		<table class="matrix-data table-sort" id="Student-Table">
		<thead>
			<tr class="font-size-80" >
				<th nowrap="nowrap" class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<th nowrap="nowrap"><?php echo __('氏名'); ?></th>
				<th nowrap="nowrap"><?php echo __('学年'); ?></th>
				<th nowrap="nowrap"><?php echo __('クラス'); ?></th>
				<th nowrap="nowrap"><?php echo __('提出'); ?></th>
				<?php if (!is_null($aQuest)): ?>
				<?php foreach ($aQuest as $aQ): ?>
					<?php $sQuick = ($aQ['qbQuickMode'])? '[Q]':''; ?>
					<th style="min-width: 10em;"><a href="/t/quest/put/<?php echo $aQ['qbID']; ?>"><?php echo $sQuick.$aQ['qbTitle']; ?><br><?php echo (int)$aQ['qpNum']; ?>名</a></th>
				<?php endforeach; ?>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','num'=>0);

					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
						$aM['year'] = $aS['stu']['stYear'];
						$aM['class'] = $aS['stu']['stClass'];
					endif;
					$aP = null;
					if (isset($aS['put'])):
						$aM['num'] = count($aS['put']);
						$aP = $aS['put'];
					endif;
		?>
			<tr>
				<td nowrap="nowrap">
					<?php echo $aM['no']; ?>
				</td>
				<td nowrap="nowrap">
					<a href="/t/quest/stuanslist/<?php echo $sStID; ?>"><?php echo $aM['name']; ?></a>
				</td>
				<td class="width-10">
					<?php echo $aM['year']; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $aM['class']; ?>
				</td>
				<td class="width-10">
					<?php echo $aM['num']; ?>
				</td>
				<?php if (!is_null($aQuest)): ?>
				<?php foreach ($aQuest as $aQ): ?>
					<td class="width-10 text-center">
					<?php if (isset($aP[$aQ['qbID']])): ?>
						<?php if ($aQ['qbAnonymous']): ?>
							<i class="fa fa-check font-green"></i>
						<?php else: ?>
							<a href="/t/quest/ansdetail/<?php echo $aQ['qbID'].DS.$sStID; ?>" class="button na width-auto do font-size-80" style="padding: 4px 8px;"><?php echo ClFunc_Tz::tz('\'y/m/d<\\b\\r>H:i',$tz,$aP[$aQ['qbID']]['qpDate']); ?></a>
						<?php endif; ?>
					<?php endif; ?>
					</td>
				<?php endforeach; ?>
				<?php endif; ?>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>

</div>
