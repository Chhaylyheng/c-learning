<div class="info-box table-box record-table admin-table">
<?php if (isset($aMsg['default'])): ?>
	<p class="error-box"><?php echo $aMsg['default']; ?></p>
<?php endif; ?>
<form action="/t/attend/editMaster" method="POST">
<table class="kreport-data" id="attend-mode" val="detail">
<thead>
	<tr>
		<th><?php echo __('番号'); ?></th>
		<th><?php echo __('名称'); ?></th>
		<th><?php echo __('短縮名'); ?></th>
		<th><?php echo __('欠席扱い'); ?></th>
		<th><?php echo __('デフォルト'); ?></th>
		<th><?php echo __('経過時間'); ?></th>
	</tr>
</thead>
<tbody>
<?php for($i = 0; $i < 20; $i++): ?>
	<?php $aR = (isset($aInput[$i]))? $aInput[$i]:array('amName'=>'','amShort'=>'','amAbsence'=>0,'amDefault'=>0,'amTime'=>0); ?>
	<tr>
		<td class="text-center"><?php echo $i; ?></td>
		<td>
			<input type="text" name="name<?php echo $i; ?>" value="<?php  echo $aR["amName"]; ?>" maxlength="20" class="width-14em">
		</td>
		<td>
			<input type="text" name="short<?php echo $i; ?>" value="<?php  echo $aR["amShort"]; ?>" maxlength="2" class="width-6em text-center">
		</td>
		<td>
			<?php $sCheck = ($aR["amAbsence"] || $i == 0)? " checked":""; ?>
			<?php $sDisable = ($i > 0)? "":" disabled"; ?>
			<label for="absence<?php echo $i; ?>"><input type="checkbox" name="absence<?php echo $i; ?>" value="1" id="absence<?php echo $i; ?>"<?php echo $sCheck.$sDisable; ?>></label>
		</td>
		<td>
			<?php $sCheck = ($aR["amDefault"])? " checked":""; ?>
			<?php if ($i > 0): ?>
				<label for="default<?php echo $i; ?>"><input type="radio" name="default" value="<?php echo $i; ?>" id="default<?php echo $i; ?>"<?php echo $sCheck; ?>></label>
			<?php else: ?>
				─
			<?php endif; ?>
		</td>
		<td>
			<?php if ($i > 0): ?>
				<select name="time<?php echo $i; ?>" class="dropdown">
					<option value="0">─</option>
					<?php for($j = 5; $j <= 120; $j += 5): ?>
						<?php $sSelect = ($j == (int)$aR["amTime"])? "selected":""; ?>
						<option value="<?php echo $j; ?>"<?php echo $sSelect ?>><?php echo __(':min分後',array('min'=>$j)); ?></option>
					<?php endfor; ?>
				</select>
			<?php else: ?>
				─
			<?php endif; ?>
		</td>
	</tr>
	<?php if (isset($aMsg[$i])): ?>
		<tr><td colspan="6" class="font-red">↑<?php echo $aMsg[$i] ?></td></tr>
	<?php endif; ?>
	<?php endfor; ?>
</tbody>
</table>
<div class="button-box"><button type="submit" class="button do formSubmit"><?php echo __('更新する'); ?></button></div>
</form>
</div>

<div class="info-box mt16">
	<ul class="">
		<li><?php echo __('【名称】（20文字以内）を空白にすると項目削除になります。'); ?></li>
		<li><?php echo __('【短縮名】（2文字以内）は一覧に利用されます。'); ?></li>
		<li><?php echo __('番号0と1の項目を省略することはできません。'); ?></li>
		<li><?php echo __('【デフォルト】は学生が出席操作を行った際に設定される項目にチェックしてください。'); ?></li>
		<li><?php echo __('【経過時間】は出席開始時刻から指定分後に学生が出席操作を行った際に設定される項目となります。'); ?><br>
			<?php echo __('同じ時間に指定した場合は番号の小さい項目が優先されます。'); ?><br>
			<?php echo __('デフォルトチェックしている項目に経過時間を指定することはできません（更新時に ─ となります）。'); ?></li>
		<li><span class="font-red"><?php echo __('運用中に出席項目の変更や削除すると、既存の出席状況に影響がでますのでご注意ください。'); ?></span></li>
	</ul>
</div>
