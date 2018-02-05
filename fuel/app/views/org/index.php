<div class="mt0">
	<div class="info-box">
		<p><a href="/org/teacher" class="link-out"><?php echo __('先生一覧'); ?></a></p>
		<p><a href="/org/class"   class="link-out"><?php echo __('講義一覧'); ?></a></p>
		<p><a href="/org/student" class="link-out"><?php echo __('学生一覧'); ?></a></p>
		<p><a href="/org/study"   class="link-out"><?php echo __('学生履修一覧'); ?></a></p>
		<hr>
		<p><a href="/org/flag" class="link-out"><?php echo __('先生と学生の利用設定'); ?></a></p>
	</div>
</div>

<?php if ($aGroup['gtLDAP']): ?>
<?php $sURL = CL_PROTOCOL.'://'.CL_DOMAIN; ?>
<?php $sMod = '/'.$aGroup['gtPrefix']; ?>

<div class="mt8">
	<div class="info-box">
		<h2 class="font-size-120"><i class="fa fa-info-circle font-blue"></i> <?php echo __('LDAP認証利用中'); ?></h2>
		<hr>
		<p><?php echo __('LDAP認証利用中は先生及び学生のログインURLが専用になります。以下のログインURLをご利用ください。'); ?></p>
		<p class="font-size-120"><?php echo __('先生'); ?>：<a href="<?php echo $sURL.$sMod.'/t'; ?>" target="_blank"><?php echo $sURL.$sMod.'/t'; ?></a></p>
		<p class="font-size-120"><?php echo __('学生'); ?>：<a href="<?php echo $sURL.$sMod.'/s'; ?>" target="_blank"><?php echo $sURL.$sMod.'/s'; ?></a></p>
	</div>
</div>

<?php endif; ?>

<div class="mt8">
	<div class="info-box">
		<p><a href="/org/annual" class="link-out"><?php echo __('年次更新'); ?></a></p>
	</div>
</div>
