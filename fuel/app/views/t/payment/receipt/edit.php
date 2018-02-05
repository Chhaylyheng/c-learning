<?php
	$errClass = array('sendto'=>'','note'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
	if (isset($error['receipt_error'])):
?>
<p class="error-box"><?php echo $error['receipt_error']; ?></p>
<?php endif; ?>

<div class="info-box estimate-input">
	<?php echo Form::open(array('action'=>'/t/payment/receiptpublish/'.$aE['eNO'],'method'=>'post')) ; ?>
	<h2>宛名：</h2>
	<p><input type="text" name="sendto" size="30" maxlength="25" value="<?php echo $sendto; ?>"<?php echo $errClass['sendto']; ?>></p>
	<?php echo $errMsg['sendto']; ?>
	<h2>但書：</h2>
	<p><input type="text" name="note" size="30" maxlength="25" value="<?php echo $note; ?>"<?php echo $errClass['note']; ?>></p>
	<?php echo $errMsg['note']; ?>
	<p>※領収書は、あと <span class="font-red font-size-140"><?php echo $aE['rNum']; ?></span> 回発行できます。</p>
	<p class="button-box mt32"><button type="submit" class="button register do" name="sub_state" value="1">発行する</button></p>
	<?php echo Form::close(); ?>
</div>

