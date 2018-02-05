<?php echo Form::open(array('action'=>'/s/contact/resdelete/'.$iNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-bottom: 8px;">
<?php echo __('以下の記事を削除してよろしいですか？'); ?>
<?php if (!$aContact['parent']): ?>
<br><?php echo __('返信がある場合は返信も削除されます。'); ?>
<?php endif; ?>
</div>

<div style="margin-top: 8px;">
	<label><?php echo __('件名'); ?><br>
		<span style="color: blue;"><?php echo ($aContact['coSubject'])? $aContact['coSubject']:'(No subject)'; ?></span>
	</label>
</div>
<div style="margin-top: 8px;">
	<label><?php echo __('本文'); ?><br>
		<div style="color: blue;"><?php echo nl2br(\Clfunc_Common::url2link($aContact['coBody'], 0)); ?></div>
	</label>
</div>

<div style="text-align: center; margin-top: 8px;">
	<input type="submit" value="<?php echo __('削除'); ?>" name="sub_state">
	<input type="submit" name="back" value="<?php echo __('キャンセル'); ?>">
</div>

<?php echo Form::close(); ?>