<?php
	$errClass = array('sendto'=>'','detail'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
	if (isset($error['estimate_error'])):
?>
<p class="error-box"><?php echo $error['estimate_error']; ?></p>
<?php endif; ?>


<div class="info-box estimate-input">
	<?php echo Form::open(array('action'=>'/t/payment/estimateedit/'.$aE['eNO'],'method'=>'post','id'=>'estimate-form')) ; ?>
	<h2>見積番号</h2>
	<p><?php echo $aE['eNO']; ?></p>

	<h2>発行日</h2>
	<p><?php echo ClFunc_Tz::tz('Y年n月j日',$tz,$aE['eDate']); ?></p>

	<h2>宛名</h2>
	<p><input type="text" name="sendto"  size="30" maxlength="25" value="<?php echo $sendto; ?>"<?php echo $errClass['sendto']; ?>></p>
	<?php echo $errMsg['sendto']; ?>

	<hr class="mt32 sp-show">

	<?php echo $errMsg['detail']; ?>
	<div class="info-box table-box no-pad record-table estimate-input-table">
	<table>
	<thead>
	<tr>
	<th>品名</th>
	<th style="width: 8em;">単価</th>
	<th style="width: 6em;">数量</th>
	<th style="width: 6em;">単位</th>
	<th style="width: 7em;">金額</th>
	<th class="estimate-input-table-th-trash">削除</th>
	</tr>
	</thead>
	<tbody id="estimate-detail" sum="<?php echo $aE['ePrice']; ?>">

	<?php foreach ($dname as $i => $v): ?>

	<tr class="detail">
		<td><span class="sp-display mb12">品名</span><input type="text" name="dname[]" size="40" maxlength="20" value="<?php echo $v; ?>" class="input-name"></td>
		<td><span class="sp-display mb12">単価</span><input type="text" name="dprice[]" size="15" maxlength="6" value="<?php echo $dprice[$i]; ?>" class="text-right input-price"></td>
		<td colspan="2">
			<div class="estimate-input-table-div-amount">
				<span class="sp-display mb12">数量</span><input type="text" name="dnum[]" size="2" maxlength="2" value="<?php echo $dnum[$i]; ?>" class="text-right input-num">
			</div>
			<div class="estimate-input-table-div-unit">
				<span class="sp-display mb12">単位</span><input type="text" name="dunit[]" size="2" maxlength="2" value="<?php echo $dunit[$i]; ?>" class="input-unit">
			</div>
		</td>
		<td><span class="sp-display mb12">金額</span><span class="text-right amount" style="display: block;"><?php echo '&yen;'.number_format($dprice[$i]*$dnum[$i]); ?></span></td>
		<td class="pc-center-sp-left estimate-input-table-td-trash">
		<?php if ($i > 0): ?>
			<span class="sp-display"><a href="" class="detail-remove">削除</a></span><span class="pc-display"><a href="" class="detail-remove"><i class="fa fa-trash-o"></i></a></span>
		<?php endif; ?>
		</td>
	</tr>

	<?php endforeach; ?>

	<tr class="estimate-input-table-tr-add-item">
	<td><a href="#" class="button add detail-add"><i class="fa fa-plus-circle"></i>項目を追加する</a></td>
	<td colspan="3" class="text-right"><p class="estimate-calc-result">内訳合計と小計との差</p></td>
	<td class="text-right"><span class="amount-sum">&yen;0</span></td>
	<td></td>
	</tr>
	<tr>
	<td colspan="4" class="text-right"><p class="estimate-calc-result">小計</p></td>
	<td class="text-right"><span id="sum-price">&yen;<?php echo number_format($aE['ePrice'])?></span></td>
	<td></td>
	</tr>
	<tr>
	<td colspan="4" class="text-right"><p class="estimate-calc-result">消費税（<?php echo CL_TAX_RATE*100; ?>%）</p></td>
	<td class="text-right"><span>&yen;<?php echo number_format($aE['ePrice']*$aE['eTax'])?></span></td>
	<td></td>
	</tr>
	<tr>
	<td colspan="4" class="text-right"><p class="estimate-calc-result">合計</p></td>
	<td class="text-right"><span>&yen;<?php echo number_format($aE['ePrice']*(1+$aE['eTax'])); ?></span></td>
	<td></td>
	</tr>
	</tbody>
	</table>
	</div>
	<p class="button-box mt32"><button type="submit" name="mode" value="input" id="estimate-submit" class="button do register">確認する</button></p>
	<?php echo Form::close(); ?>
</div>

<table style="display: none;">
	<tr id="detail-base" class="detail" style="display: none;">
		<td><span class="sp-display mb12">品名</span><input type="text" name="dname[]" size="40" maxlength="40" value="<?php echo CL_SITENAME; ?> 利用料金" class="input-name"></td>
		<td><span class="sp-display mb12">単価</span><input type="text" name="dprice[]" size="15" maxlength="15" value="0" class="text-right input-price"></td>
		<td colspan="2">
			<div class="estimate-input-table-div-amount">
				<span class="sp-display mb12">数量</span><input type="text" name="dnum[]" size="2" maxlength="2" value="1" class="text-right input-num">
			</div>
			<div class="estimate-input-table-div-unit">
				<span class="sp-display mb12">単位</span><input type="text" name="dunit[]" size="2" maxlength="2" value="式" class="input-unit">
			</div>
		</td>
		<td><span class="sp-display mb12">金額</span><span class="text-right amount" style="display: block;">&yen;0</span></td>
		<td class="pc-center-sp-left estimate-input-table-td-trash"><span class="sp-display"><a href="" class="detail-remove">削除</a></span><span class="pc-display"><a href="" class="detail-remove"><i class="fa fa-trash-o"></i></a></span></td>
	</tr>
</table>

