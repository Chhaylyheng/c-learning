<?php
$sAction = 'add';
$sButton = __('登録する');
$sPass = __('※パスワードを省略すると、パスワードを自動で生成します。');
if (isset($aStudent)):
	$sAction = 'edit'.DS.$aStudent['stID'];
	$sButton = __('更新する');
	$sPass = __('※パスワードを省略すると、パスワードを変更せずに更新します。');
endif;
?>
<div class="info-box">
	<form action="/org/student/<?php echo $sAction; ?>" method="post">
	<?php if (isset($error['studentadd'])): ?>
		<p class="error-box"><?php echo $error['studentadd'] ?></p>
	<?php endif; ?>

	<?php
		$errClass = array(
			's_login'=>'',
			's_pass'=>'',
			's_name'=>'',
			's_no'=>'',
			's_school'=>'',
			's_dept'=>'',
			's_subject'=>'',
			's_year'=>'',
			's_class'=>'',
			's_course'=>'',
		);
		$errMsg = $errClass;

		if (!is_null($error)):
			foreach ($errClass as $key => $val):
				if (isset($error[$key])):
					$errClass[$key] = ' input-error';
					$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
				endif;
			endforeach;
		endif;
	?>
	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>

	<div style="margin: auto;" class="formControl">
	<div class="formGroup">
		<div class="formLabel"><?php echo __('ログインID'); ?><sup>*</sup></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_login']; ?>" name="s_login" maxlength="50" value="<?php echo $s_login; ?>">
			<?php echo $errMsg['s_login']; ?>
		</div>
	</div>

<?php if (!$aGroup['gtLDAP']): ?>
	<div class="formGroup">
		<div class="formLabel"><?php echo __('パスワード'); ?></div>
		<div class="formContent inline-box">
			<input type="password" class="width-24em text-left<?php echo $errClass['s_pass']; ?>" name="s_pass" maxlength="32" value="" autocomplete="off">
			<p class="mt4 font-size-80"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?><br>
			<?php echo $sPass; ?></p>
			<?php echo $errMsg['s_pass']; ?>
		</div>
	</div>
<?php endif; ?>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('氏名'); ?><sup>*</sup></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_name']; ?>" name="s_name" maxlength="50" value="<?php echo $s_name; ?>">
			<?php echo $errMsg['s_name']; ?>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('性別'); ?><sup>*</sup></div>
		<div class="formContent inline-box select-box">
			<select name="s_sex" style="background-image: none;" class="width-auto">
<?php foreach ($aSex as $i => $v): ?>
<?php $sSel = ($i == $s_sex)? ' selected':''; ?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $v; ?></option>
<?php endforeach;?>
			</select>
		</div>
	</div>

<?php if (!CL_CAREERTASU_MODE): ?>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学籍番号'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_no']; ?>" name="s_no" value="<?php echo $s_no; ?>" maxlength="20">
			<?php echo $errMsg['s_no']; ?>
		</div>
	</div>

<?php endif; ?>

<?php if (CL_CAREERTASU_MODE): ?>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学校'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_school']; ?>" name="s_school" value="<?php echo $s_school; ?>" maxlength="50" id="form_s_school">
			<p class="mt4 font-size-80"><?php echo __('学校名の一部分を入力すると候補が表示されますので、候補のリストより選択してください。'); ?><br><?php echo __('候補にない場合は、新規に登録されます。'); ?><br>
			<?php echo $errMsg['s_school']; ?>
		</div>
	</div>

<?php endif; ?>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学部'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_dept']; ?>" name="s_dept" value="<?php echo $s_dept; ?>" maxlength="50">
			<?php echo $errMsg['s_dept']; ?>
		</div>
	</div>

<?php if (!CL_CAREERTASU_MODE): ?>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学科'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_subject']; ?>" name="s_subject" value="<?php echo $s_subject; ?>" maxlength="50">
			<?php echo $errMsg['s_subject']; ?>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学年'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_year']; ?>" name="s_year" value="<?php echo ($s_year)? $s_year:''; ?>" maxlength="10">
			<p class="mt4 font-size-80"><?php echo __('※数値で入力してください。'); ?><br>
			<?php echo $errMsg['s_year']; ?>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('クラス'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_class']; ?>" name="s_class" value="<?php echo $s_class; ?>" maxlength="50">
			<?php echo $errMsg['s_class']; ?>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('コース'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['s_course']; ?>" name="s_course" value="<?php echo $s_course; ?>" maxlength="50">
			<?php echo $errMsg['s_course']; ?>
		</div>
	</div>

<?php endif; ?>

</div>

	<p class="button-box mt16"><button type="submit" class="button do na"><?php echo $sButton; ?></button></p>
	</form>
</div>

