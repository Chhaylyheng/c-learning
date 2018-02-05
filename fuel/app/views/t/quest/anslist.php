<?php if (!is_null($aStudent) || !is_null($aGuest) || !is_null($aTeach)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_questanslist_sort';
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
				<th nowrap><?php echo __('提出日時'); ?></th>
				<?php
					$aQQs = array();
					$aQ = array();
					foreach ($aQuery as $aQQ):
						$aQ['Q.'.$aQQ['qqSort']] = '<td class="sp-full mt4" style="max-width: 200px; min-width: 70px;"><span class="sp-display-inline font-default">'.__('設問.:no',array('no'=>$aQQ['qqSort'])).':<br></span></td>';
				?>
				<th title="<?php echo $aQQ['qqText']; ?>" class="string-bottom" nowrap><?php echo __('設問.:no',array('no'=>$aQQ['qqSort'])); ?></th>
				<?php
						$aQQs[$aQQ['qqNO']] = $aQQ;
					endforeach;
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','date'=>'─');
					$aM = array_merge($aM,$aQ);

					if (isset($aS['put'])):
						$aM['no'] = $aS['put']['qpstNO'];
						$aM['name'] = $aS['put']['qpstName'];
						$aM['class'] = $aS['put']['qpstClass'];

						if ($aQuest['qbQuickMode'] || $aQuest['qbAnonymous']):
							$sCom = '';
						else:
							$sCom = ($aS['put']['qpComment'])? ' <i class="fa fa-commenting mr0"></i>':' <i class="fa fa-commenting-o mr0"></i>';
						endif;
						$aM['date'] = '<a href="/t/quest/ansdetail/'.$aS['put']['qbID'].'/'.$aS['put']['stID'].'" class="button na width-auto do text-center font-size-80">'.ClFunc_Tz::tz('Y/m/d\<\\b\\r\>H:i',$tz,$aS['put']['qpDate']).$sCom.'</a>';
						if (isset($aS['ans'])):
							foreach ($aS['ans'] as $iQqNO => $aA):
								$aQQ = $aQQs[$iQqNO];
								$aM['Q.'.$iQqNO] = '<td class="sp-full mt4" style="max-width: 200px; min-width: 70px;"><span class="sp-display-inline font-default">'.__('設問.:no',array('no'=>$iQqNO)).':<br></span>';
								if ($aQQ['qqStyle'] == 2):
									$aM['Q.'.$iQqNO] .= nl2br($aA['qaText']);
								else:
									for($i = 1; $i <= 50; $i++):
										if ($aA['qaChoice'.$i]):
											$aM['Q.'.$iQqNO] .= '['.$i.']'.nl2br($aQQ['qqChoice'.$i]).'<br>';
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
				<td nowrap><?php echo $aM['date']; ?></td>
				<?php foreach ($aQ as $sK => $sS): ?>
					<?php echo $aM[$sK]; ?>
				<?php endforeach; ?>
			</tr>
		<?php
				endforeach;
			endif;
			if (!is_null($aGuest)):
				foreach ($aGuest as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','date'=>'─');
					$aM = array_merge($aM,$aQ);

					if (!isset($aS['put'])):
						continue;
					else:
						$aM['no'] = '─GUEST─';
						$aM['name'] = ($aQuest['qbOpen'] == 2)? (($aS['put']['qpstName'])? $aS['put']['qpstName']:(($aS['put']['gtName'])? $aS['put']['gtName']:__('─無記名─'))):__('─匿名─');
						$aM['class'] = '';
						$aM['date'] = '<a href="/t/quest/ansdetail/'.$aS['put']['qbID'].'/'.$aS['put']['stID'].'" class="button na width-auto do text-center font-size-80">'.ClFunc_Tz::tz('Y/m/d\<\\b\\r\>H:i',$tz,$aS['put']['qpDate']).'</a>';
						if (isset($aS['ans'])):
							foreach ($aS['ans'] as $iQqNO => $aA):
								$aQQ = $aQQs[$iQqNO];
								$aM['Q.'.$iQqNO] = '<td class="sp-full mt4" style="max-width: 200px; min-width: 70px;"><span class="sp-display-inline font-default">'.__('設問.:no',array('no'=>$iQqNO)).':<br></span>';
								if ($aQQ['qqStyle'] == 2):
									$aM['Q.'.$iQqNO] .= nl2br($aA['qaText']);
								else:
									for($i = 1; $i <= 50; $i++):
										if ($aA['qaChoice'.$i]):
											$aM['Q.'.$iQqNO] .= '['.$i.']'.nl2br($aQQ['qqChoice'.$i]).'<br>';
										endif;
									endfor;
								endif;
								$aM['Q.'.$iQqNO] .= '</td>';
							endforeach;
						endif;
					endif;
		?>
			<tr>
				<td nowrap><?php echo $aM['no']; ?></td>
				<td nowrap><?php echo $aM['name']; ?></td>
				<td nowrap><?php echo $aM['year']; ?></td>
				<td nowrap><?php echo $aM['class']; ?></td>
				<td nowrap><?php echo $aM['date']; ?></td>
				<?php foreach ($aQ as $sK => $sS): ?>
					<?php echo $aM[$sK]; ?>
				<?php endforeach; ?>
			</tr>
		<?php
				endforeach;
			endif;
			if (!is_null($aTeach)):
				foreach ($aTeach as $sTtID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','date'=>'─');
					$aM = array_merge($aM,$aQ);

					if (!isset($aS['put'])):
						continue;
					else:
						$aM['no'] = '─TEACHER─';
						$aM['name'] = ($aS['put']['qpstName'])? $aS['put']['qpstName']:$aS['put']['ttName'];
						$aM['class'] = ($aS['put']['qpstClass'])? $aS['put']['qpstClass']:$aS['put']['ttDept'].$aS['put']['ttSubject'];
						$aM['date'] = '<a href="/t/quest/ansdetail/'.$aS['put']['qbID'].'/'.$aS['put']['stID'].'" class="button na width-auto do text-center font-size-80">'.ClFunc_Tz::tz('Y/m/d\<\\b\\r\>H:i',$tz,$aS['put']['qpDate']).'</a>';
						if (isset($aS['ans'])):
							foreach ($aS['ans'] as $iQqNO => $aA):
								$aQQ = $aQQs[$iQqNO];
								$aM['Q.'.$iQqNO] = '<td class="sp-full mt4" style="max-width: 200px; min-width: 70px;"><span class="sp-display-inline font-default">'.__('設問.:no',array('no'=>$iQqNO)).':<br></span>';
								if ($aQQ['qqStyle'] == 2):
									$aM['Q.'.$iQqNO] .= nl2br($aA['qaText']);
								else:
									for($i = 1; $i <= 50; $i++):
										if ($aA['qaChoice'.$i]):
											$aM['Q.'.$iQqNO] .= '['.$i.']'.nl2br($aQQ['qqChoice'.$i]).'<br>';
										endif;
									endfor;
								endif;
								$aM['Q.'.$iQqNO] .= '</td>';
							endforeach;
						endif;
					endif;
		?>
			<tr>
				<td nowrap><?php echo $aM['no']; ?></td>
				<td nowrap><?php echo $aM['name']; ?></td>
				<td nowrap><?php echo $aM['year']; ?></td>
				<td nowrap><?php echo $aM['class']; ?></td>
				<td nowrap><?php echo $aM['date']; ?></td>
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
