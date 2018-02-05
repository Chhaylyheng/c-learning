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
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
?>

<div id="content-inner" class="login">

	<h1 class="mr0"><?php echo __('プロフィールの登録'); ?></h1>

	<div class="info-box">
		<p><?php echo __('下記、全ての項目を入力してください。'); ?></p>

		<form action="<?php echo $sAction; ?>" method="post">

<?php if (isset($error['profile_error'])): ?>
<p class="error-box"><?php echo $error['profile_error']; ?></p>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_MAIL) && !$aStudent['stMail']): ?>
	<p class="mt12"><?php echo __('メールアドレス'); ?></p>
	<p class="mt4"><input type="text" name="s_mail" value="<?php echo $s_mail; ?>" maxlength="200"<?php echo $errClass['s_mail']; ?>></p>
	<?php echo $errMsg['s_mail']; ?>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_NO) && !$aStudent['stNO']): ?>
	<p class="mt12"><?php echo __('学籍番号'); ?></p>
	<p class="mt4"><input type="text" name="s_no" value="<?php echo $s_no; ?>" maxlength="20"<?php echo $errClass['s_no']; ?>></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('※半角英数字と一部記号（.-_）が利用可能です。'); ?></p>
	<?php echo $errMsg['s_no']; ?>
<?php endif; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_SEX) && !$aStudent['stSex']): ?>
	<p class="mt12"><?php echo __('性別'); ?></p>
	<p class="mt4 inline-box select-box">
		<select name="s_sex" style="background-image: none;" class="width-auto">
<?php foreach ($aSex as $i => $v): ?>
<?php if ($i == 0) continue; ?>
<?php $sSel = ($i == $s_sex)? ' selected':''; ?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $v; ?></option>
<?php endforeach;?>
		</select>
	</p>
	<?php echo $errMsg['s_sex']; ?>
<?php endif; ?>

<?php if (CL_CAREERTASU_MODE && ($aBase[$sColumn] & \Clfunc_Flag::S_GET_SCHOOL) && !$aStudent['cmKCode']): ?>
	<p class="mt12"><?php echo __('学校名'); ?></p>
	<p class="mt4"><input type="text" class="width-24em text-left<?php echo $errClass['s_school']; ?>" placeholder="<?php echo __('所属学校名を入力します'); ?>" maxlength="50" value="<?php echo $s_school; ?>" name="s_school" id="form_s_school"></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('学校名の一部分を入力すると候補が表示されますので、候補のリストより選択してください。'); ?><?php echo __('候補にない場合は、新規に登録されます。'); ?></p>
	<?php echo $errMsg['s_school']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_DEPT) && !$aStudent['stDept']): ?>
	<p class="mt12"><?php echo __('学部'); ?></p>
	<p class="mt4"><input type="text" name="s_dept" value="<?php echo $s_dept; ?>" maxlength="50"<?php echo $errClass['s_dept']; ?>></p>
	<?php echo $errMsg['s_dept']; ?>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_SUBJECT) && !$aStudent['stSubject']): ?>
	<p class="mt12"><?php echo __('学科'); ?></p>
	<p class="mt4"><input type="text" name="s_subject" value="<?php echo $s_subject; ?>" maxlength="50"<?php echo $errClass['s_subject']; ?>></p>
	<?php echo $errMsg['s_subject']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_YEAR) && !$aStudent['stYear']): ?>
	<p class="mt12"><?php echo __('学年'); ?></p>
	<p class="mt4"><input type="text" name="s_year" value="<?php echo $s_year; ?>" maxlength="10"<?php echo $errClass['s_year']; ?>></p>
	<p class="mt4 font-silver font-size-90"><?php echo __('※数値で入力します。'); ?></p>
	<?php echo $errMsg['s_year']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_CLASS) && !$aStudent['stClass']): ?>
	<p class="mt12"><?php echo __('クラス'); ?></p>
	<p class="mt4"><input type="text" name="s_class" value="<?php echo $s_class; ?>" maxlength="50"<?php echo $errClass['s_class']; ?>></p>
	<?php echo $errMsg['s_class']; ?>
<?php endif; ?>

<?php if (($aBase[$sColumn] & \Clfunc_Flag::S_GET_COURSE) && !$aStudent['stCourse']): ?>
	<p class="mt12"><?php echo __('コース'); ?></p>
	<p class="mt4"><input type="text" name="s_course" value="<?php echo $s_course; ?>" maxlength="50"<?php echo $errClass['s_course']; ?>></p>
	<?php echo $errMsg['s_course']; ?>
<?php endif; ?>
<?php endif; ?>

	<p class="button-box mt16"><button type="submit" name="mode" value="profile" class="button do na"><?php echo __('変更する'); ?></button></p>

	</form>
</div>
</div>
