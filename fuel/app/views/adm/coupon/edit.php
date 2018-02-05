<?php
	$errClass = array(
		'cpCode'=>'',
		'cpName'=>'',
		'cpDiscount'=>'',
		'cpTermDate'=>'',
		'cpPaymentType'=>'',
		'cpRange'=>'',
	);
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' input-error';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '登録';
	$sAction = 'create';
	$bPrefix = true;
	if (isset($aCoupon)):
		$sSubBtn = '更新';
		$sAction = 'edit/'.$aCoupon['no'];
	endif;
?>


<div class="info-box">
<form action="/adm/coupon/<?php echo $sAction; ?>" method="post">
	<p class="mt0 text-right"><sup>*</sup>は必須項目</p>
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel">クーポンコード<sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="cpCode" id="coupon-code" value="<?php echo $cpCode; ?>" maxlength="10" class="width-12em text-center font-bold <?php echo $errClass['cpCode']; ?>" style="font-size: 150%; line-height: 1.6; padding: 4px 16px; letter-spacing: 3px;">
				<p class="mt4 font-gray">※半角大文字アルファベットと数字で10文字ちょうどで入力してください。</p>
				<?php echo $errMsg['cpCode']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">クーポン名称<sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="cpName" value="<?php echo $cpName; ?>" maxlength="30" class="width-24em text-left<?php echo $errClass['cpName']; ?>">
				<?php echo $errMsg['cpName']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">割引率<sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="cpDiscount" value="<?php echo $cpDiscount; ?>" maxlength="3" class="text-right<?php echo $errClass['cpDiscount']; ?>" style="width: 4em; padding: 8px;"> %割引
				<p class="mt4 font-gray">※1% ～ 100%の範囲で指定可能です。</p>

				<?php foreach ($aPlan as $aP): ?>

				<p class="discountList mt4 font-size-140" style="line-height: 1.3; " data="<?php echo $aP['ptPriceCL']; ?>"><?php echo $aP['ptName']; ?>：<?php echo number_format($aP['ptPriceCL']); ?>円 <i class="fa fa-arrow-circle-right mr0"></i> <span class="font-red font-bold"> <?php echo number_format(round($aP['ptPriceCL'] * (1 - ($cpDiscount / 100)),0)); ?>円</span></p>

				<?php endforeach; ?>

				<?php echo $errMsg['cpDiscount']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">支払条件<sup>*</sup></div>
			<div class="formContent inline-box">
<?php
	foreach ($aPaymentType as $i => $v):
		$sCheck = (array_search($i, $cpPaymentType) !== false)? ' checked':'';
?>
<label class="text-left"><input type="checkbox" name="cpPaymentType[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>><?php echo $v; ?></label><br>
<?php
	endforeach;
?>
				<?php echo $errMsg['cpPaymentType']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">契約期間条件<sup>*</sup></div>
			<div class="formContent inline-box">
				<select name="cpRange" class="dropdown">
<?php
	for ($i = 1; $i <= 12; $i++):
		$sSel = ($i == $cpRange)? ' selected':'';
?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $i; ?>ヶ月以上</option>
<?php
	endfor;
?>
				</select>
				<?php echo $errMsg['cpRange']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">利用期間<sup>*</sup></div>
			<div class="formContent inline-box">
				<?php
					$chk = '';
					$disp = 'block';
					if (isset($infinityRange) && $infinityRange == 1):
						$chk = ' checked';
						$disp = 'none';
					endif;
				?>
				<label><input type="checkbox" name="infinityRange" value="1"<?php echo $chk?>>無期限</input></label>
				<div class="CouponRangeDate" style="display: <?php echo $disp; ?>;">
					<input type="text" name="cpTermDate" value="<?php echo date('Y/m/d',strtotime($cpTermDate)); ?>" readonly id="range-datepick" maxlength="10" class="width-10em text-center">
				</div>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?></button>
	</div>
</form>
</div>
