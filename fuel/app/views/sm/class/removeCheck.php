<?php echo Form::open(array('action'=>'/s/class/remove/'.$class['ctID'].Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>
<div><?php echo __('以下の履修を解除します。'); ?></div>
<ul style="margin-top: 5px;">
	<li><?php echo __('講義名'); ?>：<span style="color: #0000CC;"><?php echo $class['ctName']; ?></span></li>
	<li><?php echo __('講義コード'); ?>：<span style="color: #0000CC;"><?php echo \Clfunc_Common::getCode($class['ctCode']); ?></span></li>

<?php if (!CL_CAREERTASU_MODE): ?>
	<li><?php echo __('年度'); ?>：<span style="color: #00CC00;"><?php echo __(':year年度',array('year'=>$class['ctYear'])); ?></span></li>
	<li><?php echo __('期'); ?>：<span style="color: #00CC00;"><?php echo ($class['dpNO'] > 0)? $aPeriod[$class['dpNO']]:'─'; ?></span></li>
	<li><?php echo __('曜日'); ?>：<span style="color: #00CC00;"><?php echo ($class['ctWeekDay'] > 0)? $aWeekDay[$class['ctWeekDay']]:'─'; ?></span></li>
	<li><?php echo __('時限'); ?>：<span style="color: #00CC00;"><?php echo ($class['dhNO'] > 0)? $aHour[$class['dhNO']]:'─'; ?></span></li>
<?php endif; ?>
</ul>

<?php echo Clfunc_Mobile::hr(); ?>

<div style="text-align: center; margin-top: 5px;" align="center"><input type="submit" name="delete" value="<?php echo __('解除する'); ?>"></div>
<div style="text-align: center; margin-top: 5px;" align="center"><input type="submit" name="cancel" value="<?php echo __('キャンセル'); ?>"></div>
<?php echo Form::close(); ?>
