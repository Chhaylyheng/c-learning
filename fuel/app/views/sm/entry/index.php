<div style="text-align:center;" align="center"><span style="font-size:medium;"><?php echo __('学生アカウントの新規登録'); ?></span></div>

<?php echo Form::open(array('action'=>'/s/entry/entryform','method'=>'post')) ; ?>

<?php if (isset($error['entry'])): ?>
	<div style="margin-top: 5px;"><span style="color:#CC0000;"><?php echo $error['entry'] ?></span></div>
<?php endif; ?>

<?php
	$errClass = array('sent_name'=>'','sent_login'=>'','sent_mail'=>'','sent_pass'=>'','sent_passchk'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $c => $v):
		if (isset($error[$c])):
			$errMsg[$c] = '<div style="color:#CC0000;">'.$error[$c].'</div>';
		endif;
	endforeach;
?>

<div style="margin-top: 5px;">
	<label><?php echo __('氏名'); ?><br>
		<input type="text" name="sent_name" value="<?php echo (isset($sent_name))? $sent_name:''; ?>" maxlength="50">
	</label>
</div>
<?php echo $errMsg['sent_name']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('メールアドレス'); ?><br>
		<input type="text" name="sent_mail" value="<?php echo (isset($sent_mail))? $sent_mail:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['sent_mail']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('パスワード'); ?><br>
		<input type="password" name="sent_pass" value="" maxlength="32"><br>
		<div style="font-size: 80%;"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?></div>
	</label>
</div>
<?php echo $errMsg['sent_pass']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('パスワード（確認）'); ?><br>
		<input type="password" name="sent_passchk" value="" maxlength="32">
	</label>
</div>
<?php echo $errMsg['sent_passchk']; ?>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('登録する'); ?>" name="sub_state"></div>

<?php echo Clfunc_Mobile::hr(); ?>

<div><?php echo __('■入力したメールアドレスは今後ログインする際に利用します。'); ?></div>
<div><?php echo __('■メールアドレスを受信する端末で迷惑メール対策を行っている方は、ドメイン指定受信設定で『c-learning.jp』を受信できるようにしてください。'); ?></div>
<div style="color:#CC0000;"><?php echo __('※登録したメールアドレスが履修している講義の先生に知られることはありません。'); ?></div>

<?php echo Clfunc_Mobile::hr(); ?>

<div><?php echo Clfunc_Mobile::emj('SMILE'); ?><a href="/s/login"><?php echo __('すでにアカウントをお持ちの方はこちら'); ?></a></div>

<?php echo Form::close(); ?>
