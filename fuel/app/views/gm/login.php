<h1 style="text-align:center;" align="center"><?php echo Asset::img($sLogo.'.gif',array('alt'=>CL_SITENAME)); ?></h1>
<?php echo View::forge('selectlang_m'); ?>

<div style="text-align:center;margin-top: 5px;" align="center"><span style="font-size:medium;"><?php echo __('ゲストログイン'); ?></span></div>

<?php echo Form::open(array('action'=>'/g/login/loginchk','method'=>'post')) ; ?>

<?php if (isset($noCookie)): ?>
	<div style="color:#CC0000; margin-top: 5px;"><?php echo $noCookie; ?></div>
<?php endif; ?>

<?php if (isset($error['login'])): ?>
	<div style="color:#CC0000; margin-top: 5px;"><?php echo $error['login'] ?></div>
<?php endif; ?>

<?php
	$errClass = array('cl_code'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $c => $v):
		if (isset($error[$c])):
			$errMsg[$c] = '<div style="color:#CC0000;">'.$error[$c].'</div>';
		endif;
	endforeach;
?>

<div style="margin-top: 5px;">
	<label><?php echo __('講義コード'); ?><br>
		<input type="text" name="cl_code" value="<?php echo (isset($cl_code))? $cl_code:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['cl_code']; ?>
<div style="">
<?php echo __('※先生から指定された講義コードを入力してください。'); ?>
</div>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('ログイン'); ?>"></div>

<?php echo Form::close(); ?>
