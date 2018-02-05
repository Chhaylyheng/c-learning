<div class="info-box table-box record-table admin-table">
<?php if (isset($aMsg['default'])): ?>
	<p class="error-box"><?php echo $aMsg['default']; ?></p>
<?php endif; ?>

<p class="text-center"><?php echo __('最大10件の名称を設定できます。最低1件は名称が入っている必要があります。'); ?></p>

<form action="/t/report/ratesetting" method="POST">
<table class="kreport-data" style="width: auto; margin: 1em auto;">
<thead>
	<tr>
		<th><?php echo __('番号'); ?></th>
		<th><?php echo __('名称').' ('.__(':num文字以内',array('num'=>5)).')'; ?></th>
	</tr>
</thead>
<tbody>
<?php for($i = 1; $i <= 10; $i++): ?>
	<?php $aR = (isset($aInput[$i]))? $aInput[$i]:array('rrName'=>''); ?>
	<tr>
		<td class="text-center"><?php echo $i; ?></td>
		<td>
			<input type="text" name="name<?php echo $i; ?>" value="<?php  echo $aR["rrName"]; ?>" maxlength="5" class="width-14em">
			<?php if (isset($aMsg[$i])): ?>
				<p class="error-box"><?php echo $aMsg[$i]; ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php endfor; ?>
</tbody>
</table>
<div class="button-box"><button type="submit" class="button do formSubmit"><?php echo __('更新する'); ?></button></div>
</form>

<p class="text-center"><span class="font-red"><i class="fa fa-warning"></i> <?php echo __('運用中に変更や削除すると、既存のレポート評価に影響がでますのでご注意ください。'); ?></span></p>

</div>
