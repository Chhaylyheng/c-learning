<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_testputstudent_sort';
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
			<?php if (!is_null($aTest)): ?>
			<?php for($i = 5;$i <= (count($aTest) * 3) + 5; $i++ ):?>
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
				<?php if (!is_null($aTest)): ?>
				<?php foreach ($aTest as $aQ): ?>
					<th style="min-width: 10em;"><a href="/t/test/put/<?php echo $aQ['tbID']; ?>"><?php echo $aQ['tbTitle']; ?><br><?php echo __(':num名',array('num'=>(int)$aQ['tpNum'])); ?></a></th>
					<th style="min-width: 4em;"><?php echo __('得点'); ?><br><?php echo ($aQ['tpNum'])? round(($aQ['tpScore']/$aQ['tpNum']),1):'0'; ?></th>
					<th style="min-width: 4em;"><?php echo __('合格'); ?><br><?php echo __(':num名',array('num'=>(int)$aQ['tpQualify'])); ?></th>
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
					<a href="/t/test/stuanslist/<?php echo $sStID; ?>"><?php echo $aM['name']; ?></a>
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
				<?php if (!is_null($aTest)): ?>
				<?php foreach ($aTest as $aQ): ?>
					<?php if (isset($aP[$aQ['tbID']])): ?>
						<td class="width-10 text-center"><a href="/t/test/ansdetail/<?php echo $aQ['tbID'].DS.$sStID; ?>" class="button na width-auto do font-size-80" style="padding: 4px 8px;"><?php echo ClFunc_Tz::tz('\'y/m/d<\\b\\r>H:i',$tz,$aP[$aQ['tbID']]['tpDate']); ?></a></td>
						<td class="width-4 text-center"><?php echo $aP[$aQ['tbID']]['tpScore']; ?></td>
						<td class="width-4 text-center"><?php echo ($aP[$aQ['tbID']]['tpQualify'])? '○':''; ?></td>
					<?php else: ?>
						<td></td>
						<td></td>
						<td></td>
					<?php endif; ?>
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
