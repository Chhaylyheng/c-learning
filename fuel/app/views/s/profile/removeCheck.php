<div class="info-box text-center">
	<p><?php echo __('学生の解約を実行します。宜しいですか？'); ?></p>

	<?php echo Form::open(array('action'=>'/s/profile/remove','method'=>'post')) ; ?>

	<p class="mt12 font-red"><i class="fa fa-exclamation-triangle fa-fw"></i><?php echo __('解約を実行すると、履修情報、学生情報が全て削除されます。'); ?></p>

	<p class="button-box mt12"><?php echo Form::button('remove',__('実行する'),array('class'=>'button na do width-auto')); ?></p>
	<p class="button-box mt8"><?php echo Form::button('cancel',__('キャンセル'),array('class'=>'button na default width-auto')); ?></p>
	<?php echo Form::close(); ?>
</div>