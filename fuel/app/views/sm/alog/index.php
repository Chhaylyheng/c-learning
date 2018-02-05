<?php if (is_null($aALTheme)): ?>
<div class="font-size: 80%;">
	<?php echo __('公開されている活動履歴テーマがありません。'); ?>
</div>
<?php else: ?>

<div style="margin-bottom: 5px; font-size: 80%;">
<?php echo Form::open(array('action'=>'/s/alog'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div>
	<label><?php echo __('期間'); ?>:</label>
	<input type="text" name="sd" value="<?php echo date('Y/m/d',strtotime($aY[0])); ?>" style="width: 6em; text-align: center;"> -
	<input type="text" name="ed" value="<?php echo date('Y/m/d',strtotime($aY[1])); ?>" style="width: 6em; text-align: center;">
</div>

<div>
	<?php echo Form::label(__('テーマ選択').':','sd'); ?>
	<select name="alt">
		<?php foreach ($aALTheme as $sAltID => $aT): ?>
		<?php $sSel = ($sAltID == $sAlt)? ' selected':''; ?>
		<option value="<?php echo $sAltID; ?>"<?php echo $sSel?>><?php echo $aT['altName']; ?></option>
		<?php endforeach; ?>
	</select>
</div>


<div style="text-align: center; margin-top: 4px;">
	<button type="submit" style="padding: 2px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php Form::close(); ?>
</div>

<?php echo Clfunc_Mobile::hr(); ?>

<?php
if (!is_null($aActive)):
	$sAltID = $aActive['altID'];
?>

<div style="font-size: 80%; margin-bottom: 3px;">
<a href="/s/alog/create/<?php echo $sAltID; ?>"><?php echo ClFunc_Mobile::emj('PENCIL').__('記録追加'); ?></a>
<a href="/s/alog/goal/<?php echo $sAltID; ?>"><?php echo ClFunc_Mobile::emj('CROWN').__(':goalの設定',array('goal'=>$aActive['altGoalLabel'])); ?></a>
</div>

<?php
	if (!is_null($aALogList)):
		$sBorder = ' border: 1px solid gray;';
?>

<table cellspacing="0" style="font-size: 80%; border-colapse: colapse; border: 1px solid gray; margin-left: 2px;">
<thead>
<tr>
	<th style="background-color: #CCFFCC; border: 1px solid gray;"><?php echo __('日付'); ?></th>
	<th style="background-color: #CCFFCC; border: 1px solid gray;">
		<?php echo $aActive['altName']; ?><br>
	</th>
</tr>
</thead>
<tbody>
<?php
foreach ($aALogList as $sD => $aALTs):
	$aRow = null;
	print '<tr><td style="text-align: center; background-color: #CCCCFF; border: 1px solid gray; " nowrap>'.date('y/n/j',strtotime($sD)).'<br>'.$aWeekDay[date('N',strtotime($sD))].'</td>';
	foreach ($aALTs as $aAL):
		print '<td style="vertical-align: top; border: 1px solid gray;">';
		foreach ($aAL as $iNO => $aL):
			$sTitle = (($aL['alTitle'])? $aL['alTitle']:mb_strimwidth($aL['alText'], 0, 20, '…')).' <span class="font-size-80">'.date('H:i',strtotime($aL['alDate'])).'</span>';
			$sCom = ($aL['alCom'])? '<a href="/s/alog/detail/'.$sAltID.DS.$iNO.'">'.ClFunc_Mobile::emj('SMILE').'</a>':'';
			print '<div><a href="/s/alog/edit/'.$sAltID.DS.$iNO.'">'.$sTitle.'</a> '.$sCom.'</div>';
		endforeach;
		print '</td>';
	endforeach;
	print '</tr>';
endforeach;
?>
	</tbody>
	</table>
<?php else: ?>

<div style="font-size: 80%;"><?php echo __('表示条件に一致する記録がありません。'); ?></div>

<?php endif; ?>

<?php else: ?>

<div style="font-size: 80%;"><?php echo __('表示条件を設定してください。'); ?></div>

<?php endif; ?>

<?php endif; ?>
