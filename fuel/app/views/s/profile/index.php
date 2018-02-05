<?php
	$errClass = array(
		's_name'=>'',
		's_no'=>'',
		's_sex'=>'',
		's_school'=>'',
		's_dept'=>'',
		's_subject'=>'',
		's_year'=>'',
		's_class'=>'',
		's_course'=>'',
		's_mail'=>'',
		's_mail_chk'=>'',
		's_submail'=>'',
		's_pass_now'=>'',
		's_pass_edit'=>'',
		's_pass_chk'=>'');
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
	<?php echo Form::open(array('action'=>'/s/profile')) ; ?>
	<p><?php echo __('ログインID'); ?></p>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $aStudent['stLogin']; ?></p>

<?php $btn = false; ?>

	<p class="mt12"><?php echo __('氏名'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_NAME)): ?>
	<p class="mt4"><input type="text" name="s_name" value="<?php echo $s_name; ?>" maxlength="50"<?php echo $errClass['s_name']; ?>></p>
	<?php echo $errMsg['s_name']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $s_name; ?></p>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
	<p class="mt12"><?php echo __('学籍番号'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_NO)): ?>
	<p class="mt4"><input type="text" name="s_no" value="<?php echo $s_no; ?>" maxlength="20"<?php echo $errClass['s_no']; ?>></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('※半角英数字と一部記号（.-_）が利用可能です。'); ?></p>
	<?php echo $errMsg['s_no']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $s_no; ?></p>
<?php endif; ?>
<?php endif; ?>

	<p class="mt12"><?php echo __('性別'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_SEX)): ?>
	<p class="mt4 inline-box select-box">
		<select name="s_sex" style="background-image: none;" class="width-auto">
<?php foreach ($aSex as $i => $v): ?>
	<?php if ($i == 0 && (!is_null($aGroup) && ($aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_SEX))): ?>
		<?php continue; ?>
	<?php endif; ?>
<?php $sSel = ($i == $s_sex)? ' selected':''; ?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $v; ?></option>
<?php endforeach;?>
		</select>
	</p>
	<?php echo $errMsg['s_sex']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $aSex[$s_sex]; ?></p>
<?php endif; ?>

<?php if (CL_CAREERTASU_MODE && ($aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_SCHOOL)): ?>
	<p class="mt12"><?php echo __('学校名'); ?></p>
	<p class="mt4"><input type="text" class="text-left<?php echo $errClass['s_school']; ?>" placeholder="<?php echo __('所属学校名を入力します'); ?>" maxlength="50" value="<?php echo $s_school; ?>" name="s_school" id="form_s_school"></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('学校名の一部分を入力すると候補が表示されますので、候補のリストより選択してください。'); ?><?php echo __('候補にない場合は、新規に登録されます。'); ?></p>
	<?php echo $errMsg['s_school']; ?>
<?php endif; ?>

	<p class="mt12"><?php echo __('学部'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_DEPT)): ?>
	<p class="mt4"><input type="text" name="s_dept" value="<?php echo $s_dept; ?>" maxlength="50"<?php echo $errClass['s_dept']; ?>></p>
	<?php echo $errMsg['s_dept']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $s_dept; ?></p>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE): ?>

<p class="mt12"><?php echo __('学科'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_SUBJECT)): ?>
	<p class="mt4"><input type="text" name="s_subject" value="<?php echo $s_subject; ?>" maxlength="50"<?php echo $errClass['s_subject']; ?>></p>
	<?php echo $errMsg['s_subject']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $s_subject; ?></p>
<?php endif; ?>

	<p class="mt12"><?php echo __('学年'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_YEAR)): ?>
	<p class="mt4"><input type="text" name="s_year" value="<?php echo $s_year; ?>" maxlength="10"<?php echo $errClass['s_year']; ?>></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('※数値で入力します。'); ?></p>
	<?php echo $errMsg['s_year']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $s_year; ?></p>
<?php endif; ?>

	<p class="mt12"><?php echo __('クラス'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_CLASS)): ?>
	<p class="mt4"><input type="text" name="s_class" value="<?php echo $s_class; ?>" maxlength="50"<?php echo $errClass['s_class']; ?>></p>
	<?php echo $errMsg['s_class']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $s_class; ?></p>
<?php endif; ?>

	<p class="mt12"><?php echo __('コース'); ?></p>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_COURSE)): ?>
	<p class="mt4"><input type="text" name="s_course" value="<?php echo $s_course; ?>" maxlength="50"<?php echo $errClass['s_course']; ?>></p>
	<?php echo $errMsg['s_course']; ?>
	<?php $btn = true; ?>
<?php else: ?>
	<p class="mt4 ml16 font-green font-size-120"><?php echo $s_course; ?></p>
<?php endif; ?>

