<?php if (is_null($aALTheme)): ?>
<div class="mt0 info-box">
	<p><?php echo __('公開されている活動履歴テーマがありません。'); ?></p>
</div>
<?php else: ?>

<?php $sDisp = (!is_null($aActive))? 'none':'block'; ?>

<div class="mt0">
	<h2><a href="#" class="link-out accordion" style="padding: 6px 0 6px 30px; background-position: 8px center;"><?php echo __('表示条件設定'); ?></a></h2>
	<div class="accordion-content acc-content-open" style="display: <?php echo $sDisp; ?>;">
	<div class="accordion-content-inner pt8">
<?php echo Form::open(array('action'=>'/s/alog/index','method'=>'post','role'=>'form','class'=>'form-inline')) ; ?>
<input type="hidden" name="alt" value="">
<div class="form-group">
	<p class="mt4">
		<?php echo __('期間'); ?>
		<input type="text" name="sd" value="<?php echo date('Y/m/d',strtotime($aY[0])); ?>" id="datepick1" class="width-10em text-center inline-block"> ～
		<input type="text" name="ed" value="<?php echo date('Y/m/d',strtotime($aY[1])); ?>" id="datepick2" class="width-10em text-center inline-block">
	</p>

	<p class="mt4">
	<?php echo __('テーマ選択'); ?>
	<select class="dropdown text-left" name="alt">
		<?php foreach ($aALTheme as $sAltID => $aT): ?>
		<?php $sSel = ($sAltID == $sAlt)? ' selected':''; ?>
		<option value="<?php echo $sAltID; ?>" class="text-left"<?php echo $sSel; ?>><?php echo $aT['altName']; ?></option>
		<?php endforeach; ?>
	</select>
	</p>

</div>
<div class="form-group mt8 button-box text-left">
	<button type="submit" class="button na do width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php echo Form::close(); ?>
	</div>
	</div>
</div>


<div class="mt8 info-box" style="z-index: 1;">
<div class="table-box record-table admin-table" style="padding: 0;">
	<table class="kreport-data line-height-13 font-size-90">
	<thead>
		<tr>
			<th><?php echo __('日付'); ?></th>
			<?php $aT = $aActive; ?>

			<th>
				<?php echo $aT['altName']; ?>
				<a href="#" class="AlogGoalSet" id="<?php echo $aT['altID']; ?>" title="<?php echo __(':goalの設定',array('goal'=>$aT['altGoalLabel'])); ?>"><i class="fa fa-flag mr0"></i></a><br>
				<a href="/s/alog/create/<?php echo $aT['altID']; ?>" class="button na do width-auto"><i class="fa fa-plus mr4"></i><?php echo __('記録追加'); ?></a>
			</th>

		</tr>
	</thead>
	<tbody>
<?php
if (!is_null($aALogList)):
	foreach ($aALogList as $sD => $aALTs):
		$aRow = null;
		foreach ($aALTs as $sAltID => $aAL):
			$aRow[$sAltID] = '<td style="vertical-align: top;">';

			foreach ($aAL as $iNO => $aL):
				$sTitle = '<span class="font-size-80">'.ClFunc_Tz::tz('H:i',$tz,$aL['alDate']).'</span> '.(($aL['alTitle'])? $aL['alTitle']:mb_strimwidth($aL['alText'], 0, 20, '…'));
				$sCom = ($aL['alCom'])? '<a href="/s/alog/detail/'.$sAltID.DS.$iNO.'"><i class="fa fa-commenting mr0 ml4"></i></a>':'';
				$aRow[$sAltID] .= '<p class="mt4"><a href="/s/alog/edit/'.$sAltID.DS.$iNO.'">'.$sTitle.'</a> '.$sCom.'</p>';
			endforeach;

			$aRow[$sAltID] .= '</td>';
		endforeach;
		if (!is_null($aRow)):
			print '<tr>';
			print '<td>'.date('Y/m/d',strtotime($sD)).'<br class="sp-display">('.$aWeekDay[date('N',strtotime($sD))].')</td>';
			foreach ($aALTheme as $aT):
				if (isset($aRow[$aT['altID']])):
					print $aRow[$aT['altID']];
				endif;
			endforeach;
			print '</tr>';
		endif;
	endforeach;
endif;
?>
	</tbody>
	</table>
</div>
</div>

<form action="" method="post" id="GoalEditBox" class="width-95" style="display: none;">
	<a href="" class="font-size-180 GoalEditClose"><i class="fa fa-times mr0"></i></a>
	<input type="hidden" name="alt" value="">
	<div class="formControl font-size-90 width-100" style="margin: auto;">
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><?php echo __('活動履歴テーマ'); ?></div>
			<div class="formContent inline-box width-100 font-bold" id="ALTheme"></div>
		</div>
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;" id="ALGoalLabel"></div>
			<div class="formContent inline-box width-100">
				<p id="ALGoalDesc"></p>
				<textarea name="ag_text" class="width-100"></textarea>
			</div>
		</div>
	</div>
	<div class="button-box text-center mt4">
		<button class="button na do width-auto"><?php echo __('更新'); ?></button>
	</div>
</form>

<?php endif; ?>
