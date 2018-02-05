<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_testput_sort';
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

<?php $sTableName = __('提出').'：<span class="font-red font-size-160">'.$aTest['tpNum'].'</span> / '.$aTest['scNum']; ?>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<h2 class="mb4"><?php echo $sTableName; ?></h2>
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('学年'); ?></th>
				<th><?php echo __('クラス'); ?></th>
				<th><?php echo __('提出日時'); ?></th>
				<th class="string-bottom"><?php echo __('得点'); ?></th>
				<th><?php echo __('合格'); ?></th>
				<th class="string-bottom"><?php echo __('解答時間'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','date'=>'─','score'=>'─','qualify'=>'─','time'=>'─');

					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
						$aM['year'] = $aS['stu']['stYear'];
						$aM['class'] = $aS['stu']['stClass'];
					endif;
					if (isset($aS['put'])):
						$aM['no'] = $aS['put']['tpstNO'];
						$aM['name'] = $aS['put']['tpstName'];
						$aM['class'] = $aS['put']['tpstClass'];
//						$sCom = ($aS['put']['tpComment'])? '<i class="fa fa-commenting mr0 ml4"></i>':'<i class="fa fa-commenting-o mr0 ml4"></i>';
						$aM['date'] = '<a href="/t/test/ansdetail/'.$aS['put']['tbID'].'/'.$aS['put']['stID'].'" class="button na width-auto do font-size-80">'.ClFunc_Tz::tz('Y/m/d H:i',$tz,$aS['put']['tpDate']).'</a>';
						$aM['score'] = $aS['put']['tpScore'];
						$aM['qualify'] = ($aS['put']['tpQualify'])? __('合格'):__('不合格');
						$aM['time'] = Clfunc_Common::Sec2Min($aS['put']['tpTime']);
					endif;
		?>
			<tr>
				<td><?php echo $aM['no']; ?></td>
				<td><?php echo $aM['name']; ?></td>
				<td><?php echo $aM['year']; ?></td>
				<td><?php echo $aM['class']; ?></td>
				<td class="sp-full"><?php echo $aM['date']; ?></td>
				<td><span class="sp-display-inline font-grey"><?php echo __('得点'); ?>:</span
					><?php echo $aM['score']; ?>
				</td>
				<td><?php echo $aM['qualify']; ?></td>
				<td><span class="sp-display-inline font-grey"><?php echo __('時間'); ?>:</span
					><?php echo $aM['time']; ?>
				</td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>
