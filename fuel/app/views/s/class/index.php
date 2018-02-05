<?php
	$sContact = ($iContact)? '<span class="attention attn-emp">'.$iContact.'</span>':null;
	$sMaterial = ($iMaterial)? '<span class="attention attn-emp">'.$iMaterial.'</span>':null;
	$sCoop = ($iCoop)? '<span class="attention attn-emp">'.$iCoop.'</span>':null;
?>

<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ATTEND): ?>
<?php if (!is_null($aAttend)): ?>
<div class="info-box">
<h2 class="font-size-120 mb16">
<?php echo __('現在、出席受付中です。'); ?>
<span class="font-red font-size-80">（<?php echo __(':timeまで',array('time'=>ClFunc_Tz::tz('H:i',$tz,$aAttend['acAEnd']))); ?>）</span>
</h2>
<?php if (!$aAttend['already']): ?>
<?php echo Form::open(array('action'=>'/s/attend/request','method'=>'post','id'=>'attendForm')); ?>
<?php if ($aAttend['acKey']): ?>
<p class="adjust-style inline-box"><label><?php echo __('確認キー'); ?>
	<input type="text" name="keycode" value="" class="width-6em text-center ml16">
</label></p>
<?php endif; ?>
<?php
	$sGisMark = null;
	$sGeo = null;
	if ($aAttend['acGIS'] && $iDevice == CL_DEV_SP):
		$sGisMark=' <i class="fa fa-map-marker"></i>';
		$sGeo = 'gis';
	endif;
?>
<p class="adjust-style inline-box ml16"><button type="button" class="geoButton button na do width-8em <?php echo $sGeo; ?>"><?php echo __('出席する'); ?><?php echo $sGisMark; ?></button></p>
<input type="hidden" name="no" value="<?php echo $aAttend["no"]; ?>">
<input type="hidden" name="geoLat" value="">
<input type="hidden" name="geoLon" value="">
<?php echo Form::close(); ?>
<?php else: ?>
<?php
	$aCur = $aAttend['abData'];
	$sColor = ($aCur['amAbsence'])? 'font-red':($aCur['amTime'])? 'font-green':'font-blue';
?>
<p class="font-size-140"><i class="fa fa-check fa-fw"></i> <span class="<?php echo $sColor; ?>"><?php echo $aCur['AttendTime'].'（'.$aCur['amName'].'）'; ?></span></p>
<?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="info-box">
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_QUEST): ?>
	<p><a href="/s/quest" class="link-out"><?php echo __('アンケート'); ?></a></p>
	<?php endif; ?>

<?php if (!$bQuickTeacher && (!isset($aITSTeacher) || $aITSTeacher['ttCTPlan'] > 0)): ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_TEST): ?>
	<p><a href="/s/test" class="link-out"><?php echo __('小テスト'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_DRILL): ?>
	<p><a href="/s/drill" class="link-out"><?php echo __('ドリル'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_MATERIAL): ?>
	<p><a href="/s/material" class="link-out"><?php echo __('教材倉庫').$sMaterial; ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_COOP): ?>
	<p><a href="/s/coop" class="link-out"><?php echo __('協働板').$sCoop; ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_REPORT): ?>
	<p><a href="/s/report" class="link-out"><?php echo __('レポート'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ALOG): ?>
	<p><a href="/s/alog" class="link-out"><?php echo __('活動履歴'); ?></a></p>
	<?php endif; ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_CONTACT): ?>
	<p><a href="/s/contact" class="link-out"><?php echo __('連絡・相談').$sContact; ?></a></p>
	<?php endif; ?>
<?php if (!CL_CAREERTASU_MODE): ?>
	<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_ATTEND): ?>
	<p><a href="/s/attend/history" class="link-out"><?php echo __('出席履歴'); ?></a></p>
	<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
</div>