<?php endif; ?>

	<p class="mt12">Timezone</p>
	<?php $region = explode('/',$s_timezone,2); ?>
	<div class="mt4">
		<select class="dropdown" id="tz-region">
		<?php foreach ($tz_region as $r): ?>
			<?php $sSel = ($r == $region[0])? ' selected':'';?>
			<option value="<?php echo $r; ?>"<?php echo $sSel; ?>><?php echo $r; ?></option>
		<?php endforeach; ?>
		</select>
		<select class="dropdown" id="tz-timezone" name="s_timezone">
		<?php foreach ($tz_list as $r => $tzl): ?>
			<?php $sDisp = ($r == $region[0])? 'block':'none'; ?>
			<optgroup label="<?php echo $r; ?>" style="display: <?php echo $sDisp; ?>;">
			<?php foreach ($tzl as $t => $v): ?>
				<?php $sSel = ($t == $s_timezone)? ' selected':'';?>
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
<div class="accordion-content-inner">
	<?php echo Form::open(array('action'=>'/s/profile')) ; ?>
	<p><?php echo __('現在のメールアドレス'); ?></p>
	<p class="mt4 ml16 font-green font-size-140">
<?php if ($aStudent['stMail']): ?>
	<?php echo  $aStudent['stMail']; ?>
	<?php if ($aStudent['stMailAuth']): ?>
		<span class="font-size-70"><i class="fa fa-check-circle"></i><?php echo __('認証済み'); ?></span>
	<?php else: ?>
		<span class="font-size-70 font-red"><i class="fa fa-exclamation-circle"></i><?php echo __('未認証'); ?></span>
		<a href="/s/profile/mailauth" class="button na default width-auto"><?php echo __('メールアドレス認証メールを再送信する'); ?></a>
	<?php endif; ?>
<?php else: ?>
	<?php echo __('登録なし'); ?>
<?php endif; ?>
	</p>
	<p class="mt12"><?php echo __('新しいメールアドレス'); ?></p>
	<p class="mt4"><input type="text" name="s_mail" value="<?php echo (isset($s_mail))? $s_mail:''; ?>" maxlength="200"<?php echo $errClass['s_mail']; ?>></p>
	<?php echo $errMsg['s_mail']; ?>
	<p class="mt12"><?php echo __('新しいメールアドレス（確認）'); ?></p>
	<p class="mt4"><input type="text" name="s_mail_chk" value="<?php echo (isset($s_mail_chk))? $s_mail_chk:''; ?>" maxlength="200"<?php echo $errClass['s_mail_chk']; ?>></p>
	<?php echo $errMsg['s_mail_chk']; ?>

	<hr class="mt12">

	<p class="mt12"><?php echo __('サブメールアドレス'); ?></p>
	<p class="mt4"><input type="text" name="s_submail" value="<?php echo (isset($s_submail))? $s_submail:''; ?>" maxlength="200"<?php echo $errClass['s_submail']; ?>></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('※サブメールアドレスを指定することで、メール通知等を別のアドレスにも受け取ることができます。'); ?></p>
	<?php echo $errMsg['s_submail']; ?>


	<p class="button-box mt16"><button type="submit" name="mode" value="mail" class="button do na"><?php echo __('変更する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
</div>
</section>

<?php if (!$aGroup['gtLDAP']): ?>
<section class="">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('パスワード'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<?php echo Form::open(array('action'=>'/s/profile')) ; ?>
	<p><?php echo __('現在のパスワード'); ?></p>
	<p class="mt4"><input type="password" name="s_pass_now" value="" maxlength="32"<?php echo $errClass['s_pass_now']; ?>></p>
	<?php echo $errMsg['s_pass_now']; ?>
	<p class="mt12"><?php echo __('新しいパスワード'); ?></p>
	<p class="mt4"><input type="password" name="s_pass_edit" value="" maxlength="32"<?php echo $errClass['s_pass_edit']; ?>></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?></p>
	<?php echo $errMsg['s_pass_edit']; ?>
	<p class="mt12"><?php echo __('新しいパスワード（確認）'); ?></p>
	<p class="mt4"><input type="password" name="s_pass_chk" value="" maxlength="32"<?php echo $errClass['s_pass_chk']; ?>></p>
	<?php echo $errMsg['s_pass_chk']; ?>
	<p class="button-box mt16"><button type="submit" name="mode" value="pass" class="button do na"><?php echo __('変更する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
</div>
</section>
<?php endif; ?>


<?php if (CL_CAREERTASU_MODE): ?>
<section class="">
<h2><a class="link-out accordion acc-close" href="#"><?php echo __('学生の解約'); ?></a></h2>
<div class="accordion-content acc-content-close">
<div class="accordion-content-inner">
	<?php echo Form::open(array('action'=>'/s/profile/remove')) ; ?>
	<p class="button-box mt16"><button type="submit" name="check" value="1" class="button default na"><?php echo __('解約する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
</div>
</section>
<?php endif; ?>
