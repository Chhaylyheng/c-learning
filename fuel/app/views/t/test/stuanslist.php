<div class="info-box mt0">
	<form action="" method="" id="ChkShow">
	<button type="button" class="button default na width-auto SelShow" style="padding: 4px 8px;"><?php echo __('チェックした行のみ表示'); ?></button>
	<button type="button" class="button default na width-auto AllShow" style="padding: 4px 8px;"><?php echo __('全て表示'); ?></button>
	<div class="info-box table-box record-table admin-table scroll-box mt0">
		<table class="matrix-data" id="Student-Table" cellspacing="0">
		<thead>
			<tr class="font-size-80" >
				<th nowrap="nowrap"><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th nowrap="nowrap" class="string-bottom"><?php echo __('小テスト'); ?></th>
				<th nowrap="nowrap"><?php echo __('合格点'); ?></th>
				<th nowrap="nowrap"><?php echo __('提出'); ?></th>
				<th nowrap="nowrap"><?php echo __('得点'); ?></th>
				<th nowrap="nowrap"><?php echo __('合格'); ?></th>
				<?php for($i = 1; $i <= $iQNum; $i++): ?>
					<th style="min-width: 8em;">Q<?php echo $i; ?></th>
				<?php endfor; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			$sStID = $aStudent['stID'];
			if (!is_null($aTest)):
				foreach ($aTest as $aQ):
					$sTbID = $aQ['tbID'];
					$aM = array(
						'name'=>'<a href="/t/test/anslist/'.$sTbID.'">'.$aQ['tbTitle'].'</a>',
						'border'=>$aQ['tbQualifyScore'],
						'date'=>'<span class="font-red">'.__('未').'</span>',
						'score'=>0,
						'qualify'=>'',
					);
					$aBC = array();

					if (isset($aPut[$sTbID])):
						$aM['date'] = '<a href="/t/test/ansdetail/'.$sTbID.DS.$sStID.'" class="button do na width-auto font-size-80" style="padding: 4px 8px;">'.ClFunc_Tz::tz('\'y/m/d<\\b\\r>H:i',$tz,$aPut[$sTbID]['tpDate']).'</a>';
						$aM['score'] = $aPut[$sTbID]['tpScore'];
						$aM['qualify'] = ($aPut[$sTbID]['tpQualify'])? '○':'';
					endif;
					if (isset($aAns[$sTbID])):
						foreach ($aAns[$sTbID] as $iQS => $aA):
							$aQQ = $aQuery[$sTbID][$iQS];
							$aM['Q.'.$iQS] = '';
							$aBC['Q.'.$iQS] = 'transparent';
							if ($aQQ['tqStyle'] == 2):
								$aM['Q.'.$iQS] = nl2br($aA['taText']);
							else:
								for($i = 1; $i <= 50; $i++):
									if ($aA['taChoice'.$i]):
										$aM['Q.'.$iQS] .= '['.$i.']'.nl2br($aQQ['tqChoice'.$i]).'<br>';
									endif;
								endfor;
							endif;
							$aBC['Q.'.$iQS] = ($aA['taRight'])? 'back-green font-white':'back-red font-white';
							$aM['Q.'.$iQS] = ($aM['Q.'.$iQS])? $aM['Q.'.$iQS]:'─';
						endforeach;
					endif;
		?>
			<tr>
				<td nowrap="nowrap">
					<input type="checkbox" name="StuChk[]" class="Chk" value="<?php echo $sTbID; ?>">
				</td>
				<td style="min-width: 12em;">
					<?php echo $aM['name']; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $aM['border']; ?>
				</td>
				<td nowrap="nowrap" class="text-center">
					<?php echo $aM['date']; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $aM['score']; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $aM['qualify']; ?>
				</td>
				<?php for($i = 1; $i <= $iQNum; $i++): ?>
				<?php $sColor = ($i > $aQ['tbNum'])? 'back-silver':((isset($aBC['Q.'.$i]))? $aBC['Q.'.$i]:''); ?>
					<td class="<?php echo $sColor; ?>">
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
