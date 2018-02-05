<div style="margin-bottom: 5px; font-size: 80%;">
<?php echo Form::open(array('action'=>'/s/attend/history'.(($iALL)? DS.$iALL:'').Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div>
	<?php echo Form::label(__('表示期間').':','sy'); ?>
	<?php echo Form::select('sy',$aY[0],$aYears); ?> -
	<?php echo Form::select('ey',$aY[1],$aYears); ?>
	<?php if ($iALL): ?>
		</div>
		<div>
		<?php echo Form::label(__('講義').':','ct'); ?><br>
		<?php echo Form::select('ct',$sCtID,$aSelectClass); ?>
	<?php endif; ?>
</div>
<div style="text-align: center; margin-top: 4px;">
	<button type="submit" style="padding: 2px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php Form::close(); ?>
</div>

<?php echo Clfunc_Mobile::hr(); ?>

<div style="text-align: center;">
	<table cellpadding="3px" style="border-collapse: collapse; border: 1px solid gray; font-size: 80%;">
	<thead>
		<tr>
			<th style="border: 1px solid gray;"><?php echo __('日付'); ?></th>
			<?php if ($iALL): ?>
			<th style="border: 1px solid gray;"><?php echo __('講義'); ?></th>
			<?php endif; ?>
			<th style="border: 1px solid gray;"><?php echo __('出席'); ?></th>
			<th style="border: 1px solid gray;"><?php echo __('時刻'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
if (!is_null($aBooks))
{
	foreach ($aBooks as $sD => $aCs)
	{
		$iDRow = 0;
		$aRow = null;
		foreach ($aCs as $sCI => $aC)
		{
			if (!isset($aClassList[$sCI]))
			{
				continue;
			}
			$aCinf = $aClassList[$sCI];
			$iCRow = count($aC);
			foreach ($aC as $i => $aA)
			{
				$aRow[$iDRow] = '';
				if ($i == 0)
				{
					if ($iALL)
					{
						$aRow[$iDRow] .= '<td rowspan="'.$iCRow.'" style="border: 1px solid gray;">'.$aCinf['ctName'].'</td>';
					}
				}
				$sTitle = $aAMaster[$sCI][0]['amName'];
				$sDate = '－';
				$sColor = '#c00';

				if ($aA['abAttendDate'])
				{
					$sTitle = $aA['amName'];
					$sDate = ($aA['abAttendDate'] == CL_DATETIME_DEFAULT || !$aA['abAttendDate'])? '－':date('H:i',strtotime($aA['abAttendDate']));
					$sColor = ($aA['amAbsence'])? '#c00':(($aA['amTime'])? '#080':'#00f');
				}
				$aRow[$iDRow] .= '<td style="border: 1px solid gray; color: '.$sColor.';">'.$sTitle.'</td>';
				$aRow[$iDRow] .= '<td style="border: 1px solid gray;">'.$sDate.'</td>';
				$iDRow++;
			}
		}
		if (!is_null($aRow))
		{
			foreach ($aRow as $i => $sValue)
			{
				print '<tr>';
				if ($i == 0)
				{
					print '<td rowspan="'.$iDRow.'" style="border: 1px solid gray; text-align: center;">'.date('\'y/m/d',strtotime($sD)).'('.date('D',strtotime($sD)).')</td>';
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
