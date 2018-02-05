<?php
$sAction = 'add';
$sButton = __('登録する');
$sPass = __('※パスワードを省略すると、パスワードを自動で生成します。');
if (isset($aTeacher)):
	$sAction = 'edit'.DS.$aTeacher['ttID'];
	$sButton = __('更新する');
	$sPass = __('※パスワードを省略すると、パスワードを変更せずに更新します。');
endif;
?>
<div class="info-box">
	<form action="/org/teacher/<?php echo $sAction; ?>" method="post">
	<?php if (isset($error['teacheradd'])): ?>
		<p class="error-box"><?php echo $error['teacheradd'] ?></p>
	<?php endif; ?>

	<?php
		$errClass = array('t_mail'=>'','t_pass'=>'','t_name'=>'', 't_dept'=>'', 't_subject'=>'','t_uid'=>'','s_date'=>'','e_date'=>'');
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
		<div class="formLabel"><?php echo __('メールアドレス'); ?><sup>*</sup></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['t_mail']; ?>" name="t_mail" maxlength="200" value="<?php echo $t_mail; ?>">
			<?php echo $errMsg['t_mail']; ?>
		</div>
	</div>

<?php if (!$aGroup['gtLDAP']): ?>
	<div class="formGroup">
		<div class="formLabel"><?php echo __('パスワード'); ?></div>
		<div class="formContent inline-box">
			<input type="password" class="width-24em text-left<?php echo $errClass['t_pass']; ?>" name="t_pass" maxlength="32" value="" autocomplete="off">
			<p class="mt4 font-size-80"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?><br><?php echo $sPass; ?></p>
			<?php echo $errMsg['t_pass']; ?>
		</div>
	</div>
<?php endif; ?>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('氏名'); ?><sup>*</sup></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['t_name']; ?>" name="t_name" maxlength="50" value="<?php echo $t_name; ?>">
			<?php echo $errMsg['t_name']; ?>
		</div>
	</div>

<?php if (CL_CAREERTASU_MODE): ?>

<?php
	$aCheck = array(0=>'',1=>'');
	$aCheck[$t_plan] = ' checked';
?>
	<div class="formGroup">
		<div class="formLabel"><?php echo __('プラン'); ?></div>
		<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="t_plan" value="0"<?php echo $aCheck[0]; ?>><?php echo __('アンケート'); ?></label>
				<label class="formChk"><input type="radio" name="t_plan" value="1"<?php echo $aCheck[1]; ?>><?php echo __('スタンダード'); ?></label>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('利用期間'); ?><sup>*</sup></div>
		<div class="formContent inline-box">
			<input type="text" name="s_date" value="<?php echo $s_date; ?>" id="from" class="width-10em text-center" readonly>
			～
			<input type="text" name="e_date" value="<?php echo $e_date; ?>" id="to"   class="width-10em text-center ml8" readonly>
			<?php echo $errMsg['s_date']; ?>
			<?php echo $errMsg['e_date']; ?>
		</div>
	</div>

<?php else: ?>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学部名'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['t_dept']; ?>" name="t_dept" value="<?php echo $t_dept; ?>" size="30" maxlength="50">
			<?php echo $errMsg['t_dept']; ?>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学科名'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['t_subject']; ?>" name="t_subject" value="<?php echo $t_subject; ?>" size="30" maxlength="50">
			<?php echo $errMsg['t_subject']; ?>
		</div>
	</div>

<?php endif; ?>

<?php if ($aGroup['gtLDAP']): ?>
	<div class="formGroup">
		<div class="formLabel"><?php echo 'uid'; ?><sup>*</sup></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['t_uid']; ?>" name="t_uid" maxlength="50" value="<?php echo $t_uid; ?>">
			<p class="mt4 font-size-80"><?php echo __('LDAP連携で利用するログインIDを入力してください。'); ?></p>
			<?php echo $errMsg['t_uid']; ?>
		</div>
	</div>
<?php endif; ?>

</div>

	<p class="button-box mt16"><button type="submit" class="button do na" name="sub_state" value="1"><?php echo $sButton; ?></button></p>
	</form>
</div>

