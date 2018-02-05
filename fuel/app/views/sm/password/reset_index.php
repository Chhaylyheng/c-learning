<div style="text-align:center;" align="center"><span style="font-size:medium;"><?php echo __('学生パスワードの再設定'); ?></span></div>

<?php echo Form::open(array('action'=>'/s/password','method'=>'post')); ?>

<?php
	$errClass = array('reset_mail'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $k => $v):
		if (isset($error[$k])):
			$errMsg[$k] = '<div style="color:#CC0000;">'.$error[$k].'</div>';
		endif;
	endforeach;
?>

<div style="margin-top: 5px;">
	<label><?php echo __('メールアドレス'); ?><br>
		<input type="text" name="reset_mail" value="<?php echo (isset($reset_mail))? $reset_mail:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['reset_mail']; ?>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('パスワード再設定メールを送信'); ?>" name="sub_state"></div>

<?php echo Clfunc_Mobile::hr(); ?>

<div><?php echo __('パスワードを再設定するメールアドレスを入力してください。'); ?></div>
<div><?php echo __('入力されたメールアドレスに再設定手続きのメールを送信いたします。'); ?></div>
<div style="color: #CC0000;"><?php echo __('※メールアドレスを登録していない場合は再設定できません。その場合は履修している講義の先生にパスワードのリセットをして貰ってください。'); ?></div>

<?php echo Form::close(); ?>

<?php echo Clfunc_Mobile::hr(); ?>
<div><a href="/s"><?php echo __('ログインに戻る'); ?></a></div>
