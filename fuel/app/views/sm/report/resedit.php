<?php $sAction = ($bEdit)? 'resedit':'rescreate'; ?>
<?php $sSubmit = ($bEdit)? __('更新する'):__('登録する'); ?>
<?php $sNO = ($iNO > 0)? DS.$iNO:''; ?>

<?php echo Form::open(array('action'=>'/s/report/'.$sAction.DS.$aReport['rbID'].DS.$aStu['stID'].$sNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<?php
	$errClass = array('c_text'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $k => $v):
		if (isset($error[$k])):
			$errMsg[$k] = '<div style="color:#CC0000;">'.$error[$k].'</div>';
		endif;
	endforeach;
?>

<div style="margin-top: 8px;">
	<label><?php echo __('コメント'); ?><br>
		<textarea name="c_text" rows="6" style="width: 100%;"><?php echo $c_text; ?></textarea>
	</label>
</div>
<?php echo $errMsg['c_text']; ?>

<div style="text-align: center; margin-top: 8px;"><input type="submit" value="<?php echo $sSubmit; ?>" name="sub_state"></div>

<?php echo Form::close(); ?>