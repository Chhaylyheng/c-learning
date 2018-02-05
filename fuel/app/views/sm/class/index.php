<?php
$sContact = ($iContact)? '<span style="color: #cc0000;">['.$iContact.']</span>':null;
$sMaterial = ($iMaterial)? '<span style="color: #cc0000;">['.$iMaterial.']</span>':null;
$sCoop = ($iCoop)? '<span style="color: #cc0000;">['.$iCoop.']</span>':null;
?>
<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ATTEND): ?>
<?php if (!is_null($aAttend)): ?>
<div style="margin-top: 5px;"><?php echo __('現在、出席受付中です。'); ?>（<?php echo __(':timeまで',array('time'=>date('H:i',strtotime($aAttend['acAEnd'])))); ?>）</div>
<?php if (!$aAttend['already']): ?>
<?php echo Form::open(array('action'=>'/s/attend/request'.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>
<?php if ($aAttend['acKey']): ?>
<div style="">
	<label><?php echo __('確認キー'); ?><br>
		<input type="text" name="keycode" value="" maxlength="4">
	</label>
</div>
<?php endif; ?>
<div style="text-align: center;"><input type="submit" value="<?php echo __('出席する'); ?>" name="sub_state"></div>
<input type="hidden" name="no" value="<?php echo $aAttend["no"]; ?>">
<input type="hidden" name="geoLat" value="">
<input type="hidden" name="geoLon" value="">
<?php echo Form::close(); ?>
<?php else: ?>
<?php
	$aCur = $aAttend['abData'];
	$sColor = ($aCur['amAbsence'])? '#CC0000':($aCur['amTime'])? '#00CC00':'#0000CC';
?>
<div style="color: <?php echo $sColor; ?>;"><?php echo $aCur['AttendTime'].'（'.$aCur['amName'].'）'; ?></div>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>

<div style="text-align: center; margin-top: 5px;" align="center">
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_QUEST): ?>
	<a href="/s/quest<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('アンケート'); ?></a><br>
	<?php endif; ?>

<?php if (!$bQuickTeacher && (!isset($aITSTeacher) || $aITSTeacher['ttCTPlan'] > 0)): ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_TEST): ?>
	<a href="/s/test<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('小テスト'); ?></a><br>
	<?php endif; ?>

	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_DRILL): ?>
	<a href="/s/drill<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('ドリル'); ?></a><br>
	<?php endif; ?>

	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_MATERIAL): ?>
	<a href="/s/material<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('教材倉庫').$sMaterial; ?></a><br>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_COOP): ?>
	<a href="/s/coop<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('協働板').$sCoop; ?></a><br>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_REPORT): ?>
	<a href="/s/report<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('レポート'); ?></a><br>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ALOG): ?>
	<a href="/s/alog<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('活動履歴'); ?></a><br>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_CONTACT): ?>
	<a href="/s/contact<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('連絡・相談').$sContact; ?></a><br>
	<?php endif; ?>
<?php if (!CL_CAREERTASU_MODE): ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ATTEND): ?>
	<a href="/s/attend/history<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('出席履歴'); ?></a><br>
	<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
</div>
