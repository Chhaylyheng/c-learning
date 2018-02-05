<!-- hs[<?php echo Cookie::get('CL_TL_HASH'); ?>] -->
<?php
	$errClass = array(
		't_name'=>'',
		't_school'=>'',
		't_dept'=>'',
		't_subject'=>'',
		't_mail'=>'',
		't_mail_chk'=>'',
		't_submail'=>'',
		't_pass_now'=>'',
		't_pass_edit'=>'',
		't_pass_chk'=>''
	);
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
	$sPt = 'pt0';
?>

<?php if (isset($error['profile_error'])): ?>
<p class="error-box"><?php echo $error['profile_error']; ?></p>
<?php $sPt = ''; ?>
<?php endif; ?>

<section class="<?php echo $sPt; ?>">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('プロフィール'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner mb4">
	<?php echo Form::open(array('action'=>'/t/profile')) ; ?>
	<div class="profile-icon">
		<p><?php echo ($aTeacher['ttImage'])? '<img src="/upload/profile/t/'.$aTeacher['ttImage'].'?'.mt_rand().'" style="max-width: 100px;">':Asset::img('img_no_icon.png',array('style'=>'max-width: 100px;')); ?></p>

<?php if (is_null($aGroup) || !($aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_PHOTO)): ?>
		<p><a href="/t/profile/photo" class="button edit na"><i class="fa fa-edit"></i><?php echo __('写真を変更する'); ?></a></p>
<?php endif; ?>

	</div>

<?php $btn = false; ?>

	<p class="mt12"><?php echo __('氏名'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_NAME)): ?>
	<p class="mt4"><input type="text" name="t_name" value="<?php echo $t_name; ?>" size="30" maxlength="50"<?php echo $errClass['t_name']; ?>></p>
	<?php echo $errMsg['t_name']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="ml16 mt4 font-green font-size-120"><?php echo $aTeacher['ttName']; ?></p>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
<?php if (is_null($aGroup)): ?>
	<p class="mt12"><?php echo __('所属学校名'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_SCHOOL)): ?>
	<div class="mt4">
		<input type="text" name="t_school" value="<?php echo $t_school; ?>" size="30" maxlength="50" id="form_t_school"<?php echo $errClass['t_school']; ?>>
		<p class="mt4 font-gray font-size-90"><?php echo __('学校名の一部分を入力すると候補が表示されますので、候補のリストより選択してください。'); ?><?php echo __('候補にない場合は、新規に登録されます。'); ?></p>
	</div>
	<?php echo $errMsg['t_school']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="ml16 mt4 font-green font-size-120"><?php echo $aTeacher['cmName']; ?></p>
<?php endif; ?>
<?php endif; ?>

	<p class="mt12"><?php echo __('学部名'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_DEPT)): ?>
	<p class="mt4"><input type="text" name="t_dept" value="<?php echo $t_dept; ?>" size="30" maxlength="50"<?php echo $errClass['t_dept']; ?>></p>
	<?php echo $errMsg['t_dept']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="ml16 mt4 font-green font-size-120"><?php echo $aTeacher['ttDept']; ?></p>
<?php endif; ?>

	<p class="mt12"><?php echo __('学科名'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_SUBJECT)): ?>
	<p class="mt4"><input type="text" name="t_subject" value="<?php echo $t_subject; ?>" size="30" maxlength="50"<?php echo $errClass['t_subject']; ?>></p>
	<?php echo $errMsg['t_subject']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="ml16 mt4 font-green font-size-120"><?php echo $aTeacher['ttSubject']; ?></p>
<?php endif; ?>

<?php endif; ?>

	<p class="mt12">Timezone</p>
	<?php $region = explode('/',$t_timezone,2); ?>
	<div class="mt4">
		<select class="dropdown" id="tz-region">
		<?php foreach ($tz_region as $r): ?>
			<?php $sSel = ($r == $region[0])? ' selected':'';?>
			<option value="<?php echo $r; ?>"<?php echo $sSel; ?>><?php echo $r; ?></option>
		<?php endforeach; ?>
		</select>
		<select class="dropdown" id="tz-timezone" name="t_timezone">
		<?php foreach ($tz_list as $r => $tzl): ?>
			<?php $sDisp = ($r == $region[0])? 'block':'none'; ?>
			<optgroup label="<?php echo $r; ?>" style="display: <?php echo $sDisp; ?>;">
			<?php foreach ($tzl as $t => $v): ?>
				<?php $sSel = ($t == $t_timezone)? ' selected':'';?>
				<option value="<?php echo $t; ?>"<?php echo $sSel; ?> class="text-left"><?php echo $v; ?></option>
			<?php endforeach; ?>
			</optgroup>
			<?php endforeach; ?>
		</select>
	</div>
<?php $btn = true; ?>

<?php if ($btn): ?>
	<p class="button-box mt16"><button type="submit" name="mode" value="profile" class="button do na"><?php echo __('変更する'); ?></button></p>
