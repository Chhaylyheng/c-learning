<?php
	$errClass = array('name'=>'','org'=>'', 'pubdate'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
	if (isset($error['license_error'])):
?>
<p class="error-box"><?php echo $error['license_error']; ?></p>
<?php endif; ?>

<div class="info-box estimate-input">
	<?php echo Form::open(array('action'=>'/t/payment/licensepublish/'.$aE['eNO'],'method'=>'post')) ; ?>
	<h2>発行日</h2>
	<p><input type="text" name="pubdate" size="30" maxlength="11" value="<?php echo $pubdate; ?>" readonly id="license-datepick"<?php echo $errClass['pubdate']; ?>></p>
	<?php echo $errMsg['pubdate']; ?>

	<h2>団体名：</h2>
	<p><input type="text" name="org" size="30" maxlength="15" value="<?php echo $org; ?>"<?php echo $errClass['org']; ?>></p>
	<?php echo $errMsg['org']; ?>

	<h2>契約者名：</h2>
	<p><input type="text" name="name" size="30" maxlength="15" value="<?php echo $name; ?>"<?php echo $errClass['name']; ?>></p>
	<?php echo $errMsg['name']; ?>

	<p>※ライセンス証明書（納品書）は、あと <span class="font-red font-size-140"><?php echo $aE['lNum']; ?></span> 回発行できます。</p>
	<p class="button-box mt32"><button type="submit" class="button register do" name="sub_state" value="1">発行する</button></p>
	<?php echo Form::close(); ?>
</div>

