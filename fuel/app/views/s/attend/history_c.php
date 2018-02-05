		<ol class="breadcrumb">
			<li><a href="/s/index/class/<?php echo $aClass['ctID']; ?>"><?php echo $aClass['ctName']; ?> （<?php echo $aClass['dpName']; ?><?php echo $aWeekDay[$aClass['ctWeekDay']]; ?><?php echo $aClass['dhName']; ?>）</a></li>
		</ol>

		<div class="table-responsive" style="margin-top: 1em;">
		<table class="table table-condensed" style="width: auto;">
		<caption class="table-caption"><i class="fa fa-chevron-down"></i> <?php echo __('出席履歴'); ?></caption>
		<thead>
			<tr><td colspan="3"><h3><?php echo __('出席数'); ?>： <?php echo $iAttend; ?> / <?php echo $iAll; ?>（<?php echo $fAvg; ?>%）</h3></td></tr>
			<tr><th><?php echo __('日付'); ?></th><th><?php echo __('出席'); ?></th><th><?php echo __('時刻'); ?></th></tr>
		</thead>
		<tbody>
<?php
if (!is_null($aBooks))
{
	foreach ($aBooks as $sD => $aCs)
	{
		$aRow = null;
		$iDRow = 0;
		foreach ($aCs as $i => $aA)
		{
			$aRow[$iDRow] = '';
			$sTitle = $aAMaster[0]['amName'];
			$sDate = '－';
			$sColor = 'text-danger';

			if ($aA['abAttendDate'])
			{
				$sTitle = $aA['amName'];
				$sDate = ($aA['abAttendDate'] == CL_DATETIME_DEFAULT || !$aA['abAttendDate'])? '－':date('H:i:s',strtotime($aA['abAttendDate']));
				$sColor = ($aA['amAbsence'])? 'text-danger':(($aA['amTime'])? 'text-warning':'text-primary');
			}
			$aRow[$iDRow] .= '<td class="'.$sColor.'" style="padding-left: 10px; padding-right: 10px;">'.$sTitle.'</td>';
			$aRow[$iDRow] .= '<td style="padding-left: 10px; padding-right: 10px;">'.$sDate.'</td>';
			$iDRow++;
		}
		if (!is_null($aRow))
		{
			foreach ($aRow as $i => $sValue)
			{
				print '<tr>';
				if ($i == 0)
				{
					print '<td rowspan="'.$iDRow.'" style="padding-left: 10px; padding-right: 10px;">'.date('Y/m/d',strtotime($sD)).'（'.$aWeekDay[date('N',strtotime($sD))].'）</td>';
				}
				print $sValue;
				print '</tr>';
			}
		}
	}
}
?>
		</tbody>
		</table>
		</div>