<?php endif; ?>
	<?php echo Form::close(); ?>
</div>
</div>
</section>

<section class="">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('メールアドレス'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner mb4">
	<?php echo Form::open(array('action'=>'/t/profile')) ; ?>


	<p><?php echo __('現在のメールアドレス'); ?></p>
	<p class="mt4 ml16 font-green font-size-140">
		<?php echo $aTeacher['ttMail']; ?>
<?php if ($aTeacher['ttMailAuth']): ?>
	<span class="font-size-70"><i class="fa fa-check-circle"></i><?php echo __('認証済み'); ?></span>
<?php else: ?>
	<span class="font-size-70 font-red"><i class="fa fa-exclamation-circle"></i><?php echo __('未認証'); ?></span>
	<a href="/t/profile/mailauth" class="button na default width-auto"><?php echo __('メールアドレス認証メールを再送信する'); ?></a>
<?php endif; ?>
	</p>

<?php if (is_null($aGroup) || !($aGroup['gtTeacherProfFlag'] & \Clfunc_Flag::T_PROF_MAIL)): ?>
	<p class="mt12"><?php echo __('新しいメールアドレス'); ?></p>
	<p class="mt4"><input type="text" name="t_mail" value="<?php echo (isset($t_mail))? $t_mail:''; ?>" size="30" maxlength="200"<?php echo $errClass['t_mail']; ?>></p>
	<?php echo $errMsg['t_mail']; ?>
	<p class="mt12"><?php echo __('新しいメールアドレス（確認）'); ?></p>
	<p class="mt4"><input type="text" name="t_mail_chk" value="<?php echo (isset($t_mail_chk))? $t_mail_chk:''; ?>" size="30" maxlength="200"<?php echo $errClass['t_mail_chk']; ?>></p>
	<?php echo $errMsg['t_mail_chk']; ?>
<?php endif; ?>

	<hr class="mt12">

	<p class="mt12"><?php echo __('サブメールアドレス'); ?></p>
	<p class="mt4"><input type="text" name="t_submail" value="<?php echo (isset($t_submail))? $t_submail:''; ?>" size="30" maxlength="200"<?php echo $errClass['t_submail']; ?>></p>
	<?php echo $errMsg['t_submail']; ?>

	<p class="button-box mt16"><button type="submit" name="mode" value="mail" class="button do na"><?php echo __('変更する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
</div>
</section>

<?php if (!$aGroup['gtLDAP']): ?>
<section class="">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('パスワード'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner mb4">
	<?php echo Form::open(array('action'=>'/t/profile')) ; ?>
	<p><?php echo __('現在のパスワード'); ?></p>
	<p class="mt4"><input type="password" name="t_pass_now" value="" size="30" maxlength="32"<?php echo $errClass['t_pass_now']; ?>></p>
	<?php echo $errMsg['t_pass_now']; ?>
	<p class="mt12"><?php echo __('新しいパスワード'); ?></p>
	<p class="mt4"><input type="password" name="t_pass_edit" value="" size="30" maxlength="32"<?php echo $errClass['t_pass_edit']; ?>></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?></p>
	<?php echo $errMsg['t_pass_edit']; ?>
	<p class="mt12"><?php echo __('新しいパスワード（確認）'); ?></p>
	<p class="mt4"><input type="password" name="t_pass_chk" value="" size="30" maxlength="32"<?php echo $errClass['t_pass_chk']; ?>></p>
	<?php echo $errMsg['t_pass_chk']; ?>
	<p class="button-box mt16"><button type="submit" name="mode" value="pass" class="button do na"><?php echo __('変更する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
</div>
</section>

<section class="">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('外部サービスアカウント連携'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner mb4">
	<?php if ($aTeacher['ttFacebookID']): ?>
		<p class="mt12"><?php echo __(':providerアカウントと連携しています。',array('provider'=>'Facebook')); ?> <a href="/t/profile/socialout/facebook"><?php echo __(':providerとの連携を解除する',array('provider'=>'Facebook')); ?></a></p>
	<?php else: ?>
		<p class="button-box mt12 pc-left-sp-center"><a href="/auth/login/facebook/TCONECT" class="button do facebook"><i class="fa fa fa-facebook-official"></i><?php echo __(':providerと連携する',array('provider'=>'Facebook')); ?></a></p>
	<?php endif; ?>
	<?php if ($aTeacher['ttGoogleID']): ?>
		<p class="mt12"><?php echo __(':providerアカウントと連携しています。',array('provider'=>'Google')); ?> <a href="/t/profile/socialout/google"><?php echo __(':providerとの連携を解除する',array('provider'=>'Google')); ?></a></p>
	<?php else: ?>
		<p class="button-box mt12 pc-left-sp-center"><a href="/auth/login/google/TCONECT" class="button do google"><i class="fa fa-google"></i><?php echo __(':providerと連携する',array('provider'=>'Google')); ?></a></p>
	<?php endif; ?>
</div>
</div>
</section>
<?php endif; ?>