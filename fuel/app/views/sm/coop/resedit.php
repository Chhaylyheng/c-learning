<?php $sAction = ($bEdit)? 'resedit':'rescreate'; ?>
<?php $sNO = ($iNO > 0)? DS.$iNO:''; ?>

<?php echo Form::open(array('action'=>'/s/coop/'.$sAction.DS.$aCCategory['ccID'].$sNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<?php
	$errClass = array('c_title'=>'','c_text'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $k => $v):
		if (isset($error[$k])):
			$errMsg[$k] = '<div style="color:#CC0000;">'.$error[$k].'</div>';
		endif;
	endforeach;
?>

<?php if (!$bRes): ?>
<div style="margin-top: 8px;">
	<label><?php echo __('タイトル'); ?><br>
		<input type="text" name="c_title" value="<?php echo $c_title; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" style="width: 100%;">
	</label>
</div>
<?php echo $errMsg['c_title']; ?>
<?php endif; ?>

<?php
	if (isset($aCoop)):
		for ($i = 1; $i <= 3; $i++):
			if ($aCoop['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aCoop['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aCoop['fSize'.$i],1);
?>
<div style="font-size: 80%;"><?php echo \Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $sLink; ?>"><?php echo $aCoop['fName'.$i].'('.$sSize.')'; ?></a></div>
<?php
			endif;
		endfor;
	endif;
?>
<div style="margin-top: 8px;">
	<label><?php echo __('本文'); ?><br>
		<textarea name="c_text" rows="6" style="width: 100%;"><?php echo $c_text; ?></textarea>
	</label>
</div>
<?php echo $errMsg['c_text']; ?>

<div style="text-align: center; margin-top: 8px;"><input type="submit" name="state" value="<?php echo __('確認'); ?>"></div>

<?php echo Form::close(); ?>