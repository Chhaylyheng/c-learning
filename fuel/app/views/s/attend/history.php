<div class="mt0">
	<h2><a href="#" class="link-out accordion" style="padding: 6px 0 6px 30px; background-position: 8px center;"><?php echo __('表示条件設定'); ?></a></h2>
	<div class="accordion-content acc-content-open" style="display: none;">
	<div class="accordion-content-inner pt8">
<?php echo Form::open(array('action'=>'/s/attend/history'.(($iALL)? DS.$iALL:''),'method'=>'post','role'=>'form','class'=>'form-inline')) ; ?>
<div class="form-group">
	<?php echo Form::label(__('表示期間').'：','sy',array('class'=>'control-label')); ?><br class="sp-display">
	<?php echo Form::select('sy',$aY[0],$aYears,array('class'=>'dropdown')); ?> -
	<?php echo Form::select('ey',$aY[1],$aYears,array('class'=>'dropdown')); ?>
	<?php if ($iALL): ?>
		<br>
		<?php echo Form::label(__('講義').'：','ct',array('class'=>'control-label')); ?><br class="sp-display">
		<?php echo Form::select('ct',$sCtID,$aSelectClass,array('class'=>'dropdown mt4 width-100')); ?>
	<?php endif; ?>
</div>
<div class="form-group mt8 button-box">
	<button type="submit" class="button na do width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php Form::close(); ?>
	</div>
	</div>
</div>


<div class="mt8 info-box">
<div class="table-box record-table admin-table" style="padding: 0;">
	<table class="kreport-data">
	<thead>
		<tr>
			<th><?php echo __('日付'); ?></th>
			<?php if ($iALL): ?>
			<th><?php echo __('講義'); ?></th>
			<?php endif; ?>
			<th><?php echo __('出席'); ?></th>
			<th><?php echo __('時刻'); ?></th>
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
						$aRow[$iDRow] .= '<td rowspan="'.$iCRow.'">'.$aCinf['ctName'].'</td>';
					}
				}
				$sTitle = $aAMaster[$sCI][0]['amName'];
				$sDate = '－';
				$sColor = 'text-danger';

				if ($aA['abAttendDate'])
				{
					$sTitle = $aA['amName'];
					$sDate = ($aA['abAttendDate'] == CL_DATETIME_DEFAULT || !$aA['abAttendDate'])? '－':ClFunc_Tz::tz('H:i',$tz,$aA['abAttendDate']);
					$sColor = ($aA['amAbsence'])? 'font-red':(($aA['amTime'])? 'font-green':'font-blue');
				}
				$aRow[$iDRow] .= '<td class="'.$sColor.'">'.$sTitle.'</td>';
				$aRow[$iDRow] .= '<td>'.$sDate.'</td>';
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
					print '<td rowspan="'.$iDRow.'" class="line-height-13">'.date('Y/m/d',strtotime($sD)).'<br class="sp-display">('.$aWeekDay[date('N',strtotime($sD))].')</td>';
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
</div>
