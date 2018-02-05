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
		's_pass_now'=>'',
		's_pass_edit'=>'',
		's_pass_chk'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errMsg[$key] = '<div style="color:#CC0000;">'.$error[$key].'</div>';
			endif;
		endforeach;
	endif;
?>

<?php if (isset($error['profile_error'])): ?>
<div style="color:#CC0000; margin-top: 5px;"><?php echo $error['profile_error']; ?></div>
<?php endif; ?>

<div style="margin-top: 5px; font-size: 80%;">
<div style="font-weight: bold; background-color: #AAAAFF; padding: 4px 0;"><?php echo __('プロフィール'); ?></div>
<?php echo Form::open(array('action'=>'/s/profile'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-top: 10px;">
	<label><?php echo __('ログインID'); ?><br>
		<span style="color: #0000CC">　<?php echo $aStudent['stLogin']; ?></span>
	</label>
</div>

<?php $btn = false; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('氏名'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_NAME)): ?>
		　<input type="text" name="s_name" value="<?php echo (isset($s_name))? $s_name:''; ?>" maxlength="50">
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_name))? $s_name:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_name']; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学籍番号'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_NO)): ?>
		　<input type="text" name="s_no" value="<?php echo (isset($s_no))? $s_no:''; ?>" maxlength="20"><br>
		<div style="font-size: 80%;"><?php echo __('※半角英数字と一部記号（.-_）が利用可能です。'); ?></div>
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_no))? $s_no:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_no']; ?>
<?php endif; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('性別'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_SEX)): ?>
		　<select name="s_sex">
<?php foreach ($aSex as $i => $v): ?>
	<?php if ($i == 0 && (!is_null($aGroup) && ($aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_SEX))): ?>
		<?php continue; ?>
	<?php endif; ?>
<?php $sSel = ($i == $s_sex)? ' selected':''; ?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $v; ?></option>
<?php endforeach;?>
		</select>
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_sex))? $aSex[$s_sex]:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_sex']; ?>

<?php if (CL_CAREERTASU_MODE): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学校'); ?><br>
<?php if ($aGroup['gtStudentGetFlag'] & \Clfunc_Flag::S_GET_SCHOOL): ?>
		　<input type="text" name="s_school" value="<?php echo (isset($s_school))? $s_school:''; ?>" maxlength="50">
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_school))? $s_school:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_school']; ?>
<?php endif; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('学部'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_DEPT)): ?>
		　<input type="text" name="s_dept" value="<?php echo (isset($s_dept))? $s_dept:''; ?>" maxlength="50">
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_dept))? $s_dept:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_dept']; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学科'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_SUBJECT)): ?>
		　<input type="text" name="s_subject" value="<?php echo (isset($s_subject))? $s_subject:''; ?>" maxlength="50">
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_subject))? $s_subject:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_subject']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('学年'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_YEAR)): ?>
		　<input type="text" name="s_year" value="<?php echo (isset($s_year))? $s_year:''; ?>" maxlength="10">
		<div style="font-size: 80%;"><?php echo __('※数値で入力します。'); ?></div>
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_year))? $s_year:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_year']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('クラス'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_CLASS)): ?>
		　<input type="text" name="s_class" value="<?php echo (isset($s_class))? $s_class:''; ?>" maxlength="50">
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_class))? $s_class:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_class']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('コース'); ?><br>
<?php if (is_null($aGroup) || !($aGroup['gtStudentProfFlag'] & \Clfunc_Flag::S_PROF_COURSE)): ?>
		　<input type="text" name="s_course" value="<?php echo (isset($s_course))? $s_course:''; ?>" maxlength="50">
		<?php $btn = true; ?>
<?php else: ?>
		<span style="color: #0000CC">　<?php echo (isset($s_course))? $s_course:''; ?></span>
<?php endif; ?>
	</label>
</div>
<?php echo $errMsg['s_course']; ?>
<?php endif; ?>

<?php if ($btn): ?>
<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('変更する'); ?>" name="sub_state"></div>
<input type="hidden" name="mode" value="profile">
<?php endif; ?>

<?php echo Form::close(); ?>
</div>

<div style="margin-top: 10px; font-size: 80%;">
<div style="font-weight: bold; background-color: #AAAAFF; padding: 4px 0;"><?php echo __('メールアドレス'); ?></div>
<?php echo Form::open(array('action'=>'/s/profile'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-top: 8px;">
	<label><?php echo __('現在のメールアドレス'); ?><br>
		<span style="color: #0000CC">　
		<?php if ($aStudent['stMail']): ?>
			<?php echo $aStudent['stMail']; ?>
		<?php else: ?>
			<?php echo __('登録なし'); ?>
		<?php endif; ?>
		</span>
	</label>
</div>

<div style="margin-top: 5px;">
	<label><?php echo __('新しいメールアドレス'); ?><br>
		　<input type="text" name="s_mail" value="<?php echo (isset($s_mail))? $s_mail:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['s_mail']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('新しいメールアドレス（確認）'); ?><br>
		　<input type="text" name="s_mail_chk" value="<?php echo (isset($s_mail_chk))? $s_mail_chk:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['s_mail_chk']; ?>

<div style="margin-top: 10px;">
	<label><?php echo __('サブメールアドレス'); ?><br>
		　<input type="text" name="s_submail" value="<?php echo (isset($s_submail))? $s_submail:''; ?>" maxlength="200">
	</label>
</div>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('変更する'); ?>" name="sub_state"></div>
<input type="hidden" name="mode" value="mail">

<?php echo Form::close(); ?>
</div>

<?php if (!$aGroup['gtLDAP']): ?>
<div style="margin-top: 10px; font-size: 80%;">
<div style="font-weight: bold; background-color: #AAAAFF; padding: 4px 0;"><?php echo __('パスワード'); ?></div>
<?php echo Form::open(array('action'=>'/s/profile'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-top: 8px;">
	<label><?php echo __('現在のパスワード'); ?><br>
		　<input type="password" name="s_pass_now" value="" maxlength="32">
	</label>
</div>
<?php echo $errMsg['s_pass_now']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('新しいパスワード'); ?><br>
		　<input type="password" name="s_pass_edit" value="" maxlength="32"><br>
		<div style="font-size: 90%;"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?></div>
	</label>
</div>
<?php echo $errMsg['s_pass_edit']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('新しいパスワード（確認）'); ?><br>
		　<input type="password" name="s_pass_chk" value="" maxlength="32">
	</label>
</div>
<?php echo $errMsg['s_pass_chk']; ?>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('変更する'); ?>" name="sub_state"></div>
<input type="hidden" name="mode" value="pass">

<?php echo Form::close(); ?>
</div>
<?php endif; ?>

<?php if (CL_CAREERTASU_MODE): ?>
<div style="margin-top: 10px; font-size: 80%;">
<div style="font-weight: bold; background-color: #AAAAFF; padding: 4px 0;"><?php echo __('学生の解約'); ?></div>
<?php echo Form::open(array('action'=>'/s/profile/remove'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="text-align: center; margin-top: 5px;"><input type="submit" name="check" value="<?php echo __('解約する'); ?>" name="sub_state"></div>

<?php echo Form::close(); ?>
</div>
<?php endif; ?>














