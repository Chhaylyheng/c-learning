<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr>
				<!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('タイトル'); ?></th>
				<th><?php echo __('実施状況'); ?></th>
				<th><?php echo __('出題 / 登録'); ?></th>
				<th><?php echo __('公開'); ?></th>
				<th><?php echo __('操作'); ?></th>
				<th><?php echo __('登録日時'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aDrill)):
				$iMax = count($aDrill);
				foreach ($aDrill as $aQ):
					$sJsKey = $aQ['dcID'].'_'.$aQ['dbNO'];
					$sParam = $aQ['dcID'].DS.$aQ['dbNO'];
					$aPub = array(__('非公開'),'font-default');
					if ($aQ['dbPublic'] == 1):
						$aPub = array(__('公開中'),'font-blue');
					endif;
					$aSort = array(' ',' ');
					if ($aQ['dbSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aQ['dbSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;
					$sQQNum = ($aQ['dbPublicNum'] > $aQ['dbQueryNum'])? 'font-red':'';

					$iNum = 0;
					$sAvg = '0%';
					if (isset($aPut[$aQ['dbNO']])):
						$iNum = $aPut[$aQ['dbNO']]['num'];
						if ($iNum)
						{
							$sAvg = round($aPut[$aQ['dbNO']]['sum']/$iNum, 1).'%';
						}
					endif;
		?>
		<tr id="<?php echo $sJsKey; ?>">
			<td class="sp-full">
				<?php if ($aQ['dbQueryNum']): ?>
				<a href="/t/drill/queryanalysis/<?php echo $sParam; ?>" class="button na do width-auto"><i class="fa fa-bar-chart pr8"></i><?php echo mb_strimwidth($aQ['dbTitle'], 0, 42, '…'); ?></a>
				<?php else: ?>
				<?php echo mb_strimwidth($aQ['dbTitle'], 0, 42, '…'); ?>
				<?php endif; ?>
			</td>
			<td class="" nowrap="nowrap">
				<a href="/t/drill/put/<?php echo $sParam; ?>" class="button na default width-auto"><span class="RightAvg"><?php echo $sAvg; ?></span> / <span class="PutNum"><?php echo $iNum; ?></span></a>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('出題 / 登録'); ?>:</span
				><span class="font-size-100 <?php echo $sQQNum?>"><?php echo $aQ['dbPublicNum'].' / '.$aQ['dbQueryNum']; ?></span>
			</td>

			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="drill-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sJsKey; ?>_public"><div><?php echo $aPub[0]; ?><?php if (isset($aPub[2])): ?><br><span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif;?></div></button>
				</div>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="drill-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<?php if ($aQ['dbQueryNum']): ?>
					<a href="/t/drill/preview/<?php echo $sParam; ?>" target="drillpreview" class="button na default width-auto" style="padding: 8px 4px;">Preview</a>
				<?php else: ?>
					<button class="button na default width-auto back-silver" title="<?php echo __('問題を作成してください'); ?>" style="padding: 7px 4px;">Preview</button>
				<?php endif; ?>
				<button<?php echo $aSort[0]; ?> class="DrillSort button na default width-auto text-center" style="padding: 7px 2px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off" title="上へ"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="DrillSort button na default width-auto text-center" style="padding: 7px 2px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off" title="下へ"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
			<td class="font-size-80" style="line-height: 1.1;">
				<?php echo ClFunc_Tz::tz('Y/m/d\<\\b\\r\> H:i',$tz,$aQ['dbDate']); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-public" obj="">
	<li><a href="" class="DrillPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="" class="DrillPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="" class="DrillEdit text-left"><span class="font-default"><?php echo __('ドリル情報の編集'); ?></span></a></li>
	<li><a href="" class="DrillQueryEdit text-left"><span class="font-default"><?php echo __('問題の追加/編集'); ?></span></a></li>
	<li><a href="" class="DrillToCSV text-left"><span class="font-default"><i class="fa fa-download"></i> <?php echo __('問題のCSV保存'); ?></a></li>
	<li><a href="" class="Drill2Test text-left"><span class="font-default"><?php echo __('問題を小テストにコピー'); ?></span></a></li>

	<li><a href="" class="DrillPutReset text-left"><span class="font-default"><?php echo __('実施状況をリセット'); ?></span></a></li>
	<li><a href="" class="DrillDelete text-left"><span class="font-default"><?php echo __('ドリルの削除'); ?></span></a></li>
</ul>
