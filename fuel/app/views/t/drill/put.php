<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="table-sort">
		<thead>
			<tr>
				<th class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<th class=""><?php echo __('氏名'); ?></th>
				<th class=""><?php echo __('学年'); ?></th>
				<th class=""><?php echo __('クラス'); ?></th>
				<th class=""><?php echo __('実施回数'); ?></th>
				<th class=""><?php echo __('正答率'); ?></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
				<th class=""></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','num'=>'─','avg'=>'─');
					$sP = null;

					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
						$aM['year'] = $aS['stu']['stYear'];
						$aM['class'] = $aS['stu']['stClass'];
						$aM['num'] = ($aS['num'])? $aS['num']:'─';
						$aM['avg'] = ($aS['num'])? round($aS['sum']/$aS['num'],1).'%':'─';
					endif;
					for ($i = 0; $i < 10; $i++):
						if (isset($aS['put'][$i])):
							$sP .= '<td class="font-size-80">'.ClFunc_Tz::tz('n/j H:i',$tz,$aS['put'][$i]['dpDate']).'<br>'.$aS['put'][$i]['dpAvg'].'%</td>';
						else:
							$sP .= '<td></td>';
						endif;
					endfor;
		?>
			<tr>
				<td><?php echo $aM['no']; ?></td>
				<td><?php echo $aM['name']; ?></td>
				<td><?php echo $aM['year']; ?></td>
				<td><?php echo $aM['class']; ?></td>
				<td><?php echo $aM['num']; ?></td>
				<td><?php echo $aM['avg']; ?></td>
				<?php echo $sP; ?>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>

<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(window).load(function() {
	var sskey = 'cl_t_drillput_sort';
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
			6:{sorter: false},
			7:{sorter: false},
			8:{sorter: false},
			9:{sorter: false},
			10:{sorter: false},
			11:{sorter: false},
			12:{sorter: false},
			13:{sorter: false},
			14:{sorter: false},
			15:{sorter: false}
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
