<?php echo Form::open(array('action'=>'/s/profile/remove'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>
<div><?php echo __('学生の解約を実行します。宜しいですか？'); ?></div>

<div style="color: #cc0000;">
	<?php echo Clfunc_Mobile::emj('WARN').__('解約を実行すると、履修情報、学生情報が全て削除されます。'); ?><br>
</div>

<?php echo Clfunc_Mobile::hr(); ?>

<div style="text-align: center; margin-top: 5px;" align="center"><input type="submit" name="remove" value="<?php echo __('実行する'); ?>"></div>
<div style="text-align: center; margin-top: 5px;" align="center"><input type="submit" name="cancel" value="<?php echo __('キャンセル'); ?>"></div>
<?php echo Form::close(); ?>
