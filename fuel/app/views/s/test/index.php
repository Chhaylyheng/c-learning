<div class="info-box mt16">
	<div class="table-box record-table admin-table mt0">
	<?php if (!is_null($aTest)): ?>
		<table class="">
		<thead>
			<tr>
				<th><?php echo __('タイトル'); ?></th>
				<th><?php echo __('ステータス'); ?></th>
				<th><?php echo __('得点'); ?></th>
				<th><?php echo __('解答時間'); ?></th>
				<th><?php echo __('解答日時'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
				foreach ($aTest as $aQ):
					$sLink = null;
					$aPub = array(__('締切'),'font-red');
					$sScore = '─';
					$sTime = '─';
					if ($aQ['tbPublic'] == 1):
						$sLink = 'ans';
						$aPub = array(__('公開中'),'font-blue');
						if ($aQ['tbAutoCloseDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = '～ '.Clfunc_Tz::tz('n/j H:i',$tz,$aQ['tbAutoCloseDate']);
						endif;
					endif;
					$sPut = __('未解答');
					if (isset($aQ['TPut'])):
						$sLink = 'result';
						$sPut = Clfunc_Tz::tz('Y/m/d H:i',$tz,$aQ['TPut']['tpDate']);
						if ($aQ['tbScorePublic'] == 1 || $aQ['tbScorePublic'] == 3)
						{
							$sScore   = $aQ['TPut']['tpScore'].'/'.__(':num点',array('num'=>$aQ['tbTotal']));
						}
						$sTime  = Clfunc_Common::Sec2Min($aQ['TPut']['tpTime']);
					endif;
		?>
<tr>
<td class="sp-full">
<?php if (!is_null($sLink)): ?>
	<a href="/s/test/<?php echo $sLink.'/'.$aQ['tbID']; ?>" class="button na do"><?php echo $aQ['tbTitle']; ?></a>
<?php else: ?>
	<?php echo $aQ['tbTitle']; ?>
<?php endif; ?>
</td>
<td>
	<span class="<?php echo $aPub[1]; ?>"><?php echo $aPub[0]; ?></span>
	<?php if (isset($aPub[2])): ?><br class="pc-display-inline"><span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif; ?>
</td>
<td><span class="sp-display-inline font-grey"><?php echo __('得点'); ?>:</span
	><?php echo $sScore; ?>
</td>
<td><span class="sp-display-inline font-grey"><?php echo __('時間'); ?>:</span
	><?php echo $sTime; ?>
</td>
<td><span class="sp-display-inline font-grey"><?php echo __('提出'); ?>:</span
	><?php echo $sPut; ?>
</td>
</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php else: ?>
		<p><?php echo __('解答可能な小テストはありません'); ?></p>
	<?php endif; ?>
	</div>
</div>
