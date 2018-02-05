<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_testanslist_sort';
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
	<div class="info-box table-box record-table admin-table scroll-box mt0">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th class="string-bottom" nowrap><?php echo __('学籍番号'); ?></th>
				<th nowrap><?php echo __('氏名'); ?></th>
				<th nowrap><?php echo __('学年'); ?></th>
				<th nowrap><?php echo __('クラス'); ?></th>
				<th class="string-bottom" nowrap><?php echo __('提出日時'); ?></th>
				<th class="string-bottom" nowrap><?php echo __('得点'); ?></th>
				<th nowrap><?php echo __('合格'); ?></th>
				<?php
					$aQ = array();
					foreach ($aQuery as $aQQ):
						$aQ['Q.'.$aQQ['tqSort']] = '<td class="sp-full mt4" style="max-width: 200px; min-width: 70px;"><span class="sp-display-inline font-default">'.__('問題').'.'.$aQQ['tqSort'].':<br></span></td>';
				?>
				<th title="<?php echo $aQQ['tqText']; ?>" nowrap><?php echo __('問題').'.'.$aQQ['tqSort']; ?></th>
				<?php
					endforeach;
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','date'=>'─','score'=>0,'qualify'=>'─');
					$aM = array_merge($aM,$aQ);

					if (isset($aS['put'])):
						$aM['no'] = $aS['put']['tpstNO'];
						$aM['name'] = $aS['put']['tpstName'];
						$aM['class'] = $aS['put']['tpstClass'];
						$aM['score'] = $aS['put']['tpScore'];
						$aM['qualify'] = ($aS['put']['tpQualify'])? __('合格'):__('不合格');

//						$sCom = ($aS['put']['tpComment'])? '<i class="fa fa-commenting mr0 ml4"></i>':'<i class="fa fa-commenting-o mr0 ml4"></i>';
						$aM['date'] = '<a href="/t/test/ansdetail/'.$aS['put']['tbID'].'/'.$aS['put']['stID'].'" class="button na width-auto do text-center font-size-80">'.ClFunc_Tz::tz('Y/m/d\<\\b\\r\>H:i',$tz,$aS['put']['tpDate']).'</a>';
						if (isset($aS['ans'])):
							foreach ($aS['ans'] as $iQqNO => $aA):
								$sColor = ($aA['taRight'])? 'back-green font-white':'back-red font-white';
								$aM['Q.'.$iQqNO] = '<td class="'.$sColor.' sp-full mt4" style="max-width: 200px; min-width: 70px;"><span class="sp-display-inline font-default">'.__('問題').'.'.$iQqNO.':<br></span>';
								if ($aA['tqStyle'] == 2):
									$aM['Q.'.$iQqNO] .= nl2br($aA['taText']).'</td>';
								else:
									for($i = 1; $i <= 50; $i++):
										if ($aA['taChoice'.$i]):
											$aM['Q.'.$iQqNO] .= '['.$i.']'.nl2br($aA['tqChoice'.$i]).'<br>';
										endif;
									endfor;
								endif;
								$aM['Q.'.$iQqNO] .= '</td>';
							endforeach;
						endif;
					endif;
					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
						$aM['year'] = $aS['stu']['stYear'];
						$aM['class'] = $aS['stu']['stClass'];
					endif;
		?>
			<tr>
				<td nowrap><?php echo $aM['no']; ?></td>
				<td nowrap><?php echo $aM['name']; ?></td>
				<td nowrap><?php echo $aM['year']; ?></td>
				<td nowrap><?php echo $aM['class']; ?></td>
				<td nowrap class="sp-full"><?php echo $aM['date']; ?></td>
				<td nowrap><span class="sp-display-inline font-grey"><?php echo __('得点'); ?>:</span
					><?php echo $aM['score']; ?>
				</td>
				<td nowrap>
					<?php echo $aM['qualify']; ?>
				</td>
				<?php foreach ($aQ as $sK => $sS): ?>
					<?php echo $aM[$sK]; ?>
				<?php endforeach; ?>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>
