	<section class="pt0">
		<div class="info-box">
			<?php $iY = date('Y'); $iP = 1; ?>
			<?php if (date("n") < 4 || date("n") > 9): ?>
				<?php $iY = (date("n") < 4)? date('Y') - 1:$iY; ?>
				<?php $iP = 2; ?>
			<?php endif; ?>
			<p><a href="/adm/kreport/create/<?php echo $iY.'/'.$iP; ?>" class="button do">今期レポートを追加</a></p>
			<div class="info-box table-box record-table admin-table mt0">
			<table>
			<thead>
				<tr><th>タイトル</th><th>公開</th><th>提出状況</th><th>操作</th></tr>
			</thead>
			<tbody>
				<?php
					if (!is_null($aReport)):
						$iMax = count($aReport);
						foreach ($aReport as $i => $aQ):
							$sOdd = ($i % 2)? '':'odd';
							$sTitle = $aQ['krYear'].'年度 '.(($aQ['krPeriod'] == 1)? '4～9月期':'10～3月期');
							$sJsKey = $aQ['krYear'].'_'.$aQ['krPeriod'];
							$aPub = array('締切','text-warning');
							if ($aQ['krPublic'] == 1):
								$aPub = array('公開中','text-primary');
								if ($aQ['krAutoCloseDate'] != CL_DATETIME_DEFAULT):
									$aPub[2] = '～ '.date('n/j H:i',strtotime($aQ['krAutoCloseDate']));
								endif;
							elseif ($aQ['krPublic'] == 0):
								$aPub = array('非公開','text-default');
								if ($aQ['krAutoPublicDate'] != CL_DATETIME_DEFAULT):
									$aPub[2] = date('n/j H:i',strtotime($aQ['krAutoPublicDate'])).' ～';
								endif;
							endif;
				?>
<tr class="<?php echo $sOdd; ?>">
<td class="">
<?php echo $sTitle; ?>
</td>
<td class="">
	<div class="btn-group">
		<button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle" type="button">
			<span class="<?php echo $aPub[1]; ?>"><?php echo $aPub[0]; ?></span><?php if (isset($aPub[2])): ?><br /><small><?php echo $aPub[2]; ?></small><?php endif; ?>
		</button>
	</div>
</td>
<td class="KReportPutColumn"><a href="/adm/kreport/put/<?php echo $aQ['no']; ?>"><span class="lead"><?php echo $aQ['krPutNum']; ?></span> / <?php echo $aQ['krSetNum']; ?></a></td>
<td>
	<div>
		<a href="/adm/kreport/preview/<?php echo $aQ['no']; ?>" target="questpreview" class="btn btn-default btn-sm">プレビュー</a><br>
		<a href="/adm/kreport/target/<?php echo $aQ['no']; ?>">回答対象者の変更</a><br>
		<a href="#" class="KReportPutReset" value="<?php echo $sJsKey; ?>">提出状況をリセット</a><br>
	</div>
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
	</section>
