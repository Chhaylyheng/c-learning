<?php
	$errMsg = array(
		'ag_text'  =>'',
	);

	if (!is_null($error)):
		foreach ($errMsg as $key => $val):
			if (isset($error[$key])):
				$errMsg[$key] = '<div style="color:#CC0000;">'.$error[$key].'</div>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '更新';
	$sAction = 'goal/'.$aALTheme['altID'];
?>

<form action="/s/alog/<?php echo $sAction; ?>" method="post" style="font-size: 80%;">
	<div style="text-align: right;"><?php echo __(':astは必須項目',array('ast'=>'※')); ?></div>

	<div style="margin-top: 8px;">
		<label style="color: #008800;"><?php echo $aALTheme['altGoalLabel']; ?></label>
		<div>
			<div style="color: gray;"><?php echo $aALTheme['altGoalDescription']; ?></div>
			<textarea name="ag_text" rows="4" style="width: 100%;"><?php echo $ag_text; ?></textarea>
			<?php echo $errMsg['ag_text']; ?>
		</div>
	</div>
	<div style="margin-top: 8px; text-align: center;">
		<input type="submit" value="<?php echo $sSubBtn; ?>" name="sub_state">
	</div>
</form>
