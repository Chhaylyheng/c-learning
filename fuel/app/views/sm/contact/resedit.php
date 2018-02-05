<?php $sAction = 'rescreate'; ?>
<?php $sNO = ($iNO > 0)? DS.$iNO:''; ?>

<?php echo Form::open(array('action'=>'/s/contact/'.$sAction.DS.$sNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<?php
	$errClass = array('c_subject'=>'','c_text'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $k => $v):
		if (isset($error[$k])):
			$errMsg[$k] = '<div style="color:#CC0000;">'.$error[$k].'</div>';
		endif;
	endforeach;
?>

<div style="margin-top: 8px;">
	<label><?php echo __('件名'); ?><br>
		<input type="text" name="c_subject" value="<?php echo $c_subject; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" style="width: 100%;">
	</label>
</div>
<?php echo $errMsg['c_subject']; ?>

<div style="margin-top: 8px;">
	<label><?php echo __('本文'); ?><sup style="color: #cc0000; font-size: 80%;">*</sup><br>
		<textarea name="c_text" rows="6" style="width: 100%;"><?php echo $c_text; ?></textarea>
	</label>
</div>
<?php echo $errMsg['c_text']; ?>

<div style="text-align: center; margin-top: 8px;"><input type="submit" value="<?php echo __('確認'); ?>" name="sub_state"></div>

<?php echo Form::close(); ?>