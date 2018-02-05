<?php
$sAction = 'add';
$sButton = __('登録する');

if (isset($aStu)):
	$sAction = 'edit'.DS.$aStu['stID'];
	$sButton = __('更新する');
endif;
?>
<div class="info-box">
	<form action="/t/student/<?php echo $sAction; ?>" method="post">
	<?php if (isset($error['studentadd'])): ?>
		<p class="error-box"><?php echo $error['studentadd'] ?></p>
	<?php endif; ?>

	<?php
		$errClass = array(
			's_login'=>'',
			's_pass'=>'',
			's_name'=>'',
			's_no'=>'',
			's_sex'=>'',
			's_dept'=>'',
			's_subject'=>'',
			's_year'=>'',
			's_class'=>'',
			's_course'=>''
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
	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>

<?php if (!isset($aStu)): ?>
	<p class="mt4"><?php echo __('ログインID'); ?><sup>*</sup></p>
	<p class="mt4"><input type="text" name="s_login" maxlength="20" value="<?php echo $s_login; ?>"<?php echo $errClass['s_login']; ?>></p>
	<?php echo $errMsg['s_login']; ?>

<?php if (!$aGroup['gtLDAP']): ?>
	<p class="mt16"><?php echo __('パスワード'); ?></p>
	<p class="mt4"><input type="password" name="s_pass" maxlength="32" value="<?php echo $s_pass; ?>" <?php echo $errClass['s_pass']; ?>></p>
	<p class="mt4 font-size-80"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?><br><?php echo __('※パスワードを省略すると、パスワードを自動で生成します。'); ?></p>
	<?php echo $errMsg['s_pass']; ?>
<?php endif; ?>
<?php endif; ?>

	<p class="mt16"><?php echo __('氏名'); ?><sup>*</sup></p>
	<p class="mt4"><input type="text" name="s_name" maxlength="50" value="<?php echo $s_name; ?>"<?php echo $errClass['s_name']; ?>></p>
	<?php echo $errMsg['s_name']; ?>

	<p class="mt16"><?php echo __('性別'); ?><sup>*</sup></p>
	<p class="mt4 inline-box select-box">
		<select name="s_sex" style="background-image: none;" class="width-auto">
<?php foreach ($aSex as $i => $v): ?>
<?php $sSel = ($i == $s_sex)? ' selected':''; ?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $v; ?></option>
<?php endforeach;?>
		</select>
	</p>
	<?php echo $errMsg['s_sex']; ?>

	<p class="mt16"><?php echo __('学籍番号'); ?></p>
	<p class="mt4"><input type="text" name="s_no" maxlength="20" value="<?php echo $s_no; ?>"<?php echo $errClass['s_no']; ?>></p>
	<?php echo $errMsg['s_no']; ?>

<?php if (!CL_CAREERTASU_MODE): ?>
	<p class="mt16"><?php echo __('学部'); ?></p>
	<p class="mt4"><input type="text" name="s_dept" maxlength="50" value="<?php echo $s_dept; ?>"<?php echo $errClass['s_dept']; ?>></p>
	<?php echo $errMsg['s_dept']; ?>

	<p class="mt16"><?php echo __('学科'); ?></p>
	<p class="mt4"><input type="text" name="s_subject" maxlength="50" value="<?php echo $s_subject; ?>"<?php echo $errClass['s_subject']; ?>></p>
	<?php echo $errMsg['s_subject']; ?>

	<p class="mt16"><?php echo __('学年'); ?></p>
	<p class="mt4"><input type="text" name="s_year" maxlength="10" value="<?php echo ($s_year)? $s_year:''; ?>"<?php echo $errClass['s_year']; ?>></p>
	<p class="mt4 font-size-80"><?php echo __('※数値で入力します。'); ?></p>
	<?php echo $errMsg['s_year']; ?>
<?php endif; ?>

	<p class="mt16"><?php echo __('クラス'); ?></p>
	<p class="mt4"><input type="text" name="s_class" maxlength="50" value="<?php echo $s_class; ?>"<?php echo $errClass['s_class']; ?>></p>
	<?php echo $errMsg['s_class']; ?>

	<p class="mt16"><?php echo __('コース'); ?></p>
	<p class="mt4"><input type="text" name="s_course" maxlength="50" value="<?php echo $s_course; ?>"<?php echo $errClass['s_course']; ?>></p>
	<?php echo $errMsg['s_course']; ?>

	<p class="button-box mt16"><button type="submit" class="button do na" name="sub_state" value="1"><?php echo $sButton; ?></button></p>
	</form>
</div>

