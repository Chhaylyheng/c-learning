<?php if ($aTSAL['caProgress'] == 1): ?>
<script type="text/javascript">
var intervalTime = 2000;
var timerID;

timerID = setInterval(function() { ArchiveDownload() },intervalTime);
</script>
<?php endif; ?>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr>
				<!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('タイトル'); ?></th>
				<th><?php echo __('提出状況'); ?></th>
				<th><?php echo __('合格数'); ?></th>
				<th><?php echo __('平均点'); ?></th>
				<th><?php echo __('合格点'); ?></th>
				<th><?php echo __('満点'); ?></th>
				<th><?php echo __('制限時間'); ?></th>
				<th><?php echo __('小テスト公開'); ?></th>
				<th><?php echo __('点数と解説の公開'); ?></th>
				<th><?php echo __('操作'); ?></th>
				<th><?php echo __('登録日時'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aTest)):
				$iMax = count($aTest);
				foreach ($aTest as $aQ):
					$sJsKey = $aQ['ctID'].'_'.$aQ['tbID'];
					$aPub = array(__('締切'),'font-red');
					$aBent = array(__('非公開'),'font-default');
					if ($aQ['tbPublic'] == 1):
						$aPub = array(__('公開中'),'font-blue');
						if ($aQ['tbAutoCloseDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = '～ '.ClFunc_Tz::tz('n/j H:i',$tz,$aQ['tbAutoCloseDate']);
						endif;
					elseif ($aQ['tbPublic'] == 0):
						$aPub = array(__('非公開'),'font-default');
						if ($aQ['tbAutoPublicDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = ClFunc_Tz::tz('n/j H:i',$tz,$aQ['tbAutoPublicDate']).' ～';
						endif;
					endif;
					switch ($aQ['tbScorePublic']):
						case 1:
							$aBent = array(__('点数'),'font-green');
						break;
						case 2:
							$aBent = array(__('解説'),'font-green');
						break;
						case 3:
							$aBent = array(__('点数・解説'),'font-blue');
						break;
					endswitch;
					$aSort = array(' ',' ');
					if ($aQ['tbSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aQ['tbSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;
		?>
		<tr id="<?php echo $aQ['tbID']; ?>">
			<!-- <td class="text-center" nowrap="nowrap"><input type="checkbox" name="Close[]" value="<?php echo $aQ['tbID']; ?>"></td> -->
			<td class="sp-full">
			<?php if ($aQ['tbNum']): ?>
				<a href="/t/test/bent/<?php echo $aQ['tbID']; ?>" class="button na do width-auto" target="_blank"><i class="fa fa-bar-chart pr8"></i><?php echo mb_strimwidth($aQ['tbTitle'], 0, 42, '…'); ?></a>
			<?php else: ?>
				<?php echo $aQ['tbTitle']; ?>
			<?php endif; ?>
			</td>
			<td class="" nowrap="nowrap">
				<a href="/t/test/put/<?php echo $aQ['tbID']; ?>" class="button na default width-auto"><span class="PutNum"><?php echo $aQ['tpNum']; ?></span><?php echo __('名'); ?></a>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('合格'); ?>:</span
				><span class="QualifyNum font-size-120"><?php echo $aQ['tpQualify']; ?></span>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('平均'); ?>:</span
				><span class="AvgScore font-size-120"><?php echo ($aQ['tpNum'])? round($aQ['tpScore']/$aQ['tpNum'],2):0; ?></span><?php echo __('点'); ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('合格点'); ?>:</span
				><?php echo __(':num点',array('num'=>$aQ['tbQualifyScore'])); ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('満点'); ?></span
				><?php echo __(':num点',array('num'=>$aQ['tbTotal'])); ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('時間'); ?>:</span
				><?php echo ($aQ['tbLimitTime'])? __(':num分',array('num'=>$aQ['tbLimitTime'])):'─'; ?>
			</td>

			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="test-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sJsKey; ?>_public"><div><?php echo $aPub[0]; ?><?php if (isset($aPub[2])): ?><br><span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif;?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="test-dropdown-toggle <?php echo $aBent[1]; ?>" id="<?php echo $sJsKey; ?>_bent"><div><?php echo $aBent[0]; ?></div></button>
				</div>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="test-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<?php if ($aQ['tbNum']): ?>
					<a href="/t/test/preview/<?php echo $aQ['tbID']; ?>" target="testpreview" class="button na default width-auto" style="padding: 8px 4px;">Preview</a>
				<?php else: ?>
					<button class="button na default width-auto back-silver" title="<?php echo __('問題を作成してください'); ?>" style="padding: 7px 4px;">Preview</button>
				<?php endif; ?>
				<button<?php echo $aSort[0]; ?> class="TestSort button na default width-auto text-center" style="padding: 7px 2px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off" title="上へ"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="TestSort button na default width-auto text-center" style="padding: 7px 2px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off" title="下へ"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
			<td class="font-size-80" style="line-height: 1.1;">
				<?php echo ClFunc_Tz::tz('Y/m/d\<\\b\\r\> H:i',$tz,$aQ['tbDate']); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-public" obj="">
	<li><a href="#" class="TestPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="TestPublic" obj="close"><span class="font-red"><?php echo __('締切'); ?></span></a></li>
	<li><a href="#" class="TestPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-bent" obj="">
	<li><a href="#" class="TestScorePublic" obj="public3"><span class="font-blue"><?php echo __('点数・解説'); ?></span></a></li>
	<li><a href="#" class="TestScorePublic" obj="public2"><span class="font-green"><?php echo __('解説'); ?></span></a></li>
	<li><a href="#" class="TestScorePublic" obj="public1"><span class="font-green"><?php echo __('点数'); ?></span></a></li>
	<li><a href="#" class="TestScorePublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="TestEdit text-left"><span class="font-default"><?php echo __('小テスト情報の編集'); ?></span></a></li>
	<li><a href="#" class="TestQueryEdit text-left"><span class="font-default"><?php echo __('問題の編集'); ?></span></a></li>
	<li><a href="#" class="TestCopy text-left"><span class="font-default"><?php echo __('小テストのコピー'); ?></a></li>
	<li><a href="#" class="TestToCSV text-left"><span class="font-default"><i class="fa fa-download"></i> <?php echo __('小テスト情報のCSVの保存'); ?></a></li>
	<li><a href="#" class="TestResultToCSV text-left"><span class="font-default"><i class="fa fa-download"></i> <?php echo __('解答内容CSVのダウンロード'); ?></span></a></li>

	<li><a href="#" class="Test2Drill text-left"><span class="font-default"><?php echo __('問題をドリルにコピー'); ?></a></li>

	<li><a href="#" class="TestPutReset text-left"><span class="font-default"><?php echo __('提出状況をリセット'); ?></span></a></li>
	<li><a href="#" class="TestDelete text-left"><span class="font-default"><?php echo __('小テストの削除'); ?></span></a></li>
</ul>
