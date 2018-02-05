<?php echo Form::open(array('action'=>'/s/report/resdelete/'.$aReport['rbID'].DS.$aStu['stID'].DS.$iNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-bottom: 8px;">
<?php echo __('以下のコメントを削除します。よろしいですか？'); ?>
</div>

<div style="margin-top: 8px;">
	<label><?php echo __('コメント'); ?><br>
		<div style="color: blue;"><?php echo nl2br(\Clfunc_Common::url2link($aCom['rcComment'], 0)); ?></div>
	</label>
</div>

<div style="text-align: center; margin-top: 8px;">
	<input type="submit" value="<?php echo __('削除'); ?>" name="sub_state"><br>
	<input type="submit" name="back" value="<?php echo __('キャンセル'); ?>">
</div>

<?php echo Form::close(); ?>