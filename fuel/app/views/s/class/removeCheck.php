<div class="info-box text-center">
	<p><?php echo __('以下の履修を解除します。'); ?></p>

	<?php echo Form::open(array('action'=>'/s/class/remove/'.$class['ctID'],'method'=>'post')) ; ?>
	<ul class="table-list font-size-120" style="margin: 10px auto!important;">
		<li>
			<p class="th width-50"><?php echo __('講義名'); ?></p>
			<p class="font-green width-50"><?php echo $class['ctName']; ?></p>
		</li>
		<li>
			<p class="th width-50"><?php echo __('講義コード'); ?></p>
			<p class="font-green width-50"><?php echo \Clfunc_Common::getCode($class['ctCode']); ?></p>
		</li>
<?php if (!CL_CAREERTASU_MODE): ?>
		<li>
			<p class="th width-50"><?php echo __('年度'); ?></p>
			<p class="font-green width-50"><?php echo __(':year年度',array('year'=>$class['ctYear'])); ?></p>
		</li>
		<li>
			<p class="th width-50"><?php echo __('期/曜日/時限'); ?></p>
			<p class="font-green width-50"><?php echo ($class['dpNO'] > 0)? $aPeriod[$class['dpNO']]:'─'; ?>/<?php echo ($class['ctWeekDay'] > 0)? $aWeekDay[$class['ctWeekDay']]:'─'; ?>/<?php echo ($class['dhNO'] > 0)? $aHour[$class['dhNO']]:'─'; ?></p>
		</li>
<?php endif; ?>
	</ul>

	<p class="button-box"><?php echo Form::button('delete',__('解除する'),array('class'=>'button na do width-auto')); ?></p>
	<p class="button-box mt8"><?php echo Form::button('cancel',__('キャンセル'),array('class'=>'button na default width-auto')); ?></p>
	<?php echo Form::close(); ?>
</div>