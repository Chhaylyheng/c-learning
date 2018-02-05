<?php
$bEdit = false;
$sAction = 'estimatecreate';
$sNumber = $number;
$sHidden = null;
if (isset($aE)):
	$bEdit = true;
	$sAction = 'estimateedit/'.$number;
	$sNumber .= '-T';
else:
	$sHidden = '<input type="hidden" name="point" value="'.$aPoint['pt'].'">';
	$sHidden .= '<input type="hidden" name="purchase" value="'.$aPoint['purchase'].'">';
endif;
?>
<div class="info-box estimate-input">

	<form action="/t/payment/<?php echo $sAction; ?>" method="post" id="estimate-check">

	<embed src="/t/payment/pdfview/e/<?php echo $sNumber; ?>" width="100%" height="500px" type="application/pdf"></embed>

	<p>見積内容を確認の上、手続きを選択してください。</p>
	<?php echo $sHidden; ?>
	<input type="hidden" name="ses_hash" value="<?php echo $ses_hash; ?>">
	<input type="hidden" name="sfid" value="<?php echo Session::key(); ?>">
	<input type="hidden" name="mode" value="">
	<p class="button-box mt32">
		<button type="submit" value="save" class="button confirm register">見積を保存する</button>
		<button type="submit" value="check" class="button do register purchaseBtn" billing="<?php echo $billing; ?>">購入する</button>
	</p>
	<p class="button-box mt32">
		<button type="submit" value="back" class="button cancel register">内容を変更する</button>
	</p>

	</form>

</div>
