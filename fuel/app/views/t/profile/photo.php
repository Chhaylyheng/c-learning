<?php
	$errClass = array('ttImage'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
?>

<div class="info-box">
	<h2><?php echo __('写真の選択'); ?></h2>
	<hr>
	<?php echo Form::open(array('action'=>'/t/profile/photo','method'=>'post','enctype'=>'multipart/form-data')) ; ?>
	<div class="profile-icon">
		<p><?php echo ($aTeacher['ttImage'])? '<img src="/upload/profile/t/'.$aTeacher['ttImage'].'?'.mt_rand().'" style="max-width: 100px;">':Asset::img('img_no_icon.png',array('style'=>'max-width: 100px;')); ?></p>
		<p>
			<input type="file" name="ttImage"<?php echo $errClass['ttImage']; ?>><br>
			<?php echo __('※:sizeMBまでの画像ファイル（JPG,JPEG,GIF,PNG）が設定できます。',array('size'=>CL_IMGSIZE)); ?><br>
			<?php echo __('※画像は自動で100×100（px）に縮小されます。'); ?>
		</p>
	</div>
	<?php echo $errMsg['ttImage']; ?>
	<p class="button-box mt16"><button type="submit" name="mode" value="photo" class="button do na"><?php echo __('変更する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
