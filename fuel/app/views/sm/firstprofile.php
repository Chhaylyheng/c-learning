<?php
$sAction = '/s/login/getprofile';
$sColumn = 'gtStudentGetFlag';
if (isset($sCtID)):
	$sAction = '/s/class/getprofile/'.$sCtID;
	$aBase = $aClass;
	$sColumn = 'ctStudentGetFlag';
else:
	$aBase = $aGroup;
endif;

	$errClass = array(
		's_no'=>'',
		's_sex'=>'',
		's_school'=>'',
		's_dept'=>'',
		's_subject'=>'',
		's_year'=>'',
		's_class'=>'',
		's_course'=>'',
		's_mail'=>''
	);
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errMsg[$key] = '<div style="color:#CC0000;">'.$error[$key].'</div>';
			endif;
		endforeach;
	endif;
?>

<div style="text-align:center; margin-top: 5px;" align="center"><span style="font-size:medium;"><?php echo __('プロフィールの登録'); ?></span></div>

<div style="margin-top: 5px;"><?php echo __('下記、全ての項目を入力してください。'); ?></div>

<?php if (isset($error['profile_error'])): ?>
<div style="color:#CC0000; margin-top: 5px;"><?php echo $error['profile_error']; ?></div>
<?php endif; ?>

<div style="margin-top: 5px;">
<form action="<?php echo $sAction; ?>" method="post">
<?php echo Clfunc_Mobile::SesID('post'); ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_MAIL) && !$aStudent['stMail']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('メールアドレス'); ?><br>
		<input type="text" name="s_mail" value="<?php echo (isset($s_mail))? $s_mail:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['s_mail']; ?>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_NO) && !$aStudent['stNO']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学籍番号'); ?><br>
		<input type="text" name="s_no" value="<?php echo (isset($s_no))? $s_no:''; ?>" maxlength="20"><br>
		<div style="font-size: 80%;"><?php echo __('※半角英数字と一部記号（.-_）が利用可能です。'); ?></div>
	</label>
</div>
<?php echo $errMsg['s_no']; ?>
<?php endif; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_SEX) && !$aStudent['stSex']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('性別'); ?><br>
		<select name="s_sex">
<?php foreach ($aSex as $i => $v): ?>
<?php if ($i == 0) continue; ?>
<?php $sSel = ($i == $s_sex)? ' selected':''; ?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $v; ?></option>
<?php endforeach;?>
		</select>
	</label>
</div>
<?php echo $errMsg['s_sex']; ?>
<?php endif; ?>

<?php if (CL_CAREERTASU_MODE && ($aBase[$sColumn] & \Clfunc_Flag::S_GET_SCHOOL) && !$aStudent['cmKCode']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学校'); ?><br>
		<input type="text" name="s_school" value="<?php echo  (isset($s_school))? $s_school:''; ?>" maxlength="50">
	</label>
</div>
<?php echo $errMsg['s_school']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_DEPT) && !$aStudent['stDept']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学部'); ?><br>
		<input type="text" name="s_dept" value="<?php echo (isset($s_dept))? $s_dept:''; ?>" maxlength="50">
	</label>
</div>
<?php echo $errMsg['s_dept']; ?>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_SUBJECT) && !$aStudent['stSubject']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学科'); ?><br>
		<input type="text" name="s_subject" value="<?php echo (isset($s_subject))? $s_subject:''; ?>" maxlength="50">
	</label>
</div>
<?php echo $errMsg['s_subject']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_YEAR) && !$aStudent['stYear']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('学年'); ?><br>
		<input type="text" name="s_year" value="<?php echo (isset($s_year))? $s_year:''; ?>" maxlength="10">
		<div style="font-size: 80%;"><?php echo __('※数値で入力します。'); ?></div>
	</label>
</div>
<?php echo $errMsg['s_year']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_CLASS) && !$aStudent['stClass']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('クラス'); ?><br>
		<input type="text" name="s_class" value="<?php echo (isset($s_class))? $s_class:''; ?>" maxlength="50">
	</label>
</div>
<?php echo $errMsg['s_class']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_COURSE) && !$aStudent['stCourse']): ?>
<div style="margin-top: 5px;">
	<label><?php echo __('コース'); ?><br>
		<input type="text" name="s_course" value="<?php echo (isset($s_course))? $s_course:''; ?>" maxlength="50">
	</label>
</div>
<?php echo $errMsg['s_course']; ?>
<?php endif; ?>
<?php endif; ?>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('変更する'); ?>" name="sub_state"></div>

</form>
</div>