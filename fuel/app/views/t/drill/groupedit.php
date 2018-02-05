<div class="info-box table-box record-table admin-table">
<?php if (isset($aMsg['default'])): ?>
	<p class="error-box"><?php echo $aMsg['default']; ?></p>
<?php endif; ?>
<table class="kreport-data" id="DQGList">
<thead>
	<tr>
		<th><?php echo __('名称'); ?></th>
		<th><?php echo __('問題数'); ?></th>
		<th><?php echo __('操作'); ?></th>
	</tr>
</thead>
<tbody>
<?php
foreach($aDQGroup as $aD):
	$sJsKey = $aD['dcID'].'_'.$aD['dgNO'];
	if ($aD['dgQNum']):
		$sDel = '';
	else:
		$sDel = '<button class="button na default width-auto DQGDelBtn">'.__('削除').'</button>';
	endif;
	$iMax = count($aDQGroup) - 1;
	$aSort = array(' ',' ');
	if ($aD['dgSort'] == $iMax):
		$aSort[1] = ' disabled="disabled"';
	endif;
	if ($aD['dgSort'] == 1):
		$aSort[0] = ' disabled="disabled"';
	endif;

?>
	<tr obj="<?php echo $sJsKey; ?>" no="<?php echo $aD['dgNO']; ?>">
		<td class="DQGName">
			<input type="text" name="dg_name" value="<?php  echo $aD["dgName"]; ?>" maxlength="20" class="width-20em" style="display: inline-block;" placeholder="<?php echo __('名称を設定してください');?>">
			<button class="button na default width-auto DQGUpdate"><?php echo __('更新'); ?></button>
		</td>
		<td class="DQGNum"><?php echo $aD['dgQNum']; ?></td>
		<td class="DQGDel">
			<?php if ($aD['dgNO']): ?>
				<button<?php echo $aSort[0]; ?> class="DQGSort button na default width-auto text-center" style="padding: 6px 4px;" value="up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="DQGSort button na default width-auto text-center" style="padding: 6px 4px;" value="down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<?php echo $sDel; ?>
			<?php else: ?>
				─
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<button class="button na default width-auto DQGadd mt8"><?php echo __('グループ追加'); ?></button>
</div>

<div class="info-box mt16">
	<ul class="">
		<li><?php echo __('先頭の項目または登録問題がある場合は削除できません。'); ?></li>
		<li><?php echo __('グループ追加後は名称を入力して、更新ボタンを押してください。'); ?></li>
	</ul>
</div>

<button disabled="disabled" class="DQGSort button na default width-auto text-center" style="padding: 6px 4px; display: none;" value="up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
<button disabled="disabled" class="DQGSort button na default width-auto text-center" style="padding: 6px 4px; display: none;" value="down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
<button class="button na default width-auto DQGDelBtn" style="display: none;"><?php echo __('削除'); ?></button>