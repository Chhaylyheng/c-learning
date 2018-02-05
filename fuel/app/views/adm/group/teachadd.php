<?php
	$errClass = array('gtlist'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '追加';
	$sAction = 'teachadd/'.$aGroup['gtID'];
?>


<div class="info-box">
<form action="/adm/group/<?php echo $sAction; ?>" method="post">
	<p class="mt0 text-center">先生ID（tt00000000）を改行で区切って入力してください。</p>
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel">先生ID</div>
			<div class="formContent inline-box">
				<textarea name="gtlist" rows="20" <?php echo $errClass['gtlist']; ?>><?php echo $gtlist; ?></textarea>
				<?php echo $errMsg['gtlist']; ?>
			</div>
		</div>
	</div>
	<div class="button-box mt8 mb8">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?></button>
	</div>
</form>
</div>
