<?php
	$errClass = array('sendto'=>'', 'pubdate'=>'');
	$errMsg = $errClass;

	$aMin = explode('|', date('Y|n|j',strtotime($aE['bDate'])));
	if ($aP['product'] != 'contract'):
		$aMax = explode('|', date('Y|n|t', strtotime($aE['bDate'])));
	else:
		$aMax = explode('|', date('Y|n|t', strtotime($aCon['coStartDate'])));
	endif;

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
<p class="error-box"><?php echo $error['bill_error']; ?></p>
<?php endif; ?>

<div class="info-box estimate-input">
	<?php echo Form::open(array('action'=>'/t/payment/billpublish/'.$aE['eNO'],'method'=>'post')) ; ?>
	<h2>請求日</h2>
	<p><input type="text" name="pubdate" size="30" maxlength="11" value="<?php echo $pubdate; ?>" readonly id="bill-datepick"<?php echo $errClass['pubdate']; ?>></p>
	<?php echo $errMsg['pubdate']; ?>

	<h2>宛名：</h2>
	<p><input type="text" name="sendto" size="30" maxlength="25" value="<?php echo $sendto; ?>"<?php echo $errClass['sendto']; ?>></p>
	<?php echo $errMsg['sendto']; ?>

	<p>※請求書は、あと <span class="font-red font-size-140"><?php echo $aE['bNum']; ?></span> 回発行できます。</p>
	<p class="button-box mt32"><button type="submit" class="button register do" name="sub_state" value="1">発行する</button></p>
	<?php echo Form::close(); ?>
</div>

<script>
$(window).load(function() {
	$('#bill-datepick').datepicker({
		autoclose: true,
		todayHighlight: true,
		language: 'ja',
		dateFormat: ((GetCookie('CL_LANG') == 'ja' || GetCookie('CL_LANG') == 'ct')? 'yy年m月d日':'d MM, yy'),
		defaultDate: null,
		minDate: new Date(<?php echo $aMin[0].','.($aMin[1] - 1).','.$aMin[2]; ?>),
		maxDate: new Date(<?php echo $aMax[0].','.($aMax[1] - 1).','.$aMax[2]; ?>),
		numberOfMonths: 2,
		beforeShow: function(input, inst) {
			inst.dpDiv.css({marginTop: -48 + 'px'});
		},
	});
});
</script>