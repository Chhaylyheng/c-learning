<div class="info-box mt0">
	<form action="" method="" id="ChkShow">
	<button type="button" class="button default na width-auto SelShow" style="padding: 4px 8px;"><?php echo __('チェックした行のみ表示'); ?></button>
	<button type="button" class="button default na width-auto AllShow" style="padding: 4px 8px;"><?php echo __('全て表示'); ?></button>
	<div class="info-box table-box record-table admin-table scroll-box mt0">
		<table class="matrix-data" id="Student-Table" cellspacing="0">
		<thead>
			<tr class="font-size-80" >
				<th nowrap="nowrap"><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th nowrap="nowrap" class="string-bottom"><?php echo __('アンケート'); ?></th>
				<th nowrap="nowrap"><?php echo __('提出'); ?></th>
				<?php for($i = 1; $i <= $iQNum; $i++): ?>
					<th style="min-width: 8em;">Q<?php echo $i; ?></th>
				<?php endfor; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			$sStID = $aStudent['stID'];
			if (!is_null($aQuest)):
				foreach ($aQuest as $aQ):
					$sQbID = $aQ['qbID'];
					$sQuick = ($aQ['qbQuickMode'])? '[Q]':'';
					$aM = array('name'=>'<a href="/t/quest/anslist/'.$sQbID.'">'.$sQuick.$aQ['qbTitle'].'</a>','date'=>'<span class="font-red">'.__('未').'</span>');

					if (isset($aPut[$sQbID])):
						if ($aQ['qbAnonymous']):
							$aM['date'] = '<i class="fa fa-check font-green"></i>';
						else:
							$aM['date'] = '<a href="/t/quest/ansdetail/'.$sQbID.DS.$sStID.'" class="button do na width-auto font-size-80" style="padding: 4px 8px;">'.ClFunc_Tz::tz('\'y/m/d<\\b\\r>H:i',$tz,$aPut[$sQbID]['qpDate']).'</a>';
						endif;
					endif;
					if (isset($aAns[$sQbID])):
						foreach ($aAns[$sQbID] as $iQS => $aA):
							$aQQ = $aQuery[$sQbID][$iQS];
							$aM['Q.'.$iQS] = '';
							if ($aQ['qbAnonymous']):
								$aM['Q.'.$iQS] = '─';
								continue;
							endif;
							if ($aQQ['qqStyle'] == 2):
								$aM['Q.'.$iQS] = nl2br($aA['qaText']);
							else:
								for($i = 1; $i <= 50; $i++):
									if ($aA['qaChoice'.$i]):
										$aM['Q.'.$iQS] .= '['.$i.']'.nl2br($aQQ['qqChoice'.$i]).'<br>';
									endif;
								endfor;
							endif;
						endforeach;
					endif;
		?>
			<tr>
				<td nowrap="nowrap">
					<input type="checkbox" name="StuChk[]" class="Chk" value="<?php echo $sQbID; ?>">
				</td>
				<td style="min-width: 12em;">
					<?php echo $aM['name']; ?>
				</td>
				<td nowrap="nowrap" class="text-center">
					<?php echo $aM['date']; ?>
				</td>
				<?php for($i = 1; $i <= $iQNum; $i++): ?>
				<?php $sColor = ($i > $aQ['qbNum'])? 'silver':'white'; ?>
					<td style="background-color: <?php echo $sColor; ?>;">
						<?php if (isset($aM['Q.'.$i])): ?>
						<?php echo mb_strimwidth($aM['Q.'.$i], 0, 70, '…'); ?>
						<?php endif; ?>
					</td>
				<?php endfor; ?>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
	</form>
</div>
