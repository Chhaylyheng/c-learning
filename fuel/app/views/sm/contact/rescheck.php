<?php echo Form::open(array('action'=>'/s/contact/rescheck/'.$sCheck.DS.$iNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-bottom: 8px;">
<?php echo __('以下の内容で送信します。'); ?>
</div>

<div style="margin-top: 8px;">
	<label><?php echo __('件名'); ?><br>
		<span style="color: blue;"><?php echo ($c_subject)? $c_subject:'(No subject)'; ?></span>
	</label>
</div>
<div style="margin-top: 8px;">
	<label><?php echo __('本文'); ?><br>
		<div style="color: blue;"><?php echo nl2br(\Clfunc_Common::url2link($c_text, 0)); ?></div>
	</label>
</div>

<div style="text-align: center; margin-top: 8px;">
	<input type="submit" value="<?php echo __('送信'); ?>" name="sub_state">
	<input type="submit" name="back" value="<?php echo __('戻る'); ?>">
</div>

<?php echo Form::close(); ?>