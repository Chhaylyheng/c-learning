<?php echo Form::open(array('action'=>'/s/coop/resdelete/'.$aCCategory['ccID'].DS.$iNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-bottom: 8px;">
<?php echo __('以下の記事を削除してよろしいですか？'); ?>
</div>

<?php if ($aCoop['cTitle']): ?>
<div style="margin-top: 8px;">
	<label><?php echo __('タイトル'); ?><br>
		<span style="color: blue;"><?php echo $aCoop['cTitle']; ?></span>
	</label>
</div>
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
		<div style="color: blue;"><?php echo nl2br(\Clfunc_Common::url2link($aCoop['cText'], 0)); ?></div>
	</label>
</div>

<div style="text-align: center; margin-top: 8px;">
	<input type="submit" value="<?php echo __('削除'); ?>" name="sub_state">
	<input type="submit" name="back" value="<?php echo __('キャンセル'); ?>">
</div>

<?php echo Form::close(); ?>