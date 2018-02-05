<div class="info-box text-center">
	<p><?php echo __('以下の講義を履修します。'); ?></p>

	<?php echo Form::open(array('action'=>'/s/class/entry','method'=>'post')) ; ?>
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
	<?php echo Form::hidden('c_code',$c_code); ?>
	<?php echo Form::hidden('c_check',true); ?>

<?php if (CL_CAREERTASU_MODE): ?>
	<p class="mt8 mb8 font-red text-center">
		<i class="fa fa-exclamation-triangle fa-fw"></i><?php echo __('講義に登録すると、個人の情報が先生に開示されます。'); ?><br>
		<?php echo __('※メールアドレスは開示されません。'); ?>
	</p>
<?php endif; ?>

	<p class="button-box"><?php echo Form::button('c_submit',__('履修する'),array('class'=>'button na do')); ?></p>
	<?php echo Form::close(); ?>
</div>