<div class="info-box mt16">
	<div class="table-box record-table admin-table mt0">
	<?php if (!is_null($aQuest)): ?>
		<table>
		<thead>
			<tr><th><?php echo __('タイトル'); ?></th><th><?php echo __('ステータス'); ?></th><th><?php echo __('回答日時'); ?></th><th><?php echo __('公開情報'); ?></th></tr>
		</thead>
		<tbody>
		<?php
				foreach ($aQuest as $aQ):
					$sLink = null;
					$sQuick = ($aQ['qbQuickMode'])? '[Q] ':'';
					$aPub = array(__('締切'),'font-red');
					if ($aQ['qbPublic'] == 1):
						$sLink = 'ans';
						$aPub = array(__('公開中'),'font-blue');
						if ($aQ['qbAutoCloseDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = '～ '.ClFunc_Tz::tz('n/j H:i',$tz,$aQ['qbAutoCloseDate']);
						endif;
					endif;
					$sPut = __('未回答');
					if (isset($aQ['QPut'])):
						$sLink = 'result';
						$sPut = ClFunc_Tz::tz('Y/m/d H:i',$tz,$aQ['QPut']['qpDate']);
						if ($aQ['qbComPublic'] > 0):
							if ($aQ['QPut']['qpComment']):
								$sPut .= '<a href="/g/quest/result/'.$aQ['qbID'].'" title="'.__('コメントあり').'"><i class="fa fa-commenting fa-fw fa-lg" style="vertical-align: top;"></i></a>';
							else:
								$sPut .= '<a href="/g/quest/result/'.$aQ['qbID'].'" title="'.__('コメントなし').'"><i class="fa fa-comment-o"></i></a>';
							endif;
						endif;
					endif;
		?>
<tr>
<td class="sp-full">
<?php if (!is_null($sLink)): ?>
	<a href="/g/quest/<?php echo $sLink.'/'.$aQ['qbID']; ?>" class="button na do"><?php echo $sQuick.$aQ['qbTitle']; ?></a>
<?php else: ?>
	<?php echo $sQuick.$aQ['qbTitle']; ?>
<?php endif; ?>
</td>
<td class="">
	<span class="<?php echo $aPub[1]; ?>"><?php echo $aPub[0]; ?></span>
	<?php if (isset($aPub[2])): ?><br class="pc-display-inline"><span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif; ?>
</td>
<td class=""><span class="sp-display-inline font-grey"><?php echo __('提出'); ?>:</span
	><?php echo $sPut; ?>
</td>
<td class="sp-full">
<?php
	$aOut = array();
	if ($aQ['qbBentPublic']):
		$aOut[] = '<a href="/g/quest/bent/'.$aQ['qbID'].'">'.__('集計結果').'</a>';
	endif;
?>
	<?php echo implode(' / ',$aOut); ?>
</td>
</tr>
		<?php
				endforeach;
		?>
		</tbody>
		</table>
	<?php else: ?>
		<p><?php echo __('回答可能なアンケートはありません。'); ?></p>
	<?php endif; ?>
	</div>
</div>
